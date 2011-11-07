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
 * bedita.sys.php - system specific settings,
 					overrides settings in bedita.ini
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: 3195 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2011-03-17 12:40:57 +0100 (Thu, 17 Mar 2011) $
 * 
 * $Id: bedita.sys.php.sample 3195 2011-03-17 11:40:57Z ste $
 */


/**
 ** ******************************************
 **  FileSystem Paths, URIs, Files defaults
 ** ******************************************
 */
// BEdita URL
$config['beditaUrl'] = "http://localhost/bedita";

/** Multimedia - files root folder on filesystem (use absolute path, if you need to change it)
 **
 **	On Linux could be /var/www/bedita/bedita-app/webroot/files
 ** On Windows could be C:\\xampp\\htdocs\\bedita\\bedita-app\\webroot\\files
 ** Or you can use DS as crossplatform directory separator as in default
 ** BEDITA_CORE_PATH . DS . "webroot" . DS . "files"
 ** where BEDITA_CORE_PATH points to bedita/bedita_app dir
 */
$config['mediaRoot'] = BEDITA_CORE_PATH . DS . "webroot" . DS . "files";

// Multimedia - URL prefix (without trailing slash)
$config['mediaUrl'] = $config['beditaUrl'] . '/files';

// alternative frontends path (absolute path)
//define('BEDITA_FRONTENDS_PATH', '/var/www/bedita/bedita-frontends');

// alternative bedita modules path (absolute path)
//define('BEDITA_MODULES_PATH', '/var/www/bedita/bedita-plugins');

// alternative bedita addons path (absolute path)
//define('BEDITA_ADDONS_PATH', '/var/www/bedita/bedita-addons');

/**
 ** ******************************************
 **  Statistics settings
 ** ******************************************
 */
 
// apache log statistics url - e.g. awstats ...
//$config["logStatsUrl"] = array(
//	"pub-nickname" => "http://user:passwd@awstatsurl",
//);


/**
 ** ******************************************
 **  SMTP and mail support settings
 ** ******************************************
 */
 
/** 
 ** smtp server configuration used for any kind of mail (notifications, newsletters, etc...)
 */
//$config['smtpOptions'] = array(
//	'port' => '25',
//	'timeout' => '30',
//	'host' => 'your.smtp.server',
//	'username' => 'your_smtp_username',
//	'password' => 'your_smtp_password'
//);

/**
 * mail support configuration
 * uncomment and fill to send error messages to your support
 */
//$config["mailSupport"] = array(
//	"from" => "bedita-support@...",	
//	'to' => "bedita-support@...",
//	'subject' => "[bedita] error message",
//);

?>