<?php
/**
 * 
 *
 * @version			$Revision: 2510 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2009-12-17 13:36:03 +0100 (Thu, 17 Dec 2009) $
 * 
 * $Id: comment.data.php 2510 2009-12-17 12:36:03Z bato $
 */
class CommentTestData extends BeditaTestData {
	var $data =  array(
			
		'document' => array(
			'title' => "document to comment",
			'description' => "bla bla bla bla",
		),
	
		'comment' => array(
			'description' => "what a fraking document.....",
			'author' => "Admiral Adama",
			'email' => "wadama@bsg.com",
			'url' => "www.fightthecylons.com",
		),

		'editor_note' => array(
			'title' => "the final five",
			'description' => "you are one of them!",
		),
		
	);
}
?>