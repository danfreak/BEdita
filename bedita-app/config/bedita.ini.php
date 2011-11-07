<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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
 * bedita.ini.php - settings, constants, variables for BEdita local installation
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: 3399 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2011-10-28 17:49:43 +0200 (Fri, 28 Oct 2011) $
 * 
 * $Id: bedita.ini.php 3399 2011-10-28 15:49:43Z ste $
 */

$config = array() ;
 
$config["projectName"] = "BEdita 3.1"; // override in bedita.cfg

$config["userVersion"] = "BEdita 3.1"; // don't override

$config["majorVersion"] = "3.1.4"; // don't override -- admin/system

$config["helpBaseUrl"] = "http://docs.bedita.com/behelp/v31"; // don't override --

// Multimedia - image file substituting missing content (as now used in BeThumb helper)
$config['imgMissingFile'] = "/img/iconMissingImage_130x85.gif" ;

/**
 ** ******************************************
 **  Content and UI Elements defaults
 ** ******************************************
 */

// User Interface default language [see also 'multilang' below]
$config['Config']['language'] = "eng"; // or "ita", "eng", "spa", "por"

// Set 'multilang' true for user choice [also set 'multilang' true if $config['Config']['language'] is set]
$config['multilang'] = true;
$config['defaultLang'] = "eng"; // default fallback


/* Dates - default presentation format [syntax used by strftime php function]
 * It is used for datepicker calendar. Format has to contain %d,%m,%Y tags
 */
$config['datePattern'] 	= "%m-%d-%Y" ;

/* Date patterns for different locales
 * It is used for datepicker calendar. Format has to contain %d,%m,%Y tags
 */
$config['datePatternLocale'] = array(
	"ita"	=> "%d/%m/%Y"
);

// Default date time pattern
$config['dateTimePattern'] 	= "%m-%d-%Y  %H:%M";  //

$config['dateTimePatternLocale'] = array(
	"ita"	=> "%d/%m/%Y %H:%M" 
);


// Default status of new objects
$config['defaultStatus'] = "draft" ;

// TinyMCE Rich Text Editor for long_text ['true' to enable]
$config['mce'] = true;

// Application messages - temporary messages duration
$config['msgPause'] = 3000;		// milliseconds

// bedita user for unit test
$config['unitTestUserId'] = 1;

// activity timeout used in Home/"connected user" time duration in minutes
$config['activityTimeout'] = 20;

// concurrent users check - time interval, duration in milliseconds
$config["concurrentCheckTime"] = 20000;

// Autosave time check - time interval, duration in milliseconds
$config["autoSaveTime"] = 120000;

/**
 ** ******************************************
 **  Image, Video and Audio defaults
 ** ******************************************
 */
$config['media']['image']['thumbWidth']  = 130;      // px thumb width
$config['media']['image']['thumbHeight'] = 85;       // px thumb height
$config['media']['image']['thumbMode']   = "crop";   // crop, fill, croponly, stretch - thumb mode
$config['media']['image']['thumbFill']   = "FFFFFF"; // hex - fill color when thumb mode is fill
$config['media']['image']['thumbCrop']   = "C";      // string, crop mode when thumb mode is crop/croponly 'C', 'T', 'B', 'L', 'R', 'TL', 'TR', 'BL', 'BR'
$config['media']['image']['thumbQ']      = 75;       // int, JPEG thumbnail image quality [1-100]
$config['media']['image']['thumbUpscale']= false;     // bool, allow thumbnail upscale
$config['media']['image']['preferImagemagick'] = true;       // bool, use image_magick or not (even if available)
$config['media']['image']['imagemagick'] = "";       // string, path to image_magick executable
$config['media']['image']['over']        = "";       // string, path to overlay image
$config['media']['image']['wmi']['f']    = "";       // string, path to watermark image file
$config['media']['image']['wmi']['a']    = "C";      // string, wm alignment B=bottom, T=top, L=left, R=right, C=centre, *=tile, 2 letters ie TL, or absolute position in px
$config['media']['image']['wmi']['o']    = 100;      // int, wm opacity 0 (transparent) to 100 (opaque)
 
$config['media']['video']['width']       = 300;      // px video player width
$config['media']['video']['height']      = 250;      // px video player height
$config['media']['video']['thumbWidth']  = 130;      // px thumb height
$config['media']['video']['thumbHeight'] = 85;       // px thumb height
$config['media']['video']['player'] 	 = "flowplayer.swf"; // flash player file (it has to stay in webroot/swf directory)

$config['media']['audio']['width']       = 300;      // px - audio player width
$config['media']['audio']['height']      = 24;       // px - audio player height
$config['media']['audio']['player'] 	 = "flowplayer.swf"; // flash player file (it has to stay in webroot/swf directory)


/**
 ** ******************************************
 **  Login (backend) and Security Policies
 ** ******************************************
 */

// Here default settings, override settings in bedita.cfg.php
$config['loginPolicy'] = array (
	"maxLoginAttempts" => 10,
	"maxNumDaysInactivity" => 180,
	"maxNumDaysValidity" => 60,
	"passwordRule" => "/\w{3,}/", // regexp to match for valid passwords (empty => no regexp)
	"passwordErrorMessage" => "Password must contain at least 3 valid alphanumeric characters", // error message for passwrds not matching given regexp
);

/**
 ** ******************************************
 **  More specific settings
 ** ******************************************
 */

/**
 ** Import PHP constants for smarty templates 
 */
$config['DS']        = DS;

/**
 * Modules permissions
 */
define("BEDITA_PERMS_READ",	0x1) ; // read-only module permission
define("BEDITA_PERMS_MODIFY",	0x2) ;
define("BEDITA_PERMS_READ_MODIFY",	BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY) ; // read-write module permission

/**
 * Permission params, for Smarty 
 */
$config['BEDITA_PERMS_READ'] 	= BEDITA_PERMS_READ ;
$config['BEDITA_PERMS_MODIFY'] 	= BEDITA_PERMS_MODIFY ;
$config['BEDITA_PERMS_READ_MODIFY'] = BEDITA_PERMS_READ_MODIFY;

/**
 * object permissions
 */
$config["objectPermissions"] = array(
	"write" => 1,
	"frontend_access_with_block" => 2,
	"frontend_access_without_block" => 3
);


/**
 * BEdita modules
 */
$config['modules'] = array(
	"areas" => array("id" => 1, "label" => "publications"),
	"admin" => array("id" => 2, "label" => "admin"),
	"translations" => array("id" => 3, "label" => "translations"),
	"documents" => array("id" => 6, "label" => "documents"),
	"news" => array("id" => 7, "label" => "news"),
	"galleries" => array("id" => 8, "label" => "galleries"),
	"events" => array("id" => 10, "label" => "events"),
	"webmarks" => array("id" => 12, "label" => "webmarks"),
	"addressbook" => array("id" => 16, "label" => "addressbook"),
	"newsletter" => array("id" => 18, "label" => "newsletter"),
	"statistics" => array("id" => 23, "label" => "statistics"),
	"tags" => array("id" => 24, "label" => "tags"),
	"comments" => array("id" => 25, "label" => "comments"),
	"multimedia" => array("id" => 26, "label" => "multimedia")
);


/*
 * Relations - default objects' relation types
 * 
 * Array("defaultObjRelationType" => 
 * 		array(
 * 			"hidden" => define if relation is hidden. Used in object's view in relationship tab,
 * 			"left" => array of object_type_id related to right object_type_id (empty array means all object_type_id) 
 * 			"right => array of object_type_id related to left object_type_id (empty array means all object_type_id)
 * 			
 * OPTIONAL "objectType" => array of object_type_id related to objectType. If defined it's used instead of 'left', 'right'
 * 		)
 *	)
 * 
 */
$config["defaultObjRelationType"] = array(

	"language" => array(
		"hidden" => true,
		"left" 		=> array(),
		"right" 		=> array()	
	),
	"seealso" => array(
		"hidden" => false,
		"left" 		=> array(),
		"right" 		=> array()	
	),
	"download" => array(
		"hidden" => false,
		"left" 		=> array("b_e_file","image","application","audio","video"),
		"right" 		=> array()	
	),
	"gallery" => array(
		"hidden" => true,
		"left" 		=> array("gallery"),
		"right" 		=> array()	
	),
	"attach" => array(
		"hidden" => true,
		"left" => array("b_e_file","image","application","audio","video"),
		"right" => array()
	),
	"link" => array(
		"hidden" => true,
		"left" => array("link"),
		"right" => array()
	),
	"author" => array(
		"hidden" => true,
		"left" => array("book"),
		"right" => array("author")
	)
);

// Relations - local objects' relation types (override in bedita.cfg)
$config["objRelationType"] = array ();

/**
 * One-way relation
 * array of relations' label that define one-way relations
 */ 
$config["defaultOneWayRelation"] = array();

// Cfg One-way relation (override in bedita.cfg)
$config["cfgOneWayRelation"] = array();

// Default reserved words [avoided in nickname creation]
$config["defaultReservedWords"] = array("section", "content", "rss", "feed", "georss", "georssatom", "kml",
	"download", "xml", "xmlobject", "sitemap", "sitemap.xml", "sitemap_xml", 
	"json", "captchaImage", "saveComment", "search", "tag", "category", "login", "logout", "js",
	"hashjob", "subscribe", "printme", "homePage", "img", "css", "files", "swf", "webroot",
	"lang", "favicon.ico", "favicon.gif");

// Cfg reserved words (override in bedita.cfg)
$config["cfgReservedWords"] = array();

// download - redirect extensions to mediaURL [FrontenController::download]
$config["redirectExtensionsDownload"] = array ("gz", "tar", "zip");
// download - redirect mimetypes to mediaURL [FrontenController::download]
$config["redirectMimeTypesDownload"] = array ();

/**
 * Session handling parameters
 */
$config['session'] = array (
	"sessionUserKey"	=> "BEAuthUser", // session var name of info user connected
) ;


/**
 * 
 * Options status select
 * 
 */
$config['statusOptions'] = array(
	"on"	=> "ON",
	"off"	=> "OFF",
	"draft"	=> "DRAFT"
) ;

/**
 * Lang selection options ISO-639-3 - Default language options for contents
 */
$config['langOptions'] = array(
		"ara" => "Arabic",
		"bul" => "Bulgarian",
		"cat" => "Catalan",
		"zho" => "Chinese",
		"hrv" => "Croatian",
		"ces" => "Czech",
		"dan" => "Danish",
		"nld" => "Dutch",
		"eng" => "English",
		"fin" => "Finnish",
		"fra" => "French",
		"deu" => "German",
		"ell" => "Greek",
		"heb" => "Hebrew",
		"hin" => "Hindi",
		"ita" => "Italian",
		"jpn" => "Japanese",
		"kor" => "Korean",
		"lav" => "Latvian",
		"lit" => "Lithuanian",
		"nor" => "Norwegian",
		"pol" => "Polish",
		"por" => "Portuguese",
		"ron" => "Romanian",
		"rus" => "Russian",
		"srp" => "Serbian",
		"slk" => "Slovak",
		"slv" => "Slovenian",
		"spa" => "Spanish",
		"swe" => "Swedish",
		"ukr" => "Ukrainian",
		"vie" => "Vietnamese"
) ;

// ISO-639-3 codes - User interface language options (backend)
$config['langsSystem'] = array(
	"eng"	=> "english",
	"ita"	=> "italiano",
//	"deu"	=> "deutsch",
//	"por"	=> "portuguěs"

) ;

// maps ISO-639-2 known codes to ISO-639-3
$config['langsSystemMap'] = array(
	"it"	=> "ita",
	"en"	=> "eng",
	"en_us"	=> "eng",
	"en_gb"	=> "eng",
	"es"	=> "spa",
	"pt"	=> "por",
	"pt_br"	=> "por"
) ;

// 'langsIso' empty, overridden if 'langOptionsIso' is true
$config['langsIso'] = array();
// add langs.iso.php to language options for content 
$config['langOptionsIso'] = false;

// media types for objects in multimedia module
$config['mediaTypes'] = array('image','video','audio','text','spreadsheet','presentation','drawing','chart','formula','application');


/**
 * variables used for accepting remote URL and for identifying a BEdita object type from mime  
 */
$config['validate_resource'] = array(
	'paranoid'	=> true,	// if true and 'allow_url_fopen'=false doesn't accept remte URL

	// generic URL
	'URL'	=> '/^\s*[a-z\d\+\-\.]+\:\/\//i',
	
	 // URL allowed
	'allow'	=> array( 
				'/^\s*http:\/\/(.*)\.(html|htm)$/',
				'/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/'
			   ),
	/**
	 * Accepted MIME types for different object types
	 * The structure is:
	 * 		ModelName => array(mime type regular expressions) 
	 * or
	 * 		ModelName => array(
	 * 			"specific type" => array(
	 * 				"mime_type" => array(mime type regular expressions),
	 * 				other parameters...
	 * 			)
	 * 		)
	 */
	'mime'	=> array(
				'Image'			=> array('/image\/\.*/'),
				'Audio'			=> array('/audio\/\.*/'),
				'Video'			=> array('/video\/\.*/','/application\/flash-video/'),
			    'Application'	=> array(
							    		"flash" => array(
											"mime_type" => array('/application\/x-shockwave-flash/'),
											"application_type" => "application/x-shockwave-flash",
											"label" => "Adobe Flash"
										),
										"shockwave" => array(
											"mime_type" => array('/application\/x-director/'),
											"application_type" => "application/x-director",
											"label" => "Adobe Director"
										)
									),
				'BEFile'		=> array('/application\/\.*/', '/text\/\.*/', '/beexternalsource/')
				)
) ;


/**
 *  Supported media providers
 *  
 *  for any providers supported are defined an array of regexp to identify provider and media id 
 *  and an array of params
 */
$config['media_providers'] = array(
	"youtube"	=> array(
		"regexp" => array(
			'/^http:\/\/\w{3}\.youtube\.com\/watch\?v=(.[^&]+)/',
			'/^http:\/\/youtube\.com\/watch\?v=(.[^&]+)/',
			'/^http:\/\/[a-z]{2}\.youtube\.com\/watch\?v=(.[^&]+)/'
		),
		"params" => array(
			"width" 	=> 300,
			"height" 	=> 250,
			"urlthumb"	=> "http://i.ytimg.com/vi/%s/default.jpg",
			"urlembed"	=> "http://www.youtube.com/oembed?url=%s"
		)
	),
	"blip"	=> array(
		"regexp" => array(
			'/^http:\/\/\w{3}\.blip\.tv\/file\/(\d+)\?{0,1}.*/',
			'/^http:\/\/blip\.tv\/file\/(\d+)\?{0,1}.*/',
			'/^http:\/\/blip\.tv\/[^"\&\?\/]+\/([^"\&\?\/]+)/',
			'/^http:\/\/w{3}\.blip\.tv\/[^"\&\?\/]+\/([^"\&\?\/]+)/'
		),
		"params" => array(
			"width"		=> 300,
			"height"	=> 250,
			"urlinfo" 	=> "http://www.blip.tv/file/%s?skin=json&no_wrap=1",
			"urlembed"	=> "http://blip.tv/oembed/?url=%sz"
		)
	),
	"vimeo"	=> array(
		"regexp" => array(
			'/^http:\/\/\w{3}\.vimeo\.com\/(\d+)/',
			'/^http:\/\/vimeo\.com\/(\d+)/'
		),
		"params" => array(
			"width"		=> 300,
			"height"	=> 250,
			"urlinfo" 	=> "http://vimeo.com/api/v2/video/%s.%s",
			"urlembed"	=> "http://vimeo.com/api/oembed.json?url=%s"
		)
	)
) ;

/**
 *  Default model bindings for Containable Behaviour 
 */
$config['modelBindings'] = array() ;


// default email fields -- override in bedita.cfg or bedita.sys
$config['mailOptions'] = array(
	"sender" => "noreply@bedita.com", 
	"reply_to" => "noreply@bedita.com", 
	"signature" => "powered by BEdita - http://www.bedita.com",
);


/**
 *  default values for fulltext search. Override in bedita.cfg  
 */
$config['searchFields'] = array() ;

/**
 * Default css filename for newsletter templates
 */
$config['newsletterCss'] = "base.css";

/**
 * default timeout in minutes to assume mail jobs blocked and try to resend
 */
$config['newsletterTimeout'] = 15;

/**
 * Default value in milliseconds, between autosave of objects (status draft or off)
 */
$config['autosaveTime'] = 120000; // two minutes

/**
 * Hash jobs expired time in seconds (7 days)
 */
$config['hashExpiredTime'] = 604800;


/**
 * Constraints for database VARCHAR fields with enumerated choiches
 * first item is the default
 */
$config['checkConstraints'] = array(
	"applications" => array("text_dir" => array("ltr", "rtl")),
	"banned_ips" => array("status" => array("ban", "accept")),
	"cards" => array("mail_status" => array("valid", "blocked")),
	"categories" => array("status" => array("on", "off")),
	"events" => array("log_level" => array("debug", "info","warn", "err")),
	"hash_job" => array("status" => array("pending", "expired", "closed", "failed")),
	"links" => array("target" => array("_self", "_blank", "parent", "top", "popup")),
	"mail_groups" => array("security" => array("all", "none")),
	"mail_group_cards" => array("status" => array("pending", "confirmed")),
	"mail_logs" => array("log_level" => array("info","warn", "err")),
	"mail_jobs" => array("status" => array("unsent", "pending", "sent", "failed")),
	"mail_messages" => array("status" => array("unsent", "pending", "injob", "sent")),
	"modules" => array(
		"status" => array("on", "off"),
		"module_type" => array("core", "plugin")
	),
	"objects" => array(
		"status" => array("on", "off", "draft"),
		"comments" => array("on", "off", "moderated")
	),
	"permissions" => array("switch" => array("user","group")),
	"permission_modules" => array("switch" => array("user","group")),
	"properties" => array("property_type" => array("number", "date", "text", "options")),
	"sections" => array(
		"syndicate" => array("on", "off"),
		"priority_order" => array("asc", "desc")
	),
	"users" => array(
		"comments" => array("never", "mine", "all"),
		"notes" => array("never", "mine", "all")
	)
);

/**
 * default notify options when an user is created
 * values available: never, mine, all
 */
$config["notifyOptions"] = array(
	"comments" => "mine",
	"notes" => "mine"
);

/**
 * Specific licenses, to add in bedita.cfg
 */
$config["defaultLicenses"] = array(
	"CC-BY" => array("title" => "Creative Commons Attribution", "url" => "http://creativecommons.org/licenses/by/3.0"),
	"CC-BY-SA" => array("title" => "Creative Commons Attribution Share Alike", "url" => "http://creativecommons.org/licenses/by-sa/3.0"),
	"CC-BY-ND" => array("title" => "Creative Commons Attribution No Derivatives", "url" => "http://creativecommons.org/licenses/by-nd/3.0"),
	"CC-BY-NC" => array("title" => "Creative Commons Attribution Non-Commercial", "url" => "http://creativecommons.org/licenses/by-nc/3.0"),
	"CC-BY-NC-SA" => array("title" => "Creative Commons Attribution Non-Commercial Share Alike", "url" => "http://creativecommons.org/licenses/by-nc-sa/3.0"),
	"CC-BY-NC-ND" => array("title" => "Creative Commons Attribution Non-Commercial No Derivatives", "url" => "http://creativecommons.org/licenses/by-nc-nd/3.0"),
	"RES" => array("title" => "All rights reserved", "url" => ""),
);
				
/**
 * Specific licenses, to add in bedita.cfg
 */
$config["cfgLicenses"] = array();

/**
 * GeoTag options
 * 
 * zoom: keys are google maps zoom level
 * 
 * mapType: keys are google maps standard url parameters plus "s" for street view layer
 * 
 */
$config["geoTagOptions"] = array(
	"zoom" => array(
		21 => "maximum",
		18 => "neighbourhood",
		16 => "quarter",
		14 => "city",
		9 => "district",
		7 => "country",
		4 => "continent",
		1 => "world",
	),
	"mapType" => array(
		"m" => "map",
		"k" => "satellite",
		"h" => "hybrid",
		"p" => "terrain",
		"c" => "Google maps StreetView™",
		"e" => "Google Earth™",
	)
);


/**
 * Specific System settings
 * ALWAYS AT THE END of bedita.ini.php
 */
if (file_exists (BEDITA_CORE_PATH. DS ."config".DS."bedita.sys.php") ) {
	include_once(BEDITA_CORE_PATH. DS ."config".DS."bedita.sys.php") ;	
}
?>
