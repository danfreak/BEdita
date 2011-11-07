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
 * @version			$Revision: 2857 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2010-06-01 17:04:04 +0200 (Tue, 01 Jun 2010) $
 * 
 * $Id: youtube.php 2857 2010-06-01 15:04:04Z bato $
 */
class YoutubeHelper extends AppHelper {

	var $helpers = array("Html");
	
	function thumbnail($obj, $htmlAttributes, $URLonly) {
		$this->conf = Configure::getInstance() ;
		$src = sprintf($this->conf->media_providers["youtube"]["params"]["urlthumb"], $obj['video_uid']);
		return (!$URLonly)? $this->Html->image($src, $htmlAttributes) : $src;
	}
	
	/**
	 * embed youtube video
	 *
	 * @param array $obj
	 * @param array $attributes
	 * @return html embed
	 */
	function embed($obj, $attributes) {
		$this->conf 	= Configure::getInstance() ;
		if(!isset($this->conf->media_providers["youtube"]["params"])) 
			return "" ;
		
		if (empty($attributes["width"])) { 
			$attributes["width"] = $this->conf->media_providers["youtube"]["params"]["width"];
		}
		if (empty($attributes["height"])) { 
			$attributes["height"] = $this->conf->media_providers["youtube"]["params"]["height"];
		}

		$url = rawurlencode($obj["uri"]);
		$url .= "&format=json&maxwidth=" . $attributes["width"] . "&maxheight=" . $attributes["height"];
		$url = sprintf($this->conf->media_providers["youtube"]["params"]["urlembed"], $url);
		if (!$oEmbed = $this->oEmbedInfo($url)) {
			return false;
		}
						
		return $oEmbed["html"] ;
	}
	
	/**
	 *
	 * @param array $obj
	 * @return youtube url
	 */
	function sourceEmbed(array &$obj) {
		return $obj['uri'] ;
	}
	
}
 
?>