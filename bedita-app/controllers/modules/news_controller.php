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
 * Short news handling
 * 
 *
 * @version			$Revision: 2712 $
 * @modifiedby 		$LastChangedBy: dante $
 * @lastmodified	$LastChangedDate: 2010-03-26 09:14:23 +0100 (Fri, 26 Mar 2010) $
 * 
 * $Id: news_controller.php 2712 2010-03-26 08:14:23Z dante $
 */
class NewsController extends ModulesController {

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText');
	var $uses = array('BEObject','ShortNews','Category','Area') ;
	protected $moduleName = 'news';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = $conf->objectTypes['short_news']["id"];
		$filter["count_annotation"] = array("Comment","EditorNote");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
		$this->loadCategories($filter["object_type_id"]);
	 }

	public function view($id = null) {
    	$this->viewObject($this->ShortNews, $id);
	 }


	public function save() {
        $this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->ShortNews);
	 	$this->Transaction->commit();
 		$this->userInfoMessage(__("News saved", true)." - ".$this->data["title"]);
		$this->eventInfo("news [". $this->data["title"]."] saved");
	 }

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("ShortNews");
		$this->userInfoMessage(__("News deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("News $objectsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("ShortNews");
		$this->userInfoMessage(__("News deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("News $objectsListDeleted deleted");
	}

	public function categories() {
		$this->showCategories($this->ShortNews);
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
										"OK"	=> "/news/view/{$this->ShortNews->id}",
										"ERROR"	=> "/news/view/{$this->ShortNews->id}" 
										),
				"save"	=> 	array(
										"OK"	=> "/news/view/{$this->ShortNews->id}",
										"ERROR"	=> $this->referer() 
									), 
				"delete" =>	array(
										"OK"	=> $this->fullBaseUrl . $this->Session->read('backFromView'),
										"ERROR"	=> $this->referer()
									), 
				"saveCategories" 	=> array(
										"OK"	=> "/news/categories",
										"ERROR"	=> "/news/categories"
										),
				"deleteCategories" 	=> array(
										"OK"	=> "/news/categories",
										"ERROR"	=> "/news/categories"
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
		) ;
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false ;
	 }

}

?>