<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {add_js} function plugin
 *
 * Type:     function<br>
 * Name:     add_js<br>
 * Purpose:  handle js files from template<br>
 *
 * @author   Mihai Varga
 * @param array
 * @param Smarty
 * @return string
 */

function smarty_function_add_js($params, &$smarty)
{
	global $page;
    if (empty($params['src'])) {
        $smarty->trigger_error("validator: missing 'form' parameter");
        return;
    }
    $src=$params['src'];
    $local=isset_or($params['local']) && $params['local']=='false'?false:true;
    $type=isset_or($params['type'],'text/javascript');

	$page->add_js_file($src,$local,$type);

	echo '';
}

/* vim: set expandtab: */

?>
