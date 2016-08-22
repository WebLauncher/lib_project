<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {captcha} function plugin
 *
 * Type:     function<br>
 * Name:     captcha<br>
 * Purpose:  dsplays captcha
 *
 * @author   Mihai Varga
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_captcha($params, &$smarty)
{
	global $page;

	unset($page->session['signature']);
	$result='<img src="'.$page->paths['current'].'?a=signature&rand='.base64_encode(microtime()).'"/>';

    if (empty($params['assign']))
    {
    	echo $result;
    } else {
        $smarty->assign($params['assign'],$result);
    }
}

/* vim: set expandtab: */

?>
