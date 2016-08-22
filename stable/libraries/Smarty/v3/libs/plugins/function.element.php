<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {element} function plugin
 *
 * Type:     function<br>
 * Name:     element<br>
 * Purpose:  loading elements and views from components<br>
 *
 * @author   Mihai Varga
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_element($params, &$smarty)
{
	global $page;
	    
    if (empty($params['path'])) {
        $smarty->trigger_error("element: missing 'path' parameter");
        return;
    }
	
	// process path and check if it is ok
	$path=pathinfo($params['path']);
	$dirname=str_replace('/', DS.'components'.DS, $path['dirname']);
	$template_path=$page->paths['root_code'].$dirname.DS.'views'.DS.$page->skin.DS.$path['filename'].'.tpl';
	if(!file_exists($template_path))
		$template_path=$page->paths['root_code'].$dirname.DS.'views'.DS.$page->default_skin.DS.$path['filename'].'.tpl';
	if(file_exists($template_path))
	{
		$s_t_dir=$page->template->template_dir;
		$s_c_dir=$page->template->compile_dir;
		
	    $params['file']=$template_path;
		$page->change_template_dir($page->paths['root_code'].$dirname.DS.'views'.DS.$page->skin.DS);
		$cache_path=$page->paths['root_cache'].$dirname.DS.'views'.DS.$page->skin.DS;
		
		unset($params['path']);
		$old_vars=array_intersect_key($smarty->get_template_vars(),$params);
		$smarty->assign($params);
		$template=$page->fetch_template($path['filename'], $template_path, $cache_path,true);
		$smarty->assign($old_vars);
		$page->change_template_dir($s_t_dir);
		$page->change_cache_dir($s_c_dir);
		if (empty($params['assign']))
	    {
	    	echo $template;
	    } else {
	        $smarty->assign($params['assign'],$template);
	    }
    }	
	else{
		$smarty->trigger_error("element: template file not found at: ".$template_path);
        return;	
	}
}

/* vim: set expandtab: */

?>
