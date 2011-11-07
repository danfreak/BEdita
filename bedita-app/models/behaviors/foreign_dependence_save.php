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
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: foreign_dependence_save.php 2487 2009-11-25 16:56:37Z ste $
 */

/**
 * 
 * Foreign key dependencies on save management
 * Models names to manage passed through Configure
 *
 */

class ForeignDependenceSaveBehavior extends ModelBehavior {
	var $config = array();
	
	function setup(&$model, $config = array()) {
		$this->config[$model->name] = $config ;
	}

	/**
	 * If some model/s has to be saved before current, related data is passed and saving is performed
	 *
	 * @param object $model
	 * @return boolean
	 */
	function beforeSave(&$model) {
		$first 		= true ;		// true, first model processing (if necessary, new id is created)
		$id			= null ;	// If present, a new element has been created
		$firstModel	= null ;		// First model name
		
		foreach ($this->config[$model->name] as $name) {

			$data = array($name => $model->data[$model->name]);
			// if model has relationships hasAndBelongsToMany, format data array
			if(count($model->$name->hasAndBelongsToMany))  {
				foreach ($model->$name->hasAndBelongsToMany as $k => $assoc) {
					if(isset($data[$name][$k])) {
						$data[$k] = array($k => &$data[$name][$k]);
					}
				}
			}
				
			// save parent/s
			$run = true ;
			$model->$name->create();
			if(!$res = $model->$name->save($data)) {
				$model->validationErrors = $model->$name->validationErrors ;
				// If first element has been created already, performe delete and return
				if(!$first) {
					$model->$firstModel->delete($id) ;
				}
				return false ;
			}
			
			// If it's first model on the list, get ID inserted
			if($first) {
				$firstModel = $name ;
				if(empty($model->data[$model->name]['id'])) {
					$id = $model->$name->getInsertID() ;
					$model->data[$model->name]['id'] = $id;
				}
				
				if ($firstModel == "BEObject" && !empty($res["BEObject"]["user_modified"]))
					$model->data[$model->name]["user_modified"] = $res["BEObject"]["user_modified"];
				
				$first  = false ;
			}
		}
		return true ;	
	}
}
?>
