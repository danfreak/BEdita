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
 * @version			$Revision: 3323 $
 * @modifiedby 		$LastChangedBy: dante $
 * @lastmodified	$LastChangedDate: 2011-06-28 14:52:52 +0200 (Tue, 28 Jun 2011) $
 * 
 * $Id: be_thumb.php 3323 2011-06-28 12:52:52Z dante $
 */
class BeThumbHelper extends AppHelper {

	private $_helpername = "BeThumb Helper";


	// output template for sprintf non funziona e vai a fare in culo lo vedo dopo
	// private $_html	= '<img src="%s" width="%d" height="%d" alt="%s" />';


	// supported image types (order is important)
	private $_imagetype = array ("", "gif", "jpg", "png", "jpeg");
	private $_defaultimagetype = 2; // defaults to 2 [= JPG]

	private $_mimeType = array("image/gif", "image/jpeg", "image/pjpeg", "image/png");

	// empties (see private method _resetObjects)
	private $_resample    = false;
	private $_conf        = array ();
	private $_imageInfo   = array ();
	private $_imageTarget = array ();



	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	// var $helpers = array('Html');
	
	
	
	function __construct()
	{
		// get configuration parameters and defaults
		$this->_conf = Configure::read('media') ;
		$this->_conf['root']  = Configure::read('mediaRoot');
		$this->_conf['url']   = Configure::read('mediaUrl');
		$this->_conf['tmp']   = Configure::read('tmp');
		$this->_conf['imgMissingFile'] = Configure::read('imgMissingFile');
	}





	/*
	 * image public method: embed an image after resample and cache
	 * 
	 * params: be_obj, required, object, BEdita Multimedia Object
	 *         width, height, longside, at least one required, integer (if longside, w&h are ignored)
	 *         mode, optional, 'crop'/'fill'/'croponly'/'stretch'
	 *         modeparam, optional, depends on mode:
	 *             if fill, string representing hex color, ie 'FFFFFF'
	 *             if croponly, string describing crop zone 'C', 'T', 'B', 'L', 'R', 'TL', 'TR', 'BL', 'BR'
	 *             if stretch, bool to allow upscale (default false)
	 *         type, optional, 'gif'/'png'/'jpg', force image target type
	 *         upscale, optional, bool, allow or not upscale
	 *         
	 *         NB: optionally the second argument may be the associative array of said parameters
	 *         
	 * return: resampled and cached image URI (using $html helper)
	 * 
	 */
	public function image ($be_obj, $params = null) 
	//$width = false, $height = false, $longside = null, $mode = null, $modeparam = null, $type = null, $upscale = null)
	{
		// this method is for image only, check bedita object type
		if ( strpos($be_obj['mime_type'], "image") === false )
		{
			$this->_triggerError ( $this->_helpername . ": '" . $be_obj['name'] . "' is not a valid Bedita image object (object type is " . $be_obj['mime_type'] . ")", E_USER_NOTICE ) ;
			return $this->_conf['imgMissingFile'];
		}
		elseif (!in_array($be_obj["mime_type"], $this->_mimeType))
		{
			return false;
		}
		else $this->_resetObjects();

		// read params as an associative array or multiple variable
		$expectedArgs = array ('width', 'height', 'longside', 'mode', 'modeparam', 'type', 'upscale');
		if ( func_num_args() == 2 && is_array( func_get_arg(1) ) )
		{
			extract ($params);
		}
		else
		{
			$argList = func_get_args() ;
			array_shift($argList);
		    for ($i = 0; $i < sizeof($expectedArgs); $i++)
			{
		        if ( isset($argList[$i]) )
					$$expectedArgs[$i] = $argList[$i];
		    }
		}


		// filepath & name
		$this->_imageInfo['path']		= $be_obj['uri'];
		$this->_imageInfo['filename']	= $be_obj['name'];
		$this->_imageInfo['ext']		= end ( explode ( '.', $this->_imageInfo['filename'] ) );
		$this->_imageInfo['filepath']	= $this->_conf['root'] . $this->_imageInfo['path'];  // absolute
		if (DS != "/") {
			$this->_imageInfo['filepath'] = str_replace("/", DS, $this->_imageInfo['filepath']);
		}
		$this->_imageInfo['filenameBase'] = pathinfo($this->_imageInfo['filepath'], PATHINFO_FILENAME);
		$this->_imageInfo['filenameMD5'] = md5($this->_imageInfo['filename']);
		$this->_imageInfo['cacheDirectory'] = dirname($this->_imageInfo['filepath']) . DS . 
											  substr($this->_imageInfo['filenameBase'],0,5) . "_" . 
											  $this->_imageInfo['filenameMD5'];

		// test source file
		if ( !$this->_testForSource () )
		{
			return $this->_conf['imgMissingFile'];
		}


		// upscale
		if ( isset ($upscale) )	$this->_imageTarget['upscale'] = $upscale;
		else 					$this->_imageTarget['upscale'] = $this->_conf['image']['thumbUpscale'];


		// cropmode
		$this->_imageTarget['cropmode'] = $this->_conf['image']['thumbCrop'];


		// upscale, mode & fill
		if ( !isset ($mode) ) $mode = $this->_conf['image']['thumbMode'];
		switch ($mode)
		{
			case "fill":
				$this->_imageTarget['mode'] = 1;
				if ( isset ($modeparam) )	$this->_imageTarget['fillcolor'] = $modeparam;
				else						$this->_imageTarget['fillcolor'] = $this->_conf['image']['thumbFill'];
				break;
			
			case "stretch":
				$this->_imageTarget['mode'] = 2;
				break;
			
			case "croponly":
				$this->_imageTarget['mode'] = 3;
				if ( isset ($modeparam) )	$this->_imageTarget['cropmode'] = $modeparam; // overwrite
				break;
			
			case "crop":
			default:
				$this->_imageTarget['mode'] = 0;
				break;
		}



		// build _image_info with getimagesize() or available parameters
		if ( empty($be_obj['width']) || empty($be_obj['height']) )
		{
			if ( !$_image_data =@ getimagesize($this->_imageInfo['filepath']) )
			{
				$this->_triggerError ( $this->_helpername . ": '" . $this->_imageInfo['path'] . "' is not a valid image file", E_USER_NOTICE ) ;
				return $this->_conf['imgMissingFile'];
			}
			
			// set up the rest of image info array
			$this->_imageInfo["w"]		= $_image_data [0];
			$this->_imageInfo["h"]		= $_image_data [1];
			$this->_imageInfo['type']	= $this->_imagetype[$_image_data [2]]; // 1=GIF, 2=JPG, 3=PNG
			unset ($_image_data);
		}
		else
		{
			$this->_imageInfo["w"] = $be_obj['width'];
			$this->_imageInfo["h"] = $be_obj['height'];
	
			// since not using getimagesize(), try to get image type from object or extension
			if ( !($this->_imageInfo['ntype'] =@ $this->array_isearch ( substr (strrchr ($be_obj['mime_type'], "/"), 1), $this->_imageInfo['ext'] ) ) )
			{
				if ( !( $this->_imageInfo['ntype'] =@ $this->_array_isearch ($this->_imageInfo['ext'], $this->_imagetype) ) )
				{
					$this->_imageInfo['ntype'] = $this->_defaultimagetype; // defaults to 2 [= JPG]
				}
			}
			
			if ($this->_imageInfo['ntype'] == 4)
				$this->_imageInfo['ntype'] = 2; // JPEG == JPG
			
			// set string type
			$this->_imageInfo['type'] = $this->_imagetype[ $this->_imageInfo['ntype'] ];
		}



		// target image type
		if ( !@empty($type) )
		{
			$this->_imageTarget['type'] = $type;
		} else {
			$this->_imageTarget['type'] = $this->_imageInfo['type'];
		}



		// target size, _imageTarget['w'] &  _imageTarget['h']   [unused $this->_targetSize ($width, $height)]
		// target image size
		if ( empty($width) && empty($height) && !isset($longside) )
		{
			$width  = $this->_conf['image']['thumbWidth'];
			$height = $this->_conf['image']['thumbHeight'];
		}
		else if ( !empty($longside) )
		{
			if ($this->_imageInfo["w"] > $this->_imageInfo["h"])
			{
				$width  = $longside; 
				$height = false;
			}
			else
			{
				$width  = false;
				$height = $longside;
			}
		}
		else
		{
			// set the one missing
			if ( empty($width) )  $width  = "";
			if ( empty($height) ) $height = "";
		}
		$this->_imageTarget['w'] = $width;
		$this->_imageTarget['h'] = $height;



		// target filename, filepath, uri
		$this->_imageTarget['filename'] = $this->_targetFileName ();
		$this->_imageTarget['filepath'] = $this->_targetFilePath ();
		$this->_imageTarget['uri']      = $this->_targetUri ();



		// manage cache and resample if not cached
		$this->_imageTarget['cached']   = $this->_testForCache ();

		if ( empty($this->_imageTarget['cached']) )
		{
			if ( !$this->_resample() )
			{
				return $this->_conf['imgMissingFile'];
			}
		}
		else
		{
			// skip resample, it's cached
			//pr ($this->_imageTarget);
		}



		// return HTML <img> tag
		return $this->_imageTarget['uri'];
	}





	/*
	 * public interface to phpThumb (da finire)
	 */
	public function phpThumb ($image, $params = array())
	{
		return;
	}







	/**************************************************************************
	** private methods follow
	**************************************************************************/




	/*
	 * test source file for existance and correctness
	 */
	private function _testForSource ()
	{
		if ( !file_exists($this->_imageInfo['filepath']) )
		{
			// file does not exist
			$this->_triggerError ($this->_helpername . ": file '" . $this->_imageInfo['filepath'] . "' does not exist");
			return false;
		}
		elseif ( !is_readable ($this->_imageInfo['filepath']) )
		{
			// cannot access source file on filesystem
			$this->_triggerError ($this->_helpername . ": cannot read file '" . $this->_imageInfo['filepath'] . "' on filesystem");
			return false;
		}
		else return true;
	}



	/*
	 * image resize strategy, set $this->_imageTarget['w'] & $this->_imageTarget['h']
	 * input param $width and/or height must be set
	 * $this->_imageTarget['mode'] must be set (integer 0<i<4) if both width & height are
	 * NB: IT'S UNUSED: DELEGATE TO PHPTHUMB
	 */
	private function _targetSize ($width = null, $height = null)
	{
		if ( !empty ($width) && !empty ($height) )
		{
			// upscaling
			if ( ($width < $this->_imageInfo["w"]) && ($height < $this->_imageInfo["h"]) )
			{
				if (!$this->_imageTarget['upscale'])
				{
					$this->_imageTarget['w'] = $this->_imageInfo["w"];
					$this->_imageTarget['h'] = $this->_imageInfo["h"];
					return;
				}
			}


			// special -> same ratio or mode croponly/stretch (2/3)
			if ( ( $width / $height ) == ( $this->_imageInfo["w"] / $this->_imageInfo["h"] ) || $this->_imageTarget['mode'] > 1 )
			{
				$this->_imageTarget['w'] = $width;
				$this->_imageTarget['h'] = $height;
			}
			else 
			{
				// compare target size ratio vs original size ratio
				if ( ( $width / $height ) < ( $this->_imageInfo["w"] / $this->_imageInfo["h"] ) )
					$_temp = 1;
				else
					$_temp = 0;
	
				// invert if fill
				if ( $this->_imageTarget['mode'] )  $_temp = 1 - $_temp;
	
	
				// set target size
				if ( $_temp )
				{
					$this->_imageTarget['h'] = $height;
					$this->_imageTarget['w'] = floor ( $this->_imageTarget['h'] * ( $this->_imageInfo["w"] / $this->_imageInfo["h"] ) );
				}
				else
				{
					$this->_imageTarget['w'] = $width;
					$this->_imageTarget['h'] = floor ( $this->_imageTarget['w'] * ( $this->_imageInfo["h"] / $this->_imageInfo["w"] ) );
				}
			}
		}
		else if ( !empty ($width) )
		{
			$this->_imageTarget['w'] = $width;
			$this->_imageTarget['h'] = round ( $this->_imageTarget['w'] * ( $this->_imageInfo["h"] / $this->_imageInfo["w"] ) );
		}
		else if ( !empty ($height) )
		{
			$this->_imageTarget['h'] = $height;
			$this->_imageTarget['w'] = round ( $this->_imageTarget['h'] * ( $this->_imageInfo["w"] / $this->_imageInfo["h"] ) );
		}
	}



	/*
	 * build target filename
	 */
	private function _targetFileName ()
	{
		// build hash on file path, modification time and mode
		$this->_imageInfo['modified'] = filemtime ($this->_imageInfo['filepath']);
		$this->_imageInfo['hash']     = md5 ( $this->_imageInfo['filename'] . $this->_imageInfo['modified'] . join($this->_imageTarget) );
	
		// destination filename = orig_filename + "_" + w + "x" + h + "_" + hash + "." + ext
		return $this->_imageInfo['filenameBase'] . "_" .
							$this->_imageTarget['w'] . "x" . $this->_imageTarget['h'] . "_" .
							$this->_imageInfo['hash'] . "." . $this->_imageTarget['type'];
	}



	/*
	 * build target filepath
	 */
	private function _targetFilePath ()
	{
		// cached file is in the same folder as original
		if ( $this->_imageTarget['filename']) 
		{
			if (!file_exists($this->_imageInfo['cacheDirectory']))
			{
				if (!mkdir($this->_imageInfo['cacheDirectory']))
				{
					return false;
				}
			}
			elseif (!is_dir($this->_imageInfo['cacheDirectory']))
			{
				return false;
			}
			
			return $this->_imageInfo['cacheDirectory'] . DS . $this->_imageTarget['filename'];
		}
		else
		{
			return false;
		}
	}



	/*
	 * build target uri
	 */
	private function _targetUri ()
	{
		// set target image uri to resampled cached file (also urlencode filename here)
		return $this->_conf['url'] . $this->_change_file_in_url ($this->_imageInfo['path'], rawurlencode($this->_imageTarget['filename']));
	}



	/*
	 * verify existence of cached file, build and set target filename and filepath
	 */
	private function _testForCache ()
	{
		// if file exist (with same hash it's not been modified)
		if ( file_exists ($this->_imageTarget['filepath']) )
		{
			return true;
		}
		else return false;
	}



	/*
	 * effectively call phpThumb class (vendor), resample image and cache file
	 */
	private function _resample ()
	{
		// import and instantiate phpThumb class from vendors
		App::import ('Vendor', 'phpthumb', array ('file' => 'phpThumb' . DS . 'phpthumb.class.php') );
		$thumbnail = new phpthumb;
		$thumbnail->resetObject(); // important if calling the same object for multiple images


		// set up phpThumb class parameters
		$thumbnail->setSourceFilename( $this->_imageInfo['filepath'] );

		$thumbnail->w   = $this->_imageTarget['w'];
		$thumbnail->h   = $this->_imageTarget['h'];
		$thumbnail->q   = $this->_conf['image']['thumbQ'];
		$thumbnail->aoe = $this->_imageTarget['upscale'];
		$thumbnail->config_output_format         = $this->_imageTarget['type'];
		$thumbnail->config_temp_directory        = $this->_conf['tmp'] ;
		$thumbnail->cache_filename               = $this->_imageTarget['filename'];
		$thumbnail->config_cache_directory       = $this->_imageInfo['cacheDirectory'];
		$thumbnail->config_error_die_on_error    = false;
		$thumbnail->config_cache_disable_warning = true;
		$thumbnail->config_prefer_imagemagick    = $this->_conf['image']['preferImagemagick'];
		$thumbnail->config_imagemagick_path      = $this->_conf['image']['imagemagick'];

		//$thumbnail->config_document_root         = $this->_conf['root'];
		$thumbnail->config_allow_src_above_docroot = true; //important since bedita media are usually outside webroot


		// more params about resample mode
		switch ( $this->_imageTarget['mode'] )
		{
			// fill
			case 1:
				$thumbnail->far = $thumbnail->aoe;  // force aspect ratio
				$thumbnail->iar = false; // ignore aspect ratio
				$thumbnail->zc  = false; // zoom crop to center
				$thumbnail->bg  = $this->_imageTarget['fillcolor']; // fill color
				break;

			// stretch
			case 2:
				$thumbnail->far = false;
				$thumbnail->iar = true;
				break;

			// croponly
			case 3:
				list ($thumbnail->sx, $thumbnail->sy)  = $this->_getCropCoordinates ( $this->_imageInfo['w'], $this->_imageInfo['h'], $thumbnail->w, $thumbnail->h, $this->_imageTarget['cropmode'] );
				$thumbnail->sw  = $thumbnail->w;
				$thumbnail->sh  = $thumbnail->h;
				$thumbnail->far = true;
				$thumbnail->iar = false;
				break;

			// crop
			case 0:
			default:
				$thumbnail->far = true;
				$thumbnail->iar = true;
				$thumbnail->zc  = "C"; // zoom crop to center
				break;
		}


		// create image file and write to disk
		if ( $thumbnail->GenerateThumbnail() )
		{
			if ( $thumbnail->RenderToFile ( $this->_imageTarget['filepath'] ) )
				return true;
			else
			{
				$this->_triggerError ($this->_helpername . ": phpThumb error:\n" . $thumbnail->fatalerror . "\n" . implode("\n---", $thumbnail->debugmessages) );
				return false;
			}
		}
		else
		{
			$this->_triggerError ($this->_helpername . ": phpThumb error:\n" . $thumbnail->fatalerror . "\n" . implode("\n---", $thumbnail->debugmessages) );
			return false;
		}
	}
	// end _resample




	/*
	 * calculate cropping coordinates (top, left)
	 */
	private function _getCropCoordinates ( $origW, $origH, $targetW, $targetH, $position )
	{
		$coordinates = array ();

		switch ($position)
		{
			case "TL":
				$coordinates['x']  = 0;
				$coordinates['y']  = 0;
				break;

			case "T":
				$coordinates['x']  = ($origW / 2) - ($targetW / 2);
				$coordinates['y']  = 0;
				break;

			case "TR":
				$coordinates['x']  = $origW - $targetW;
				$coordinates['y']  = 0;
				break;

			case "L":
				$coordinates['x']  = 0;
				$coordinates['y']  = ($origH / 2) - ($targetH / 2);
				break;

			case "R":
				$coordinates['x']  = $origW - $targetW;
				$coordinates['y']  = ($origH / 2) - ($targetH / 2);
				break;

			case "BL":
				$coordinates['x']  = 0;
				$coordinates['y']  = ($origH / 2) - ($targetH / 2);
				break;

			case "B":
				$coordinates['x']  = ($origW / 2) - ($targetW / 2);
				$coordinates['y']  = $origH - $targetH;
				break;

			case "BR":
				$coordinates['x']  = $origW - $targetW;
				$coordinates['y']  = $origH - $targetH;
				break;

			case "C":
			case 1:
			default:
				$coordinates['x']  = ($origW / 2) - ($targetW / 2);
				$coordinates['y']  = ($origH / 2) - ($targetH / 2);
				break;
		}
		
		return array ($coordinates['x'], $coordinates['y']);
	}



	/*
	 * reset internal objects to empty defaults
	 */
	private function _resetObjects()
	{
		$this->_imageInfo = array (
									"filename"		=> "",
									"path"			=> "", // path without file
									"filepath"		=> "", // file + path
									"ext"			=> "",
									"filesize"		=> "",
									"w"				=> "",
									"h"				=> "",
									"orientation"	=> "",
									"type"			=> "",
									"ntype"			=> 0,
									"modified"		=> false,
									"hash"			=> ""
								);
	
		$this->_imageTarget = array (
									"filename"		=> "",
									"filepath"		=> "",
									"uri"			=> "",
									"w"				=> "",
									"h"				=> "",
									"offsetx"		=> 0,
									"offsety"		=> 0,
									"type"			=> "",
									"mode"			=> "",
									"fillcolor"		=> "",
									"cropmode"		=> "",
									"upscale"		=> false,
									"cached"		=> false
								);

		$this->_resample = false;
	}



	// error reporting
	private function _triggerError ($errorMsg) 
	{
		// chiamare il dispatcher degli errori? chiedere a alb/ste
		$this->log($errorMsg);
		return;
	}
	
	
	


	/**
	 ** minor private functions
	 */


	// substitute file part only in a given url
	private function _change_file_in_url ($url, $newfile)
	{
		$_parsed = parse_url ($url);
		$_parsedplus = $this->_parseURLplus ($url);
	
	    if ( !is_array($_parsed) ) return false;
	
	    $uri =  isset ($_parsed['scheme']) ? $_parsed['scheme'] . ':' . ( ( strtolower ($_parsed['scheme']) == 'mailto' ) ? '' : '//' ) : '';
	    $uri .= isset ($_parsed['user']) ?   $_parsed['user'] . ( isset($_parsed['pass']) ? ':'.$_parsed['pass'] : '') . '@'            : '';
	    $uri .= isset ($_parsed['host']) ?   $_parsed['host']       : '';
	    $uri .= isset ($_parsed['port']) ?   ':' . $_parsed['port'] : '';
	
		$uri .= $_parsedplus['dir'] . "/" . $newfile;
		
	    $uri .= isset ($parsed['query']) ? '?'.$parsed['query'] : '';
	    $uri .= isset ($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
	
	    return $uri;
	}
	
	
	
	// improved version of parse_url (returns also 'file' and 'dir')
	private function _parseURLplus ($url)
	{
		$URLpcs  = parse_url ($url);
		$PathPcs = explode ("/", $URLpcs['path']);
		$URLpcs['file'] = end ($PathPcs);
		unset ($PathPcs[key($PathPcs)]);
		$URLpcs['dir'] = implode ("/", $PathPcs); 
		if (file_exists($this->_imageInfo['cacheDirectory']) && is_dir($this->_imageInfo['cacheDirectory']))
		{
			$URLpcs['dir'] .= "/" . substr($this->_imageInfo['filenameBase'],0,5) . "_" . $this->_imageInfo['filenameMD5'];;
		}
		return ($URLpcs);
	}
	
	
	
	// case insensitive array search
	private function _array_isearch ($str, $array)
	{
		foreach ($array as $k => $v)
		{
			if (strcasecmp ($str, $v) == 0) return $k;
		}
		return false;
	}

}

?>
