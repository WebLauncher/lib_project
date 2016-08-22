<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty change https: in http:
 *
 * Type:     modifier<br>
 * Name:     http<br>
 * Purpose:  simple search/replace 
 * @author   Mihai Varga
 * @param string
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_http($string)
{
	$url=str_replace('https:', 'http:', $string);
   	return $url;	
}

/* vim: set expandtab: */

?>
