<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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
 * User custom properties
 *
 * @version			$Revision: 2861 $
 * @modifiedby 		$LastChangedBy: niki $
 * @lastmodified	$LastChangedDate: 2010-06-03 16:02:12 +0200 (Thu, 03 Jun 2010) $
 * 
 * $Id: user_property.php 2861 2010-06-03 14:02:12Z niki $
 */
class UserProperty extends BEAppModel
{
	var $belongsTo = array(
		"Property" => array(
				"conditions" => array("Property.object_type_id" => null) ,
		), 
		"User");
}
?>
