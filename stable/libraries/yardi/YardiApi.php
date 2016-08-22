<?php
class YardiApi {
	private $_wsdl = 'https://www.iyardiasp.com/8223thirdparty708dev/webservices/ItfResidentData.asmx?WSDL';
	//private $_wsdl = 'https://www.iyardiasp.com/8223thirdparty708dev/webservices/itfilsguestcard.asmx?WSDL';
	private $_username = '';
	private $_password = '';
	private $_server_name = '';
	private $_database = '';
	private $_platform = '';
	private $_yardi_property_id = '';
	private $_interface_entity = '';
	private $_interface_license = '';
	public $after_call_execute = '';
	/**
	 * Constructor
	 */
	function __construct($username = '', $password = '', $server_name = '', $database = '', $platform = '', $yardi_property_id = '', $interface_entity = '', $interface_license = '') {
		$this -> _username = $username ? $username : $this -> _username;
		$this -> _password = $password ? $password : $this -> _password;
		$this -> _server_name = $server_name ? $server_name : $this -> _server_name;
		$this -> _database = $database ? $database : $this -> _database;
		$this -> _platform = $platform ? $platform : $this -> _platform;
		$this -> _yardi_property_id = $yardi_property_id ? $yardi_property_id : $this -> _yardi_property_id;
		$this -> _interface_entity = $interface_entity ? $interface_entity : $this -> _interface_entity;
		$this -> _interface_license = $interface_license ? $interface_license : $this -> _interface_license;
		$this -> _client = new SoapClient($this -> _wsdl);
	}

	function __call($name, $arguments) {
		$params = new stdClass();
		if (count($arguments) == 1)
			$params_arr = $arguments[0];
		foreach ($params_arr as $field => $value)
			$params -> $field = $value;

		return $this -> _call($name, $params);
	}

	/**
	 * Private: call
	 */
	private function _call($method, $params) {
		$params -> UserName = $this -> _username;
		$params -> Password = $this -> _password;
		$params -> ServerName = $this -> _server_name;
		$params -> Database = $this -> _database;
		$params -> Platform = $this -> _platform;
		//$params -> YardiPropertyId = $this -> _yardi_property_id;
		$params -> InterfaceEntity = $this -> _interface_entity;
		$params -> InterfaceLicense = $this -> _interface_license;
		echopre($params);
		$response = null;
		try {
			$this -> _last_call_start = microtime(true);
			$response = $this -> _client -> {$method}($params);
			$this -> _last_call_end = microtime(true);
			if ($this -> after_call_execute && is_callable($this -> after_call_execute))
				call_user_func($this -> after_call_execute, $method);
		} catch(Exception $ex) {
			trigger_error('Yardi API Error: ' . $ex -> getMessage() . '<br>' . $ex);
		}
		return $this -> _process_response($response);
	}

	public function get_duration() {
		return $this -> _last_call_end - $this -> _last_call_start;
	}

	private function _process_response($response) {
		return $response;
	}

	public function UnitAvailability_Login() {
		$params = new stdClass();
		return $this -> _call(__FUNCTION__, $params);
	}

}
?>