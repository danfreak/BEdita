<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 *------------------------------------------------------------------->8-----
 */

/**
 * Authentication test
 * 
 * @version			$Revision: 2857 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2010-06-01 17:04:04 +0200 (Tue, 01 Jun 2010) $
 * 
 * $Id: authentication_controller.test.php 2857 2010-06-01 15:04:04Z bato $
 */
require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class AuthenticationControllerTestCase extends BeditaTestCase {

    var $dataSource	= 'test' ;
    var $data		= null ;
	var $components	= array('Session', 'BeAuth') ;

	////////////////////////////////////////////////////////////////
	function testLoginOk() {
		pr("Create user and log in....") ;

		$this->assertTrue($this->BeAuth->createUser($this->data['new.user'], array('administrator')));
		
		$return = array('return' => 'result', 
			'data' => array('login' => $this->data['new.user']['User'], 
				'method' => 'post'));
		$this->testAction('/authentications/login',	$return);
		
		$user 	= $this->Session->read('BEAuthUser') ;
		
		pr($user);
		
		pr($this->Session);
		$this->assertEqual($user['userid'], $this->data['new.user']['User']['userid']);
	} 

	function testLogout() {
		pr("Closing session....") ;
		
		$this->testAction('/authentications/logout');
		$user = $this->Session->read('BEAuthUser') ;
		$this->assertEqual($user, null);

		$this->assertTrue($this->BeAuth->removeUser($this->data['new.user']['User']['userid']));
	}
	
	public   function __construct () {
		parent::__construct('AuthenticationController', dirname(__FILE__)) ;
	}

}

?>

