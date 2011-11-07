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
 * Author object
 *
 * @version			$Revision: 2744 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2010-04-07 17:45:50 +0200 (Wed, 07 Apr 2010) $
 * 
 * $Id: author.php 2744 2010-04-07 15:45:50Z bato $
 */
class Author extends BEAppObjectModel
{
	var $actsAs = array();
	
	public $searchFields = array("title" => 10 , "description" => 6, 
		"name" => 8, "surname" => 8);	
	
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"RelatedObject",
															"Alias",
															"Version" => array("User.realname", "User.userid")
														)
													),

       			"default" => array("BEObject" => array("ObjectType", "RelatedObject" )),

				"minimum" => array("BEObject" => array("ObjectType"))		
		);
		
	public $objectTypesGroups = array("leafs", "related");
	
}
?>
