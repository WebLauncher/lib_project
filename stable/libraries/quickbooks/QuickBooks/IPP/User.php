<?php

class QuickBooks_IPP_User
{
	protected $_userid;
	
	protected $_email;
	
	protected $_firstname;
	
	protected $_lastname;
	
	protected $_login;
	
	protected $_screenname;
	
	protected $_is_verified;
	
	protected $_external_auth;
	
	protected $_authid;
	
	public function __construct($userid, $email, $firstname, $lastname, $login, $screenname, $is_verified, $external_auth, $authid)
	{
		$this->_userid = $userid;
		$this->_email = $email;
		$this->_firstname = $firstname;
		$this->_lastname = $lastname;
		$this->_login = $login;
		$this->_screenname = $screenname;
		$this->_is_verified = $is_verified;
		$this->_external_auth = $external_auth;
		$this->_authid = $authid;
	}
	
	public function getUserId()
	{
		return $this->_userid;
	}
	
	public function getEmail()
	{
		return $this->_email;
	}
}