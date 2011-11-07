<?php 
/**
 * 
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: transaction.data.php 2487 2009-11-25 16:56:37Z ste $
 */

class TransactionTestData extends BeditaTestData {
	var $data =  array(
		'minimo'	=> array('title' 			=> 'Test title'),
		
		'makeFileFromData'	=> array(
				'title' 	=> 'Test title File', 
				'name'		=> 'txtFileTest.txt',
				'mime_type'		=> 'plain/txt',
				'data'		=> "Questo file e' una prova"
		),
		
		'makeFileFromFile'		=> array(
				'title' 		=> 'Test title File', 
				'name'			=> 'test_target.jpg',
				'mime_type'			=> 'image/jpeg',
				'nameSource'	=> 'test.jpg'
		),

		) ;
}

?> 