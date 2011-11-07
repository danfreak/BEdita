<?php
/**
 * Smarty plugin
 * @file function.htmlHelper.php
 */

/*
 * @param array
 * @param Smarty
 * @return string of valid XHTML
 */

function smarty_function_htmlHelper($params, &$smarty)
{
	extract($params);
	
    if (@empty($fnc)) {
        $smarty->trigger_error("function_htmlHelper: missing 'fnc' argument");
        return ;
    }
    if (@empty($args)) $args = "" ;
	
	$vs = &$smarty->get_template_vars() ;
	$html = &$vs["html"] ;
	
	eval("echo \$html->$fnc($args);") ;
}
?>