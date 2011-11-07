<?php
/**
 * block.bedev.php - dev block plugin
 *
 *
 * @version		$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 * 
 * $Id: block.bedev.php 2487 2009-11-25 16:56:37Z ste $
 */
 
/**
 * Smarty block: shows or hides content using a global var for devel purposes.
 * Tipically shows or hides unimplemented features...
 * {bedev}.... {/bedev}
 */
function smarty_block_bedev($params, $text, &$smarty)
{
	if (empty($text)) {
        return;
    }
	
	if(!defined('BEDITA_DEV_SYSTEM'))
		return;

	return "<div class='bedev'>".$text."</div>";
}

?>
