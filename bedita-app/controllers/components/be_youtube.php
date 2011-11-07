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
 * Youtube media component
 *
 * @version			$Revision: 2857 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2010-06-01 17:04:04 +0200 (Tue, 01 Jun 2010) $
 * 
 * $Id: be_youtube.php 2857 2010-06-01 15:04:04Z bato $
 */
class BeYoutubeComponent extends Object {
 	
	var $controller	;
	var $info = null ;
	
	function startup(&$controller) {
		$this->controller 	= $controller;
	}
	
	public function getThumbnail($uid) {
		$youtubeParams = Configure::read("media_providers.youtube.params");
		$thumbnail = sprintf($youtubeParams["urlthumb"], $uid);
		return $thumbnail;
	}
	
	/**
	 * set data to save multimedia object
	 * @param $id
	 * @param $data
	 * @return boolean
	 */
	public function setInfoToSave(&$data) {
		$data['title']		= (!empty($data['title'])) ? trim($data['title']) : 'youtube video';
		$data['name']		= preg_replace("/[\'\"]/", "", $data['title']) ;
		$data['mime_type']	= "video/" . $data["provider"] ;
		if (empty($data['thumbnail']))
			$data['thumbnail']	= $this->getThumbnail($data["video_uid"]);
		return true;
	}
	
}
 
?>