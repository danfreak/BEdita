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
 * $Id: vimeo.php 2857 2010-06-01 15:04:04Z bato $
 */
class VimeoHelper extends AppHelper {
	
	var $helpers = array("Html");
	
	function thumbnail(&$obj, $htmlAttributes, $URLonly) {
		$url = rawurlencode($obj["uri"]);
		if (!$oEmbed = $this->oEmbedVideo($url)) {
			return false;
		}
		$src = $oEmbed['thumbnail_url'];
		return (!$URLonly)? $this->Html->image($src, $htmlAttributes) : $src;
	}
	
	/**
	 * embed vimeo video
	 * 
	 * @param array $obj
	 * @param array $attributes
	 * @return html embed video
	 */
	function embed(&$obj, $attributes) {
		$conf = Configure::getInstance();
		$url = rawurlencode($obj["uri"]);
		if (empty($attributes["width"]) && empty($attributes["height"])) {
			$attributes["width"] = $conf->media_providers["vimeo"]["params"]["width"];
			$attributes["height"] = $conf->media_providers["vimeo"]["params"]["height"];
		}
		foreach ($attributes as $key => $val) {
			$url .= "&" . $key . "=" . $val; 
		}
		$vimeoParams = Configure::read("media_providers.vimeo.params");
		$url = sprintf($vimeoParams["urlembed"], $url);
		if (!$oEmbed = $this->oEmbedInfo($url)) {
			return false;
		}
						
		return $oEmbed["html"] ;
	}
	
	/**
	 * path to vimeo video (can't get flv from api)
	 *
	 * @param array $obj
	 * @return youtube path
	 */
	function sourceEmbed($obj) {
		return $obj['uri'] ;
	}
	
}
 
?>