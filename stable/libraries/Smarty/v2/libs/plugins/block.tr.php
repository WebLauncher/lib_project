<?php
	/**
	 * Smarty plugin
	 * @package Smarty
	 * @subpackage plugins
	 */

	/**
	 * Smarty {tr}{/tr} block plugin
	 *
	 * Type:     block function<br>
	 * Name:     translation<br>
	 * Purpose:  translates the content if translation foun in db or inserts the text
	 * as default in database
	 * @author Mihai Varga
	 * @param string contents of the block
	 * @param Smarty clever simulation of a method
	 * @return string string $content translated
	 */
	function smarty_block_tr($params, $content, &$smarty)
	{
		$assign = isset_or($params['assign']);        
		if($content != "")
		{
			global $page;
			$language = isset($params['language']) ? $params['language'] : isset($page -> session['language_id'])?$page -> session['language_id']:0;
			$quer = $content;
			$tags = isset($params['tags']) ? $params['tags'] : "site";
            
            $other_params=$params;
            if(isset($params['assign']))
                unset($other_params['assign']);
            
            if(isset($params['language']))
                unset($other_params['language']);
            
            if(isset($params['tags']))
                unset($other_params['tags']);

			$content=tr($content,$language,$tags,$other_params);
		}
		return $assign ? $smarty -> assign($assign, $content) : $content;
	}

	/* vim: set expandtab: */
?>
