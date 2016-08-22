<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
/**
 * Smarty duration_format modifier plugin
 *
 * Type: modifier<br>
 * Name: duration_format<br>
 * Purpose: Display formated duration provided in seconds<br>
 * Input:<br>
 * - seconds: duration
 * - format: format how to display the duration
 * @author Rob Ruchte <rob at thirdpartylabs dot com>
 * @param integer
 * @return string
 */

function smarty_modifier_duration_format($seconds=0, $format='%H:%M:%S') {
    return strftime( $format, strtotime( '+' . $seconds . ' seconds', mktime( 0,0,0,0,0,0 ) ) );
}

/* vim: set expandtab: */
?>