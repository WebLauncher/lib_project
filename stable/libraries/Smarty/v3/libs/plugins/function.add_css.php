<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {add_css} function plugin
 *
 * Type:     function<br>
 * Name:     add_css<br>
 * Purpose:  handle css files from template<br>
 *
 * @author   Mihai Varga
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_add_css($params, &$smarty)
{
	global $page;
    if (empty($params['src'])) {
        $smarty->trigger_error("validator: missing 'form' parameter");
        return;
    }
    $src=$params['src'];
    $type=isset_or($params['type'],'text/css');
    $media=isset_or($params['media'],'screen, projection');
    $browser_cond=isset_or($params['browser_cond']);

	$page->add_css_file($src,$type,$media,$browser_cond);

	echo '';
}

/* vim: set expandtab: */

?>
