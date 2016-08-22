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
 * Name:     bind<br>
 * Purpose:  displays the bindind value requested
 * @author Mihai Varga
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content translated
 */
function smarty_block_bind($params, $content, &$smarty,&$repeat)
{
	$return="";
	$assign=isset_or($params['assign']);
	if($content!=null)
	{
		$content=trim($content);

		global $page;
		if($page->db_conn)
		{
			$table=isset_or($params['table']);
			$get_field=isset_or($params['get_field']);
			$field=(isset_or($params['field'])?$params['field']:"id");
			$value=$content;
			if($table && $get_field && $field && $value)
			{
				$query="select `$get_field` from `$table` where `$field`='$value'";
				$row=$page->db_conn->getRow($query);

				if(isset($row[$get_field])) $return=$row[$get_field];
				else $return=isset_or($params['default']);	
			}
		}
	}

	return $assign ? $smarty->assign($assign, $return) : $return;
}

/* vim: set expandtab: */

?>
