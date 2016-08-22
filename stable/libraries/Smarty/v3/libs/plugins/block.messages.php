<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {messages}{/messages} block plugin
 *
 * Type:     block function<br>
 * Name:     display messages<br>
 * Purpose:  display system messages
 * @author Mihai Varga
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content translated
 */
function smarty_block_messages($params, $content, &$smarty,&$repeat)
{

	$assign=isset_or($params['assign']);
	$class=isset_or($params['class'],'');
	if($class)$class=' class="'.$class.'"';
	$success=isset_or($params['class_success'],'message-success');
	$error=isset_or($params['class_error'],'message-error');
	$other=isset_or($params['class_other'],'message-other');
	$tag=isset_or($params['tag'],'div');

	$template=$content;
	$content='';
	global $page;
	$messages=isset_or($page->messages);
	if(count($messages) && is_array($messages))
	{
		$content='<'.$tag.$class.'>';
		foreach($messages as $v)
		{
			$message=$template;
			$message=str_replace('**message**',tr($v['text']),$message);
			switch($v['type'])
			{
				case 'success':
					$message=str_replace('**class_type**',$success,$message);
				break;
				case 'error':
					$message=str_replace('**class_type**',$error,$message);
				break;
				default:
					$message=str_replace('**class_type**',$other,$message);
			}
			$content.=$message;
		}
		$content.='</'.$tag.'>';
		$page->clear_messages();
	}

	return $assign ? $smarty->assign($assign, $content) : $content;;
}

/* vim: set expandtab: */

?>
