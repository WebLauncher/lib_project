<?php
class LinkedIn_Status
{
	protected $_currentStatus;
	protected $_status;
	
	public function __construct()
	{
		
	}
	
	public function setStatus ($status)
	{
		$this->_status = $status;
	}
	
	public function getStatus ()
	{
		return $this->_status;
	}
	
	public function createXmlStatus()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?><current-status>'.$this->_status.'</current-status>';
		return $xml;			
	}
	
	public function publishStatus ($status = null)
	{
		if(null != $status)
			$this->setStatus($status);
			
		$this->_oauth->fetch($this->apiUrl.'/v1/people/~/current-status',$this->createXmlStatus,OAUTH_HTTP_METHOD_PUT);
	}
	
}