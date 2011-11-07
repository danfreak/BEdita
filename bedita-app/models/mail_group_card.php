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
 * Mail group card for addressbook
 *
 * @version			$Revision: 2484 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2009-11-25 12:05:12 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: mail_group_card.php 2484 2009-11-25 11:05:12Z bato $
 */
class MailGroupCard extends BEAppModel 
{
	var $belongsTo = array("MailGroup", "Card");
	
	protected $modelBindings = array( 
				"detailed" => array("Card" => array("User"), "MailGroup"),
				"default" => array("Card","MailGroup"),
				"minimum" => array()		
	);
}
?>