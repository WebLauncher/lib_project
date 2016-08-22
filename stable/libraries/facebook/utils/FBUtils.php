<?php
include_once 'imp_explode_wkey.php';
 
  class FBUtils {
	private $facebook;
	private $loggedin_uid;
	private $friendsIDs;
	private $friendsNames;
	private $friendsNamesAreSorted;
	private $friends_ids_raw;
	private $friends_info_raw;
	private $user_info_raw;
	private $userInfo;
	private $loadedFriendsFields;
	private $loadedUserFields;
	private $friendIDNameArray;
	
   	public function __construct(){
		//login to the api
		$this->facebook = new Facebook($GLOBALS['mm_appapikey'], $GLOBALS['mm_appsecret']);
		$this->loggedin_uid = $this->facebook->require_login();
		$this->friendsNamesAreSorted=false;
		$this->totalFriends=null;
		$this->log=Logger::getInstance();

		//ini_set('display_errors', 1);
		//error_reporting(E_ALL);
	}
	
	private function loadFriendsIDsArray(){
		//prepare an array that contains the IDs of all this user's friends
			$query="SELECT uid2 FROM friend WHERE uid1=".$this->loggedin_uid;
			$this->friends_ids_raw=$this->facebook->api_client->fql_query($query);
			$counter=0;
			foreach ($this->friends_ids_raw as $var => $value) { //take out of associative array and put into a simpler array format
				$this->friendsIDs[$counter]=$value['uid2'];
				$counter++;
			}
	}
	private function loadFriendsInfoArray($fields){
		//prepare an array that contains information about all of this user's friends
			$query="SELECT ".implode_keys($this->loadedFriendsFields)." FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=".$this->loggedin_uid.")";
			$this->friends_info_raw = $this->facebook->api_client->fql_query($query);
			$this->totalFriends=count(array_keys($this->friends_info_raw));
	}
	private function loadUserInfoArray($fields){
		//prepare an array that contains information about all of this user
			$query="SELECT ".implode_keys($this->loadedUserFields)." FROM user WHERE uid IN (".$this->loggedin_uid.")";
			$this->user_info_raw = $this->facebook->api_client->fql_query($query);
			foreach ($this->loadedUserFields as $field_name => $dummy) { //take out of associative array and put into a simpler array format
				$this->userInfo[$field_name]=$this->user_info_raw[0][$field_name];
			}
	}
	public function publish_feed($feed_title,$feed_body,$img_uri,$img_url){
		$this->log->logme('FBUtils',LOGGER_DEBUG,"publish_feed",$GLOBALS['server'].":".$feed_title."->". $feed_body);
		try {
			if ($GLOBALS['server']=="stage"){
				$response=$this->facebook->api_client->feed_publishStoryToUser($feed_title, $feed_body, $img_uri, $img_url); //for debugging, doesn't run into feed limits
				$this->log->logme('FBUtils',LOGGER_NOTICE,"publish_feed","feed_publishStoryToUser. Debugging mode. Response:".$img_uri."->".implode($response));
			}else{
				$response=$this->facebook->api_client->feed_publishActionOfUser($feed_title, $feed_body, $img_uri ,$img_url);
				$this->log->logme('FBUtils',LOGGER_NOTICE,"publish_feed","feed_publishActionOfUser. Response:".implode($response));
			}
		} catch (Exception $e) {
			$this->log->logme('FBUtils',LOGGER_NOTICE,"publish_feed","Error:".$e->getMessage());
		}
	}
	/*
		The syntax is $facebook->api_client->notifications_sendRequest( $to, $type, $content, $img, $invite );

		Where $to is an array of IDs to send the request to (limit is 10)
		$content is fbml and $img is a URI of the image
		$type is the type of request e.g. event/movie, whatever
		$invite is a bool if true it calls the request an "invite" not a "request"

		Which returns the url which you should send the logged in user to to finalize the message. Look in the restlib PHP file around line 272 ,
		that's where the function's defined and http://developers.facebook.com/documentation.php?v=1.0&method=notifications.sendRequest if I
		didn't explain the params well.

		$fbml:
		Content of the request/invitation. This should be FBML containing only links and the special tag <fb:req-choice url="" label="" />
		to specify the buttons to be included in the request.
	*/
	public function send_notificationRequest($uids,$notif_type,$fbml,$img_uri,$invite=true){
		$this->log->logme('FBUtils',LOGGER_DEBUG,"send_notificationRequest");
		try {
			$response=$this->facebook->api_client->notifications_sendRequest( $uids, $notif_type, $fbml, $img_uri, $invite );
			$this->log->logme('FBUtils',LOGGER_NOTICE,"send_notificationRequest","notifications_sendRequest. Response:".$response);
			return($response);
		} catch (Exception $e) {
			$this->log->logme('FBUtils',LOGGER_NOTICE,"send_notificationRequest","Error:".$e->getMessage());
		}
	}

	public function send_emailNotification($uids,$fbml,$invite=false){
		$this->log->logme('FBUtils',LOGGER_DEBUG,"send_emailNotification");
		try {
			$response=$this->facebook->api_client->notifications_send( $uids, $fbml, $invite);
			$this->log->logme('FBUtils',LOGGER_NOTICE,"send_emailNotification","notifications_sendRequest. Response:".$response);
			return($response);
		} catch (Exception $e) {
			$this->log->logme('FBUtils',LOGGER_NOTICE,"send_emailNotification","Error:".$e->getMessage());
		}
	}
	
	public function set_profile($fbml, $uids){
		$this->log->logme('FBUtils',LOGGER_NOTICE,"set_profile");
		try {
			$response=$this->facebook->api_client->profile_setFBML($fbml, $uids);
			$this->log->logme('FBUtils',LOGGER_NOTICE,"set_profile", "Response:".$response);
			return($response);
		} catch (Exception $e) {
			$this->log->logme('FBUtils',LOGGER_NOTICE,"set_profile","Error:".$e->getMessage());
		}
	}
	
	//clears out the profile in FBs cache so that it can be reset by set_profile
	public function refresh_profile($url){
		$this->log->logme('FBUtils',LOGGER_NOTICE,"refresh_profile");
		try {
			$response=$this->facebook->api_client->fbml_refreshRefUrl($url);
			$this->log->logme('FBUtils',LOGGER_NOTICE,"refresh_profile", "Response:".$response);
			return($response);
		} catch (Exception $e) {
			$this->log->logme('FBUtils',LOGGER_NOTICE,"refresh_profile","Error:".$e->getMessage());
		}
	}
	
	//makes an array in the format
	//$this->friendIDNameArray[FBID=>FBName]
	//so that name can be accessed via friend ID
	//is used by get_friendNamebyID()
	//which does all of the prepwork to load appropriate data from FB
	private function make_friendIDNameArray(){
		$this->friendIDNameArray=array_combine ( array_values($this->friendsIDs), array_keys($this->friendsNames) );
	}
	
	//GETTERS
	public function get_uid(){
		return ($this->loggedin_uid);
	}
	public function get_userInfo($fields, $refresh=false){
		for ($i=0;$i<count($fields);$i++){
			if (!isset($this->loadedUserFields[$fields[$i]])){
				$refresh=true;
				$this->loadedUserFields[$fields[$i]]=true;
			}
		}
		//make the array if it's not already loaded, load it
		if ($refresh){
			$this->loadUserInfoArray($fields,$refresh);
		}
		return ($this->userInfo);
	}
	
	public function get_friendsInfo($fields, $refresh=false){
		for ($i=0;$i<count($fields);$i++){
			if ($this->loadedFriendsFields[$fields[$i]]==null){
				$refresh=true;
				$this->loadedFriendsFields[$fields[$i]]=true;
			}
		}
		if ($refresh){ //make the array if it's not already loaded, load it
			$this->loadFriendsInfoArray($fields,$refresh);
		}
		return ($this->friends_info_raw);
	}
	public function get_friendsNames($sort=false, $refresh=false){
		//make the array if it's not already set, otherwise, return it
		if ( (count($this->friendsNames)<1) || ($refresh) || ($this->friendsNamesAreSorted && ($sort==false))){
			$fields=array("name");
			$this->get_friendsInfo($fields,$refresh);
			unset($this->friendsNames); //delete old array
			$this->friendsNames=array(); //make it new again
			$counter=0;
			foreach ($this->friends_info_raw as $var => $value) {
				$this_name=$value['name'];
				$this->friendsNames[$this_name]=$counter; //create an associative array by friend name, the value is the order in which it appears - should match up with FB iD
				$counter++;
			}
			if ($sort){
				ksort($this->friendsNames);
				$this->friendsNamesAreSorted=true;
			}else{
				$this->friendsNamesAreSorted=false;
			}
		}
		return ($this->friendsNames);
	}
	
	
	public function get_friendIDbyName($friendName){
		$this->get_friendsIDs(); //will load up the friends ID list if it's not already loaded
		if ($this->friendsNamesAreSorted){
			$refreshVal=true; //got to refresh the list to make it unsorted so that IDs and names will match up
		}else{
			$refreshVal=false;
		}
		$this->get_friendsNames($refresh=$refreshVal);
		$this->log->logme('FBUtils',LOGGER_DEBUG,"get_friendIDbyName","Getting:".$friendName." response is: ".$this->friendsIDs[$this->friendsNames[$friendName]]);
		return($this->friendsIDs[$this->friendsNames[$friendName]]); //the value of friendsNames should be the same number at which this person's ID is indexed in the friendsIds array
	}
	
	public function get_friendNamebyID($friendID){
		$this->get_friendsNames();
		if (!isset($this->friendIDNameArray)){
			$this->make_friendIDNameArray();
		}
		return ($this->friendIDNameArray[$friendID]);
	}
	
	public function get_totalFriends(){
		if ($this->totalFriends == null){ //load up the friends if they haven't been already loaded in order to count them, use simplest case query
			$fields=array("name");
			$this->get_friendsInfo($fields);
		}
		return ($this->totalFriends);
	}
	public function get_friendsIDs($refresh=false){
		//make the array if it's not already set, otherwise, return it
		if ( (count($this->friendsIDs)<1) || ($refresh) ){
			$this->loadFriendsIDsArray();
		}
		return ($this->friendsIDs);
	}
	
	public function catchInvalidSession(){
		//catch the exception that gets thrown if the cookie has an invalid session_key in it
		try {
  			if (!$this->facebook->api_client->users_isAppAdded()) {
    			$this->facebook->redirect($this->facebook->get_add_url());
  			}
		} catch (Exception $ex) {
  			//this will clear cookies for your app and redirect them to a login prompt
  			$this->facebook->set_user(null, null);
  			$this->facebook->redirect($GLOBALS['mm_appapikey']);
		}
	}
	
    
    
  }
?>
