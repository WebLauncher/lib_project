<?php
global $page;
$page->import('file', $page -> paths['root_code'] . $_REQUEST['module']. '/config.php');
$page->module=$page->session_cookie_module?$page->session_cookie_module:$_REQUEST['module'];
$page->session_cookie=str_replace('_'.$page->module,'',$_REQUEST['ck']);
	
$page->init_session();

/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/
$arr=array();
foreach($page->session['__js_files'] as $k=>$v)
{
	$arr['js_site'.$k]=$v;
}
return $arr;
?>