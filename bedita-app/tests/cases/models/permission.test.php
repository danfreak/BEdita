<?php 
/*-----8<--------------------------------------------------------------------
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
 *------------------------------------------------------------------->8-----
 */

/**
 * 
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: permission.test.php 2487 2009-11-25 16:56:37Z ste $
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class PermissionTestCase extends BeditaTestCase {
	
 	var $uses		= array('Area', 'Section', 'Document', 'Permission') ;
 	var $components	= array('Transaction') ;
    var $dataSource	= 'test' ;

	////////////////////////////////////////////////////////////////////

	function testAddSingleObject() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['min']) ;

		sort($this->data['addPerms1']);

		$this->Permission->add($this->Area->id, $this->data['addPerms1']) ;
		pr("Added perms");

		$modelPerms = $this->Permission->load($this->Area->id) ;
		pr("Verify perms") ;
		$perms = $this->perms2array($modelPerms);
		sort($perms);
		$this->assertEqual(sort($this->data['addPerms1']), $perms);

		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 


	function testAddMultipleObject() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['min']) ;
		$this->data['min']['parent_id'] = $this->Area->id;
		$this->_insert($this->Section, $this->data['min']) ;
		$this->data['min']['parent_id'] = $this->Section->id;
		$this->_insert($this->Document, $this->data['min']) ;

		$this->Permission->add($this->Area->id, $this->data['addPerms1']) ;
		$this->Permission->add($this->Section->id, $this->data['addPerms1']) ;
		$this->Permission->add($this->Document->id, $this->data['addPerms1']) ;

		$addPerms1 = $this->data['addPerms1'];
		array_multisort($addPerms1);
		$modelPerms = $this->Permission->load($this->Area->id) ;
		$perms = $this->perms2array($modelPerms);
		array_multisort($perms);
		$this->assertEqual($addPerms1, $perms);
		
		$modelPerms = $this->Permission->load($this->Section->id) ;
		$perms = $this->perms2array($modelPerms);
		array_multisort($perms);
		$this->assertEqual($addPerms1, $perms);

		$modelPerms = $this->Permission->load($this->Document->id) ;
		$perms = $this->perms2array($modelPerms);
		array_multisort($perms);
		$this->assertEqual($addPerms1, $perms);

		$this->_delete($this->Document) ;
		$this->_delete($this->Section) ;
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 


	function testDeleteBySingleObject() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['min']) ;

		$this->Permission->add($this->Area->id, $this->data['addPerms1']) ;

		$this->Permission->remove($this->Area->id, $this->data['removePerms1']) ;

		$modelPerms = $this->Permission->load($this->Area->id) ;
		$perms = $this->perms2array($modelPerms);
		sort($perms);
		
		$this->assertEqual(sort($this->data['resultDeletePerms1']), $perms);
		$this->_delete($this->Area) ;
		
		$this->Transaction->rollback() ;
	} 
	
	function testDeleteAllBySingleObject() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['min']) ;

		// add perms
		$this->Permission->add($this->Area->id, $this->data['addPerms1']) ;

		// remove perms
		$this->Permission->removeAll($this->Area->id) ;
		
		// load
		$modelPerms = $this->Permission->load($this->Area->id) ;
		$perms = $this->perms2array($modelPerms);
		$this->assertEqual(array(), $perms);
		
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 


/*	
	function testReplaceByRootTree() {	
		$this->Transaction->begin() ;

		$this->_insert($this->Area, $this->data['min']) ;
		$this->data['min']['parent_id'] = $this->Area->id;
		$this->_insert($this->Section, $this->data['min']) ;
		$this->data['min']['parent_id'] = $this->Section->id;
		$this->_insert($this->Document, $this->data['min']) ;
		
		sort($this->data['addPerms1']);
		$ret = $this->Permission->addTree($this->Area->id, $this->data['addPerms1']) ;
		$this->assertEqual($ret,true);
		
		// area permissions
		$perms = $this->Permission->load($this->Area->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Area->id) ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		// section permissions
		$perms = $this->Permission->load($this->Section->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Section->id) ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		// document permissions
		$perms = $this->Permission->load($this->Document->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Document->id) ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$this->_delete($this->Document) ;
		$this->_delete($this->Section) ;
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 

	function testDeleteByRootTree() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['min']) ;
		$this->data['min']['parent_id'] = $this->Area->id;
		$this->_insert($this->Section, $this->data['min']) ;
		$this->data['min']['parent_id'] = $this->Section->id;
		$this->_insert($this->Document, $this->data['min']) ;
		
		sort($this->data['addPerms1']);
		$ret = $this->Permission->addTree($this->Area->id, $this->data['addPerms1']) ;
		$this->assertEqual($ret,true);
		
		// remove permissions
		$ret = $this->Permission->removeTree($this->Area->id, $this->data['removePerms1']) ;
		$this->assertEqual($ret,true);
		
		// area permissions
		$perms = $this->Permission->load($this->Area->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Area->id) ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		// section permissions
		$perms = $this->Permission->load($this->Section->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Section->id) ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		// document permissions
		$perms = $this->Permission->load($this->Document->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Document->id) ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		$this->_delete($this->Document) ;
		$this->_delete($this->Section) ;
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 

	function testDeleteAllByRootTree() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['min']) ;
		$this->data['min']['parent_id'] = $this->Area->id;
		$this->_insert($this->Section, $this->data['min']) ;
		$this->data['min']['parent_id'] = $this->Section->id;
		$this->_insert($this->Document, $this->data['min']) ;
		
		sort($this->data['addPerms1']);
		$ret = $this->Permission->addTree($this->Area->id, $this->data['addPerms1']) ;
		$this->assertEqual($ret,true);

		// remove all permissions
		$ret = $this->Permission->removeAllTree($this->Area->id);
		$this->assertEqual($ret,true);
		
		// area permissions
		$perms = $this->Permission->load($this->Area->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Area->id) ;
		$this->assertEqual(array(), $perms);
		
		// section permissions
		$perms = $this->Permission->load($this->Section->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Section->id) ;
		$this->assertEqual(array(), $perms);
		
		// document permissions
		$perms = $this->Permission->load($this->Document->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Document->id) ;
		$this->assertEqual(array(), $perms);
		
		$this->_delete($this->Document) ;
		$this->_delete($this->Section) ;
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 
*/

	function testWritePermissions() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['min']) ;
		
		sort($this->data['addPerms1']);
		// add perms
		$this->Permission->add($this->Area->id, $this->data['addPerms1']) ;
				
		// Verify user/group permissions
		foreach ($this->data['addPerms1'] as $d) {
			$userdata = array("id" => 1);
			if($d["switch"] == "group") {
				$userdata["groups"] = array($d["name"]);
			} else {
				$user = ClassRegistry::init("User");
				$userdata["id"]= $user->field('id', array('userid'=>$d["name"]));
				$userdata["groups"] = array();
			}
			$ret = $this->Permission->isWritable($this->Area->id, $userdata) ;
			$this->assertEqual($ret, true);
		}
		
		$userdata = array("id" => -1, "groups" =>array("#########"));
		$ret = $this->Permission->isWritable($this->Area->id, $userdata);
		$this->assertEqual($ret, false);
		
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 
	
	/////////////////////////////////////////////////
	private function perms2array($modelData) {
		$res = array();
		foreach ($modelData as $m) {
			$item = $m["Permission"];
			unset($item["id"]);
			unset($item["ugid"]);
			unset($item["object_id"]);
			if(!empty($m["Group"]["name"])) {
				$item["name"] = $m["Group"]["name"];	
			} else {
				$item["name"] = $m["User"]["userid"];	
			}
			$res[] = $item;
		}
		return $res;
	}
	
	
	private function _insert($model, $data) {
		// Create
		$result = $model->save($data) ;
		$this->assertEqual($result,true);		
		
		// View
		$obj = $model->findById($model->id) ;
		pr("Created object: {$model->id}") ;
		
	} 
	
	private function _delete($model) {
		$id = $model->id;
		$result = $model->delete($model->{$model->primaryKey});
		$this->assertEqual($result,true);		
		pr("Removed object: $id");
	} 

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	public   function __construct () {
		parent::__construct('Permission', dirname(__FILE__)) ;
	}
}
?> 