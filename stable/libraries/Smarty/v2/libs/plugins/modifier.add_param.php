<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty edit/add parameter to url
 *
 * Type:     modifier<br>
 * Name:     add_param<br>
 * Purpose:  simple search/replace 
 * @author   Mihai Varga
 * @param string
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_add_param($string, $param, $value)
{
    $url_data = parse_url($string);
     if(!isset($url_data["query"]))
         $url_data["query"]="";

     $params = array();
     parse_str($url_data['query'], $params);
     $params[$param] = $value;   
     $url_data['query'] = http_build_query($params);

	 $url="";
     if(isset($url_data['host']))
     {
         $url .= $url_data['scheme'] . '://';
         if (isset($url_data['user'])) {
             $url .= $url_data['user'];
                 if (isset($url_data['pass'])) {
                     $url .= ':' . $url_data['pass'];
                 }
             $url .= '@';
         }
         $url .= $url_data['host'];
         if (isset($url_data['port'])) {
             $url .= ':' . $url_data['port'];
         }
     }
     $url .= $url_data['path'];
     if (isset($url_data['query'])) {
         $url .= '?' . $url_data['query'];
     }
     if (isset($url_data['fragment'])) {
         $url .= '#' . $url_data['fragment'];
     }
     return $url;	
}

/* vim: set expandtab: */

?>
