<?php
/**
 * Session manager class
 */
/**
 * Session Manager Class
 * @package WebLauncher\Managers
 */
class SessionManager {
	/**
	 * @var array $array Data array
	 */
	private static $array =array();
	/**
	 * @var string $hash Session hash
	 */
	private static $hash = '';
	/**
	 * @var string $cookie_name Session cookie name
	 */
	private static $cookie_name = 'default';
	/**
	 * @var int $default_offset Session default timeout
	 */
	private static $default_offset = 1800;
	/**
	 * @var int $remmember_offset Session remmember timeout
	 */
	private static $remmember_offset = 864000;
	/**
	 * @var bool $deleted_old Session deleted_old
	 */
	private static $deleted_old = false;
	/**
	 * @var bool $create Create new session
	 */
	private static $create = false;
	/**
	 * @var string $method Session store method
	 */
	private static $method = 'db';
	/**
	 * @var object $handler Session handler
	 */
	private static $handler=null;

	/**
	 * Save session
	 */
	public static function save() {
		$func = 'save_' . self::$method;
		self::$func();
	}

	/**
	 * Init session
	 * @param string $_cookie
	 * @param int $default_offset
	 */
	public static function init($_cookie = 'default',$default_offset=1800) {
		self::$default_offset=$default_offset;
		$func = 'init_' . self::$method;
		self::$func($_cookie);
	}
	
	/**
	 * Check if session is new
	 */
	public static function is_new(){
		return self::$create;
	}

	/**
	 * Save to database
	 */
	private static function save_db() {
		
	}
	
	/**
	 * Init to database
	 * @param string $_cookie
	 */
	private static function init_db($_cookie = 'default') {
		session_set_cookie_params(self::$default_offset);
		if ($_cookie)
		{
			if(!isset($_COOKIE[$_cookie]))self::$create=true;
			self::$cookie_name = $_cookie;
			self::$handler=new SessionHandlerDb();
			self::$handler->set_remmember_time(self::$remmember_offset);
			session_name(self::$cookie_name);
			session_set_save_handler(array(self::$handler, 'open'),
                         array(&self::$handler, 'close'),
                         array(&self::$handler, 'read'),
                         array(&self::$handler, 'write'),
                         array(&self::$handler, 'destroy'),
                         array(&self::$handler, 'gc'));			
			@session_start();
		}
		if (!isset($_SESSION['expire']) || $_SESSION['expire'] < time()) {
			session_unset();			
			if (!isset($_SESSION))
				@session_start();				
			$_SESSION['_timestamp_created'] = time();
			$_SESSION['_hash'] = session_id();									
		}		
		$offset = self::$default_offset;
		if (isset($_SESSION['remmember']))
			$offset = self::$remmember_offset;
		$_SESSION['expire'] = time() + $offset;	
		session_set_cookie_params($offset);
		setcookie(session_name(),session_id(),time()+$offset,'/');
		self::$array = &$_SESSION;		
	}
}

/**
 * Session Db Handler
 */
class SessionHandlerDb{
	/**
	 * @var bool $create Create new session
	 */
	private $create=false;
	/**
	 * @var int $remmember_offset Session remmember timeout
	 */
	private $remmember_offset;
	
	/**
	 * Open session
	 */
	public function open(){
		return true;
	}
	
	/**
	 * Close session
	 */
	public function close(){
		return true;
	}
	
	/**
	 * Set the remmember timeout
	 * @param int $remmember_offset
	 */
	public function set_remmember_time($remmember_offset){
		$this->remmember_offset=$remmember_offset;
	}
	
	/**
	 * Write session
	 * @param string $id
	 * @param string $data
	 */
	public function write($id, $data){
		global $dal;
		$sql = '';
		$data=base64_encode(urlencode($data));
		if (!$this->create)
			$sql = 'UPDATE sessions	SET array = "' . $data . '" WHERE hash = "' . $id . '"';
		else {
			$sql = 'INSERT INTO sessions (hash, array) VALUES ("' . $id . '", "' . $data . '")';
			$this->create = false;
		}
		$dal -> db -> query($sql);
	}
	
	/**
	 * Read session
	 * @param string $id
	 */
	public function read($id){
		global $dal;
		$row = $dal -> db -> getRow('SELECT * FROM sessions	WHERE hash = "' . $id. '"');
		if (!isset($row['array'])) {
			$this->create=true;
			return '';
		} else {
			return urldecode(base64_decode($row['array']));
		}
	}
	
	/**
	 * Garbage collector
	 * @param object $max
	 */
	public function gc($max){
		global $dal;
		$sql = 'DELETE FROM sessions WHERE TIMESTAMPADD(SECOND,'.$this->remmember_offset.',`timestamp`)<NOW()';
		$dal -> db -> query($sql);	
		return true;	
	}
	
	/**
	 * Destroy session
	 * @param string $id
	 */
	public function destroy($id){		
      	global $dal;
		$sql = 'DELETE FROM sessions WHERE hash=' . $id;
		$dal -> db -> query($sql);
        return TRUE;
	}	
	
	/**
	 * Destruct session
	 */
	function __destruct ()    
    {
        @session_write_close();
    }
}
?>