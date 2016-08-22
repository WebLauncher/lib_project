<?php
	/**
	 * Init system site handler and render
	 */

	 /**
	  * 
	  */
	require_once dirname(__FILE__).'/system.init.php';
	global $page;
	$page->render();
	unset($page);
?>
