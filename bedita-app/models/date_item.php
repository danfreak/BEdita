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
 * Base Date object
 *
 * @version			$Revision: 2857 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2010-06-01 17:04:04 +0200 (Tue, 01 Jun 2010) $
 * 
 * $Id: date_item.php 2857 2010-06-01 15:04:04Z bato $
 */
class DateItem extends BEAppModel 
{
	var $recursive = 0 ;

	var $validate = array(
//		'start_date' => array('rule' => 'notEmpty'),
//		'end_date' => array('rule' => 'notEmpty')
	) ;
	
	function beforeValidate() {

        $this->checkDate('start_date');
        $this->checkDate('end_date');
        $data = &$this->data[$this->name] ;
        if(!empty($data['start_date']) && !empty($data['timeStart'])) {
            $data['start_date'] .= " " . $data['timeStart'];
        }
        if (!empty($data['end_date']) && !empty($data['timeEnd'])) {
            $data['end_date'] .= " " . $data['timeEnd'];
        }
        
        return true;
	}
}
?>
