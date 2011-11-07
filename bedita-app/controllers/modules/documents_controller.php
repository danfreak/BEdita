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
 * 
 *
 * @version			$Revision: 2752 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2010-04-13 11:59:48 +0200 (Tue, 13 Apr 2010) $
 * 
 * $Id: documents_controller.php 2752 2010-04-13 09:59:48Z bato $
 */
class DocumentsController extends ModulesController {
	var $name = 'Documents';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeLangText', 'BeFileHandler');

	var $uses = array('BEObject', 'Document', 'Tree','Category') ;
	protected $moduleName = 'documents';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {    	
    	$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = array($conf->objectTypes['document']["id"]);
		$filter["count_annotation"] = array("Comment","EditorNote");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
		$this->loadCategories($filter["object_type_id"]);
	 }
	
	public function view($id = null) {
		$this->viewObject($this->Document, $id);
		$this->set('autosave', true);
	}

	public function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->Document);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Document saved", true)." - ".$this->data["title"]);
		$this->eventInfo("document [". $this->data["title"]."] saved");
	 }

	public function autosave() {
		$this->layout = 'ajax';
		$this->Transaction->begin();
		$this->autoSaveObject($this->Document);
		$this->Transaction->commit();
		$time = strftime(Configure::read("dateTimePattern"), time());
		$this->userInfoMessage(__("Document Saved on", true)."<br/>".$time);
		$this->eventInfo("document [". $this->data["title"]."] saved");
		$this->render(null, null, "/elements/flash_messages");
	 }
	 	 
	 public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Document");
		$this->userInfoMessage(__("Documents deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("documents $objectsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Document");
		$this->userInfoMessage(__("Documents deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("documents $objectsListDeleted deleted");
	}

	public function categories() {
		$this->showCategories($this->Document);
	}
	
	public function saveCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->data["label"])) 
			throw new BeditaException( __("No data", true));
		$this->Transaction->begin() ;
		if(!$this->Category->save($this->data)) {
			throw new BeditaException(__("Error saving tag", true), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category saved", true)." - ".$this->data["label"]);
		$this->eventInfo("category [" .$this->data["label"] . "] saved");
	}

	public function deleteCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->data["id"])) 
			throw new BeditaException( __("No data", true));
		$this->Transaction->begin() ;
		if(!$this->Category->del($this->data["id"])) {
			throw new BeditaException(__("Error saving tag", true), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category deleted", true) . " -  " . $this->data["label"]);
		$this->eventInfo("Category " . $this->data["id"] . "-" . $this->data["label"] . " deleted");
	}

	protected function forward($action, $esito) {
		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/documents/view/".@$this->Document->id,
							"ERROR"	=> "/documents/view/".@$this->Document->id 
							),
			"view"	=> 	array(
							"ERROR"	=> "/documents" 
							), 
			"save"	=> 	array(
							"OK"	=> "/documents/view/".@$this->Document->id,
							"ERROR"	=> $this->referer()
							),
			"saveCategories" 	=> array(
							"OK"	=> "/documents/categories",
							"ERROR"	=> "/documents/categories"
							),
			"deleteCategories" 	=> array(
							"OK"	=> "/documents/categories",
							"ERROR"	=> "/documents/categories"
							),
			"delete" =>	array(
							"OK"	=> $this->fullBaseUrl . $this->Session->read('backFromView'),
							"ERROR"	=> $this->referer()
							),
			"deleteSelected" =>	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							),
			"addItemsToAreaSection"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							),
			"moveItemsToAreaSection"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							),
			"removeItemsFromAreaSection"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							),
			"assocCategory"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							),
			"disassocCategory"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							)
			);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	

?>