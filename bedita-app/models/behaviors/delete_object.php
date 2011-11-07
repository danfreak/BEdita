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
 * @version			$Revision: 2887 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2010-06-22 12:10:32 +0200 (Tue, 22 Jun 2010) $
 * 
 * $Id: delete_object.php 2887 2010-06-22 10:10:32Z ste $
 */

/**
 * 
 * Delete object using dependence of foreign key. 
 * Delete only the record of base table then database's referential integrity do the rest  
 *
 */
class DeleteObjectBehavior extends ModelBehavior {
	
	/**
	 * contain base table
	 */
	var $config = array();

	function setup(&$model, $config = array()) {
		$this->config[$model->name] = $config ;
	}

	/**
	 * Delete all associations, they will be re-established after deleting
	 * Considering foreignKeys among tables, force deleting records on table 'objects'
	 *
	 * if specified delete related object too
	 * @return unknown
	 */
	
	function beforeDelete(&$model) {
		$model->tmpAssociations = array();
		$model->tmpTable 		= $model->table ;
		
		$associations = array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany');
		foreach ($associations as $association) {
			$model->tmpAssociations[$association] = $model->$association ;
			$model->$association = array() ;
		}
		$configure = $this->config[$model->name] ;
		
		if (!empty($configure)) {
			if (is_string($configure)) {
				$model->table = $configure;
			} elseif (is_array($configure) && count($configure) == 1) {
				
				if (is_string(key($configure))) {
					
					$model->table = key($configure);
					if (!empty($configure[$model->table]["relatedObjects"])) {
						$this->delRelatedObjects($configure[$model->table]["relatedObjects"], $model->id);
					}
					
				} else {
					$model->table = array_shift($configure);
				}
			}
		}
		
		$model->table =  (isset($configure) && is_string($configure)) ? $configure : $model->table ;

		// Delete object references on tree as well
		$tree = ClassRegistry::init('Tree');
		if (!$tree->deleteAll(array("id" => $model->id))) {
			throw new BeditaException(__("Error deleting tree",true));	
		}
		if (!$tree->deleteAll(array("object_path LIKE '%/".$model->id."/%'"))) {
			throw new BeditaException(__("Error deleting children tree",true));
		}

		$st = ClassRegistry::init('SearchText');
		$st->deleteAll("object_id=".$model->id) ;

		$this->deleteAnnotations($model->id);
		
		return true ;
	}

	/**
	 * Re-establish associations (insert associations)
	 *
	 */
	function afterDelete(&$model) {
		if (!empty($model->tmpTable)) {
			$model->table = $model->tmpTable ;
			unset($model->tmpTable) ;
		}
		if (!empty($model->tmpAssociations)) {
			// Re-establish associations
			foreach ($model->tmpAssociations as $association => $v) {
				$model->$association = $v ;
			}
			unset($model->tmpAssociations) ;
		}
	}

	/**
	 * Delete related objects
	 *
	 * @param array $relations: array of relations type. 
	 * 							The object related to main object by a relation in $relations will be deleted 
	 * @param int $object_id: main object that has to be deleted
	 */
	private function delRelatedObjects($relations, $object_id) {

		$o = ClassRegistry::init('BEObject') ;
		
		$res = $o->find("first", array(
									"contain" => array("RelatedObject"),
									"conditions" => array("BEObject.id" => $object_id)
									)
							);
		if (!empty($res["RelatedObject"])) {
			
			$conf = Configure::getInstance();
			foreach ($res["RelatedObject"] as $obj) {
				
				if (in_array($obj["switch"],$relations)) {
					
					$modelClass = $o->getType($obj['object_id']);

					$model = ClassRegistry::init($modelClass);
				
					if (!$model->del($obj["object_id"])) 
						throw new BeditaException(__("Error deleting related object ", true), "id: ". $obj["object_id"] . ", switch: " . $obj["switch"]);
				}
				
			}
			
		}
		
	}

	/**
	 * delete annotation objects like Comment, EditorNote, etc... related to main object
	 *
	 * @param int $object_id main object
	 */
	private function deleteAnnotations($object_id) {
		// delete annotations
		$annotation = ClassRegistry::init("Annotation");
		$aList = $annotation->find("list", array(
			"fields" => array("id"),
			"conditions" => array("object_id" => $object_id)
		));

		if (!empty($aList)) {
			$o = ClassRegistry::init('BEObject');
			foreach ($aList as $id) {
				$modelClass = $o->getType($id);

				$model = ClassRegistry::init($modelClass);

				if (!$model->del($id)) {
					throw new BeditaException(__("Error deleting annotation " . $modelClass, true), "id: ". $id);
				}
			}
		}

	}

}
?>
