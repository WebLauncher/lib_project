<?php
	class SalesForceAPI{
		private static $_username='';
		private static $_password='';
		
		private static $_connection='';
		private static $_client='';
		private static $_login='';
		
		private static $_logged=false;
		
		public static function Authenticate($username,$password)
		{
			if(!self::$_logged || ($username!=self::$_username && $password!=self::$_password))
			{
					self::$_username=$username;
					self::$_password=$password;
					
					require_once (dirname(__FILE__).'/SforceEnterpriseClient.php');
					require_once (dirname(__FILE__).'/SforceHeaderOptions.php');
					
					self::$_connection = new SforceEnterpriseClient();
					self::$_client = self::$_connection->createConnection(dirname(__FILE__).'/enterprise.wsdl.xml');
				try{
					self::$_login= self::$_connection->login(self::$_username, self::$_password);
					self::$_logged=true;
				}
				catch(Exception $ex)
				{
					echopre($ex);	
				}
			}
		}
		
		public static function Connection()
		{			
			return self::$_connection;
		}
		
		public static function Logout()
		{
			self::$_connection->logout();
			self::$_logged=false;
		}
	}
?>