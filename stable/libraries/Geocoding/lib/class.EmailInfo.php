<?php
class EmailInfo
{

	function __construct(){}
	function __destruct(){}

	function getInfo($email)
	{
	    $emailinfo = explode('@', $email);
		if($emailinfo[1] == '')
		{
		    return array();
		}

		$ip = gethostbyname($emailinfo[1]);

		if(!$this -> isIPAdress($ip)){
		    return array();
		}
		$tags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$ip);

		return $tags;

	}

	function isIPAdress($value)
	{

        if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $value))
		{
            return true;
        }
        return false;
    }

}
?>
