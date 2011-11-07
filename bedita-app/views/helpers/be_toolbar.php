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
 * @version			$Revision: 3211 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2011-03-23 16:47:51 +0100 (Wed, 23 Mar 2011) $
 * 
 * $Id: be_toolbar.php 3211 2011-03-23 15:47:51Z bato $
 */
class BeToolbarHelper extends AppHelper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Form', 'Html');

	var $tags = array(
		'with_text' => '<span %s >%s</span>',
		'without_text' => '<span %s />'
	) ;

	/**
	 * initialize toolbar in params['toolbar']
	 * 
	 * @param array $toolbar
	 * @return unknown_type
	 */
	function init(&$toolbar) {
		$this->params['toolbar'] = $toolbar;
	}
	
	/**
	 * Return the link (html anchor tag) for the next page
	 *
	 * @param string $title			Label link
	 * @param array $option			HTML attributes for link
	 * @param string $disabledTitle		Label link disabled
	 * @param array  $disabledOption	HTML attributes for link disabled
	 * 									(if present, insert a tag SPAN)
	 */
	function next($title = ' > ', $options = array(), $disabledTitle = ' > ', $disabledOption = array()) {
		return $this->_scroll('next', $title, $options, $disabledTitle, $disabledOption) ;
	}

	/**
	 * Return the link (html anchor tag) for the previous page
	 *
	 * @param string $title			Label link
	 * @param array $option			HTML attributes for link
	 * @param string $disabledTitle		Label link disabled
	 * @param array  $disabledOption	HTML attributes for link disabled
	 * 									(if present, insert a tag SPAN)
	 */
	function prev($title = ' < ', $options = array(), $disabledTitle = ' < ', $disabledOption = array()) {
		return $this->_scroll('prev', $title, $options, $disabledTitle, $disabledOption) ;
	}

	/**
	 * Return the link (html anchor tag) for the first page
	 *
	 * @param string $title			Label link
	 * @param array $option			HTML attributes for link
	 * @param string $disabledTitle		Label link disabled
	 * @param array  $disabledOption	HTML attributes for link disabled
	 * 									(if present, insert a tag SPAN)
	 */
	function first($title = ' |< ', $options = array(), $disabledTitle = ' |< ', $disabledOption = array()) {
		return $this->_scroll('first', $title, $options, $disabledTitle, $disabledOption) ;
	}

	/**
	 * Return the link (html anchor tag) for the last page
	 *
	 * @param string $title			Label link
	 * @param array $option			HTML attributes for link
	 * @param string $disabledTitle		Label link disabled
	 * @param array  $disabledOption	HTML attributes for link disabled
	 * 									(if present, insert a tag SPAN)
	 */
	function last($title = ' >| ', $options = array(), $disabledTitle = ' >| ', $disabledOption = array()) {
		return $this->_scroll('last', $title, $options, $disabledTitle, $disabledOption) ;
	}

	/**
	 * Return number of records found
	 *
	 */
	function size() {
		return (isset($this->params['toolbar']['size'])?$this->params['toolbar']['size']:"" ) ;
	}

	/**
	 * Return current page
	 *
	 */
	function current() {
		return (isset($this->params['toolbar']['page'])?$this->params['toolbar']['page']:"" ) ;
	}

	/**
	 * Return total number of pages
	 *
	 */
	function pages() {
		return (isset($this->params['toolbar']['pages'])?$this->params['toolbar']['pages']:"" ) ;
	}

	/**
	 * View page size html select tag
	 *
	 * @param array $htmlAttributes		associative Array with HTML attributes
	 * @param arry $options				Array. Default: 1, 5, 10,20, 50, 100
	 */
	function changeDim($htmlAttributes = array(), $options = array(1, 5, 10, 20, 50, 100)) {
		if(!isset($this->params['toolbar']['dim'])) return "" ;

		// Define script for page change
		$data = $this->getPassedArgs();
		unset($data["page"]);
		unset($data["dim"]);
		$url = Router::url($data) ;
		$htmlAttributes['onchange'] = "document.location = '{$url}'+'/dim:'+ this[this.selectedIndex].value +'/page:1'" ;

		$tmp = array() ;
		foreach ($options as $k) $tmp[$k] = $k ;
		$options = $tmp ;

		return $this->Form->select("", $options, $this->params['toolbar']['dim'], $htmlAttributes, false) ;
	}

	function changeDimSelect($selectId, $htmlAttributes = array(), $options = array(1, 5, 10, 20, 50, 100)) {
		if(!isset($this->params['toolbar']['dim'])) return "" ;

		// Define script for page change
		$data = $this->getPassedArgs();
		unset($data["page"]);
		unset($data["dim"]);
		$url = Router::url($data) ;
		if($this->params["action"] == "index" && !preg_match("/\/(index$|index\/)/i", $url )) {
			$url .= "/".$this->params["action"];
		}
		$htmlAttributes['onchange'] = "document.location = '{$url}'+'/dim:'+ this[this.selectedIndex].value" ;

		$tmp = array() ;
		foreach ($options as $k) $tmp[$k] = $k ;
		$options = $tmp ;

		return $this->Form->select($selectId, $options, $this->params['toolbar']['dim'], $htmlAttributes, false) ;
	}

	/**
	 * Change selected page
	 *
	 * @param array $htmlAttributes		associative Array with HTML attributes
	 * @param arry $items				number of available pages, before and after current. Default: 5
	 */
	function changePage($htmlAttributes = array(),	$items = 5) {
		if(!isset($this->params['toolbar']['page'])) return "" ;

		// Define script for page change
		$data = $this->getPassedArgs();
		unset($data["page"]);
		$url = Router::url($data) ;
		
		$htmlAttributes['onchange'] = "document.location = '{$url}'+'/page:'+ this[this.selectedIndex].value" ;

		// Define the number of pages available
		$pages = array() ;
		for($i = $this->params['toolbar']['page']; $i >= 1 ; $i--) {
			$pages[] =  $i ;
		}

		for($i = $this->params['toolbar']['page']; $i <= $this->params['toolbar']['pages'] ; $i++) {
			$pages[] =  $i ;
		}
		sort($pages) ;

		// View select
		$tmp = array() ;
		foreach ($pages as $k) $tmp[$k] = $k ;
		$pages = $tmp ;

		return $this->Form->select("", $pages, $this->params['toolbar']['page'], $htmlAttributes, false) ;
	}

	function changePageSelect($selectId, $htmlAttributes = array(),	$items = 5) {
		if(!isset($this->params['toolbar']['page'])) return "" ;

		// Define script for page change
		$data = $this->getPassedArgs();
		unset($data["page"]);
		$url = Router::url($data) ;
		if($this->params["action"] == "index" && !preg_match("/\/(index$|index\/)/i", $url)) {
			$url .= "/".$this->params["action"];
		}
		$htmlAttributes['onchange'] = "document.location = '{$url}'+'/page:'+ this[this.selectedIndex].value" ;

		// Define the number of pages available
		$pages = array() ;
		for($i = $this->params['toolbar']['page']; $i >= 1 ; $i--) {
			$pages[] =  $i ;
		}

		for($i = $this->params['toolbar']['page']; $i <= $this->params['toolbar']['pages'] ; $i++) {
			$pages[] =  $i ;
		}
		sort($pages) ;

		// View select
		$tmp = array() ;
		foreach ($pages as $k) $tmp[$k] = $k ;
		$pages = $tmp ;

		return $this->Form->select($selectId, $pages, $this->params['toolbar']['page'], $htmlAttributes, false) ;
	}

	/**
	 * Change list order
	 *
	 * @param string $field				Field for the "order by"
	 * @param string $title				Title for the link. Default: field name
	 * @param array $htmlAttributes			associative Array with HTML attributes
	 * @param boolean $dir				Se presente impone la direzione. 1: ascending, 0: descending
	 * 									otherwise, !(<current value>)
	 */
	function order($field, $title="", $image="", $htmlAttributes = array(), $dir=null) {
		
		if(!isset($this->params['toolbar'])) return "" ;
		
		$data = $this->getPassedArgs();
		
		if(isset($data['order']) && $data['order'] == $field) {
			if(!isset($dir)) $dir = (isset($data['dir']))  ? (!$data['dir']) : true  ;
			$class = "SortUp";
		}  else {
			if(!isset($dir)) $dir = true ;
			$class = "";
		}

		// Crea l'url
		$data['order'] 	= $field ;
		$data['dir'] 	= (integer)$dir ;

		$url = Router::url($data) ;
		
		if (!empty($image)) {
			$htmlAttributes["alt"] = __($htmlAttributes["alt"], true);
			$title = $this->Html->image($image, $htmlAttributes);
		} else {
			$title = __($title, true);
		}
		
		return '<a class="'.$class.'" href="' . $url . '">' . $title . '</a>';
		
	}

	/**
	 * Return the link (html anchor tag) for the page $where
	 *
	 * @param string $where			page target (next, prev, first, last)
	 * @param string $title			Label link
	 * @param array $option			HTML attributes for link
	 * @param string $disabledTitle		Label link disabled
	 * @param array  $disabledOption	HTML attributes for link disabled
	 * 									(if present, insert a tag SPAN)
	 */
	private function _scroll($where, $title, $options, $disabledTitle, $disabledOption) {
		$page = (isset($this->params['toolbar'][$where]))?$this->params['toolbar'][$where]:false ;

		// Next page not found or toolbar not found, link disabled
		if(!$page) {
			return $this->_output($disabledTitle, $disabledOption) ;
		}

		// Create url
		$data = $this->getPassedArgs();
		$data['page'] = $page ;

		$url = Router::url($data) ;
		return '<a title="go to '.$where.' page" href="' . $url . '">' . __($title, true) . '</a>';
	}

	private function _output($text, $options) {
		return $this->output(
			sprintf(
					(($text)?$this->tags['with_text']:$this->tags['without_text']),
					$this->_parseAttributes($options, null, ' ', ''), __($text,true)
			)
		);
	}
	
	public function getPassedArgs($otherParams=array()) {
		if (empty($otherParams))
			return array_merge($this->params["pass"], $this->params["named"]);
		else
			return array_merge($this->params["pass"], $this->params["named"], $otherParams);
	}
}


?>
