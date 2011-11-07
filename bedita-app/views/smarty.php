<?php
	/* 
	 * This is a drop-in class to support smarty templating engine
	 * from within CakePHP.
	 * 
	 * For more information on how to use this, please visit this page:
	 *   http://projects.simpleteq.com/CakePHP/smarty.html
	 *
	 * Developed by: Mark John S. Buenconsejo <mjwork@simpleteq.com>
	 * Last Updated: Feb. 2, 2006
	 *
	 * Feel free to use, re-distribute, hack, modify, or whatever you
	 * feel like doing, just remember to give credit to whomever it is 
	 * due. :D
	 * 
	 * Like any software you find on the Internet, i take no 
	 * responsibility to problems that may arise by using this; 
	 * you're on your own.
	 * 
	 * In case of problems, try the CakePHP mailing lists, or Smarty's.
	 * 
	 * 
	 * *Quick installation*
	 * 
	 * 1. Download and install the smarty library, preferably on the 
	 * 'vendors' directory of CakePHP. However you can place it 
	 * anywhere you want; if you do, make sure to change the line 
	 * below to include the smarty library properly.
	 * 
	 * 2. Place this file in the view directory of smarty, either on 
	 * the app/views directory, or on cake/libs/view.
	 * 
	 * 3. That's it!
	 * 
	 * 
	 * *How to use*
	 * 
	 * To invoke the smarty rendering engine, simply set the 
	 * controller's 'view' attribute to 'Smarty', 
	 * 
	 * e.g. $this->view = 'Smarty';
	 * 
	 * You can change it at the end of the controller's action method, 
	 * or change it in the constructor of your controller class.
	 * 
	 * Note, smarty templates have different extensions (.tpl), and 
	 * they will look for the template and layout files with that 
	 * extension (instead of .thtml). This is to distinguish smarty 
	 * templates from the templates for CakePHP's built-in rendering 
	 * engine. The smarty rendering engine will still look for files  
	 * in the proper directories as mandated by the CakePHP design.
	 * 
	 * But if you prefer to use the .thtml extension, you can do so, 
	 * by changing the view class's 'ext' attribute. You can find that 
	 * in the constructor method of the 'SmartyView' class below.
	 *
	 */

	
	class SmartyView extends View
	{
		var $_sv_template_dir;
		var $_sv_layout_dir;
		var $_sv_compile_dir;
		var $_sv_cache_dir;
		var $_sv_config_dir;
		
		var $_smarty = NULL;

		var $sv_processedTpl = NULL;
				
		function __construct (&$controller)
		{
			parent::__construct($controller);

			$this->ext = ".tpl";
			
			$this->_sv_template_dir = array(
				VIEWS . $this->viewPath . DS . $this->subDir,
				VIEWS . $this->viewPath,
				VIEWS
			);
			
			$this->_sv_layout_dir = array(
				LAYOUTS . $this->subDir,
				VIEWS
			) ;
						
			$this->_sv_compile_dir = TMP . 'smarty' . DS . 'compile' ;
			$this->_sv_cache_dir = TMP . 'smarty' . DS . 'cache' . DS;
			$this->_sv_config_dir = ROOT . APP_DIR . DS . 'config' . DS . 'smarty' . DS;
			
            App::import('vendor', "Smarty", true, array(), "smarty/libs/Smarty.class.php");
            $this->_smarty = & new Smarty();

			$this->_smarty->compile_dir = $this->_sv_compile_dir;
			$this->_smarty->cache_dir 	= $this->_sv_cache_dir;
			$this->_smarty->config_dir 	= $this->_sv_config_dir;
			$this->_smarty->compile_id	= $controller->name ;
			
			// Add by BEdita team - Giangi
			$this->_smarty->plugins_dir[] = ROOT . DS . APP_DIR . DS . 'vendors' . DS . '_smartyPlugins' ;
			if(!BACKEND_APP)
				$this->_smarty->plugins_dir[] = BEDITA_CORE_PATH . DS . 'vendors' . DS . '_smartyPlugins';
			

			// Add by BEdita team - xho
			// inherit error_reporting level upon cake debug settings
			// ("if condition" in first 3 lines is useless, it's there just for further customization/improvements)
			if ( empty(Configure::getInstance()->debug) )
				$this->_smarty->error_reporting = false ;
			else
				$this->_smarty->error_reporting = error_reporting () ;

			// Add by BEdita team - xho
			// change default delimiters
//			$this->_smarty->left_delimiter = '<!--//{';
//			$this->_smarty->right_delimiter = '}//-->';


			$svckResFuncs = array(
				__CLASS__ . "::svck_get_template",
				__CLASS__ . "::svck_get_timestamp",
				__CLASS__ . "::svck_get_secure",
				__CLASS__ . "::svck_get_trusted");

			$this->_smarty->register_resource("svck", $svckResFuncs);
			$this->_smarty->register_function("svck_assign_assoc", __CLASS__ . "::svck_func_assign_assoc");

			$this->_smarty->sv_this = &$this;
			
			return;
		}
		
		// Add by BEdita team - Giangi
		// Change template dir 
		function setTemplateDir($path = VIEW) {
			$old = $this->_sv_template_dir ;
			$this->_sv_template_dir  = $path ;
			
			return $old ;
		}
		
		function getTemplateDir() {
			return $this->_sv_template_dir ;
		}

		
		function _render($___viewFn, $___data_for_view, $loadHelpers = true, $cached = false)
		{
			// Add by BEdita team (modify) - used to restore smarty vars
			$prevSmartyVars = $this->_smarty->get_template_vars();

			$this->sv_processedTpl = NULL;
			// clears all assigned variables to the smarty class
			$this->_smarty->clear_all_assign();
			
            $loadedHelpers = array();
            if ($this->helpers != false && $loadHelpers === true) {
                $loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);

	            foreach (array_keys($loadedHelpers) as $helper) {
	                $camelBackedHelper = Inflector::variable($helper);
	                ${$camelBackedHelper} =& $loadedHelpers[$helper];
	                $this->loaded[$camelBackedHelper] =& ${$camelBackedHelper};
	                // this part loads the helpers are registered objects to smarty
	                // good thing the register_object, passes the variable via reference :)
	                $this->_smarty->assign_by_ref($camelBackedHelper, ${$camelBackedHelper});
	            }
	            foreach ($loadedHelpers as $helper) {
                    if (is_object($helper)) {
                        if (is_subclass_of($helper, 'Helper') || is_subclass_of($helper, 'helper')) {
                            $helper->beforeRender();
                        }
                    }
                }
            }

			// let's determine if this is a layout call or a template call
			// and change the template dir accordingly
			$layout = false;
			if(isset($___data_for_view['content_for_layout']))
			{
				$this->_smarty->template_dir = $this->_sv_layout_dir;				
				$layout = true;
			} else
			{
				$this->_smarty->template_dir = $this->_sv_template_dir;
			}
			
			// alright, let's load the data variables, being set by the controller
			// this is pretty cheezy really. :D
			// all by refs, to save on memory space
			foreach(array_keys($___data_for_view) as $k) {
				if (!is_object($k)) {
					$this->_smarty->assign_by_ref($k, $___data_for_view[$k]);
				}
			}
			$this->_smarty->assign_by_ref("view", $this);
					
			// Add by BEdita team (modify) - giangi
			if($this->sv_processedTpl !== NULL)
				$out = $this->_smarty->fetch('svck:' . basename($___viewFn));
			else {
				$out = $this->_smarty->fetch($___viewFn);
			}
	        if (!empty($loadedHelpers)) {
	            foreach ($loadedHelpers as $helper) {
	                if (is_object($helper)) {
	                    if (is_subclass_of($helper, 'Helper') || is_subclass_of($helper, 'helper')) {
	                        $helper->afterRender();
	                    }
	                }
	            }
	        }
	        if (isset($this->loaded['cache']) && (($this->cacheAction != false)) && (Configure::read('Cache.check') === true)) {
	            if (is_a($this->loaded['cache'], 'CacheHelper')) {
	                $cache =& $this->loaded['cache'];
	                $cache->base = $this->base;
	                $cache->here = $this->here;
	                $cache->helpers = $this->helpers;
	                $cache->action = $this->action;
	                $cache->controllerName = $this->name;
	                $cache->layout  = $this->layout;
	                $cache->cacheAction = $this->cacheAction;
	                $cache->cache($___viewFn, $out, $cached);
	            }
	        }


			// Add by BEdita team (modify) - unset local var (like $params in View::element)
			foreach ($___data_for_view as $key => $value) {
				if (!key_exists($key, $prevSmartyVars) && !key_exists($key, $this->loaded)) {
					$this->_smarty->clear_assign($key);
				}
			}
			// Add by BEdita team (modify) - restore smarty vars setted before clear_all_assign called
			if (!empty($prevSmartyVars)) {
				foreach ($prevSmartyVars as $k => $v) {
					if ($this->_smarty->get_template_vars($k) === null) {
						$this->_smarty->assign_by_ref($k, $prevSmartyVars[$k]);
					}
				}
			}

			// Add by BEdita team (modify) - force smarty template dir to _sv_template_dir
			$this->_smarty->template_dir = $this->_sv_template_dir;

			return $out;
		}


		/**
		 * Add by BEdita team
		 *
		 * Override View::set to set Smarty var too
		 *
		 * @param mixed $one see View::set (cake/libs/view/view.php)
		 * @param mixed $two see View::set
		 * @return unknow
		 */
		public function set($one, $two = null) {
			$data = null;
			if (is_array($one)) {
				if (is_array($two)) {
					$data = array_combine($one, $two);
				} else {
					$data = $one;
				}
			} else {
				$data = array($one => $two);
			}

			if ($data == null) {
				return false;
			}

			foreach ($data as $name => $value) {
				if ($name == 'title') {
					$this->pageTitle = $value;
				} else {
					$this->viewVars[$name] = $value;
					$this->_smarty->assign_by_ref($name, $this->viewVars[$name]);
				}
			}
		}

		function & getSmarty()
		{
			return ($this->smarty);
		}

		function svck_func_assign_assoc($params, &$smarty)
		{
			//extracts variables passed in
			extract($params);
			$assoc_array = array();
			
			if(!isset($value) || !isset($var))
				return;
			
			if(!isset($glue))
				$glue = ',';
			
			$key_val_pairs = explode($glue, $value);
			foreach($key_val_pairs as $pair)
			{
				list($key,$val) = explode('=>', $pair);
				$assoc_array[trim($key)] = trim($val);
			}
			
			$smarty->assign($var, $assoc_array);
		}
		
		function svck_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
		{
			$tpl_source = $smarty_obj->sv_this->sv_processedTpl;
			return true;
		}
	
		function svck_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
		{
			$tpl_timestamp = time();
			return true;
		}
	
		function svck_get_secure($tpl_name, &$smarty_obj)
		{
			return true;
		}
		
		function svck_get_trusted($tpl_name, &$smarty_obj)
		{
			return;
		}
	}
?>