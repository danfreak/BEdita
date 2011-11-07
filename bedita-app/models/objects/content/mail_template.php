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
 * Mail template for email
 *
 * @version			$Revision: 2909 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2010-07-09 14:57:44 +0200 (Fri, 09 Jul 2010) $
 * 
 * $Id: mail_template.php 2909 2010-07-09 12:57:44Z bato $
 */
class MailTemplate extends BeditaContentModel
{
	var $useTable = 'mail_messages';

	public $searchFields = array();
	
	var $actsAs 	= array(
			'ForeignDependenceSave' => array('Content')
	); 
	
	var $hasOne= array(
			'BEObject' => array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' => array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				)
		);
	
		
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permission",
															"Annotation",
															"Version" => array("User.realname", "User.userid")
														),
									 "Content"
									),
				"default" => array("BEObject" => array("ObjectType"), "Content"),

				"minimum" => array("BEObject" => array("ObjectType"), "Content")
	);
	
}
?>