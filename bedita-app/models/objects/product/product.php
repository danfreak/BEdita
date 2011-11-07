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
 * Base product
 *
 * @version			$Revision: 3079 $
 * @modifiedby 		$LastChangedBy: dante $
 * @lastmodified	$LastChangedDate: 2010-12-10 15:19:53 +0100 (Fri, 10 Dec 2010) $
 * 
 * $Id: product.php 3079 2010-12-10 14:19:53Z dante $
 */
class Product extends BEAppModel
{
	function beforeValidate() {
        $this->checkDate('production_date');
	}
}
?>
