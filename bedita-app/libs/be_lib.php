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

if (!class_exists('ClassRegistry')) {
	App::import('Core', array('ClassRegistry'));
}

/**
 * BEdita libs class. Instantiate and put in the registry other classes
 *
 * @version			$Revision: 3394 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2011-10-25 11:02:16 +0200 (Tue, 25 Oct 2011) $
 * 
 * $Id: be_lib.php 3394 2011-10-25 09:02:16Z ste $
 */

class BeLib {
	
	public static function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] =& new BeLib();
		}
		return $instance[0];
	}
	
	/**
	 * return an instance of a class (by default search in libs dir)
	 * If class is not instantiated do it and put in CakePHP registry
	 * 
	 * @param string $name class name (file has to be underscorized MyClass => my_class.php)
	 * @param string or array $paths paths where search class file
	 * @return class instance
	 */
	public static function &getObject($name, $paths=BEDITA_LIBS) {
		if (!$libObject = ClassRegistry::getObject($name)) {
			if (!class_exists($name)) {
				$file = Inflector::underscore($name) . ".php";
				$paths = (is_array($paths))? $paths : array($paths);
				if (!App::import("File", $name, true, $paths, $file)) {
					return false;
				}
			}
			$libObject = new $name();
			ClassRegistry::addObject($name, $libObject);
		}
		return $libObject;
	}
	
	/**
	 * check if a class name is a BEdita object type
	 * 
	 * @param string $name the class name
	 * @param mixed $paths array of paths or string path where searching the class
	 * 					   leave empty to use ClassRegistry
	 * @return boolean
	 */
	public function isBeditaObjectType($name, $paths=null) {
		if (!$paths) {
			$classInstance = ClassRegistry::init($name);
		} else {
			$classInstance = $this->getObject($name, $paths);
		}
		if (!$classInstance) {
			return false;
		}
		$parents = class_parents($classInstance);
		if (empty($parents) || !in_array("BEAppObjectModel", $parents)) {
			return false;
		}
		return true;
	}
	
	/**
	 * check if a file name is already used in Configure::$type."Paths"
	 * 
	 * @param string $filename
	 * @param string $type see Configure::*Paths
	 * @param array of path to exclude from search (paths have to end with DS trailing slash)
	 * @return boolean
	 */
	public function isFileNameUsed($filename, $type, $excludePaths=array()) {
		$conf = Configure::getInstance();
		$pathName = strtolower(Inflector::singularize($type)) . "Paths";
		if (!isset($conf->{$pathName})) {
			throw new BeditaException(__("No paths to search for " . $type, true));
		}
		$paths = array_diff($conf->{$pathName},$excludePaths);
		$folder = new Folder();
		foreach ($paths as $p) {
			$folder->cd($p);
			$ls = $folder->ls(true, true);
			if (!empty($ls[1]) && in_array($filename, $ls[1])) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * return an array of available addons
	 * 
	 * @return array in this form
	 * 				array(
	 * 					"models" => array(
	 * 						"objectTypes" => array(
	 * 							"on" => array(
	 * 								0 => array(
	 * 									"model" => model name,
	 *								 	"file" => file name,
	 *									"type" => object type name,
	 *									"path" => path to
	 * 								), 
	 * 								1 => array()
	 * 								....
	 * 							),
	 * 							"off" => array(
	 * 								0 => array(
	 * 									like "on" array,
	 * 									"fileNameUsed" => true if file name is already used for model
	 * 								), 1 => array(...)), ...
	 * 						),
	 * 						"others" => array(
	 * 							0 => array(
	 * 								"file" => file name,
	 *								"path" => path to,
	 *								"fileNameUsed" => true if file name is already used for model
	 * 							)
	 * 						)
	 * 					),
	 * 					
	 * 					"components" => array(like "others" array),
	 * 					"helpers" => array(like "others" array),
	 * 				)
	 */
	public function getAddons() {
		$conf = Configure::getInstance();
		$addons = array();
		$folder = new Folder();
		$items = array("models", "components", "helpers");
		foreach ($items as $val) {
			if ($folder->cd(BEDITA_ADDONS_PATH . DS . $val)) {
				$ls = $folder->ls(true, true);
				if ($val == "models") {
					foreach ($ls[1] as $modelFile) {
						$m = new File(BEDITA_ADDONS_PATH . DS . $val . DS . $modelFile);
						$name = $m->name();
						$modelName = Inflector::camelize($name);
						if ($this->isBeditaObjectType($modelName, BEDITA_ADDONS_PATH . DS . $val)) {
							$ot = array(
									"model" => $modelName,
									"file" => $modelFile,
									"type" => $name,
									"path" => BEDITA_ADDONS_PATH . DS . $val
							);
							$used = $this->isFileNameUsed($modelFile, $val, array(BEDITA_ADDONS_PATH . DS . $val . DS));
							if (!empty($conf->objectTypes[$name]) && !$used) {
								$addons[$val]["objectTypes"]["on"][] = $ot;
							} else {
								$ot["fileNameUsed"] = $used;
								$addons[$val]["objectTypes"]["off"][] = $ot;
							}
						} else {
							$addons[$val]["others"][] = array(
								"file" => $modelFile,
								"path" => BEDITA_ADDONS_PATH . DS . $val,
								"fileNameUsed" => $this->isFileNameUsed($modelFile, $val, array(BEDITA_ADDONS_PATH . DS . $val . DS))
							);
						}
					}
				} else {
					foreach ($ls[1] as $addonFile) {
						$addons[$val][] = array(
							"file" => $addonFile,
							"path" => BEDITA_ADDONS_PATH . DS . $val,
							"fileNameUsed" => $this->isFileNameUsed($addonFile, $val, array(BEDITA_ADDONS_PATH . DS . $val . DS))
						);
					}
				}
			}
		}
		
		return $addons;
	}
	
	/**
	 * perform operations on a string to use it in friendly url
	 * 
	 * @param string $value
	 * @return string
	 */
	public function friendlyUrlString($value) {
		if(is_null($value)) {
			$value = "";
		}
		if (is_numeric($value)) {
			$value = "n" . $value;
		}
		
		$value = htmlentities( strtolower($value), ENT_NOQUOTES, "UTF-8" );
		
		// replace accent, uml, tilde,... with letter after & in html entities
		$value = preg_replace("/&(.)(uml);/", "$1e", $value);
		$value = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $value);
		// replace special chars and space with dash (first decode html entities)
		$value = preg_replace("/[^a-z0-9\-_]/i", "-", html_entity_decode($value,ENT_NOQUOTES,"UTF-8" ) ) ;
		// remove digits and dashes in the beginning 
		$value = preg_replace("/^[0-9\-]{1,}/", "", $value);
		// replace two or more consecutive dashes with one dash
		$value = preg_replace("/[\-]{2,}/", "-", $value);
		// trim dashes in the beginning and in the end of nickname
		return trim($value,"-");	
	}
	
	/**
	 * Strip scripts, images, whitespace or all together on $data
	 * using Sanitize::stripScripts, Sanitize::stripImages, Sanitize::stripWhitespace, Sanitize::stripAll methods
	 * see Sanitize class of cakephp for more info
	 * 
	 * @param mixed $data string or array
	 * @param array $options, possible values are:
	 *				"what" => "scripts" (default), "images", "whitespace", "all",
	 *				"recursive" => true (default) strip recursively on $data
	 * 
	 * @return mixed 
	 */
	public function stripData($data, array $options = array()) {
		$options = array_merge(array("what" => "scripts", "recursive" => true), $options);
		$method = "strip".ucfirst($options["what"]);
		App::import("Sanitize");
		
		if (method_exists("Sanitize", $method)) {
			if (is_array($data)) {
				foreach ($data as $key => $value) {
					if (is_array($value) && $options["recursive"]) {
						$data[$key] = $this->stripData($value, $options);
					} else {
						$data[$key] = Sanitize::$method($value);
					}
				}
			} else {
				$data = Sanitize::$method($value);
			}
		}
		
		return $data;
	}
	
	
	/**
	 * return values of multidimensional array
	 *
	 * @param array $array
	 * @param boolean $addStringKeys if it's true add string keys to the returned array
	 * @return array 
	 */
	public function arrayValues(array $array, $addStringKeys = false) {
		$values = array();
		array_walk_recursive($array , array($this, "arrayValuesCallback"), &$values);
		if ($addStringKeys) {
			$keys = $this->arrayKeys($array);
			$values = array_merge($values, $keys);
		}
		return $values;
	}
	
	/**
	 * callback method used from BeLib::arrayValues
	 * 
	 * @param mixed $item
	 * @param mixed $key
	 * @param array $values 
	 */
	static private function arrayValuesCallback($item, $key, $values) {
		$values[] = $item;
	}
	
	/**
	 * return keys of multidimensional array
	 * 
	 * @param array $ar
	 * @param boolean $stringKeys if it's true add string keys to the returned array
	 * @return array 
	 */
	public function arrayKeys(array $ar, $stringKeys = true) {
		$keys = array();
		foreach($ar as $k => $v) {
			if (!$stringKeys || ($stringKeys && is_string($k))) { 
				$keys[] = $k;
			}
			if (is_array($ar[$k])) {
				$keys = array_merge($keys, $this->arrayKeys($ar[$k], $stringKeys));
			}
		}
		return $keys;
	} 

	/**
	 * Transform any numeric date in SQL date/datetime string format
	 * Date types accepted: "little-endian"/"middle-endian"/"big-endian"
	 * 
	 * if little endian, expected format id dd/mm/yyyy format, or dd.mm.yyyy, or dd-mm-yyyy
	 * if middle endian, expected format is mm/dd/yyyy format, or mm.dd.yyyy (USA standard)
	 * if big endian ==> yyyy-mm-dd
	 * Examples:
	 * 
	 *  Little endian
	 *  "22/04/98", "22/04/1998", "22.4.1998", "22-4-98", "22 4 98", "1998", "98", "22.04", "22/4", "22 4"
	 *  
	 *  Middle endian
	 *  "4/22/98", "02/22/1998", "4.22.1998", "4-22-98", "4/22", "04.22"
	 * 
	 * If format is not valid or string is not parsable, an exception maybe thrown
	 * 
	 * @param string $val, string in generic numeric form
	 * @param string $dateType, "little-endian"/"middle-endian"/"big-endian"
	 * 
	 */
	public function sqlDateFormat($value, $dateType = "little-endian") {
		// check if it's already in SQL format
		$pattern = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$|^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/";
		if (preg_match($pattern, $value)) {
			return $value;
		}
		$pattern = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$|^[0-9]{4}-[0-9]{2}-[0-9]{2}$/";
		if (preg_match($pattern, $value)) {
			return $value;
		}
		$d = false;
		
		if($dateType === "little-endian") {
			// dd/mm/yyyy - dd.mm.yyy like formats			
			$pattern = "/^([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,4})$/";
			$match = array();
			if (preg_match($pattern, $value, $match)) {
				$d = $match[5] . "-" . $match[3] . "-" . $match[1];
			}	
			// dd/mm - dd.mm like formats			
			if($d === false) {
				$pattern = "/^([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,2})$/";
				$match = array();
				if (preg_match($pattern, $value, $match)) {
					$d = $match[3] . "/" . $match[1];
				}	
			}
		} elseif($dateType === "middle-endian") {
			// mm/dd/yyyy - mm.dd.yyyy like formats			
			$pattern = "/^([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,4})$/";
			$match = array();
			if (preg_match($pattern, $value, $match)) {
				$d = $match[5] . "-" . $match[1] . "-" . $match[3];
			}
			// dd/mm - dd.mm like formats			
			if($d === false) {
				$pattern = "/^([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,2})$/";
				$match = array();
				if (preg_match($pattern, $value, $match)) {
					$d = $match[1] . "/" . $match[3];
				}	
			}
		}

		if($d === false) {
			$pattern = "/^([0-9]{4})$/";
			$match = array();
			if (preg_match($pattern, $value, $match)) {
				$d = $match[1] . "-01-01";
			}	
		}

		if($d === false) {
			$pattern = "/^([0-9]{1,2})$/";
			$match = array();
			if (preg_match($pattern, $value, $match)) {
				$y = intval($match[1]);
				$date = new DateTime();
				// which year 08, 12, 18, 28 ??? - if earlier than current year add 2000, otherwise add 1900
				$yNow = intval($date->format("Y"));
				$ys = strval($y + ((2000 + $y > $yNow) ? 1900 : 2000));
				$d = $ys . "-01-01";				
			}
		}

		if($d === false) {
			$d = $value; // use $value if pattern not recognized
		}
		$date = new DateTime($d);
		return $date->format('Y-m-d');
	}
}

?>