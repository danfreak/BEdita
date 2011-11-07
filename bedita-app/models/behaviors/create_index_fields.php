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
 * $Id: create_index_fields.php 2487 2009-11-25 16:56:37Z ste $
 */

class CreateIndexFieldsBehavior extends ModelBehavior {
	var $config = array();
	
	function setup(&$model, $config = array()) {
		$this->config[$model->name] = $config ;
	}
	
	/**
	 * @param object $model
	 * @return boolean
	 */
	function afterSave($model) {
		if(!isset($model->{$model->primaryKey})) 
		  throw new BeditaException("Missing primary key from {$model}");	
		
		// get object created/saved
		$bviorCompactResults 	= $model->bviorCompactResults ;
		$model->bviorCompactResults = true ;
		$model->contain(array("BEObject"));
		if(!($data = $model->findById($model->{$model->primaryKey}))) 
		  throw new BeditaException("Error loading {$model}");
		$model->bviorCompactResults 	= $bviorCompactResults ;

        if(!isset($model->BEObject->SearchText)) 
          return true ;
        
        $relevance = array("title" => 10 , "description" => 5);
		foreach ($data as $k => $v) {
			if($k === 'title' || $k === 'description') {
                $sText = array(
	                'object_id' => $data['id'],
	                'lang'      => $data['lang'], 
	                'content'   => $v,
	                'relevance' => $relevance[$k]
                );
                if(!$model->BEObject->SearchText->save($sText)) 
                    throw new BeditaException("Error saving search text {$model}: $k => $v");
			}
		}

		return true ;	
	}

}
?>
