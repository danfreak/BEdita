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
 * bedita.cfg.php - local installation specific settings,
 					overrides settings in bedita.ini
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: 3098 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2011-01-04 14:50:15 +0100 (Tue, 04 Jan 2011) $
 * 
 * $Id: bedita.cfg.php.sample 3098 2011-01-04 13:50:15Z bato $
 */
if (!isset($config)) {
	$config = array();
}
// BEdita instance name 
//$config["projectName"] = "My Project Name";

/**
 ** ******************************************
 **  Content and UI Elements defaults
 ** ******************************************
 */

// User Interface default language [see also 'multilang' below]
//$config['Config']['language'] = "ita"; // or "eng", "spa", "por"

// Set 'multilang' true for user choice [also set 'multilang' true if $config['Config']['language'] is set]
// $config['multilang'] = true;
// $config['defaultLang'] = "eng"; // default fallback

// ISO-639-3 codes - User interface language options (backend)
// $config['langsSystem'] = array(
//	"eng"	=> "english",
//	"ita"	=> "italiano",
//	"deu"	=> "deutsch",
//	"por"	=> "portuguěs"
// );

// Status of new objects
//$config['defaultStatus'] = "draft" ;

// TinyMCE Rich Text Editor for long_text ['false' to disable - defaults true]
// $config['mce'] = true;


/**
 ** ******************************************
 **  Login (backend) and Security Policies
 ** ******************************************
 */
//
// A simple example with a simple password regexp rule, uncomment and change according to your needs
//
//$config['loginPolicy'] = array (
//	"maxLoginAttempts" => 3,
//	"maxNumDaysInactivity" => 60,
//	"maxNumDaysValidity" => 10,
//	"passwordRule" => "/\w{4,}/", // regexp to match for valid passwords (empty => no regexp)
//	"passwordErrorMessage" => "Password must contain at least 4 valid alphanumeric characters", // error message for passwrds not matching given regexp
//);



/**
 ** ******************************************
 **  Local installation specific settings
 ** ******************************************
 */

 
/**
 ** Relations - local objects' relation types
 ** define here custom semantic relations
 */
// $config["objRelationType"] = array(
// 		"language" => array()
// );

// One-way relation, array of objRelationType keys
// $config["cfgOneWayRelation"] = array();

// Reserved words [avoided in nickname creation]
// $config["cfgReservedWords"] = array();

/**
 * Lang selection options ISO-639-3 - Language options for contents
 */
//$config['langOptions'] = array(
//	"ita"	=> "italiano",
//	"eng"	=> "english",
//	"spa"	=> "espa&ntilde;ol",
//	"por"	=> "portugu&ecirc;s",
//	"fra"	=> "fran&ccedil;ais",
//	"deu"	=> "deutsch"
//) ;


// add langs.iso.php to language options for content 
//$config['langOptionsIso'] = false; 


// default values for fulltext search
// $config['searchFields'] = array(
//	'ModelName' => array('title'=> 6, 'description' => 4),
//) ;


// specific css filename for newsletter templates
//$config['newsletterCss'] = "base.css";

/**
 * save history navigation
 
 * "sessionEntry" => number of history items in session
 * "showDuplicates" => false to not show duplicates in history session 
 * "trackNotLogged" => true save history for all users (not logged too)
 */
//$config["history"] = array(
//	"sessionEntry" => 5,
//	"showDuplicates" => false,
//	"trackNotLogged" => false
//);

?>