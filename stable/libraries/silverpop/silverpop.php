<?php
class SilverPop {
	static protected $_logged = false;
	static protected $_session_id = '';
	static protected $_user = '';
	static protected $_password = '';
	static protected $_url = '';

	public static function logIn($user = '', $password = '') {
		if (!self::$_logged) {
			if ($user && $password) {
				self::$_user = $user;
				self::$_password = $password;
			} else {
				self::$_user = SILVERPOP_USER;
				self::$_password = SILVERPOP_PASSWORD;
			}
			$content = '<Login>
				<USERNAME>' . self::$_user . '</USERNAME>
				<PASSWORD>' . self::$_password . '</PASSWORD>
				</Login>';
			$response=self::_request($content);			
			if(echo_r($response->Body->RESULT->SUCCESS))
			{
				self::$_logged=true;
				self::$_session_id=echo_r($response->Body->RESULT->SESSIONID);
			}
			else
				trigger_error('Can not login to SilverPop with the credetials provided! ');
		}
	}

	public static function setUrl($url = '') {
		if ($url)
			self::$_url = $url;
		else
			self::$_url = SILVERPOP_URL;
	}

	private static function _request($content) {
		self::_setUrl();
		$xml = '<Envelope><Body>';
		$xml .= $content;
		$xml .= '</Body></Envelope>';

		$c = new curl(self::$_url);
		$c -> setopt(CURLOPT_FOLLOWLOCATION, true);
		$c -> setopt(CURLOPT_POST, true);
		$params=$c -> asPostString(array('XML' => $xml));		
		$c -> setopt(CURLOPT_POSTFIELDS, $params);

		$response = $c -> exec();
		$response=simplexml_load_string($response);
		return $response;
	}

	private static function _setUrl() {
		if (!self::$_url)
			self::setUrl();
	}

}
?>