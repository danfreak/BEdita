<?php 
/**
 * 
 *
 * @version			$Revision: 3131 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2011-02-09 17:25:01 +0100 (Wed, 09 Feb 2011) $
 * 
 * $Id: permission.data.php 3131 2011-02-09 16:25:01Z bato $
 */
 
class PermissionTestData extends BeditaTestData {

	var $data =  array(
		'min'	=> array('title' => 'Test title'),
		'addPerms1' => array(
				// write permission
				array('switch' => 'user', 'flag' => 1, 'name' => 'bedita'),
				// frontend_access_with_block
				array('switch' => 'group', 'flag' => 2, 'name' => 'reader'),
		),
		'removePerms1' => array(
				// frontend_access_with_block
				array('switch' => 'group', 'flag' => 2, 'name' => 'reader'),
		),
		'resultDeletePerms1' => array(
				// write permission
				array('switch' => 'user', 'flag' => 1, 'name' => 'bedita'),
		)
	);

}

?> 