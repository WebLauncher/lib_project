<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {validator} function plugin
 *
 * Type:     function<br>
 * Name:     validator<br>
 * Purpose:  handle validations in template<br>
 *
 * @author   Mihai Varga
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_validator($params, &$smarty)
{
	global $page;

    if (empty($params['form'])) {
        $smarty->trigger_error("validator: missing 'form' parameter");
        return;
    }

	if (empty($params['field'])) {
        $smarty->trigger_error("validator: missing 'field' parameter");
        return;
    }

	if (empty($params['rule'])) {
        $smarty->trigger_error("validator: missing 'rule' parameter");
        return;
    }

	if (empty($params['message'])) {
        $smarty->trigger_error("validator: missing 'message' parameter");
        return;
    }
    $form_id = $params['form'];
	$field=$params['field'];
	$rule=$params['rule'];
	$location=isset_or($params['location']);
	$translate=empty($params['translate'])||strtolower($params['translate'])=="yes";
	$message=$translate?tr(trim($params['message'])):trim($params['message']);
	$client=empty($params['client'])||strtolower($params['client'])=="yes";
	$server=empty($params['client'])||strtolower($params['server'])=="yes";

	$found_error=isset($page->errors[$field])?"":" style='display:none' ";
	if(isset($page->errors[$field]))
	{
		$message=$page->errors[$field];
	}
	if(!isset($page->errors['_smarty_showed']))$page->errors['_smarty_showed']=array();
	$show_error=($found_error==""||(!empty($params['show']) && $params['show']=='yes'))&&(!in_array($field,$page->errors['_smarty_showed']));
	$result='';
	if($show_error)
	{
		$result="<label class='error' for='$field' $found_error>".$message."</label>";
		$page->errors['_smarty_showed'][]=$field;
	}
	else
	{
		if(!isset($page->temp))$page->temp=array();
		if(!isset($page->temp['_smarty_location_fixed'][$form_id][$field]) && $location){
			$result='<label class="error" for="'.$field.'" style="display:none" generated="true">'.$message.'</label>';
			$page->temp['_smarty_location_fixed'][$form_id][$field]=1;
		}
	}

	$page->add_validator($form_id,$field,$rule,$message,$client,$server);
	$page->save_session();
	$smarty->assign('p',$page->get_page());
	if($page->ajax)
	{
		$page->template->assign('bottom_script','<script type="text/javascript">'.$page->session['script'].'</script>');
	}
    if (empty($params['assign']))
    {
    	echo $result;
    } else {
        $smarty->assign($params['assign'],$result);
    }
}

/* vim: set expandtab: */

?>
