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
 * Custom properties handling component
 * 
 *
 * @version			$Revision: 2861 $
 * @modifiedby 		$LastChangedBy: niki $
 * @lastmodified	$LastChangedDate: 2010-06-03 16:02:12 +0200 (Thu, 03 Jun 2010) $
 * 
 * $Id: be_custom_property.php 2861 2010-06-03 14:02:12Z niki $
 */
class BeCustomPropertyComponent extends Object {
	static $SWITCH_USER		= 'user' ;
	static $SWITCH_GROUP	= 'group' ;
	
	var $controller			= null ;
	
	/**
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$this->controller 	= $controller;
	}
	
	/**
	 * unset property with empty property_value field from data form 
	 */
	function setupForSave() {
		if (!empty($this->controller->data["ObjectProperty"])) {
			$objProp = array();
			foreach($this->controller->data["ObjectProperty"] as $key => $value) {
				
				if (!empty($value["property_value"])) {
					$value["property_value"] = trim($value["property_value"]);
					
					if (!empty($value["property_value"])) {
						$objProp[] = $value;
					}
					
				} 
			}
			
			$this->controller->data["ObjectProperty"] = $objProp;
		}
		
	}
	
	/**
	 * unset property with empty property_value field from data form 
	 */
	function setupUserPropertyForSave() {
		if (!empty($this->controller->data["UserProperty"])) {
			$objProp = array();
			foreach($this->controller->data["UserProperty"] as $key => $value) {
				
				if (!empty($value["property_value"])) {
					$value["property_value"] = trim($value["property_value"]);
					
					if (!empty($value["property_value"])) {
						$objProp[] = $value;
					}
				} 
			}
			$this->controller->data["UserProperty"] = $objProp;
		}
	}
	
	
	/**
	 * set property array of object for view
	 *
	 * @param array $obj, array of object data 
	 * @param int $object_type_id
	 * @return array
	 */
	function setupForView(&$obj, $object_type_id=null) {
		$property = array();
		
		if (!empty($obj) || !empty($object_type_id)) {
		
			if (empty($obj["ObjectProperty"])) {
				$propertyModel = ClassRegistry::init("Property");
				$object_type_id = (!empty($obj["object_type_id"]))? $obj["object_type_id"] : $object_type_id;
				$property = $propertyModel->find("all", array(
								"conditions" => array("object_type_id" => $object_type_id),
								"contain" => array("PropertyOption")
							)
						);
			} else {
				$property = $obj["ObjectProperty"];
				unset($obj["ObjectProperty"]);
			}
			
		}
		
		return $property;
	}
	
	/**
	 * set property array for user view
	 *
	 * @param array $obj, array of object data 
	 * @param int $object_type_id
	 * @return array
	 */
	function setupUserPropertyForView(&$user) {
		
		$property = array();
		
		if (empty($user["UserProperty"])) {

			$propertyModel = ClassRegistry::init("Property");
			$property = $propertyModel->find("all", array(
							"conditions" => array("object_type_id" => null),
							"contain" => array("PropertyOption")
						)
					);
		} else {
			$property = $user["UserProperty"];
			unset($user["UserProperty"]);
		}
		
		return $property;
	}
	
}

?>