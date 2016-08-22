<?php

class Geocoder
{
	public static $url = 'http://maps.google.com/maps/geo';

	const G_GEO_SUCCESS             = 200;
	const G_GEO_BAD_REQUEST         = 400;
	const G_GEO_SERVER_ERROR        = 500;
	const G_GEO_MISSING_QUERY       = 601;
	const G_GEO_MISSING_ADDRESS     = 601;
	const G_GEO_UNKNOWN_ADDRESS     = 602;
	const G_GEO_UNAVAILABLE_ADDRESS = 603;
	const G_GEO_UNKNOWN_DIRECTIONS  = 604;
	const G_GEO_BAD_KEY             = 610;
	const G_GEO_TOO_MANY_QUERIES    = 620;

	protected $_apiKey;

	public function __construct($key)
	{
		$this->_apiKey = $key;
	}

	// other code

	public function performRequest($search, $output = 'xml')
	{
		$url = sprintf('%s?q=%s&output=%s&key=%s&oe=utf-8',
		self::$url,
		urlencode($search),
		$output,
		$this->_apiKey);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	public function lookup($search)
	{
		$response = $this->performRequest($search, 'xml');
		$xml      = new SimpleXMLElement($response);
		$status   = (int) $xml->Response->Status->code;

		switch ($status) {
			case self::G_GEO_SUCCESS:

				$placemarks = array();
				foreach ($xml->Response->Placemark as $placemark)
				$placemarks[] = Placemark::FromSimpleXml($placemark);

				return $placemarks;

			case self::G_GEO_UNKNOWN_ADDRESS:
			case self::G_GEO_UNAVAILABLE_ADDRESS:
				return array();
			default:
				throw new Exception(sprintf('Google Geo error %d occurred', $status));
		}
	}
}

class Placemark
{
	const ACCURACY_UNKNOWN      = 0;
	const ACCURACY_COUNTRY      = 1;
	const ACCURACY_REGION       = 2;
	const ACCURACY_SUBREGION    = 3;
	const ACCURACY_TOWN         = 4;
	const ACCURACY_POSTCODE     = 5;
	const ACCURACY_STREET       = 6;
	const ACCURACY_INTERSECTION = 7;
	const ACCURACY_ADDRESS      = 8;

	protected $_point;
	protected $_address;
	protected $_accuracy;
	protected $_street;
	protected $_city;
	protected $_locality;
	protected $_country;
	protected $_country_name;
	protected $_state;
	protected $_zip;

	public function setAddress($address)
	{
		$this->_address = (string) $address;
	}

	public function getAddress()
	{
		return $this->_address;
	}

	public function __toString()
	{
		return $this->getAddress();
	}

	public function setPoint(Point $point)
	{
		$this->_point = $point;
	}

	public function getPoint()
	{
		return $this->_point;
	}

	public function setAccuracy($accuracy)
	{
		$this->_accuracy = (int) $accuracy;
	}

	public function getAccuracy()
	{
		return $this->_accuracy;
	}

	public function setAddressDetails($xml)
	{
		$this->_country=(string)$xml->Country->CountryNameCode;
		$this->_country_name=(string)$xml->Country->CountryName;

		$this->_state=(string)$xml->Country->AdministrativeArea->AdministrativeAreaName;
		$this->_city=(string)$xml->Country->AdministrativeArea->SubAdministrativeArea->SubAdministrativeAreaName;
		$this->_locality=(string)$xml->Country->AdministrativeArea->SubAdministrativeArea->Locality->LocalityName;
		$this->_street=(string)$xml->Country->AdministrativeArea->SubAdministrativeArea->Locality->Thoroughfare->ThoroughfareName;
		$this->_zip=(string)$xml->Country->AdministrativeArea->SubAdministrativeArea->Locality->PostalCode->PostalCodeNumber;
	}

	public static function FromSimpleXml($xml)
	{
		$point = Point::Create($xml->Point->coordinates);
		$placemark = new self;
		$placemark->setPoint($point);
		$placemark->setAddress($xml->address);
		$placemark->setAddressDetails($xml->AddressDetails);
		$placemark->setAccuracy($xml->AddressDetails['Accuracy']);

		return $placemark;
	}

	function get($name)
	{
		return $this->{'_'.$name};
	}
}

class Point
{
	protected $_lat;
	protected $_lng;

	public function __construct($latitude, $longitude)
	{
		$this->_lat = $latitude;
		$this->_lng = $longitude;
	}

	public function getLatitude()
	{
		return $this->_lat;
	}

	public function getLongitude()
	{
		return $this->_lng;
	}

	public static function Create($str)
	{
		list($longitude, $latitude, $elevation) = explode(',', $str, 3);

		return new self($latitude, $longitude);
	}

}




/**
 * Google Maps HTTP Request 1.0
 *
 * Copyright (C) 2007 Özgür Karatag <oezguer@karatag.de>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 *
 * @class        googleRequest
 * @version      V1.0 04 April 2007
 * @author       Özgür Karatag <oezguer@karatag.de>
 * @copyright    2007 Özgür Karatag
 */

class googleRequest {

	/**
	 * @var string gKey
	 * @access private
	 */
	var $gKey;

	/**
	 * @var int code
	 * @access private
	 */
	var $code;

	/**
	 * @var int Accuracy
	 * @access private
	 */
	var $Accuracy;

	/**
	 * @var float latitude
	 * @access private
	 */
	var $latitude;

	/**
	 * @var float longitude
	 * @access private
	 */
	var $longitude;

	/**
	 * @var string address
	 * @access private
	 */
	var $address;

	/**
	 * @var string city
	 * @access private
	 */
	var $city;

	var $state;

	/**
	 * @var string country
	 * @access private
	 */
	var $country;

	var $zip;
	/**
	 * @var string error
	 * @access private
	 */
	var $error;

	var $placemark;

	/**
	 * @constructor
	 * @param string address
	 * @param string city
	 * @param string country
	 * @param string zip
	 * @author Özgür Karatag
	 * @description Constructor
	 */
	function googleRequest($address = '', $city = '',$state='', $country = '', $zip = '') {
		$this->setcode($address, $city,$state, $country, $zip);
	}

	/**
	 * @function setcode
	 * @param string address
	 * @param string city
	 * @param string country
	 * @param string zip
	 * @author Özgür Karatag
	 * @description Sets the value
	 */
	function setcode($address = '', $city = '', $country = '', $zip = '') {
		$this->address = $address;
		$this->city    = $city;
		$this->country = $country;
		$this->state=$state;
		$this->zip     = $zip;
	}

	function setGoogleKey($value) {
		$this->gKey = $value;
	}

	/**
	 * @function GetRequest
	 * @author Özgür Karatag
	 * @description Gets the CSV-File of Google
	 */
	function GetRequest() {
		$address = str_replace(' ', '+', $this->address.','.$this->zip.'+'.$this->city.','.$this->state.','.$this->country);
		$geocoder = new Geocoder($this->gKey);
		$placemarks=null;
		//echo $address;
		try {
			$placemarks = $geocoder->lookup($address);
		}
		catch (Exception $ex) {
			echo $ex->getMessage();
		}
		$this->placemark=$placemarks[0];
	}

	/*
	 * @function     getVar
	 * @returns      mixed
	 * @param        $name
	 * @author       Özgür Karatag
	 * @description  Gets the value of $name
	 */
	function getVar($name)
	{
		if($this->placemark)
			return $this->placemark->get($name);
		return '';
	}

	function getPoint()
	{
		return $this->placemark->getPoint();
	}
}
?>
