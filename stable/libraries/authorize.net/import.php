<?php
	require_once dirname(__FILE__).'/AuthorizeNet.php';
	global $page;
	if(is_file($page->paths['root_dir'].'../config_authorize_net.php'))
		require_once ($page->paths['root_dir'].'../config_authorize_net.php');
	
?>