<?php


class EmailSpider
{
	var $url='';
	var $links=array();
	var $cache_file='';
	var $emails_file='';
	var $processed_file='';
	var $status_file='';
	
	function __construct($url,$emails_file='emails.txt',$cache_file='cache.txt',$processed_file='processed.txt',$status_file='status.txt')
	{
		$this->url=$url;
		$this->cache_file=$cache_file;
		$this->emails_file=$emails_file;
		$this->processed_file=$processed_file;
		$this->status_file=$status_file;
	}
	
	function start()
	{
		if($url)
		{
			$this->InitFiles();
			
			
		}
	}
	
	function init_files()
	{
		// cache file
		if(file_exists($this->cache_file))
		{
			unlink($this->cache_file);
		}
		$fcache=fopen($this->cache_file,'x');
		fclose($fcache);
		
		// processed file
		if(file_exists($this->processed_file))
		{
			unlink($this->processed_file);
		}
		$fproc=fopen($this->processed_file,'x');
		fclose($fproc);
		
		// emails file
		if(file_exists($this->emails_file))
		{
			unlink($this->emails_file);
		}
		$fem=fopen($this->emails_file,'x');
		fclose($fem);
		
		// status file
		if(file_exists($this->status_file))
		{
			unlink($this->status_file);
		}
		$fstatus=fopen($this->status_file,'x');
		fclose($fstatus);
	}
	
	function write_processed_link($link)
	{
		$femails=fopen($this->processed_file,'a');
		fwrite($femails,$link.'\n\r');
		fclose($femails);
	}
	
	function write_link($link)
	{
		$femails=fopen($this->cache_file,'a');
		fwrite($femails,$link.'\n\r');
		fclose($femails);
	}
	
	function write_email($email)
	{
		$femails=fopen($this->emails_file,'a');
		fwrite($femails,$email.'\n\r');
		fclose($femails);
	}
}

?>