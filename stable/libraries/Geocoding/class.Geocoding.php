<?php
class Geocoding
{
	public $provider='';

	public $use_google=true;
	public $google_key='';
	protected $google_request;

	public $use_yahoo=true;
	public $yahoo_key='YahooDem';
	protected $yahoo_request;

	public $use_db=false;
	public $db_server='';
	public $db_user='';
	public $db_password='';
	public $db_name='';
	public $db_table='';
	public $db_type='mysql';
	public $db_request;

	public $use_email_tracking=false;

	function __contruct()
	{

	}

	function getZip($country='US',$state='',$city='',$street='',$email='')
	{
		$zip='';
		if($this->use_google)
		{
			$this->provider='google';
			$this->init_google_request($street,$city,$state,$country);
			$zip=$this->google_request->getVar('zip');
		}
		if($this->use_yahoo && !$zip)
		{
			$this->provider='yahoo';
			$this->init_yahoo_request($street,$city,$state,$country);
			$zip=$this->yahoo_request->getVar('zip');
		}
		if($this->use_db && !$zip && $state)
		{
			$latitude='';
			$longitude='';
			if(!$this->google_request)
			{
				$this->init_google_request($street,$city,$state,$country);
			}
			if(isset($this->google_request->placemark))
			{
				$point=$this->google_request->getPoint();
				$latitude=$point->getLatitude();
				$longitude=$point->getLongitude();
			}
			if(!$latitude || !$longitude)
			{
				if(!$this->yahoo_request)
				{
					$this->init_yahoo_request($street,$city,$state,$country);
				}
				$latitude=$this->yahoo_request->latitude;
				$longitude=$this->yahoo_request->longitude;
			}

			$this->provider='db';
			$this->init_db_request();
			if($latitude && $longitude)
				$zip=$this->db_request->getZip($latitude,$longitude);

		}

		if($this->use_db && $this->use_email_tracking && !$zip && $email)
		{
			$this->provider='email';
			$this->init_db_request();
			require_once dirname(__FILE__).'/lib/class.EmailInfo.php';
			$email_info = new EmailInfo();
			$tags = $email_info->getInfo($email);
			if(isset($tags['latitude']) && isset($tags['longitude']))
				$zip=$this->db_request->getZip($tags['latitude'],$tags['longitude']);
		}
		return $zip;
	}

	private function init_google_request($street,$city,$state,$country)
	{
		if(!class_exists('googleRequest'))
			require_once dirname(__FILE__).'/lib/class.GoogleMapsHTTPRequest.php';
		$this->google_request = new googleRequest($street,$city,$state,$country);
		$this->google_request->setGoogleKey($this->google_key);
		$this->google_request->getRequest();
	}

	private function init_yahoo_request($street,$city,$state,$country)
	{
		if(!class_exists('yahooRequest'))
			require_once dirname(__FILE__).'/lib/class.YahooMapsHTTPRequest.php';
		$this->yahoo_request = new yahooRequest($street,$city,$state,$country);
		$this->yahoo_request->setKey($this->yahoo_key);
		$this->yahoo_request->getRequest();
	}

	private function init_db_request()
	{
		if(!isset($this->db_request))
		{
			if(!class_exists('CehckDb'))
				require_once dirname(__FILE__).'/lib/class.CheckDb.php';
			$this->db_request=new CheckDb();
			$this->db_request->db_name=$this->db_name;
			$this->db_request->db_server=$this->db_server;
			$this->db_request->db_password=$this->db_password;
			$this->db_request->db_user=$this->db_user;
			$this->db_request->db_table=$this->db_table;
			$this->db_request->db_type=$this->db_type;

			$this->db_request->connect();
		}
	}
}
?>