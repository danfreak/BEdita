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
 * @version			$Revision: 3126 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2011-02-03 18:04:29 +0100 (Thu, 03 Feb 2011) $
 * 
 * $Id: category.test.php 3126 2011-02-03 17:04:29Z bato $
 */
require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class CategoryTestCase extends BeditaTestCase {

 	var $uses		= array('BEObject','Category') ;
	var $dataSource	= 'default' ;
 	
 	function testTags() {

		// show orphans
 		$result = $this->Category->getTags() ;
		pr("Tags with orphans:");
 		pr($result);
 		
 		$result = $this->Category->getTags(array("showOrphans" => false)) ;
		pr("Tags without orphans:");
 		pr($result);

 	 	$result = $this->Category->getTags(array("status" => 'on')) ;
		pr("Tags with status: on");
 		pr($result);
 		
 	 	$result = $this->Category->getTags(array("status" => array('on', 'off', 'draft'))) ;
		pr("Tags with status: on/off/draft");
 		pr($result);
 	
 	 	$result = $this->Category->getTags(array("cloud" => true)) ;
		pr("Tags with cloud: ");
 		pr($result);
 	
 	} 
 	
	public   function __construct () {
		parent::__construct('Category', dirname(__FILE__)) ;
	}	
}

?> 