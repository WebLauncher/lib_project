<?php
class GoToWebinar_Api{
	private $token='3f03d4e333e6e9cf81caff5b003aacd5';
	private $organizer_key='200000000001180776';
	private $register_url='https://api.citrixonline.com/G2W/rest/organizers/{organizerKey}/webinars/{webinarKey}/registrants';
	private $params=array();
	
	function __construct($params=array()){		
		$params=array(
			'base_uri'=>'https://api.citrixonline.com/oauth/',
			'authorize_uri'=>'https://api.citrixonline.com/oauth/authorize',
			'access_token_uri'=>'https://api.citrixonline.com/oauth/access_token',
			'client_id'=>'0514b5ff2cbebe5a69a483dcba9f47ef',
			'client_secret'=>'none',
			'code'=>'LTMwMzk5YTcxOjEzNGY2ZWE0MWNmOi0yNWIx',
			'username'=>'jennataufer@insideoutdev.com',
			'password'=>'Insideout1',
			'certificate_path'=>dirname(__FILE__)."/certificates/api.citrixonline.com.crt"
		);
		$this->params=$params;
	}
	
	private function _prepareUrl($url,$webinar=''){
		$url=str_replace('{organizerKey}', $this->organizer_key, $url);
		$url=str_replace('{webinarKey}', $webinar, $url);
		return $url;
	}
	
	function register($webinar,$params=array()){
		$url=$this->_prepareUrl($this->register_url,$webinar);
		$client=new Zend_Http_Client($url);
		$client->setHeaders(
			array(
				'Accept: application/json',
				'Accept: application/vnd.citrix.g2wapi-v1.1+json',
				'Content-Type: application/json',
				'Authorization: OAuth oauth_token='.$this->token				
			)
		);
		$data=json_encode($params);		
		$client->setRawData($data);
		$response=$client->request('POST');
		echopre($response);
	}

	function get_registrants($webinar){
		$url=$this->_prepareUrl($this->register_url,$webinar);
		$client=new Zend_Http_Client($url);
		$client->setHeaders(
			array(
				'Accept: application/json',
				'Content-Type: application/json',
				'Authorization: OAuth oauth_token='.$this->token				
			)
		);		
		$response=$client->request('GET');
		return json_decode($response->getBody());
	}
}

class GoToWebinar
{
	private $_url='https://www3.gotomeeting.com/en_US/island/webinar/registration.flow';
	private $_certificate='gotomeeting_certificate.crt';
	private $_webinar_id='';
	private $_error='';

	function __construct($webinar_id)
	{
		$this->_webinar_id=$webinar_id;
	}

	function register_old($fname,$lname,$email,$address_country,$address_state,$address_city,$address_street,$address_zip='',$industry='',$phone='',$organization='',$jobtitle='')
	{
		$c = new curl($this->_url) ;


		$c->setopt(CURLOPT_FOLLOWLOCATION, true) ;
		$c->setopt(CURLOPT_POST, true) ;
		$c->setopt(CURLOPT_HTTPHEADER,array("Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
			'Cache-Control: max-age=0',
			'Connection: Keep-Alive',
			'Keep-Alive: 300',
			'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
			'Accept-Language: en-us,en;q=0.5',
			'Content-type: application/x-www-form-urlencoded;charset=UTF-8'
		));
		$c->setopt(CURLOPT_HEADER, 1);
		$c->setopt(CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)');
        $c->setopt(CURLOPT_REFERER,'https://www3.gotomeeting.com/register/');
        $c->setopt(CURLOPT_RETURNTRANSFER,true);

		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_SSL_VERIFYHOST, 0);
		$c->setopt(CURLOPT_CAINFO, dirname(__FILE__)."/certificates/".$this->_certificate);
		
		if($address_state=='N-CA' || $address_state=='S-CA')$address_state='CA';

		$theFields =
		  array
		    (
		    	'Form'=>'webinarRegistrationForm',
		    	'WebinarKey'=>$this->_webinar_id,
		    	'ViewArchivedWebinar'=>'false',
		    	'registrant'=>'',
		    	'RegistrantTimeZoneKey'=>'65',
		    	'Template'=>'island/webinar/registration.tmpl',
		      	'Name_First' => $fname,
		      	'Name_Last' => $lname,
		      	'Email' => $email,
		    	'Address_Street'=>$address_street,
		    	'Address_City'=>$address_city,
		    	'Address_State'=>$address_state,
		    	'Address_Zip'=>$address_zip,
		    	'Address_Country'=>$address_country,
		    	'Industry'=>$industry,
		    	'PhoneNumber'=>$phone,
		    	'Organization'=>$organization,
		    	'JobTitle'=>$jobtitle
		    ) ;

		$c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($theFields)) ;

		//
		// By default, the curl class expects to return data to
		// the caller.
		//

		$str=$c->exec() ;
echo $str;die;
		//
		// Check to see if there was an error and, if so, print
		// the associated error message.
		//

		$this->_error= $c->hasError();

		//
		// Done with the cURL, so get rid of the cURL related resources.
		//

		$c->close() ;

		if(strpos($str,'<b>Webinar Unavailable</b>')===FALSE)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function register($fname,$lname,$email,$address_country,$address_state,$address_city,$address_street,$address_zip='',$industry='',$phone='',$organization='',$jobtitle='')
	{
		if($address_state=='N-CA' || $address_state=='S-CA')$address_state='CA';
		if($address_state=='Northern California' || $address_state=='Southern California')$address_state='California';
		if(!isset_or($address_city))$address_city='N/A';
		if(!isset_or($address_street))$address_street='N/A';
		$api=new GoToWebinar_Api();	
		$arr=array(
			"firstName"=>$fname,
			"lastName"=>$lname,
			"email"=>$email,
			"address"=>$address_street,
			"city"=>$address_city,
			"state"=>str_replace(' ', '', $address_state),
			"zipCode"=>$address_zip,
			"country"=>$address_country,
			"phone"=>$phone,
			"industry"=>$industry,
			"organization"=>$organization,
			"jobTitle"=>$jobtitle
		);	
		$api->register($this->_webinar_id,$arr);
	}
	
	function get_registrants(){
		$api=new GoToWebinar_Api();	
		return $api->get_registrants($this->_webinar_id);
	}
}
?>