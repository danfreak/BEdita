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
 * Geographic tag object - geographical identification metadata
 *
 * @version			$Revision: 3331 $
 * @modifiedby 		$LastChangedBy: xho $
 * @lastmodified	$LastChangedDate: 2011-07-28 16:12:06 +0200 (Thu, 28 Jul 2011) $
 * 
 * $Id: geo_tag.php 3331 2011-07-28 14:12:06Z xho $
 */
class GeoTag extends BEAppModel 
{
	var $recursive = 0 ;
	
	function beforeValidate() {

		$this->checkFloat('latitude');
		$this->checkFloat('longitude');

		return true;
	}
	
	function beforeSave() {
		
		// convert gmaps_lookat array in json format
		if (empty($this->data["GeoTag"]["gmaps_lookat"]) || is_array($this->data["GeoTag"]["gmaps_lookat"])) {	
		
			if (!empty($this->data["GeoTag"]["latitude"])) {
				$this->data["GeoTag"]["gmaps_lookat"]["latitude"] = $this->data["GeoTag"]["latitude"];
			}
			
			if (!empty($this->data["GeoTag"]["longitude"])) {
				$this->data["GeoTag"]["gmaps_lookat"]["longitude"] = $this->data["GeoTag"]["longitude"];
			}
			
			// calculate lookat.range from zoom = Math.round(26-(Math.log(range)/Math.log(2))) http://www.msa.mmu.ac.uk/~fraser/ge/viewinmaps/ 
			if (empty($this->data["GeoTag"]["gmaps_lookat"]["range"]) && !empty($this->data["GeoTag"]["gmaps_lookat"]["zoom"])) {
				$this->data["GeoTag"]["gmaps_lookat"]["range"] = exp(26 - $this->data["GeoTag"]["gmaps_lookat"]["zoom"] + log(2));
			}
			
			if (!empty($this->data["GeoTag"]["gmaps_lookat"])) {
				$recordsToString = trim( implode("", $this->data["GeoTag"]["gmaps_lookat"]) );
				if (!empty( $recordsToString )) {
					$this->data["GeoTag"]["gmaps_lookat"] = json_encode($this->data["GeoTag"]["gmaps_lookat"]);
				} else {
					$this->data["GeoTag"]["gmaps_lookat"] = null;
				}
			}
		}
		
		return true;
	}
	
	function afterFind($results, $primary = false) {
		// decode json gmaps_lookat field
		if (!empty($results[0]["GeoTag"])) {
			
			foreach ($results as &$geotag) {
				if (!empty($geotag["GeoTag"]["gmaps_lookat"])) {
					$geotag["GeoTag"]["gmaps_lookat"] = $this->decodeLookat($geotag["GeoTag"]["gmaps_lookat"]);
				}
			}
			
		} else if (!empty($results["GeoTag"])) {
			if (!empty($results["GeoTag"]["gmaps_lookat"])) {
				$results["GeoTag"]["gmaps_lookat"] = $this->decodeLookat($results["GeoTag"]["gmaps_lookat"]);
			}
		}
		
		return $results;
	}
	
	/**
	 * try to decode json gmaps_lookat field
	 * 
	 * @param string $gmapsLookat json format
	 * @return mixed string if decode fails, array otherwise
	 */
	public function decodeLookat($gmapsLookat) {
		$lookatDecoded = json_decode($gmapsLookat, true);
		if (empty($lookatDecoded)) {
			$lookatDecoded = $gmapsLookat;
		}
		
		return $lookatDecoded;
	}
	
	// TODO: convert geo coordinates if necessary....es http://www.phpclasses.org/browse/file/10671.html
	private function geoConvert($str) {
		return $str;
	}
}
?>