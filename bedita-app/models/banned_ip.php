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
 * Banned ip object
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: banned_ip.php 2487 2009-11-25 16:56:37Z ste $
 */
class BannedIp extends BEAppModel
{
	var $actsAs = array(
			'CompactResult' 		=> array()
	);
	
	public function ban($ipNumber, $status = "ban") {
		$data = array("ip_address" => $ipNumber, "status" => $status);
		$banned = $this->find('first', array(
                	'conditions' => "BannedIp.ip_address='$ipNumber'",
                	'fields' => array('BannedIp.id')));
        if(!empty($banned)) {
        	$data['id'] = $banned['id'];
        }
		
		if(!$this->save($data)) {
	 		throw new BeditaException(__("Error saving IP", true), $this->validationErrors);
	 	}
				
	}
	
	public function isBanned($ipNumber) {
		$banned = $this->find('first', array(
                	'conditions' => array('BannedIp.status' => 'ban', 
                	'BannedIp.ip_address' => $ipNumber), 
                	'fields' => array('BannedIp.id')));
		return !empty($banned);
	}
	
}
?>
