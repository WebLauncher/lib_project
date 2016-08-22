<?php
	set_time_limit(0);
	ini_set('memory_limit', '150M');
	ini_set('upload_max_filesize', '30M');
	ini_set('default_socket_timeout', '6000');
	ini_set('max_input_time', '6000');
	ini_set('post_max_size', '100M');
	ini_set('max_execution_time', '6000');	
	    
	global $page;
	$page->load_library('Zend');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_YouTube');
    Zend_Loader::loadClass('Zend_Gdata_App_Exception');
	Zend_Loader::loadClass('Zend_Gdata_App_HttpException');
	
	require_once dirname(__FILE__).'/youtube.php';
	require_once dirname(__FILE__).'/config.php';
?>