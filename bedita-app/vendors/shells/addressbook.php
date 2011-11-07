<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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

require_once 'bedita_base.php';

/**
 * Shell script to import/export/manipulate cards.
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class AddressbookShell extends BeditaBaseShell {

	public function import() {
		if (!isset($this->params['f'])) {
			$this->out("Input file is mandatory");
			return;
		}
		
		$options = array();
		$mail_group_id = null;
		if (isset($this->params['m'])) {
			$mailgroup = ClassRegistry::init("MailGroup");
			$mail_group_id = $mailgroup->field("id", array("group_name" => $this->params['m']));
			if(empty($mail_group_id)) {
				$this->out("Mail group " . $this->params['m'] . " not found: import aborted");
				return false;
			}
			$options["joinGroup"][0]["mail_group_id"] = $mail_group_id;
			$options["joinGroup"][0]["status"] = "confirmed";
		}

		$cardFile = $this->params['f'];
		if(!file_exists($cardFile)) {
			$this->out("$cardFile not found, bye");
			return;
		}

		// categories
		if (!isset($this->params['c'])) {
			$this->out("No categories set");
		} else {
			$categories = trim($this->params['c']);
			$catTmp = split(",", $categories);
			$categoryModel = ClassRegistry::init("Category");
			$cardTypeId = Configure::read("objectTypes.card.id");
			$options["Category"] = $categoryModel->findCreateCategories($catTmp, $cardTypeId);
		}

		$ext = strtolower(substr($cardFile, strrpos($cardFile, ".")+1));
		$isCsv = ($ext == "csv");
		$this->out("Importing file $cardFile using " . (($isCsv) ? "CSV" : "VCard") . " format");
		
		$cardModel = ClassRegistry::init("Card");
		if($isCsv) {
			$result = $cardModel->importCSVFile($cardFile, $options);
		} else {
			$result = $cardModel->importVCardFile($cardFile, $options);
		}
		$this->out("Done\nResult: " . print_r($result, true));		
	}

	public function export() {
		if (!isset($this->params['f'])) {
			$this->out("Output file is mandatory");
			return;
		}

		$cardFile = $this->params['f'];
    	$this->checkExportFile($cardFile);
		
		$isCsv = false; // default vcard
		if(isset( $this->params['t'])) {
    		$type = $this->params['t'];
			if(strcasecmp("csv", $type) == 0) {
				$isCsv = true;
			} else if(strcasecmp("vcard", $type) != 0) {
				$this->out("Unknown type $type");
				return;
			}
		} else {
			$ext = strtolower(substr($cardFile, strrpos($cardFile, ".")+1));
			$isCsv = ($ext == "csv");
		}
		$this->out("Exporting to $cardFile using " . (($isCsv) ? "CSV" : "VCard") . " format");
		
		$cardModel = ClassRegistry::init("Card");

		$cardModel->contain();
		$res = $cardModel->find('all',array("fields"=>array('id')));
		$handle = fopen($cardFile, "w");		
		if($isCsv) {
			fwrite($handle, $cardModel->headerCSV() . "\n");
		}
		foreach ($res as $r) {
			$cardModel->id = $r["id"];
			if($isCsv) {
				$res = $cardModel->exportCSV();
			} else {
				$res = $cardModel->exportVCard();
			}
			fwrite($handle, $res . "\n");
		}
		fclose($handle);
		$this->out("$cardFile created.");		
	}
	
	
	function help() {
        $this->out('Available functions:');
  		$this->out(' ');
        $this->out('1. import: import vcf/vcard or microsoft outlook csv file, or generic csv file');
  		$this->out(' ');
        $this->out('    Usage: import -f <csv-cardfile> [-c <categories>] [-m <mail-group-name>]' );
  		$this->out(' ');
  		$this->out("    -f <csv-cardfile>\t vcf/vcard or csv file to import");
  		$this->out("    -c <categories> \t comma separated <categories> to use on import (created if not exist)");
  		$this->out("    -m <mail-group-name> \t name of mail group to associate with imported cards");
  		$this->out(' ');
        $this->out('2. export: export to vcf/vcard or microsoft outlook csv file');
  		$this->out(' ');
        $this->out('    Usage: export -f <csv-cardfile> [-t <type>]');
  		$this->out(' ');
  		$this->out("    -f <csv-cardfile>\t vcf/vcard or csv file to export");
  		$this->out("    -t <type> \t 'vcard' or 'csv'");
  		$this->out(' ');
	}

}
?>
