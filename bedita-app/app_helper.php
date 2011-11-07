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

App::import('Core', 'Helper');

/**
 * Helper base class
 * contains common helpers methods
 * 
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: app_helper.php 2487 2009-11-25 16:56:37Z ste $
 */
class AppHelper extends Helper {
 	
	/**
	 * try to get from the registry the helper instance. Eventually instance a new helper object ant put
	 * it in the registry
	 * @param $name, helper name without suffix Helper (i.e. BlipHelper => $name=Blip)
	 * @return $nameHelper instance  
	 */
	protected function getHelper($name) {
		$helper = $name."Helper";
		if (!$helperObject = ClassRegistry::getObject($helper)) {
			if (!class_exists($helper)) {
				if (!App::import("Helper", $name)) {
					return false;
				}
			}
			$helperObject = new $helper();
			
			/* copy class attributes to new helper instance
			 * see file cake/libs/view/view.php
			 * class View
			 * method _loadHelpers
			 * line 749
			 */
			$vars = array(
				'base', 'webroot', 'here', 'params', 'action', 'data', 'themeWeb', 'plugin'
			);
			$c = count($vars);
			for ($j = 0; $j < $c; $j++) {
				$helperObject->{$vars[$j]} = $this->{$vars[$j]};
			}
			
			ClassRegistry::addObject($helper, $helperObject);
			if (!empty($helperObject->helpers)) {
				foreach ($helperObject->helpers as $subHelper) {
					$helperObject->{$subHelper} = $this->getHelper($subHelper);
				}
			}
		}
		
		return $helperObject;
	}
	
	/**
	 * return oEmbed format (see http://www.oembed.com)
	 * 
	 * @param $url, URL on third party sites
	 * @param $arrayFrom, specify which format is expected (json, xml) to build array in the right way
	 * 					if != "json" and != "xml" return original oEmbed format (i.e. JSON object or XML) 
	 * @return array
	 */
	protected function oEmbedInfo($url, $arrayFrom="json") {
		if (!$oEmbedInfo = file_get_contents($url)) {
			return false;
		}
		if ($arrayFrom == "json") {
			$oEmbedInfo = (json_decode($oEmbedInfo,true));
		} elseif ($arrayFrom == "xml") {
			$xml = ClassRegistry::init("Xml", "Core");
			$xml->load($oEmbedInfo);
			$oEmbedInfo = Set::reverse($xml);
			$oEmbedInfo = $oEmbedInfo["Oembed"];
		}
		return $oEmbedInfo;
	}
	
}
 
?>