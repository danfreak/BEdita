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
 * Editor annotation
 *
 * @version			$Revision: 3073 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2010-12-09 13:41:12 +0100 (Thu, 09 Dec 2010) $
 * 
 * $Id: editor_note.php 3073 2010-12-09 12:41:12Z bato $
 */
class EditorNote extends BeditaAnnotationModel
{
	var $useTable = 'annotations';
	
	var $actsAs = array();
	
	var $validate = array(
    	'description' => array( 
        	'rule' => 'notEmpty',
        	'message' => 'This field cannot be left blank'
    	)
    );
	
	
}
?>
