<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty change http: in https:
 *
 * Type:     modifier<br>
 * Name:     https<br>
 * Purpose:  simple search/replace 
 * @author   Mihai Varga
 * @param string
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_https($string)
{
	$url=str_replace('http:', 'https:', $string);
   	return $url;	
}

/* vim: set expandtab: */

?>
