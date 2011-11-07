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
 * Bedita core group test
 * 
 *
 * @version			$Revision: 3037 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2010-11-09 12:43:22 +0100 (Tue, 09 Nov 2010) $
 * 
 * $Id: bedita_core.group.php 3037 2010-11-09 11:43:22Z ste $
 */
class BeditaCoreGroupTest extends GroupTest {

	var $label = 'Bedita core tests';

	protected function  addTest($t) {
		TestManager::addTestFile($this, APP_TEST_CASES . DS . $t);
	}
	
	function BeditaCoreGroupTest() {
		$this->addTest('controllers' . DS . 'authentication_controller');
		$this->addTest('components' . DS . 'be_auth');
		$this->addTest('components' . DS . 'be_system');
		$this->addTest('models' . DS . 'area');
		$this->addTest('models' . DS . 'card');
		$this->addTest('models' . DS . 'category');
		$this->addTest('models' . DS . 'comment');
		$this->addTest('models' . DS . 'document');
		$this->addTest('models' . DS . 'event');
		$this->addTest('models' . DS . 'gallery');
		$this->addTest('models' . DS . 'generic_object');
		$this->addTest('models' . DS . 'link');
		$this->addTest('models' . DS . 'permission');
		$this->addTest('models' . DS . 'permission_module');
		$this->addTest('models' . DS . 'section');
		$this->addTest('models' . DS . 'tree');
		$this->addTest('models' . DS . 'user');
		$this->addTest('models' . DS . 'version');
	}
}
?>