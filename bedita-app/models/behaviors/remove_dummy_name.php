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
 * $Id: remove_dummy_name.php 2487 2009-11-25 16:56:37Z ste $
 */
class RemoveDummyNameBehavior extends ModelBehavior {
 	
	private $associations = array("hasOne", "hasMany", "belongsTo", "hasAndBelongsToMany"); 
	
	function setup(&$Model, $settings=array()) {
		if ( !$Model->Behaviors->enabled('CompactResult') || !empty($Model->actsAs["CompactResult"]) ) {
			
			if (empty($this->settings[$Model->alias])) {
				$this->settings[$Model->alias] = array();
			}
			
			foreach ($this->associations as $assocType) {
				if (!empty($Model->{$assocType})) {
					foreach ($Model->{$assocType} as $modelName => $val) {
						if (substr($modelName, -5) == "Dummy") {
							$this->settings[$Model->alias]["dummyModel"][$modelName] = substr($modelName,0,-5);
						}
					}
				}
			}
			
			if (!is_array($settings)) {
				$settings = array();
			}
			
			$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $settings);
		}
	}
 	
	
	function afterFind(&$Model, $results) {
		if (!empty($this->settings[$Model->alias]["dummyModel"])) {
			foreach ($results as $key => $value) {
				foreach ($this->settings[$Model->alias]["dummyModel"] as $dummy => $realModelName) {
					if (isset($results[$key][$dummy])) {
						$results[$key][$realModelName] = $results[$key][$dummy];
						unset($results[$key][$dummy]); 
					}
				}
			}
		}
  		
		return $results ;	
	}
 	
}
 
?>