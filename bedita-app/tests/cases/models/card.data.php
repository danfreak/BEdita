<?php 
/**
 * 
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: card.data.php 2487 2009-11-25 16:56:37Z ste $
 */
 class CardTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			'name' => 'John',
			'surname' => 'Smith',
			'email' => 'john.smith@bedita.com',
			'ObjectUser' => array(
				'card' => array(
					0 => array(
						"user_id" => 1,
						"switch" => "card"
					)
				)
			)
		),
		'insertError' => array(
			'email' => 'john.smith@bedita'	
		)
	);
}

?> 