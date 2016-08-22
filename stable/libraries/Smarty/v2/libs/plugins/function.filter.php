<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {filter} function plugin
 *
 * Type:     function<br>
 * Name:     filter<br>
 * Purpose:  handle validations in template<br>
 *
 * @author   Mihai Varga
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_filter($params, &$smarty)
{
	global $page;
	    
    if (empty($params['form'])) {
        $smarty->trigger_error("filter: missing 'form' parameter");
        return;
    }
	
	if (empty($params['type'])) {
        $smarty->trigger_error("filter: missing 'type' parameter");
        return;
    }

    $form_id = isset_or($params['form']);
	$field= isset_or($params['field']);
	$filter= isset_or($params['type']);
	$pars=isset_or($params['params']);
	$client=empty($params['client'])||strtolower($params['client'])=="yes";
	$server=empty($params['client'])||strtolower($params['server'])=="yes";
	
	$result='';
	if(!isset($page->validate[$form_id]))
	{
		$hash=$page->validate->get_form_hash($form_id);
		$result.='<input type="hidden" name="__hash" value="'.$hash.'"/>';
	}
	$page->add_filter($form_id,$field,$filter,$pars,$client,$server);
	$smarty->assign('p',$page->get_page());
	if($page->ajax)
		$smarty->assign('bottom_script','<script type="text/javascript">'.$page->session['script'].'</script>');
	
	if (empty($params['assign']))
    {
    	echo $result;
    } else {
        $smarty->assign($params['assign'],$result);
    }
}

/* vim: set expandtab: */

?>
