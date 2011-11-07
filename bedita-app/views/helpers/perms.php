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

/**
 * Object permission helper
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: perms.php 2487 2009-11-25 16:56:37Z ste $
 * 
 * BEDITA_PERMS_READ,			0x1
 * BEDITA_PERMS_MODIFY,			0x2
 * BEDITA_PERMS_READ_MODIFY,	BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY
 */
class PermsHelper extends AppHelper {

	public function isReadable($user,$groups,$permissions) {
		$conf = Configure::getInstance();
		return $this->checkPerm($user,$groups,$permissions,$conf->BEDITA_PERMS_READ);
	}

	public function isWritable($user,$groups,$permissions) {
		$conf = Configure::getInstance();
		return $this->checkPerm($user,$groups,$permissions,$conf->BEDITA_PERMS_MODIFY);
	}

	private function checkPerm($u,$g_arr,$p_arr,$p) {
		if(empty($p_arr))
			return true;
		$res = false;
		foreach($p_arr as $k => $v) {
			if($v['switch']=='user' && $v['name']==$u) {
				if($v['flag'] & $p) {
					$res = true;
				}
			} else {
				if(!empty($g_arr)) {
					foreach($g_arr as $key => $gname) {
						if($v['switch']=='group' && $v['name']==$gname) {
							if($v['flag'] & $p) {
								$res = true;
							}
						}
					}
				}
			}
		}
		return $res ;
	}
}

?>