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

require_once 'bedita_base.php';

/**
 * Newsletter shell: methods to import/export newsletter data (for example phplist filters), 
 * other newsletter related utilities
 * 
 * @version			$Revision: 2889 $
 * @modifiedby 		$LastChangedBy: niki $
 * @lastmodified	$LastChangedDate: 2010-06-24 18:14:32 +0200 (Thu, 24 Jun 2010) $
 * 
 * $Id: module.php 2889 2010-06-24 16:14:32Z niki $
 */
class ModuleShell extends BeditaBaseShell {

	public function plug() {
		$op = (empty($this->params["name"]))? "list" : "name";
		$moduleModel = ClassRegistry::init("Module");
		$pluggedModules = $moduleModel->find("list", array(
				"fields" => array("id", "name"),
				"conditions" => array("module_type" => "plugin")
			)
		);
		
		$pluginPaths = Configure::getInstance()->pluginPaths;
		
		if ($op == "list") {
		
			$unpluggedModules = array();
			foreach ($pluginPaths as $pluginsBasePath) {
				$folder = new Folder($pluginsBasePath);
				$plugins = $folder->ls(true, true);
				foreach ($plugins[0] as $plugin) {
					if (file_exists($pluginsBasePath . $plugin . DS . "config" . DS . "bedita_module_setup.php") && !in_array($plugin, $pluggedModules)) {
						$unpluggedModules[] = $plugin;
					}
				}
			}
	
			if (empty($unpluggedModules)) {
				$this->out("No module to plug");
				return;
			}
			
			$this->out("Current unplugged modules on istance " . Configure::read("projectName") . ":");
			$this->out("");
			foreach ($unpluggedModules as $key => $um) {
				$this->out(++$key . ". " . $um);
			}
			$this->out("");
			$moduleToPlug = $this->in("Choose the module to plug. Digit the name or the corresponding number:");
			
			if (is_numeric($moduleToPlug) && !empty($unpluggedModules[$moduleToPlug-1])) {
				$moduleToPlug = $unpluggedModules[$moduleToPlug-1];
			}
			if (empty($moduleToPlug) || !in_array($moduleToPlug, $unpluggedModules)) {
				$this->out("Plugin doesn't exist");
				return;
			}
			
			$this->params["name"] = $moduleToPlug;
			$this->plug();
			
		} elseif ($op == "name") {
			$plugin = $this->params["name"];
			$pluginsBasePath = false;
			foreach ($pluginPaths as $pPath) {
				if (file_exists($pPath . $plugin . DS . "config" . DS . "bedita_module_setup.php") && !in_array($plugin, $pluggedModules)) {
					$pluginsBasePath = $pPath;	
				}
			}
			if (!$pluginsBasePath) {
				$this->out("Plugin doesn't exist");
				return;
			}
			
			if (in_array($plugin, $pluggedModules)) {
				$this->out("Module " . $plugin . " is already installed.");
				return;
			}
			
			include $pluginsBasePath . $plugin . DS . "config" . DS . "bedita_module_setup.php";
			$beditaVersion = Configure::read("majorVersion");
			if ($beditaVersion != $moduleSetup["BEditaVersion"]) {
				$this->out("");
				$this->out("WARNING: installed version and version required mismatched!");
				$this->out("BEdita version: " . $beditaVersion);
				$this->out("BEdita version required by " . $plugin . ": " . $moduleSetup["BEditaVersion"]);
				$command = $this->in("Do you want continue anyway?", array("yes", "no"), "no");
				if ($command != "yes") {
					$this->out("Bye");
					return;
				}
			}
			$this->out("");
			$this->out("You are about to plug in the module " . $plugin . " version " . $moduleSetup["version"]);
			$this->out("Module description: " . $moduleSetup["description"]);
			$this->out("");
			$command = $this->in("Do you wanto to proceed?", array("yes", "no"), "yes");
			if ($command != "yes") {
				$this->out("Bye");
				return;
			}
			
			if (!$moduleModel->plugModule($plugin, $moduleSetup)) {
				$this->out("Failed installing module");
				return;
			}
			
			$this->out("Plugin " . $plugin . " installed successfully");
		}
	}
	
	public function unplug() {
		
	}
	
	
	private function findPluginPath($pluginName) {
		$res = null;
		$pluginPaths = Configure::getInstance()->pluginPaths;
		foreach ($pluginPaths as $p) {
			if(file_exists($p . DS . $pluginName . DS . "config")) {
				$res = $p;
			}
		}
		return $res;
	}	
	
	
	function schema() {
		
		$pluginName = $this->params["name"];
		if(empty($pluginName)) {
			$this->out("Plugin name is mandatory");
			return;
		}
		
		$pluginPath = $this->findPluginPath($pluginName);
		if($pluginPath == null) {
			$this->out("Plugin $pluginName not found");
			return;
		}

		$configPath = $pluginPath . DS  . $pluginName . DS . "config" . DS;
		$setupFile =  $configPath . "bedita_module_setup.php";
		if(!file_exists($setupFile)) {
			$this->out("Plugin setup file for $pluginName not found");
			return;
		}
		include($setupFile);
		if(empty($moduleSetup["tables"])) {
			$this->out("No tables defined for plugin $pluginName");
			return;
		}

		$db =& ConnectionManager::getDataSource("default");
		$options = array();
		$tables = $moduleSetup["tables"];
		$beSchema = ClassRegistry::init("BeSchema");
		$conf = Configure::getInstance();
		
		if (!in_array($pluginPath . DS  . $pluginName . DS . "model" . DS, $conf->modelPaths)){
			$conf->modelPaths[] = $pluginPath . DS  . $pluginName . DS . "models" . DS;
		}
		
		foreach ($tables as $t) {
			$modelName = Inflector::camelize($t);
			$model = ClassRegistry::init($modelName);
			$options["tables"][$t] = $beSchema->tableMetadata($model, $db);
			ClassRegistry::removeObject($modelName);
		}
		
		$schemaFile = $configPath . "sql". DS . "schema.php";
		$skip = false;		
		if(file_exists($schemaFile)) {
			$command = $this->in("Schema file $schemaFile exists. Overwrite?", array("y", "n"), "y");
			if ($command == "n") {
				$skip = true;
				$this->out("Skipping schema file generation");
			}
		}
		
		if(!$skip) {		
			$this->out("Creating schema file: $schemaFile");
			$name = Inflector::camelize($pluginName);
			$options['name'] = $name;
			$options['path'] = $configPath . "sql";
			$beSchema->path = $options['path'];
			$beSchema->write($options);
		}
		require_once($schemaFile);
		$schemaName = $name . "Schema";
		$schema = new $schemaName();

		// sql schema
		$sqlSchema = $configPath . "sql". DS . $db->config["driver"] . "_schema.sql";
		$skip = false;		
		if(file_exists($sqlSchema)) {
			$command = $this->in("Schema file $sqlSchema exists. Overwrite?", array("y", "n"), "y");
			if ($command == "n") {
				$skip = true;
				$this->out("Skipping schema file generation");
			}
		}
		
		if(!$skip) {		
			$this->out("Creating schema file: $sqlSchema");		
			$contents = "#" . $schema->name . " sql generated on: " . date('Y-m-d H:i:s') . " : " . time() . "\n\n";
			$contents .= $db->dropSchema($schema) . "\n\n". $db->createSchema($schema);
			$file = new File($sqlSchema, true);
			$file->write($contents);
		}
		$this->out("Done");
	}
	
	function help() {
        $this->out('Available functions:');
  		$this->out(' ');
        $this->out('0. plug: initialize a new BEdita module plugin');
  		$this->out('    Usage: plug [-list] [-name <module-plugin-name>]');
  		$this->out(' ');
  		$this->out("    -list \t list all pluggable module available (default)");
  		$this->out("    -name <module-plugin-name>   \t name of plugin you want to install");
  		$this->out(' ');
        $this->out('1. unplug: todo');
  		$this->out(' ');
        $this->out('2. schema: generate schema files for a plugin');
  		$this->out('    Usage: schema [-list] [-name <module-plugin-name>]');
  		$this->out(' ');
  		$this->out("    -name <module-plugin-name>   \t plugin name");
        $this->out(' ');
	}
	
}

?>