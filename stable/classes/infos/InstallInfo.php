<?php
/**
 * InstallInfo Class
 */

/**
 * Install info
 * @package WebLauncher\Infos
 */
class InstallInfo
{
	/**
	 * Display Info
	 */
	public static function display()
	{
		global $page;
		echo 'Checking PHP and extensions<br/>';
		echo self::check('PHP version >= 5.3.3: ',phpversion()>'5.3.3');
		
		echo self::check('PDO installed: ', extension_loaded('pdo'));
		
		echo self::check('GD2 installed: ',extension_loaded('gd') && function_exists('gd_info'));
		
		echo self::check('MCrypt installed: ', (function_exists('mcrypt_decrypt')));
		
		echo self::check('SOAP installed: ', extension_loaded('soap'));
		
		echo self::check('APC or eAccelerator installed: ', extension_loaded('apc')||extension_loaded('eaccelerator'));
		
		echo self::check('CURL installed: ', extension_loaded('curl'));
		
		echo self::check('Posix installed: ', extension_loaded('posix'));
		
		echo self::check('MBstring installed: ', extension_loaded('mbstring'));
		
		echo self::check('MBregex installed: ', extension_loaded('mbregex'));
		
		echo '<br/>';		
		echo 'Checking MySQL Database connection and tables<br/>';
		flush();
		global $page;
		$connect=1;
		try {
		    $dbh = new PDO('mysql:host='.isset_or($page->db_connections[0]['host']).';dbname='.isset_or($page->db_connections[0]['dbname']), isset_or($page->db_connections[0]['user']), isset_or($page->db_connections[0]['password']));
			$dbh->query('SELECT 1;');
		    $dbh = null;
		} catch (PDOException $e) {
		    $connect=0;
		}
		
		echo self::check('MySql Database connection: ', $connect);
	}
	
	/**
	 * Get success message
	 * @param string $str
	 */
	private static function get_success($str){
		return '<span style="color:#0f0;">'.$str.'</span>';
	}
	
	/**
	 * Get error message
	 * @param string $str
	 */
	private static function get_error($str){
		return '<span style="color:#f00;">'.$str.'</span>';
	}
	
	/**
	 * Check method
	 * @param string $pretext
	 * @param string $condition
	 */
	private static function check($pretext,$condition)
	{
		return $pretext.($condition?self::get_success('PASSED'):self::get_error('NOT PASSED')).'<br/>';
	}
}

?>