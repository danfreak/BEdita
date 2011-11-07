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
 * mediatype.ini.php - settings, constants, variables for media types on upload
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: 3385 $
 * @modifiedby 		$LastChangedBy: dante $
 * @lastmodified	$LastChangedDate: 2011-10-14 15:13:14 +0200 (Fri, 14 Oct 2011) $
 * 
 * $Id: mediatype.ini.php 3385 2011-10-14 13:13:14Z dante $
 */

$config["mediaTypeMapping"] = array(
	"application/msword"							=>	"text",
	"application/vnd.oasis.opendocument.text"		=>	"text",
	"application/pdf"								=>	"text",
	"text/html"										=>	"text",
	"application/vnd.ms-powerpoint"					=>	"text",
	"application/rtf"								=>	"text",
	"text/plain"									=>	"text"
);
?>