<?php
/***************************************************************************
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 ***************************************************************************/

/**
 * User/group/authorization component:
 * 	- login, session start
 * 	- user/group creation/handling
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: 3403 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2011-10-31 12:43:28 +0100 (Mon, 31 Oct 2011) $
 * 
 * $Id: be_auth.php 3403 2011-10-31 11:43:28Z bato $
 */
class BeAuthComponent extends Object {
	var $controller	;
	var $Session	= null ;
	var $user		= null ;
	var $isValid	= true;
	var $changePasswd	= false;
	var $sessionKey = "BEAuthUser" ;
	const SESSION_INFO_KEY = "BESession" ;
	var $authResult	= 'OK';
	

	/**
	 * Set current user, if already logged in and/or valid
	 * 
	 * @param object $controller
	 */
	function initialize(&$controller)
	{
		$conf = Configure::getInstance() ;		
		$this->sessionKey = $conf->session["sessionUserKey"] ;
		
		$this->controller 	= $controller;
		$this->Session 		= &$controller->Session;
		
		if($this->checkSessionKey()) {
			$this->user 	= $this->Session->read($this->sessionKey);
		}
		$this->controller->set($this->sessionKey, $this->user);
	}

	private function checkSessionKey() {
		$res = true;
		if(!isset($this->Session)) {
			$res = false;
			$this->log("Session component not set!");
		} else if(!$this->Session->valid()) {
			$res = false;
			$this->log("Session not valid!");
		} else if(!$this->Session->check($this->sessionKey)) {
			$res = false;
		}
		return $res;
	}
	
	
	/**
	 * User authentication on external service (OpenID. LDAP, Shibbolet...)
	 *
	 * @param string $userid
	 * @return boolean 
	 */
	public function externalLogin($userid, $extAuthType, array $extAuthOptions = array()) {
		$userModel = ClassRegistry::init('User');
		// if user / auth_type not foud return false
		$user->create();
		$conditions = array("User.userid" 	=> $userid, "User.auth_type" => $extAuthType );
		$userModel->containLevel("default");
		$u = $userModel->find($conditions);
		if(empty($u["User"])) {
			$this->logout() ;
			return false ;
		}
		// load authType component
		$componentClass = "BeAuth" . Inflector::camelize($extAuthType);
		// TODO: load component dynamically??
		if(!App::import("Component", $componentClass)) {
			throw new BeditaException(__("External auth component not found: ",true) . $extAuthType);
		}
		$componentClass .= "Component";
		$authComponent = new $componentClass();
		
	}
	
	/**
	 * User authentication
	 *
	 * @param string $userid
	 * @param string $password
	 * @return boolean 
	 */
	public function login($userid, $password, $policy=null, $auth_group_name=array()) {
		$userModel = ClassRegistry::init('User');
		$conditions = array(
			"User.userid" 	=> $userid,
			"User.passwd" 	=> md5($password),
		);
		
		$userModel->containLevel("default");
		$u = $userModel->find($conditions);
		if(!$this->loginPolicy($userid, $u, $policy, $auth_group_name)) {
			return false ;
		}
		$userModel->compact($u) ;
		$this->user = $u;
		$this->setSessionVars();
		
		return true ;
	}
	
	/**
	 * Check policy using $policy array or config if null
	 * @return boolean
	 */
	private function loginPolicy($userid, $u, $policy, $auth_group_name=array()) {
		$userModel = ClassRegistry::init("User");
		// If fails, exit
		if(empty($u["User"])) {
			// look for existing user
			$userModel->containLevel("minimum");
			$u = $userModel->find(array("User.userid" => $userid));
			if(!empty($u["User"])) {
				$u["User"]["last_login_err"]= date('Y-m-d H:i:s');
				$u["User"]["num_login_err"]=$u["User"]["num_login_err"]+1;
				$userModel->save($u);
			}
			$this->logout();
			return false;
		}

		if($policy == null) {
			$policy = array(); // load backend defaults
			$config = Configure::getInstance() ;
			$policy['maxLoginAttempts'] = $config->loginPolicy['maxLoginAttempts'];
			$policy['maxNumDaysInactivity'] = $config->loginPolicy['maxNumDaysInactivity'];
			$policy['maxNumDaysValidity'] = $config->loginPolicy['maxNumDaysValidity'];
		}

		// check activity & validity
		if(!isset($u["User"]["last_login"])) {
			$u["User"]["last_login"] = date('Y-m-d H:i:s');
		}
		$daysFromLastLogin = (time() - strtotime($u["User"]["last_login"]))/(86400000);
		$this->isValid = $u['User']['valid'];
		
		if($u["User"]["num_login_err"] >= $policy['maxLoginAttempts']) {
			$this->isValid = false;
			$this->log("Max login attempts error, user: ".$userid);

		} else if($daysFromLastLogin > $policy['maxNumDaysInactivity']) {
			$this->isValid = false;
			$this->log("Max num days inactivity: user: ".$userid." days: ".$daysFromLastLogin);
			
		} else if($daysFromLastLogin > $policy['maxNumDaysValidity']) {
			$this->changePasswd = true;
		}
		
		// check group auth
		$groups = array();
		$authorized = false;
		foreach ($u['Group'] as $g) {
			array_push($groups, $g['name']) ;
			if( $g['backend_auth'] == 1 || in_array($g['name'], $auth_group_name) ) {
				$authorized = true;
			}
		}

		if($authorized === false) {
			$this->log("User login not authorized: ".$userid);
			// TODO: special message?? or not for security???
			return false;
		}

		$u['User']['valid'] = $this->isValid; // validity may have changed
		
		if($this->isValid) {
			$u["User"]["num_login_err"] = 0;
			$u["User"]["last_login"] = date('Y-m-d H:i:s');
		}		
        
		$data["User"] = $u["User"];
        $userModel->save($data); //, true, array('num_login_err','last_login_err','valid','last_login'));
        
		if(!$this->isValid) {
			$this->logout();
			return false;
		}
		
		return true;
	}

	public function changePassword($userid, $password) {
		if (empty($userid) || empty($password)) {
			return false;
		}
		$userModel = ClassRegistry::init('User');
		$u = $userModel->find("first", array(
			"conditions" => array(
				"User.userid" => $userid
			),
			"contain" => array()
		));
		$u["User"]["passwd"] = md5($password);
		$u["User"]["num_login_err"]=0;
		if (!$userModel->save($u)) {
			return false;
		}
		return true;
	}
	
	/**
	 * User logout: remove session data for the user
	 *
	 * @return boolean
	 */
	public function logout() {
		$this->user = null ;
		
		if(isset($this->Session)) {
			$this->Session->destroy();
		}
		
		if(isset($this->controller)) {
			$this->controller->set($this->sessionKey, null);
		}
		return true ;
	}
	
	public function isLogged() {
		
		if ($this->checkSessionKey()) {
			if(@empty($this->user)) $this->user 	= $this->Session->read($this->sessionKey);
			$this->controller->set($this->sessionKey, $this->user);
			// update session info
			$this->Session->write(self::SESSION_INFO_KEY, array("userAgent" => $_SERVER['HTTP_USER_AGENT'], 
				"ipNumber" => $_SERVER['REMOTE_ADDR'], "time" => time()));
			
			return true ;
		} else {
			$this->user = null;
		}
		
		if(!isset($this->controller)) return false ;
		
		$this->controller->set($this->sessionKey, $this->user);
		
		return false ;
	}
	
	public function isUserGroupAuthorized($groups=array()) {
		if (empty($this->user)) {
			if (!$this->isLogged())
				return false;
		}
		$backendGroups = ClassRegistry::init('Group')->find("list", array(
				"fields" => "name",
				"conditions" => array("backend_auth" => 1)
				)
			);
		$groups = array_merge($backendGroups,$groups);
		$groupsIntersect = array_intersect($this->user["groups"], $groups);
		if (empty($groupsIntersect)) {
			return false;
		}
		return true;
	}
	
	/**
	 * Create new user
	 *
	 * @param array $userData
	 * @param array $groups (contains groups' names)
	 * @param boolean $notify (send newUser backend notify)
	 *
	 * @return int id of user created
	 */
	public function createUser($userData, $groups=NULL, $notify=true) {
		$user = ClassRegistry::init('User');
		$user->containLevel("minimum");
		$u = $user->findByUserid($userData['User']['userid']);
		if(!empty($u["User"])) {
			$this->log("User ".$userData['User']['userid']." already created");
			throw new BeditaException(__("User already created",true));
		}
		
		$this->userGroupModel($userData, $groups);
		if ($notify) {
			$user->Behaviors->attach('Notify');
		}
		
		if(!$user->passwordValidation($userData['User'])) {
			throw new BeditaException(__("Password not valid",true) . " - " . Configure::read("loginPolicy.passwordErrorMessage"));
		}
		if (!empty($userData['User']['passwd'])) {
			$userData['User']['passwd'] = md5($userData['User']['passwd']);
		}
		$user->create();
		if(!$user->save($userData)) {
			throw new BeditaException(__("Error saving user",true), $user->validationErrors);
		}
		if ($notify) {
			$user->Behaviors->detach('Notify');
		}
		return $user->id;
	}
	
	public function checkConfirmPassword($password, $confirmedPassword) {
		if (trim($password) !== trim($confirmedPassword)) {
			return false;
		}
		return true;
	}

	private function userGroupModel(&$userData, $groups) {
		if(isset($groups)) {
			$userData['Group']= array();
			$userData['Group']['Group']= array();
			$groupModel = ClassRegistry::init('Group');
			foreach ($groups as $g) {
				$group =  $groupModel->findByName($g);
				array_push($userData['Group']['Group'], $group['Group']['id']) ;
			}
		}
	}
	
	public function updateUser($userData, $groups=NULL)	{
		$this->userGroupModel($userData, $groups);
		$user = ClassRegistry::init('User');
		if($userData['User']['valid'] == '1') { // reset number of login error, if user is valid
			$userData['User']['num_login_err'] = '0';
		}
		
		$user->Behaviors->attach('Notify');
		if(!$user->passwordValidation($userData['User'])) {
			throw new BeditaException(__("Password not valid",true). " - " . Configure::read("loginPolicy.passwordErrorMessage"));
		}
		if (!empty($userData['User']['passwd'])) {
			$userData['User']['passwd'] = md5($userData['User']['passwd']);
		}
		if(!$user->save($userData))
			throw new BeditaException(__("Error updating user",true), $user->validationErrors);
		return true;
	}
	
	public function removeGroup($groupName) {
		$groupModel = ClassRegistry::init('Group');
		$g =  $groupModel->findByName($groupName);
		if ($g['Group']['immutable'] == 1) {
			throw new BeditaException(sprintf(__("Immutable group %s", true),$groupName));
		}
		if(!$groupModel->del($g['Group']['id'])) {
			throw new BeditaException(__("Error removing group",true));
		}
		return true;
	}
	
	public function saveGroup($groupData) {
		$group = ClassRegistry::init('Group');
		$group->create();
		if(isset($groupData['Group']['id'])) {
			$immutable = $group->field('immutable', array('id' => $groupData['Group']['id']));
			if($immutable == 1) {
				throw new BeditaException(__("Immutable group",true));
			}
		} else { // check existing group
			$id = $group->field('id', array('name' => $groupData['Group']['name']));
			if(!empty($id)) {
				throw new BeditaException(__("Existing group",true));
			}
		}
		if(!$group->save($groupData))
			throw new BeditaException(__("Error saving group",true), $group->validationErrors);
		if(!isset($groupData['Group']['id']))
			return $group->getLastInsertID();
		return $group->getID();
	}
	
	public function removeUser($userId) {
		// TODO: how can we do with related objects??? default removal??
		$user = ClassRegistry::init('User');
		$user->containLevel("minimum");
		$u = $user->findByUserid($userId);
		if(empty($u["User"])) {
			throw new BeditaException(__("User not present",true));
		}
		return $user->delete($u["User"]['id']);
	}	
	
	public function connectedUser() {
		$connectedUser = array();
		$res = array();
		$sessionDb = Configure::read('Session.database');
		if ( (Configure::read('Session.save') == "database") && !empty($sessionDb) ) {
			$db =& ConnectionManager::getDataSource($sessionDb);
			$table = $db->fullTableName(Configure::read('Session.table'), false);
			// #CUSTOM QUERY
			$res = $db->query("SELECT " . $db->name($table.'.data') . " FROM " . $db->name($table) . " WHERE " . $db->name($table.'.expires') . " >= " . time(), false);
		}
		foreach($res as $key => $val) {
			$sessiondata = !empty($val[$table]['data']) ? $val[$table]['data'] : $val[0]['data'];
			$unserialized_data = $this->unserializesession($sessiondata);
			if(!empty($unserialized_data) && !empty($unserialized_data['BEAuthUser']) && !empty($unserialized_data['BESession'])) {
				$timeout = Configure::read('activityTimeout');
				if((time() - $unserialized_data['BESession']['time']) < ($timeout*60) ) {
					$usr = $unserialized_data['BEAuthUser']['userid'];
					$connectedUser[]= array($usr => array_merge($unserialized_data['BEAuthUser'], 
						$unserialized_data['BESession']));
				}
			}
		}
		return $connectedUser;
	}
	
	function unserializesession($data) {
		$result = array();
		$vars=preg_split('/([a-zA-Z0-9]+)\|/',$data,-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		for($i=0; @$vars[$i]; $i++) {
			$result[$vars[$i++]]=unserialize($vars[$i]);
		}
		return $result;
	}
	
	public function getUserSession() {
		return ($this->checkSessionKey())? $this->Session->read($this->sessionKey) : false; 
	} 
	
	public function setSessionVars() {
		if(isset($this->Session)) {
			// load history
			$historyConf = Configure::read("history");
			if (!empty($historyConf)) {
				$groupBy = ($historyConf["showDuplicates"] === false)? array("url", "area_id") : array();
				$this->user["History"] = ClassRegistry::init("History")->getUserHistory($this->user["id"], $historyConf["sessionEntry"], $groupBy);
			}
			$this->Session->write($this->sessionKey, $this->user);
			$this->Session->write(self::SESSION_INFO_KEY, array("userAgent" => $_SERVER['HTTP_USER_AGENT'], 
				"ipNumber" => $_SERVER['REMOTE_ADDR'], "time" => time()));
			if (!empty($this->user["lang"])) {
				$this->Session->write('Config.language',$this->user["lang"]);
			}
		}

		if(isset($this->controller)) {
			$this->controller->set($this->sessionKey, $this->user);
		}
	}
	
	public function updateSessionHistory($historyItem, $historyConf) {
		if (empty($historyItem) || empty($historyConf)) {
			return;
		}
		$history = $this->Session->read($this->sessionKey . ".History");
		if (empty($history)) {
			$history[] = $historyItem; 
		} else {
			if ($historyConf["showDuplicates"] === false) {
				foreach ($history as $h) {
					if ($h["url"] == $historyItem["url"]) {
						$findPath = true;
						break;
					}
				}
			}
			
			if (empty($findPath)) {
				if (count($history) == $historyConf["sessionEntry"]) {
					array_pop($history);
				}
				array_unshift($history, $historyItem);
			}
		}
		
		$this->Session->write($this->sessionKey . ".History" , $history);
	}
}
?>