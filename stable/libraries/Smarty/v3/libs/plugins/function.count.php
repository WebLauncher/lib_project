<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {math} function plugin
 *
 * Type:     function<br>
 * Name:     math<br>
 * Purpose:  handle math computations in template<br>
 * @link http://smarty.php.net/manual/en/language.function.math.php {math}
 *          (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_count($params, &$smarty)
{
    // be sure equation parameter is present
    if (empty($params['array'])) {
        $smarty->trigger_error("count: missing array parameter");
        return;
    }

	$smarty_result=is_array($params['array'])?count($params['array']):0;

    if (empty($params['assign'])) {
        return $smarty_result;
    } else {
        $smarty->assign($params['assign'],$smarty_result);
    }
}

/* vim: set expandtab: */

?>
