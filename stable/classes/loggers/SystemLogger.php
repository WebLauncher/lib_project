<?php
/**
 * System Logger
 */
	/**
	 * System Logger class
	 * @package WebLauncher\Loggers
	 */
class SystemLogger {
	/**
	 * @var array $logger Logger data array
	 */
	var $logger=array();
	/**
	 * @var bool $active Flag if logger is active
	 */
	var $active=true;
	/**
	 * @var int $no Number of logs
	 */
	var $no=0;
	
	/**
	 * Constructor
	 * @param bool $active
	 */
	function __construct($active=true)
	{
		$this->active=$active;
	}
	
	/**
	 * Add to log
	 * @param string $type
	 * @param string $message
	 */
	function log($type,$message)
	{
		if($this->active)
		{
			$this->logger[]=array('type'=>$type,'message'=>$message);
			$this->no++;
		}
	}
	
	/**
	 * Get generated code for logger
	 */
	function get()
	{
		$types=array();
			foreach($this->logger as $v)
				if(!isset($types[$v['type']]))
				{
					$types[$v['type']]=array('list'=>array($v['message']),'total'=>1);
				}
				else
				{
					$types[$v['type']]['list'][]=$v['message'];
					$types[$v['type']]['total']++;
				}

			$result='<br/><br/><table class="logger" width="100%">';
			foreach($types as $k=>$v)
			{
				$result.='<tr><td>'.$k.' ('.$v['total'].')</td><td><ul>';
				foreach($v['list'] as $m)
					$result.='<li>'.$m.'</li>';
				$result.='</ul></td></tr>';
			}
			$result.='</table>';
			return $result;
	}
}
?>