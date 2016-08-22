<?php
/**
 * Server Info Class
 */
/**
 * Server Info
 * @package WebLauncher\Infos
 * @example $this->system->server
 */
class ServerInfo
{
	/**
	 * Get information
	 */
	public static function get()
	{
		$info=array();
		if(isset($_SERVER['HTTP_HOST']))
		{
			$info['host']=isset_or($_SERVER['HTTP_HOST']);
			$info['language']=isset_or($_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$info['encoding']=isset_or($_SERVER['HTTP_ACCEPT_ENCODING']);
			$info['charset']=isset_or($_SERVER['HTTP_ACCEPT_CHARSET']);
			$info['port']=isset_or($_SERVER['SERVER_PORT']);
			$info['referer']=isset_or($_SERVER['HTTP_REFERER']);
			$info['os']=isset($_SERVER['WINDIR'])?'windows':'other';
			$info['local_root']=isset_or($_SERVER['SCRIPT_FILENAME'])?substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')).'/':'';
			$info['site_name']=isset_or($_SERVER['SCRIPT_FILENAME'])?substr($_SERVER['SCRIPT_NAME'],1,strrpos($_SERVER['SCRIPT_NAME'],'/')-1):'';
			if(strpos($info['site_name'],'.')>0)$info['site_name']='';
			switch($info['port'])
			{
				case '433':
					$info['protocol']='https';
				break;
				default:
					$info['protocol']='http';
				break;
			}
			$info['client_ip']=isset_or($_SERVER['REMOTE_ADDR']);
			$info['http_root']=$info['protocol'].'://'.$info['host'].'/'.$info['site_name'];
			if($info['site_name'])$info['http_root'].='/';
		}
		return $info;
	}
}

?>