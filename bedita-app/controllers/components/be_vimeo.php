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
 * Blip TV media component
 *  
 *
 * @version			$Revision: 2857 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2010-06-01 17:04:04 +0200 (Tue, 01 Jun 2010) $
 * 
 * $Id: be_vimeo.php 2857 2010-06-01 15:04:04Z bato $
 */
class BeVimeoComponent extends Object {
	var $controller	;
	var $info = null ;
	
	function startup(&$controller) {
		$this->controller 	= $controller;
	}
	
	
	/**
	 * get vimeo info video
	 *
	 * @return unknown
	 */
	public function getInfoVideo($id, $attributes=array()) {
		$conf = Configure::getInstance() ;
		$this->info = null ;
		
		if(!isset($conf->media_providers["vimeo"]["params"])) 
			return false ;
		
		$urlinfo = $conf->media_providers["vimeo"]["params"]['urlinfo'];
 
		if (!$info = file_get_contents(sprintf($urlinfo, $id, "php"))) {
			return false;
		}
		
		$info = unserialize($info);
		$this->info = $info[0];
		return $this->info;
	}
	
	/**
	 * get thumbnail
	 * @param $id
	 * @return url, false if error occurs
	 */
	public function getThumbnail($id) {
		if(!$this->getInfoVideo($id)) {
			return false;
		}
		return $this->info['thumbnail_medium'];
	}
	
	/**
	 * set data to save multimedia object
	 * @param $id
	 * @param $data
	 * @return boolean
	 */
	public function setInfoToSave(&$data) {
		if(!$this->getInfoVideo($data["video_uid"])) {
			return false;
		}
		
		$data['title'] = (empty($data['title']))? $this->info['title'] : trim($data['title']);
		$data['description'] = (empty($data['description']))? $this->info['description'] : $data['description'];
		$data['uri']		= $this->info['url'] ;
		if (empty($data['thumbnail']))
			$data['thumbnail']	= $this->info['thumbnail_medium'];
		if (empty($data['duration']))
			$data['duration']	= $this->info['duration']/60;
		$data['name']		= preg_replace("/[\'\"]/", "", $data['title']);
		$data['mime_type']	= "video/".$data["provider"];
		return true;
	}
	
}

?>