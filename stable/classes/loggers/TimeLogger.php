<?php
	/**
	 * Time Logger Class
	 */
	/**
	 * Time Logger Class
	 * @package WebLauncher\Loggers
	 */
	class TimeLogger
	{
		/**
		 * @var array $time Logger data array
		 */
		var $time=array();
		/**
		 * @var bool $active Flag if active
		 */
		var $active=true;
		
		/**
		 * Constructor
		 * @param bool $active
		 */
		function __construct($active=true)
		{
			$this->active=$active;
		}
		
		/**
		 * Get zone data
		 * @param string $zone
		 */
		function get($zone)
		{
			return $this->time[$zone];
		}
		
		/**
		 * Get list data@
		 */
		function get_list()
		{
			return $this->time;
		}
		
		/**
		 * Start time on zone
		 * @param string $zone
		 * @param string $value
		 */
		function start($zone,$value='')
		{
			if($this->active)
			{
				if(!$value)
					$value=$this->_get_current();

				$this->time[$zone]['start']=$value;
			}
		}
		
		/**
		 * End zone
		 * @param string $zone
		 * @param string $value
		 */
		function end($zone,$value='')
		{
			if($this->active)
			{
				if(!$value)
					$value=$this->_get_current();

				$this->time[$zone]['end']=$value;
				if(isset($this->time[$zone]['start']))
				{
					$this->time[$zone]=$this->time[$zone]['end']-$this->time[$zone]['start'];
				}
			}
		}
        
		/**
		 * Get current time
		 */
		function _get_current()
		{
			return microtime(true);
		}
	}

?>
