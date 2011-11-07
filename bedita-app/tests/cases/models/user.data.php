<?php 
/**
 * 
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: user.data.php 2487 2009-11-25 16:56:37Z ste $
 */
 class UserTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			'User' => array(
				'userid' => 'beditauser',
				'realname' => 'My name and surname',
				'email' => 'beditauser@bedita.com',
				'passwd' => 'mysecret',
				'valid' => 1
			)
		)
	);
}

?> 