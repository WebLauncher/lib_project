<?php	
	/**
	 * Init system site handler no render
	 */

	ini_set('memory_limit','64M');
	require_once dirname(__FILE__).'/System.php';
	global $page;
	$page=new System();
	$page->init();	 
?>