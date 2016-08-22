<?php
	/**
	 * Memory Usage Logger
	 */
	/**
	 * Memory usage logger
	 * @package WebLauncher\Loggers
	 */
	class MemoryLogger
	{
		/**
		 * @var array $memory memory usage data array
		 */
		var $memory=array();
		/**
		 * @var string $pattern Pattern used for display
		 */
		var $pattern='%BB (%MMB)';
		/**
		 * @var bool $active Flag if logger is active
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
		 * Get zone memory
		 * @param string $zone
		 */
		function get($zone)
		{
			return $this->memory[$zone];
		}
		
		/**
		 * Get current memory logged list
		 */
		function get_list()
		{
			return $this->memory;
		}
		
		/**
		 * Log zone memory
		 * @param string $zone
		 * @param string $value
		 * @param string $pattern
		 */
		function save($zone,$value='',$pattern='')
		{
			if($this->active)
			{
				if(!$value)
					$value=$this->_get_current();

				$this->memory[$zone]=$this->_process_pattern($value,$pattern);
			}
		}
		
		/**
		 * Get current memory usage
		 */
		function _get_current()
		{
			return memory_get_peak_usage(true);
		}
		
		/**
		 * Process pattern on memory
		 * @param string $memory 
		 * @param string $pattern
		 */
		function _process_pattern($memory,$pattern='')
		{
			if(!$pattern)
			{
				$pattern=$this->pattern;
			}
			$return=str_replace('%B',$memory,$pattern);
			$return=str_replace('%s',$memory,$return);
			$return=str_replace('%b',$memory*8,$return);
			$return=str_replace('%M',($memory/(1024*1024)),$return);
			$return=str_replace('%m',(($memory*8)/(1024*1024)),$return);
			$return=str_replace('%k',(($memory*8)/(1024)),$return);
			$return=str_replace('%K',($memory/(1024)),$return);
			$return=str_replace('%g',(($memory*8)/(1024*1024*1024)),$return);
			$return=str_replace('%G',($memory/(1024*1024*1024)),$return);

			return $return;
		}
	}

?>
