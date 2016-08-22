<?php
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

class yahooRequest {

	/**
	 * @var string gKey
	 * @access private
	 */
	var $Key;

	var $api_gateway='http://local.yahooapis.com/MapsService/V1/geocode';

	/**
	 * @var int code
	 * @access private
	 */
	var $code;

	/**
	 * @var int Accuracy
	 * @access private
	 */
	var $accuracy;

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

	/**
	 * @var string country
	 * @access private
	 */
	var $state;

	var $country;

	var $zip;
	/**
	 * @var string error
	 * @access private
	 */
	var $error;

	var $warning;

	/**
	 * @constructor
	 * @param string address
	 * @param string city
	 * @param string country
	 * @param string zip
	 * @author Özgür Karatag
	 * @description Constructor
	 */
	function yahooRequest($address = '', $city = '', $state = '', $country='US', $zip = '') {

		if (strlen($address) > 0 && strlen($city) > 0 && strlen($state) > 0) {
			$this->setcode($address, $city,$state, $country, $zip);
		}
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
	function setcode($address = '', $city = '',$state='', $country = '', $zip = '') {
		$this->address = $address;
		$this->city    = $city;
		$this->state=$state;
		$this->country = $country;
		$this->zip     = $zip;
	}

	function setKey($value) {
		$this->Key = $value;
	}

	/**
	 * @function GetRequest
	 * @author Özgür Karatag
	 * @description Gets the CSV-File of Google
	 */
	function GetRequest() {
		if($this->city || $this->state || $this->country || $address)
		{
			$api_gateway = "http://local.yahooapis.com/MapsService/V1/geocode/";
			$app_str = ( $this->Key )? "appid=" . $this->Key : "appid=YahooDem";
			$location_str = "street=" . urlencode($this->address).'&city='.urlencode($this->city).'&state='.urlencode($this->state).'&country='.urlencode($this->country).'&zip='.urlencode($this->zip);
			$output_str = "output=php";
			$request = $this->api_gateway . "?" . $app_str . "&" . $location_str . "&" . $output_str;

			$ch = curl_init($request);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

	        $response = curl_exec($ch);
	        curl_close($ch);

			if( $response ){
				$result = unserialize($response);
			} else {
				$result = FALSE;
			}
			if($result['ResultSet']['Result']['Address']!='')
				$this->address=$result['ResultSet']['Result']['Address'];
			if($result['ResultSet']['Result']['City']!='')
				$this->city=$result['ResultSet']['Result']['City'];
			if($result['ResultSet']['Result']['State']!='')
				$this->state=$result['ResultSet']['Result']['State'];
			if($result['ResultSet']['Result']['Country']!='')
				$this->country=$result['ResultSet']['Result']['Country'];
			if($result['ResultSet']['Result']['Zip']!='')
				$this->zip=$result['ResultSet']['Result']['Zip'];
			if($result['ResultSet']['Result']['Latitude']!='')
				$this->latitude=$result['ResultSet']['Result']['Latitude'];
			if($result['ResultSet']['Result']['Longitude']!='')
				$this->longitude=$result['ResultSet']['Result']['Longitude'];
			if($result['ResultSet']['Result']['precision']!='')
				$this->accuracy=$result['ResultSet']['Result']['precision'];
			if($result['ResultSet']['Result']['warning']!='')
				$this->warning=$result['ResultSet']['Result']['warning'];
		}
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
		return $this->{$name};
	}
}
?>
