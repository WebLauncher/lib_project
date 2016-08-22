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
 * Purpose:  translates the content if translation foun in db or inserts the text as default in database
 * @author Mihai Varga
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content translated
 */
function smarty_block_tr($params, $content, &$smarty, &$repeat)
{
	$assign=isset_or($params['assign']);
	if($content!="")
	{
	    global $page;
		$language=isset($params['language'])?$params['language']:$page->session['language_id'];
		$quer=$content;
		$tags=isset($params['tags'])?$params['tags']:"site";
		
		$content=tr($content,$language,$tags);
	}
	return $assign ? $smarty->assign($assign, $content) : $content;;
}

/* vim: set expandtab: */

?>
