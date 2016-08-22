<?php 
	/**
	 * Base Webservice Extender
	 */
	/**
	 * Model extender with SOAP Webservice functionality
	 * @package WebLauncher\Objects
	 */
	class BaseWebService extends BaseExtender{
		/**
		 * @var bool $accept_all_methods Accept all called methods
		 */
		public $accept_all_methods=true;
		/**
		 * @var string $wsdl WSDL location
		 */
		public $wsdl='';
		/**
		 * @var array $preset_params Preset params array
		 */
		public $preset_params=array();
		/**
		 * @var array $soap_options SOAP Options
		 */
		public $soap_options=array();
		/**
		 * @var callable $after_call_execute After call execute callback
		 */
		public $after_call_execute=null;		
		/**
		 * @var callable $before_call_execute Before call execute callback
		 */
		public $before_call_execute=null;
		/**
		 * @var callable $process_response_execute Process response callback
		 */			
		public $process_response_execute=null;
		/**
		 * @var \SoapClient $_client Client object
		 */
		private $_client=null;
		/**
		 * @var int $_last_call_start Last call start microtime
		 */
		private $_last_call_start=null;
		/**
		 * @var int $_last_call_end Last call end microtime
		 */
		private $_last_call_end=null;
		/**
		 * @var string $service_location SOAP service location
		 */
		public $service_location='';
		
		/**
		 * Init extender
		 */
		public function init(){
			parent::init();
			if($this->wsdl){
				$this->_client=new SoapClient($this -> wsdl,$this->soap_options);
				if($this->service_location)
					$this->_client->__setLocation($this->service_location);
			}
			else
				trigger_error('No wsdl provided for model '.get_class($this->_model));
		}
		
		/**
		 * Magic method call on webservice
		 * @param string $name
		 * @param array $arguments
		 */
		function __call($name,$arguments){
			if($this -> before_call_execute && is_callable($this -> before_call_execute))
				call_user_func($this -> before_call_execute, $name,$arguments);
			$params=new stdClass();
			$params_arr=$this->preset_params;
			if(count($arguments)==1)
				$params_arr=array_merge($params_arr,$arguments[0]);
			foreach($params_arr as $field=>$value)
				$params->$field=$value;
			return $this->_call($name,$params);			
		}
		
		/**
		 * Call webservice methods
		 * @param string $method
		 * @param array $params
		 */
		private function _call($method, $params)
		{
			$response = null;
			try
			{
				$this -> _last_call_start = microtime(true);
				$response = $this -> _client -> {$method}($params);
				$this -> _last_call_end = microtime(true);
				if($this -> after_call_execute && is_callable($this -> after_call_execute))
					call_user_func($this -> after_call_execute, $method,$params,$response);
			} catch(Exception $ex)
			{
				trigger_error('Webservice Error: ' . $ex -> getMessage() . '<br>' . $ex);
			}
			return $this -> _process_response($response);
		}
		
		/**
		 * Process response
		 * @param object $response
		 */
		private function _process_response($response){
			if($this->process_response_execute && is_callable($this -> process_response_execute))
				call_user_func($this -> process_response_execute, $response);
			else
				return $response;
		}
	}
?>