<?php 
/**
 * 
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: document.data.php 2487 2009-11-25 16:56:37Z ste $
 */
 class DocumentTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			'title' => "中国",
			'description' => "Inserimento contenuto UTF-8, funziona o no??",
	        'user_created' => 1,
			'object_type_id' => 22
		),
		'searches' => array("funziona", "inserimento"),
		'searchTree' => array(13, 14)
	);
}

?> 