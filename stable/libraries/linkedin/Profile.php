<?php
/* Copyright (C) 2009 Winfred Peereboom  <wpeereboom@developmentit.com>
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
 * or see http://www.gnu.org/
 */

class LinkedIn_Profile
{	
	protected $id;
	protected $firstname;
	protected $lastname;
	protected $headline;
	protected $locationname;
	protected $locationcountrycode;
	protected $industry;
	protected $distance;
	protected $relationtoviewerdistance;
	protected $relationtoviewerconnections;
	protected $currentstatus;
	protected $apistandardprofilerequestheaders;
	protected $currentstatustimestamp;
	protected $connections;
	protected $summary;
	protected $specialties;
	protected $proposalcomments;
	protected $honors;
	protected $educations;
	protected $memberurlresources;
	protected $pictureurl;
	protected $sitestandardprofilerequest;
	protected $apistandardprofilerequest;
						
	protected $positions = array();
	protected $eductions = array();
  
  protected $data = array();
	 
	public function __construct()
	{
		
	}
	
	public function __set($key, $val)
  {
      $this->$key = $val;
  }
	
	public function __get($key)
  {
      return $this->$key;
  }
}