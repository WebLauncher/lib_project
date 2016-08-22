<?php

	class YTJIC{
	    function YT_login($username, $password, $authenticationURL)//LOGIN FUNCTION
	    {
	        try {$httpClient = Zend_Gdata_ClientLogin::getHttpClient(
                                          $username,
                                          $password,
                                          $service = 'youtube',
                                          $client = null,
                                          $source = 'yoursource', // a short string identifying your application
                                          $loginToken = null,
                                          $loginCaptcha = null,
                                          $authenticationURL);
	            $values['client'] = $httpClient;
		        $values['error'] = '';
		        return $values;
            }catch(Exception $e){
                $values['client'] = '';
                $values['error'] = $e->getMessage();
	            return $values;
            } 
            	
        }
	
        function YT_upload($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, $fileFull, $fileMyme, $fileNewName, $fileTitle,  $fileDescription)//UPLOAD FUNCTION
		{    
		    $loginData = YTJIC::YT_login($username, $password, $authenticationURL);
			
			if($loginData['error'] != ''){
			    return $loginData;
			}else{
			    $httpClient = $loginData['client'];
				//echo $httpClient;
			}
			
            $yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);										  
            $myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();// create a new Zend_Gdata_YouTube_VideoEntry object

			$filesource = $yt->newMediaFileSource($fileFull);// create a new Zend_Gdata_App_MediaFileSource object
            $filesource->setContentType($fileMyme);// set slug header
            $filesource->setSlug($fileNewName);
            $myVideoEntry->setMediaSource($filesource);// add the filesource to the video entry
            $myVideoEntry->setVideoTitle($fileTitle);//file title
            $myVideoEntry->setVideoDescription($fileDescription);
            $myVideoEntry->setVideoCategory('Film'); // Note that category must be a valid YouTube category !
            $myVideoEntry->setVideoTags('io, books, community, insideout', 'alan', 'fine');// and that each keyword cannot contain whitespace// set keywords, please note that this must be a comma separated string
            $myVideoEntry->setVideoDeveloperTags(array('AlanFine', 'InsideOut'));// optionally set some developer tags (see Searching by Developer Tags for more details)
            
			$yt->registerPackage('Zend_Gdata_Geo');// optionally set the video's location
			$yt->registerPackage('Zend_Gdata_Geo_Extension');
            $where = $yt->newGeoRssWhere();
            $position = $yt->newGmlPos('37.0 -122.0');
            $where->point = $yt->newGmlPoint($position);
            $myVideoEntry->setWhere($where);

            $uploadUrl = 'http://uploads.gdata.youtube.com/feeds/users/default/uploads';// upload URI for the currently authenticated user
            
			// try to upload the video, catching a Zend_Gdata_App_HttpException if available
            // or just a regular Zend_Gdata_App_Exception
            try {
                    $newEntry = $yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry');
					$error['vidid'] = $newEntry -> getVideoId();
					$error['success'] = 'true';
					
                } catch (Zend_Gdata_App_HttpException $httpException) {
                    $error['error'] = strip_tags('Error, upload failed '.$httpException -> getMessage(). '-' . $httpException->getRawResponseBody().'.');
                    $error['success'] = 'false'; 
                } catch (Zend_Gdata_App_Exception $e) {
                    $error['error'] = strip_tags('Error, upload failed - '. $e->getMessage() .'.');
                    $error['success'] = 'false'; 
                }
				return $error;
		}		

		function editVideoData($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, $newVideoTitle, $newVideoDescription, $videoId)
        {
        	$continue = 0;
        	$error = array();
        	$error['error'] = '';
    		$loginData = YTJIC::YT_login($username, $password, $authenticationURL);
			
			if($loginData['error'] != ''){
			    return $loginData;
			}else{
			    $httpClient = $loginData['client'];
			}
			
            $youTubeService = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
            $feed = $youTubeService->getVideoFeed('http://gdata.youtube.com/feeds/users/default/uploads');
            $videoEntryToUpdate = null;

            foreach($feed as $entry) {
               if ($entry->getVideoId() == $videoId) {
                   $videoEntryToUpdate = $entry;
               break;
                }
            }

            if (!$videoEntryToUpdate instanceof Zend_Gdata_YouTube_VideoEntry) {
                $error['error'] = 'Could not find a video entry with id ' . $videoId . 'on youtube server.';
				return $error;
            }

            try {
                $putUrl = $videoEntryToUpdate->getEditLink()->getHref();
            }catch(Zend_Gdata_App_Exception $e) {
                $error['exist'] = strip_tags('Error - Could not obtain video entry\'s edit link: '. $e->getMessage() . '.');
                return $error;
			}

            if($newVideoTitle !=''){
			    $videoEntryToUpdate->setVideoTitle($newVideoTitle);
			}else{
			    $continue ++;
			}
            
			if($newVideoDescription != ''){
			    $videoEntryToUpdate->setVideoDescription($newVideoDescription);
			}else{
			    $continue ++;
			}
        
		    if($continue == 0){
                try {
                    $updatedEntry = $youTubeService->updateEntry($videoEntryToUpdate, $putUrl);
			    }catch(Zend_Gdata_App_HttpException $httpException){
                    $error['error'] = strip_tags('Error, ' . $httpException->getMessage().$httpException->getRawResponseBody(). '.');
                    return $error;
                }catch(Zend_Gdata_App_Exception $e) {
		            $error['error'] = strip_tags( 'Error, could not post video meta-data '. $e->getMessage().'.' );
                    return $error;	
                }
			}else{
			    $error['error'] = 'The title or the description of the video is empty.';
				return $error;			
			}
			
			if ($error['error'] ==''){$error['success'] = strip_tags('Video title and description was updated succesfully.');}
            return $error;
        }
		
        function deleteVideo($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, $videoId)
        {   
		    $loginData = YTJIC::YT_login($username, $password, $authenticationURL);
			$error['error'] = '';
			
			if($loginData['error'] != ''){
			    return $loginData;
			}else{
			    $httpClient = $loginData['client'];
			}
			
            $youTubeService = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
            $feed = $youTubeService->getVideoFeed('http://gdata.youtube.com/feeds/users/default/uploads');
            $videoEntryToDelete = null;

            foreach($feed as $entry) {
                if ($entry->getVideoId() == $videoId) {
                    $videoEntryToDelete = $entry;
                    break;
                }
            }

            // check if videoEntryToUpdate was found
            if (!$videoEntryToDelete instanceof Zend_Gdata_YouTube_VideoEntry) {
                $error['error'] = 'Could not find a video entry with id ' . $videoId . ' on youtube server, perhaps was already deleted.';
                return $error;
			}

            try {
                $httpResponse = $youTubeService -> delete($videoEntryToDelete);} catch (Zend_Gdata_App_HttpException $httpException) {
                $error['error'] = strip_tags('Error, ' . $httpException->getMessage(). '.'); 
                return $error;				
                } catch (Zend_Gdata_App_Exception $e) {
                $error['error'] = strip_tags( 'Error, could not delete video '. $e->getMessage().'.' );
                return $error;				
            }
			if ($error['error'] ==''){$error['success'] = strip_tags('Video deleted succesfully.');}
            return $error;
	    }
		
		function videoStatus($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, $videoId)
        {   
		
		    $status = '';
		    
		    $loginData = YTJIC::YT_login($username, $password, $authenticationURL);
			
			if($loginData['error'] != ''){
			    return $loginData;
			}else{
			    $httpClient = $loginData['client'];
			}
			
            $youTubeService = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
             $feed = $youTubeService->getuserUploads('default');
            $videoEntryToDelete = null;

            foreach($feed as $entry) {
                if ($entry->getVideoId() == $videoId) {
                    $videoEntryToDelete = $entry;
                    break;
                }
            }

            // check if videoEntryToUpdate was found
            if (!$videoEntryToDelete instanceof Zend_Gdata_YouTube_VideoEntry) {
                $error = 'unavailable';
                return $error;
			}

            try {
               $videoEntry = $youTubeService->getFullVideoEntry($videoId);
               $control = $videoEntry->getControl();
              } catch (Zend_Gdata_App_Exception $e) {
                $error = 'unavailable';
                return $error;

             }
           if ($control instanceof Zend_Gdata_App_Extension_Control) {
                if ($control->getDraft() != null && $control->getDraft()->getText() == 'yes') {
                    $state = $videoEntry->getVideoState();

                if ($state instanceof Zend_Gdata_YouTube_Extension_State) {
                  $status   = $state->getName();
				  
				  //return $status;
                }
				
                }
            } 
			if($status == ''){$status = 'available';}
		   //if($status == ''){$status = 'unavailable';}
		   return $status;
	    }
		
		function videoList($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, $videoId)
        {   
		    $loginData = YTJIC::YT_login($username, $password, $authenticationURL);
			
			if($loginData['error'] != ''){
			    return $loginData;
			}else{
			    $httpClient = $loginData['client'];
			}
			
            $youTubeService = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
             $feed = $youTubeService->getuserUploads('default');
            $videoEntryToDelete = null;

            foreach($feed as $idz => $entry) {
                    $videoEntryToDelete = $entry;
                     $return[$idz]['id'] = $entry->getVideoId();
					 $return[$idz]['title'] = $entry->getVideoTitle();

            // check if videoEntryToUpdate was found
            if (!$videoEntryToDelete instanceof Zend_Gdata_YouTube_VideoEntry) {
                $error = 'unavailable';
                $return[$idz]['status'] = $error;
			}

            try {
               $videoEntry = $youTubeService->getFullVideoEntry($videoId);
               $control = $videoEntry->getControl();
              } catch (Zend_Gdata_App_Exception $e) {
                $error = 'unavailable';
                $return[$idz]['status'] = $error;

             }
           if ($control instanceof Zend_Gdata_App_Extension_Control) {
                if ($control->getDraft() != null && $control->getDraft()->getText() == 'yes') {
                    $state = $videoEntry->getVideoState();

                if ($state instanceof Zend_Gdata_YouTube_Extension_State) {
                  $status   = $state->getName();
				  $return[$idz]['status'] = $status;
				  //return $status;
                }
				
                }
            } 
			if($status == ''){$status = 'available';}
		    $return[$idz]['status'] = $status;
		    $status = '';
		    }
		    return $return;
	    }
		
		
		function checkUpload($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, $videoId)
{
   		   
		   $loginData = YTJIC::YT_login($username, $password, $authenticationURL);
			
			if($loginData['error'] != ''){
			    return $loginData;
			}else{
			    $httpClient = $loginData['client'];
			}
			
            $youTubeService = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
			


    $feed = $youTubeService->getuserUploads('default');
    $message = 'No further status information available yet.';

    foreach($feed as $videoEntry) {
        if ($videoEntry->getVideoId() == $videoId) {
            // check if video is in draft status
            try {
                $control = $videoEntry->getControl();
            } catch (Zend_Gdata_App_Exception $e) {
                return 'ERROR - not able to retrieve control element '
                    . $e->getMessage();
                return;
            }

            if ($control instanceof Zend_Gdata_App_Extension_Control) {
                if (($control->getDraft() != null) &&
                    ($control->getDraft()->getText() == 'yes')) {
                    $state = $videoEntry->getVideoState();
                    if ($state instanceof Zend_Gdata_YouTube_Extension_State) {
                        $message = 'Upload status: ' . $state->getName() . ' '
                            . $state->getText();
                    } else {
                        return $message;
                    }
                }
            }
        }
    }
    return $message;
}
	}
	
	  ###### INITIALIZARE SI ACTIUNI ###############################################################################################3
	   //yt variables
       $authenticationURL = 'https://www.google.com/youtube/accounts/ClientLogin';
       $developerKey = 'AI39si67sUDQ4Tg_T9Ao3MabrXcw0is1YGMX9ulV3qLViFU00ksR-iELM_T2PCOGefjnwes4GsTYM3Z5KVDhQ6Epwgh4UzNeDg';
       $applicationId = 'IO Book Community Videos';
       $clientId = '';
       $username = 'iobookcommunity';
	   $password = 'alanfine1';
       
	   
	   /* here come demos how to use
       //$message = YTJIC::YT_upload($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, 'ccc.avi', 'video/avi', 'ccc.avi', 'aaa test large sds', 'aaa large test dsds');	
       // $message = YTJIC::deleteVideo($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, '7qFfUQRQNgQ');
       // $message = YTJIC::editVideoData($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, 'my test title', 'my test description', 'oz2dlFhtt6c');
       //$message = YTJIC::videoStatus($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, 'aifRGvzf1b4');
		
		//$message = YTJIC::YT_login($username, $password, 'https://www.google.com/youtube/accounts/ClientLogin');
	
	    //print_R($message);
		//print_r($message);
		
		
		
		if($_POST['upload_video'] == '1'){
		    $message = YTJIC::YT_upload($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, $_FILES['uploadedfile']['tmp_name'], $_FILES['uploadedfile']['type'], $_FILES['uploadedfile']['name'], $_POST['title'], $_POST['description']);	
        }

		if($_GET['deletevideo'] !=''){
		    $message = YTJIC::deleteVideo($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, '7qFfUQRQNgQ');
        }
		
		if($_GET['editvideo'] != ''){
		    $message = YTJIC::editVideoData($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, 'my test title', 'my test description', 'oz2dlFhtt6c');
        }
		
	
		
		
		?>
<form enctype="multipart/form-data" action="" method="POST">
Choose a file to upload: 
<input name="uploadedfile" type="file" /><br />
<input type="text" name="title" /><br />
<textarea name="description"> </textarea><br />
<input type="submit" value="Upload File" />
<input type="hidden" name="upload_video" value="1">
</form>
<?
		
		//print_r($_FILES);
		
		$lists = YTJIC::videoList($username, $password, $authenticationURL, $applicationId, $clientId, $developerKey, '#');
		//print_r($lists);
		if(count($lists)>0){
		    foreach($lists as $val){
		       echo $val['title'].' [http://www.youtube.com/watch?v='.$val['id'].'] ('.$val['status'].')<br />';
		    }
		}else{
		        echo 'no videos';
		}
	
	*/

?>
