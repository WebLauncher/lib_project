<?php
	global $page;
	$page->import('library','Zend');
	Zend_Loader::loadClass('Zend_Http_Client');
	$page->import('library','curl');
	require_once dirname(__FILE__).'/class.gotowebinar.php';
?>