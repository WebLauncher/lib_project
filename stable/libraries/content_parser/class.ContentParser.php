<?php



class ContentParser
{
	var $content='';
	
	// patterns
	var $pattern_email='/([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@([ ]+|)([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)/i';
	var $pattern_link='<a\s[^>]*href=(\'??)([^\' >]*?)\\1[^>]*>(.*)<\/a>';
	
	function __construct($content)
	{
		$this->content=$content;
	}
	
	function get_emails()
	{
		preg_match_all($this->pattern_email,$this->content,$matches);
		foreach($matches[0] as $k=>$v)
			$matches[0][$k]=trim($v);
		return array_unique($matches[0]);
	}
	
	function get_links()
	{
		$links=array();
		if(preg_match_all('/'.$this->pattern_link.'/siU', $this->content, $matches, PREG_SET_ORDER))
		{
			foreach($matches as $match)
			{
				if($this->check_link($match[2],$links))
			    	$links[]=$match[2];
		    }
		}
		return $links;
	}
	
	function check_link($link,$links)
	{
		if(in_array($link,$links))
			return false;
		if($link=='#')
			return false;
		if(strpos($link,'javascript:')===false)
			$ok=1;
		else
			return false;
			
		return true;
	}
}

?>