<?php
	/**
	 * Browser Info
	 */
	/**
	 * Browser Info Class
	 * @package WebLauncher\Infos
	 * @example $this->system->browser
	 */
	class BrowserInfo
	{
		/**
		 * The user agent of the request
		 */
		static $USER_AGENT = ''; // STRING - USER_AGENT_STRING
		/**
		 * The OS found for the request
		 */
		static $OS = ''; // STRING - operating system
		/**
		 * The OS version found in the request
		 */
		static $OS_Version = ''; // STRING - operating system version
		/**
		 * The browser found for the request
		 */
		static $Browser = '' ;// STRING - Browser name
		/**
		 * The browser version found for the request
		 */
		static $Browser_Version = ''; // STRING - Browser version
		/**
		 * BOOL - .NET Common Language Runtime
		 */
		static $NET_CLR = false;
		/**
		 * BOOL - resolving proceeded
		 */
		static $Resolved = false;
		/**
		 * STRING - Browser/Robot
		 */
		static $Type = '';


		/**
		 * Resolve browser detection
		 */
		private static function Resolve() // PUBLIC - Resolve()
		{
			self::$Resolved = false;
			self::$OS = '';
			self::$OS_Version = '';
			self::$NET_CLR = false;

			self::_GetOperatingSystem();
			self::_GetBrowser();
			self::_GetNET_CLR();
		}

		/***********************************************************************************/

		/**
		 * Get NET CLR
		 */
		private static function _GetNET_CLR()
		{
			if (self::contains('NET CLR',self::$USER_AGENT)) {self::$NET_CLR = true;}
		}
		
		/**
		 * Contains string method
		 * @param stirng $str
		 * @param string $haystack
		 * @param bool $insensitive
		 */
		private static function contains($str,$haystack,$insensitive=true)
		{			
			return ($insensitive && stripos($haystack,$str)!==false) || (!$insensitive && strpos($haystack,$str)!==false);
		}	
		
		/**
		 * Get operating system
		 */
		private static function _GetOperatingSystem()
		{
			if (self::contains('win',self::$USER_AGENT))
			{
				self::$OS = 'Windows';
				if ((self::contains('Windows 95',self::$USER_AGENT)) || (self::contains('Win95',self::$USER_AGENT))) {self::$OS_Version = '95';}
				elseif (self::contains('Windows ME',self::$USER_AGENT) || (self::contains('Win 9x 4.90',self::$USER_AGENT))) {self::$OS_Version = 'ME';}
				elseif ((self::contains('Windows 98',self::$USER_AGENT)) || (self::contains('Win98',self::$USER_AGENT))) {self::$OS_Version = '98';}
				elseif ((self::contains('Windows NT 5.0',self::$USER_AGENT)) || (self::contains('WinNT5.0',self::$USER_AGENT)) || (self::contains('Windows 2000',self::$USER_AGENT)) || (self::contains('Win2000',self::$USER_AGENT))) {self::$OS_Version = '2000';}
				elseif ((self::contains('Windows NT 5.1',self::$USER_AGENT)) || (self::contains('WinNT5.1',self::$USER_AGENT)) || (self::contains('Windows XP',self::$USER_AGENT))) {self::$OS_Version = 'XP';}
				elseif ((self::contains('Windows NT 5.2',self::$USER_AGENT)) || (self::contains('WinNT5.2',self::$USER_AGENT))) {self::$OS_Version = '.NET 2003';}
				elseif ((self::contains('Windows NT 6.0',self::$USER_AGENT)) || (self::contains('WinNT6.0',self::$USER_AGENT))) {self::$OS_Version = 'Codename: Longhorn';}
				elseif (self::contains('Windows CE',self::$USER_AGENT)) {self::$OS_Version = 'CE';}
				elseif (self::contains('Win3.11',self::$USER_AGENT)) {self::$OS_Version = '3.11';}
				elseif (self::contains('Win3.1',self::$USER_AGENT)) {self::$OS_Version = '3.1';}
				elseif ((self::contains('Windows NT',self::$USER_AGENT)) || (self::contains('WinNT',self::$USER_AGENT))) {self::$OS_Version = 'NT';}
			}
			elseif (self::contains('lindows',self::$USER_AGENT))
			{
				self::$OS = 'LindowsOS';
			}
			elseif (self::contains('mac',self::$USER_AGENT))
			{
				self::$OS = 'MacIntosh';
				if ((self::contains('Mac OS X',self::$USER_AGENT)) || (self::contains('Mac 10',self::$USER_AGENT))) {self::$OS_Version = 'OS X';}
				elseif ((self::contains('PowerPC',self::$USER_AGENT)) || (self::contains('PPC',self::$USER_AGENT))) {self::$OS_Version = 'PPC';}
				elseif ((self::contains('68000',self::$USER_AGENT)) || (self::contains('68k',self::$USER_AGENT))) {self::$OS_Version = '68K';}
			}
			elseif (self::contains('linux',self::$USER_AGENT))
			{
				self::$OS = 'Linux';
				if (self::contains('i686',self::$USER_AGENT)) {self::$OS_Version = 'i686';}
				elseif (self::contains('i586',self::$USER_AGENT)) {self::$OS_Version = 'i586';}
				elseif (self::contains('i486',self::$USER_AGENT)) {self::$OS_Version = 'i486';}
				elseif (self::contains('i386',self::$USER_AGENT)) {self::$OS_Version = 'i386';}
				elseif (self::contains('ppc',self::$USER_AGENT)) {self::$OS_Version = 'ppc';}
			}
			elseif (self::contains('sunos',self::$USER_AGENT))
			{
				self::$OS = 'SunOS';
			}
			elseif (self::contains('hp-ux',self::$USER_AGENT))
			{
				self::$OS = 'HP-UX';
			}
			elseif (self::contains('osf1',self::$USER_AGENT))
			{
				self::$OS = 'OSF1';
			}
			elseif (self::contains('freebsd',self::$USER_AGENT))
			{
				self::$OS = 'FreeBSD';
				if (self::contains('i686',self::$USER_AGENT)) {self::$OS_Version = 'i686';}
				elseif (self::contains('i586',self::$USER_AGENT)) {self::$OS_Version = 'i586';}
				elseif (self::contains('i486',self::$USER_AGENT)) {self::$OS_Version = 'i486';}
				elseif (self::contains('i386',self::$USER_AGENT)) {self::$OS_Version = 'i386';}
			}
			elseif (self::contains('netbsd',self::$USER_AGENT))
			{
				self::$OS = 'NetBSD';
				if (self::contains('i686',self::$USER_AGENT)) {self::$OS_Version = 'i686';}
				elseif (self::contains('i586',self::$USER_AGENT)) {self::$OS_Version = 'i586';}
				elseif (self::contains('i486',self::$USER_AGENT)) {self::$OS_Version = 'i486';}
				elseif (self::contains('i386',self::$USER_AGENT)) {self::$OS_Version = 'i386';}
			}
			elseif (self::contains('irix',self::$USER_AGENT))
			{
				self::$OS = 'IRIX';
			}
			elseif (self::contains('os/2',self::$USER_AGENT))
			{
				self::$OS = 'OS/2';
				if (self::contains('Warp 4.5',self::$USER_AGENT)) {self::$OS_Version = 'Warp 4.5';}
				elseif (self::contains('Warp 4',self::$USER_AGENT)) {self::$OS_Version = 'Warp 4';}
			}
			elseif (self::contains('amiga',self::$USER_AGENT))
			{
				self::$OS = 'Amiga';
			}
			elseif (self::contains('liberate',self::$USER_AGENT))
			{
				self::$OS = 'Liberate';
			}
			elseif (self::contains('qnx',self::$USER_AGENT))
			{
				self::$OS = 'QNX';
				if (self::contains('photon',self::$USER_AGENT)) {self::$OS_Version = 'Photon';}
			}
			elseif (self::contains('dreamcast',self::$USER_AGENT))
			{
				self::$OS = 'Sega Dreamcast';
			}
			elseif (self::contains('palm',self::$USER_AGENT))
			{
				self::$OS = 'Palm';
			}
			elseif (self::contains('powertv',self::$USER_AGENT))
			{
				self::$OS = 'PowerTV';
			}
			elseif (self::contains('prodigy',self::$USER_AGENT))
			{
				self::$OS = 'Prodigy';
			}
			elseif (self::contains('symbian',self::$USER_AGENT))
			{
				self::$OS = 'Symbian';
				if (self::contains('symbianos/6.1',self::$USER_AGENT)) {self::$Browser_Version = '6.1';}
			}
			elseif (self::contains('unix',self::$USER_AGENT))
			{
				self::$OS = 'Unix';
			}
			elseif (self::contains('webtv',self::$USER_AGENT))
			{
				self::$OS = 'WebTV';
			}
			elseif (self::contains('sie-cx35',self::$USER_AGENT))
			{
				self::$OS = 'Siemens CX35';
			}
			elseif( preg_match('/iPhone/i',self::$USER_AGENT) ) {
				self::$OS = 'iPhone';
				$aresult = explode('/',stristr(self::$USER_AGENT,'Version'));
				if( isset($aresult[1]) ) {
					$aversion = explode(' ',$aresult[1]);
					self::$OS_Version=$aversion[0];
				}
			}
			elseif( preg_match('/iPod/i',self::$USER_AGENT) ) {
				self::$OS = 'iPod';
				$aresult = explode('/',stristr(self::$USER_AGENT,'Version'));
				if( isset($aresult[1]) ) {
					$aversion = explode(' ',$aresult[1]);
					self::$OS_Version=$aversion[0];
				}
			}
		}


		/**
		 * Get Browser
		 */
		private static function _GetBrowser()
		{
			// boti
			if (self::contains('msnbot',self::$USER_AGENT))
			{
				self::$Browser = 'MSN Bot';
				self::$Type = 'robot';
				if (self::contains('msnbot/0.11',self::$USER_AGENT)) {self::$Browser_Version = '0.11';}
				elseif (self::contains('msnbot/0.30',self::$USER_AGENT)) {self::$Browser_Version = '0.3';}
				elseif (self::contains('msnbot/1.0',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
			}
			elseif (self::contains('almaden',self::$USER_AGENT))
			{
				self::$Browser = 'IBM Almaden Crawler';
				self::$Type = 'robot';
			}
			elseif (self::contains('BecomeBot',self::$USER_AGENT))
			{
				self::$Browser = 'BecomeBot';
				if (self::contains('becomebot/1.23',self::$USER_AGENT)) {self::$Browser_Version = '1.23';}
				self::$Type = 'robot';
			}
			elseif (self::contains('Link-Checker-Pro',self::$USER_AGENT))
			{
				self::$Browser = 'Link Checker Pro';
				self::$Type = 'robot';
			}
			elseif (self::contains('ia_archiver',self::$USER_AGENT))
			{
				self::$Browser = 'Alexa';
				self::$Type = 'robot';
			}
			elseif ((self::contains('googlebot',self::$USER_AGENT)) || (self::contains('google',self::$USER_AGENT)))
			{
				self::$Browser = 'Google Bot';
				self::$Type = 'robot';
				if ((self::contains('googlebot/2.1',self::$USER_AGENT)) || (self::contains('google/2.1',self::$USER_AGENT))) {self::$Browser_Version = '2.1';}
			}
			elseif (self::contains('surveybot',self::$USER_AGENT))
			{
				self::$Browser = 'Survey Bot';
				self::$Type = 'robot';
				if (self::contains('surveybot/2.3',self::$USER_AGENT)) {self::$Browser_Version = '2.3';}
			}
			elseif (self::contains('zyborg',self::$USER_AGENT))
			{
				self::$Browser = 'ZyBorg';
				self::$Type = 'robot';
				if (self::contains('zyborg/1.0',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
			}
			elseif (self::contains('w3c-checklink',self::$USER_AGENT))
			{
				self::$Browser = 'W3C Checklink';
				self::$Type = 'robot';
				if (self::contains('checklink/3.6',self::$USER_AGENT)) {self::$Browser_Version = '3.6';}
			}
			elseif (self::contains('linkwalker',self::$USER_AGENT))
			{
				self::$Browser = 'LinkWalker';
				self::$Type = 'robot';
			}
			elseif (self::contains('fast-webcrawler',self::$USER_AGENT))
			{
				self::$Browser = 'Fast WebCrawler';
				self::$Type = 'robot';
				if (self::contains('webcrawler/3.8',self::$USER_AGENT)) {self::$Browser_Version = '3.8';}
			}
			elseif ((self::contains('yahoo',self::$USER_AGENT)) && (self::contains('slurp',self::$USER_AGENT)))
			{
				self::$Browser = 'Yahoo! Slurp';
				self::$Type = 'robot';
			}
			elseif (self::contains('naverbot',self::$USER_AGENT))
			{
				self::$Browser = 'NaverBot';
				self::$Type = 'robot';
				if (self::contains('dloader/1.5',self::$USER_AGENT)) {self::$Browser_Version = '1.5';}
			}
			elseif (self::contains('converacrawler',self::$USER_AGENT))
			{
				self::$Browser = 'ConveraCrawler';
				self::$Type = 'robot';
				if (self::contains('converacrawler/0.5',self::$USER_AGENT)) {self::$Browser_Version = '0.5';}
			}
			elseif (self::contains('w3c_validator',self::$USER_AGENT))
			{
				self::$Browser = 'W3C Validator';
				self::$Type = 'robot';
				if (self::contains('w3c_validator/1.305',self::$USER_AGENT)) {self::$Browser_Version = '1.305';}
			}
			elseif (self::contains('innerprisebot',self::$USER_AGENT))
			{
				self::$Browser = 'Innerprise';
				self::$Type = 'robot';
				if (self::contains('innerprise/1.0',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
			}
			elseif (self::contains('topicspy',self::$USER_AGENT))
			{
				self::$Browser = 'Topicspy Checkbot';
				self::$Type = 'robot';
			}
			elseif (self::contains('poodle predictor',self::$USER_AGENT))
			{
				self::$Browser = 'Poodle Predictor';
				self::$Type = 'robot';
				if (self::contains('poodle predictor 1.0',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
			}
			elseif (self::contains('ichiro',self::$USER_AGENT))
			{
				self::$Browser = 'Ichiro';
				self::$Type = 'robot';
				if (self::contains('ichiro/1.0',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
			}
			elseif (self::contains('link checker pro',self::$USER_AGENT))
			{
				self::$Browser = 'Link Checker Pro';
				self::$Type = 'robot';
				if (self::contains('link checker pro 3.2.16',self::$USER_AGENT)) {self::$Browser_Version = '3.2.16';}
			}
			elseif (self::contains('grub-client',self::$USER_AGENT))
			{
				self::$Browser = 'Grub client';
				self::$Type = 'robot';
				if (self::contains('grub-client-2.3',self::$USER_AGENT)) {self::$Browser_Version = '2.3';}
			}
			elseif (self::contains('gigabot',self::$USER_AGENT))
			{
				self::$Browser = 'Gigabot';
				self::$Type = 'robot';
				if (self::contains('gigabot/2.0',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
			}
			elseif (self::contains('psbot',self::$USER_AGENT))
			{
				self::$Browser = 'PSBot';
				self::$Type = 'robot';
				if (self::contains('psbot/0.1',self::$USER_AGENT)) {self::$Browser_Version = '0.1';}
			}
			elseif (self::contains('mj12bot',self::$USER_AGENT))
			{
				self::$Browser = 'MJ12Bot';
				self::$Type = 'robot';
				if (self::contains('mj12bot/v0.5',self::$USER_AGENT)) {self::$Browser_Version = '0.5';}
			}
			elseif (self::contains('nextgensearchbot',self::$USER_AGENT))
			{
				self::$Browser = 'NextGenSearchBot';
				self::$Type = 'robot';
				if (self::contains('nextgensearchbot 1',self::$USER_AGENT)) {self::$Browser_Version = '1';}
			}
			elseif (self::contains('tutorgigbot',self::$USER_AGENT))
			{
				self::$Browser = 'TutorGigBot';
				self::$Type = 'robot';
				if (self::contains('bot/1.5',self::$USER_AGENT)) {self::$Browser_Version = '1.5';}
			}
			elseif (self::contains('NG',self::$USER_AGENT,false))
			{
				self::$Browser = 'Exabot NG';
				self::$Type = 'robot';
				if (self::contains('ng/2.0',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
			}
			elseif (self::contains('gaisbot',self::$USER_AGENT))
			{
				self::$Browser = 'Gaisbot';
				self::$Type = 'robot';
				if (self::contains('gaisbot/3.0',self::$USER_AGENT)) {self::$Browser_Version = '3.0';}
			}
			elseif (self::contains('xenu link sleuth',self::$USER_AGENT))
			{
				self::$Browser = 'Xenu Link Sleuth';
				self::$Type = 'robot';
				if (self::contains('xenu link sleuth 1.2',self::$USER_AGENT)) {self::$Browser_Version = '1.2';}
			}
			elseif (self::contains('turnitinbot',self::$USER_AGENT))
			{
				self::$Browser = 'TurnitinBot';
				self::$Type = 'robot';
				if (self::contains('turnitinbot/2.0',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
			}
			elseif (self::contains('iconsurf',self::$USER_AGENT))
			{
				self::$Browser = 'IconSurf';
				self::$Type = 'robot';
				if (self::contains('iconsurf/2.0',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
			}
			elseif (self::contains('zoe indexer',self::$USER_AGENT))
			{
				self::$Browser = 'Zoe Indexer';
				self::$Type = 'robot';
				if (self::contains('v1.x',self::$USER_AGENT)) {self::$Browser_Version = '1';}
			}
			// prehliadace
			elseif (self::contains('amaya',self::$USER_AGENT))
			{
				self::$Browser = 'amaya';
				self::$Type = 'browser';
				if (self::contains('amaya/5.0',self::$USER_AGENT)) {self::$Browser_Version = '5.0';}
				elseif (self::contains('amaya/5.1',self::$USER_AGENT)) {self::$Browser_Version = '5.1';}
				elseif (self::contains('amaya/5.2',self::$USER_AGENT)) {self::$Browser_Version = '5.2';}
				elseif (self::contains('amaya/5.3',self::$USER_AGENT)) {self::$Browser_Version = '5.3';}
				elseif (self::contains('amaya/6.0',self::$USER_AGENT)) {self::$Browser_Version = '6.0';}
				elseif (self::contains('amaya/6.1',self::$USER_AGENT)) {self::$Browser_Version = '6.1';}
				elseif (self::contains('amaya/6.2',self::$USER_AGENT)) {self::$Browser_Version = '6.2';}
				elseif (self::contains('amaya/6.3',self::$USER_AGENT)) {self::$Browser_Version = '6.3';}
				elseif (self::contains('amaya/6.4',self::$USER_AGENT)) {self::$Browser_Version = '6.4';}
				elseif (self::contains('amaya/7.0',self::$USER_AGENT)) {self::$Browser_Version = '7.0';}
				elseif (self::contains('amaya/7.1',self::$USER_AGENT)) {self::$Browser_Version = '7.1';}
				elseif (self::contains('amaya/7.2',self::$USER_AGENT)) {self::$Browser_Version = '7.2';}
				elseif (self::contains('amaya/8.0',self::$USER_AGENT)) {self::$Browser_Version = '8.0';}
			}
			elseif ((self::contains('aol',self::$USER_AGENT)) && !(self::contains('msie',self::$USER_AGENT)))
			{
				self::$Browser = 'AOL';
				self::$Type = 'browser';
				if ((self::contains('aol 7.0',self::$USER_AGENT)) || (self::contains('aol/7.0',self::$USER_AGENT))) {self::$Browser_Version = '7.0';}
			}
			elseif ((self::contains('aweb',self::$USER_AGENT)) || (self::contains('amigavoyager',self::$USER_AGENT)))
			{
				self::$Browser = 'AWeb';
				self::$Type = 'browser';
				if (self::contains('voyager/1.0',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
				elseif (self::contains('voyager/2.95',self::$USER_AGENT)) {self::$Browser_Version = '2.95';}
				elseif ((self::contains('voyager/3',self::$USER_AGENT)) || (self::contains('aweb/3.0',self::$USER_AGENT))) {self::$Browser_Version = '3.0';}
				elseif (self::contains('aweb/3.1',self::$USER_AGENT)) {self::$Browser_Version = '3.1';}
				elseif (self::contains('aweb/3.2',self::$USER_AGENT)) {self::$Browser_Version = '3.2';}
				elseif (self::contains('aweb/3.3',self::$USER_AGENT)) {self::$Browser_Version = '3.3';}
				elseif (self::contains('aweb/3.4',self::$USER_AGENT)) {self::$Browser_Version = '3.4';}
				elseif (self::contains('aweb/3.9',self::$USER_AGENT)) {self::$Browser_Version = '3.9';}
			}
			elseif (self::contains('beonex',self::$USER_AGENT))
			{
				self::$Browser = 'Beonex';
				self::$Type = 'browser';
				if (self::contains('beonex/0.8.2',self::$USER_AGENT)) {self::$Browser_Version = '0.8.2';}
				elseif (self::contains('beonex/0.8.1',self::$USER_AGENT)) {self::$Browser_Version = '0.8.1';}
				elseif (self::contains('beonex/0.8',self::$USER_AGENT)) {self::$Browser_Version = '0.8';}
			}
			elseif (self::contains('camino',self::$USER_AGENT))
			{
				self::$Browser = 'Camino';
				self::$Type = 'browser';
				if (self::contains('camino/0.7',self::$USER_AGENT)) {self::$Browser_Version = '0.7';}
			}
			elseif (self::contains('cyberdog',self::$USER_AGENT))
			{
				self::$Browser = 'Cyberdog';
				self::$Type = 'browser';
				if (self::contains('cybergog/1.2',self::$USER_AGENT)) {self::$Browser_Version = '1.2';}
				elseif (self::contains('cyberdog/2.0',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
				elseif (self::contains('cyberdog/2.0b1',self::$USER_AGENT)) {self::$Browser_Version = '2.0b1';}
			}
			elseif (self::contains('dillo',self::$USER_AGENT))
			{
				self::$Browser = 'Dillo';
				self::$Type = 'browser';
				if (self::contains('dillo/0.6.6',self::$USER_AGENT)) {self::$Browser_Version = '0.6.6';}
				elseif (self::contains('dillo/0.7.2',self::$USER_AGENT)) {self::$Browser_Version = '0.7.2';}
				elseif (self::contains('dillo/0.7.3',self::$USER_AGENT)) {self::$Browser_Version = '0.7.3';}
				elseif (self::contains('dillo/0.8',self::$USER_AGENT)) {self::$Browser_Version = '0.8';}
			}
			elseif (self::contains('doris',self::$USER_AGENT))
			{
				self::$Browser = 'Doris';
				self::$Type = 'browser';
				if (self::contains('doris/1.10',self::$USER_AGENT)) {self::$Browser_Version = '1.10';}
			}
			elseif (self::contains('emacs',self::$USER_AGENT))
			{
				self::$Browser = 'Emacs';
				self::$Type = 'browser';
				if (self::contains('emacs/w3/2',self::$USER_AGENT)) {self::$Browser_Version = '2';}
				elseif (self::contains('emacs/w3/3',self::$USER_AGENT)) {self::$Browser_Version = '3';}
				elseif (self::contains('emacs/w3/4',self::$USER_AGENT)) {self::$Browser_Version = '4';}
			}
			elseif (self::contains('firebird',self::$USER_AGENT))
			{
				self::$Browser = 'Firebird';
				self::$Type = 'browser';
				if ((self::contains('firebird/0.6',self::$USER_AGENT)) || (self::contains('browser/0.6',self::$USER_AGENT))) {self::$Browser_Version = '0.6';}
				elseif (self::contains('firebird/0.7',self::$USER_AGENT)) {self::$Browser_Version = '0.7';}
			}
			elseif (self::contains('firefox',self::$USER_AGENT))
			{
				self::$Browser = 'Firefox';
				self::$Type = 'browser';
				if (self::contains('firefox/9.',self::$USER_AGENT)) {self::$Browser_Version = '9';}
				elseif (self::contains('firefox/8.',self::$USER_AGENT)) {self::$Browser_Version = '8';}
				elseif (self::contains('firefox/7.',self::$USER_AGENT)) {self::$Browser_Version = '7';}
				elseif (self::contains('firefox/6.',self::$USER_AGENT)) {self::$Browser_Version = '6';}
				elseif (self::contains('firefox/5.',self::$USER_AGENT)) {self::$Browser_Version = '5';}
				elseif (self::contains('firefox/4.',self::$USER_AGENT)) {self::$Browser_Version = '4';}
				elseif (self::contains('firefox/3.6',self::$USER_AGENT)) {self::$Browser_Version = '3.6';}
				elseif (self::contains('firefox/3.5',self::$USER_AGENT)) {self::$Browser_Version = '3.5';}
				elseif (self::contains('firefox/3.0',self::$USER_AGENT)) {self::$Browser_Version = '3.0';}
				elseif (self::contains('firefox/2.',self::$USER_AGENT)) {self::$Browser_Version = '2';}
				elseif (self::contains('firefox/0.9.1',self::$USER_AGENT)) {self::$Browser_Version = '0.9.1';}
				elseif (self::contains('firefox/0.10',self::$USER_AGENT)) {self::$Browser_Version = '0.10';}
				elseif (self::contains('firefox/0.9',self::$USER_AGENT)) {self::$Browser_Version = '0.9';}
				elseif (self::contains('firefox/0.8',self::$USER_AGENT)) {self::$Browser_Version = '0.8';}
				elseif (self::contains('firefox/1.0',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
			}
			elseif (self::contains('frontpage',self::$USER_AGENT))
			{
				self::$Browser = 'FrontPage';
				self::$Type = 'browser';
				if ((self::contains('express 2',self::$USER_AGENT)) || (self::contains('frontpage 2',self::$USER_AGENT))) {self::$Browser_Version = '2';}
				elseif (self::contains('frontpage 3',self::$USER_AGENT)) {self::$Browser_Version = '3';}
				elseif (self::contains('frontpage 4',self::$USER_AGENT)) {self::$Browser_Version = '4';}
				elseif (self::contains('frontpage 5',self::$USER_AGENT)) {self::$Browser_Version = '5';}
				elseif (self::contains('frontpage 6',self::$USER_AGENT)) {self::$Browser_Version = '6';}
			}
			elseif (self::contains('galeon',self::$USER_AGENT))
			{
				self::$Browser = 'Galeon';
				self::$Type = 'browser';
				if (self::contains('galeon 0.1',self::$USER_AGENT)) {self::$Browser_Version = '0.1';}
				elseif (self::contains('galeon/0.11.1',self::$USER_AGENT)) {self::$Browser_Version = '0.11.1';}
				elseif (self::contains('galeon/0.11.2',self::$USER_AGENT)) {self::$Browser_Version = '0.11.2';}
				elseif (self::contains('galeon/0.11.3',self::$USER_AGENT)) {self::$Browser_Version = '0.11.3';}
				elseif (self::contains('galeon/0.11.5',self::$USER_AGENT)) {self::$Browser_Version = '0.11.5';}
				elseif (self::contains('galeon/0.12.8',self::$USER_AGENT)) {self::$Browser_Version = '0.12.8';}
				elseif (self::contains('galeon/0.12.7',self::$USER_AGENT)) {self::$Browser_Version = '0.12.7';}
				elseif (self::contains('galeon/0.12.6',self::$USER_AGENT)) {self::$Browser_Version = '0.12.6';}
				elseif (self::contains('galeon/0.12.5',self::$USER_AGENT)) {self::$Browser_Version = '0.12.5';}
				elseif (self::contains('galeon/0.12.4',self::$USER_AGENT)) {self::$Browser_Version = '0.12.4';}
				elseif (self::contains('galeon/0.12.3',self::$USER_AGENT)) {self::$Browser_Version = '0.12.3';}
				elseif (self::contains('galeon/0.12.2',self::$USER_AGENT)) {self::$Browser_Version = '0.12.2';}
				elseif (self::contains('galeon/0.12.1',self::$USER_AGENT)) {self::$Browser_Version = '0.12.1';}
				elseif (self::contains('galeon/0.12',self::$USER_AGENT)) {self::$Browser_Version = '0.12';}
				elseif ((self::contains('galeon/1',self::$USER_AGENT)) || (self::contains('galeon 1.0',self::$USER_AGENT))) {self::$Browser_Version = '1.0';}
			}
			elseif (self::contains('ibm web browser',self::$USER_AGENT))
			{
				self::$Browser = 'IBM Web Browser';
				self::$Type = 'browser';
				if (self::contains('rv:1.0.1',self::$USER_AGENT)) {self::$Browser_Version = '1.0.1';}
			}
			elseif (self::contains('chimera',self::$USER_AGENT))
			{
				self::$Browser = 'Chimera';
				self::$Type = 'browser';
				if (self::contains('chimera/0.7',self::$USER_AGENT)) {self::$Browser_Version = '0.7';}
				elseif (self::contains('chimera/0.6',self::$USER_AGENT)) {self::$Browser_Version = '0.6';}
				elseif (self::contains('chimera/0.5',self::$USER_AGENT)) {self::$Browser_Version = '0.5';}
				elseif (self::contains('chimera/0.4',self::$USER_AGENT)) {self::$Browser_Version = '0.4';}
			}
			elseif (self::contains('icab',self::$USER_AGENT))
			{
				self::$Browser = 'iCab';
        		self::$Type = 'browser';
				if (self::contains('icab/2.7.1',self::$USER_AGENT)) {self::$Browser_Version = '2.7.1';}
				elseif (self::contains('icab/2.8.1',self::$USER_AGENT)) {self::$Browser_Version = '2.8.1';}
				elseif (self::contains('icab/2.8.2',self::$USER_AGENT)) {self::$Browser_Version = '2.8.2';}
				elseif (self::contains('icab 2.9',self::$USER_AGENT)) {self::$Browser_Version = '2.9';}
				elseif (self::contains('icab 2.0',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
			}
			elseif (self::contains('konqueror',self::$USER_AGENT))
			{
				self::$Browser = 'Konqueror';
				self::$Type = 'browser';
				if (self::contains('konqueror/3.1',self::$USER_AGENT)) {self::$Browser_Version = '3.1';}
				elseif (self::contains('konqueror/3.3',self::$USER_AGENT)) {self::$Browser_Version = '3.3';}
				elseif (self::contains('konqueror/3.2',self::$USER_AGENT)) {self::$Browser_Version = '3.2';}
				elseif (self::contains('konqueror/3',self::$USER_AGENT)) {self::$Browser_Version = '3.0';}
				elseif (self::contains('konqueror/2.2',self::$USER_AGENT)) {self::$Browser_Version = '2.2';}
				elseif (self::contains('konqueror/2.1',self::$USER_AGENT)) {self::$Browser_Version = '2.1';}
				elseif (self::contains('konqueror/1.1',self::$USER_AGENT)) {self::$Browser_Version = '1.1';}
			}
			elseif (self::contains('liberate',self::$USER_AGENT))
			{
				self::$Browser = 'Liberate';
				self::$Type = 'browser';
				if (self::contains('dtv 1.2',self::$USER_AGENT)) {self::$Browser_Version = '1.2';}
				elseif (self::contains('dtv 1.1',self::$USER_AGENT)) {self::$Browser_Version = '1.1';}
			}
			elseif (self::contains('desktop/lx',self::$USER_AGENT))
			{
				self::$Browser = 'Lycoris Desktop/LX';
				self::$Type = 'browser';
			}
			elseif (self::contains('netbox',self::$USER_AGENT))
			{
				self::$Browser = 'NetBox';
				self::$Type = 'browser';
				if (self::contains('netbox/3.5',self::$USER_AGENT)) {self::$Browser_Version = '3.5';}
			}
			elseif (self::contains('netcaptor',self::$USER_AGENT))
			{
				self::$Browser = 'Netcaptor';
				self::$Type = 'browser';
				if (self::contains('netcaptor 7.0',self::$USER_AGENT)) {self::$Browser_Version = '7.0';}
				elseif (self::contains('netcaptor 7.1',self::$USER_AGENT)) {self::$Browser_Version = '7.1';}
				elseif (self::contains('netcaptor 7.2',self::$USER_AGENT)) {self::$Browser_Version = '7.2';}
				elseif (self::contains('netcaptor 7.5',self::$USER_AGENT)) {self::$Browser_Version = '7.5';}
				elseif (self::contains('netcaptor 6.1',self::$USER_AGENT)) {self::$Browser_Version = '6.1';}
			}
			elseif (self::contains('netpliance',self::$USER_AGENT))
			{
				self::$Browser = 'Netpliance';
				self::$Type = 'browser';
			}
			elseif (self::contains('netscape',self::$USER_AGENT)) // (1) netscape nie je prilis detekovatelny....
			{
				self::$Browser = 'Netscape';
				self::$Type = 'browser';
				if (self::contains('netscape/7.1',self::$USER_AGENT)) {self::$Browser_Version = '7.1';}
				elseif (self::contains('netscape/7.2',self::$USER_AGENT)) {self::$Browser_Version = '7.2';}
				elseif (self::contains('netscape/7.0',self::$USER_AGENT)) {self::$Browser_Version = '7.0';}
				elseif (self::contains('netscape6/6.2',self::$USER_AGENT)) {self::$Browser_Version = '6.2';}
				elseif (self::contains('netscape6/6.1',self::$USER_AGENT)) {self::$Browser_Version = '6.1';}
				elseif (self::contains('netscape6/6.0',self::$USER_AGENT)) {self::$Browser_Version = '6.0';}
			}
			elseif ((self::contains('mozilla/5.0',self::$USER_AGENT)) && (self::contains('rv:',self::$USER_AGENT)) && (self::contains('gecko/',self::$USER_AGENT))) // mozilla je troschu zlozitejsia na detekciu
			{
				self::$Browser = 'Mozilla';
				self::$Type = 'browser';
				if (self::contains('rv:1.0',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
				elseif (self::contains('rv:1.1',self::$USER_AGENT)) {self::$Browser_Version = '1.1';}
				elseif (self::contains('rv:1.2',self::$USER_AGENT)) {self::$Browser_Version = '1.2';}
				elseif (self::contains('rv:1.3',self::$USER_AGENT)) {self::$Browser_Version = '1.3';}
				elseif (self::contains('rv:1.4',self::$USER_AGENT)) {self::$Browser_Version = '1.4';}
				elseif (self::contains('rv:1.5',self::$USER_AGENT)) {self::$Browser_Version = '1.5';}
				elseif (self::contains('rv:1.6',self::$USER_AGENT)) {self::$Browser_Version = '1.6';}
				elseif (self::contains('rv:1.7',self::$USER_AGENT)) {self::$Browser_Version = '1.7';}
				elseif (self::contains('rv:1.8',self::$USER_AGENT)) {self::$Browser_Version = '1.8';}
			}
			elseif (self::contains('offbyone',self::$USER_AGENT))
			{
				self::$Browser = 'OffByOne';
				self::$Type = 'browser';
				if (self::contains('mozilla/4.7',self::$USER_AGENT)) {self::$Browser_Version = '3.4';}
			}
			elseif (self::contains('omniweb',self::$USER_AGENT))
			{
				self::$Browser = 'OmniWeb';
				self::$Type = 'browser';
				if (self::contains('omniweb/4.5',self::$USER_AGENT)) {self::$Browser_Version = '4.5';}
				elseif (self::contains('omniweb/4.4',self::$USER_AGENT)) {self::$Browser_Version = '4.4';}
				elseif (self::contains('omniweb/4.3',self::$USER_AGENT)) {self::$Browser_Version = '4.3';}
				elseif (self::contains('omniweb/4.2',self::$USER_AGENT)) {self::$Browser_Version = '4.2';}
				elseif (self::contains('omniweb/4.1',self::$USER_AGENT)) {self::$Browser_Version = '4.1';}
			}
			elseif (self::contains('opera',self::$USER_AGENT))
			{
				self::$Browser = 'Opera';
				self::$Type = 'browser';
				if ((self::contains('opera/7.21',self::$USER_AGENT)) || (self::contains('opera 7.21',self::$USER_AGENT))) {self::$Browser_Version = '7.21';}
				elseif ((self::contains('opera/8.0',self::$USER_AGENT)) || (self::contains('opera 8.0',self::$USER_AGENT))) {self::$Browser_Version = '8.0';}
				elseif ((self::contains('opera/7.60',self::$USER_AGENT)) || (self::contains('opera 7.60',self::$USER_AGENT))) {self::$Browser_Version = '7.60';}
				elseif ((self::contains('opera/7.54',self::$USER_AGENT)) || (self::contains('opera 7.54',self::$USER_AGENT))) {self::$Browser_Version = '7.54';}
				elseif ((self::contains('opera/7.53',self::$USER_AGENT)) || (self::contains('opera 7.53',self::$USER_AGENT))) {self::$Browser_Version = '7.53';}
				elseif ((self::contains('opera/7.52',self::$USER_AGENT)) || (self::contains('opera 7.52',self::$USER_AGENT))) {self::$Browser_Version = '7.52';}
				elseif ((self::contains('opera/7.51',self::$USER_AGENT)) || (self::contains('opera 7.51',self::$USER_AGENT))) {self::$Browser_Version = '7.51';}
				elseif ((self::contains('opera/7.50',self::$USER_AGENT)) || (self::contains('opera 7.50',self::$USER_AGENT))) {self::$Browser_Version = '7.50';}
				elseif ((self::contains('opera/7.23',self::$USER_AGENT)) || (self::contains('opera 7.23',self::$USER_AGENT))) {self::$Browser_Version = '7.23';}
				elseif ((self::contains('opera/7.22',self::$USER_AGENT)) || (self::contains('opera 7.22',self::$USER_AGENT))) {self::$Browser_Version = '7.22';}
				elseif ((self::contains('opera/7.20',self::$USER_AGENT)) || (self::contains('opera 7.20',self::$USER_AGENT))) {self::$Browser_Version = '7.20';}
				elseif ((self::contains('opera/7.11',self::$USER_AGENT)) || (self::contains('opera 7.11',self::$USER_AGENT))) {self::$Browser_Version = '7.11';}
				elseif ((self::contains('opera/7.10',self::$USER_AGENT)) || (self::contains('opera 7.10',self::$USER_AGENT))) {self::$Browser_Version = '7.10';}
				elseif ((self::contains('opera/7.03',self::$USER_AGENT)) || (self::contains('opera 7.03',self::$USER_AGENT))) {self::$Browser_Version = '7.03';}
				elseif ((self::contains('opera/7.02',self::$USER_AGENT)) || (self::contains('opera 7.02',self::$USER_AGENT))) {self::$Browser_Version = '7.02';}
				elseif ((self::contains('opera/7.01',self::$USER_AGENT)) || (self::contains('opera 7.01',self::$USER_AGENT))) {self::$Browser_Version = '7.01';}
				elseif ((self::contains('opera/7.0',self::$USER_AGENT)) || (self::contains('opera 7.0',self::$USER_AGENT))) {self::$Browser_Version = '7.0';}
				elseif ((self::contains('opera/6.12',self::$USER_AGENT)) || (self::contains('opera 6.12',self::$USER_AGENT))) {self::$Browser_Version = '6.12';}
				elseif ((self::contains('opera/6.11',self::$USER_AGENT)) || (self::contains('opera 6.11',self::$USER_AGENT))) {self::$Browser_Version = '6.11';}
				elseif ((self::contains('opera/6.1',self::$USER_AGENT)) || (self::contains('opera 6.1',self::$USER_AGENT))) {self::$Browser_Version = '6.1';}
				elseif ((self::contains('opera/6.	0',self::$USER_AGENT)) || (self::contains('opera 6.0',self::$USER_AGENT))) {self::$Browser_Version = '6.0';}
				elseif ((self::contains('opera/5.12',self::$USER_AGENT)) || (self::contains('opera 5.12',self::$USER_AGENT))) {self::$Browser_Version = '5.12';}
				elseif ((self::contains('opera/5.0',self::$USER_AGENT)) || (self::contains('opera 5.0',self::$USER_AGENT))) {self::$Browser_Version = '5.0';}
				elseif ((self::contains('opera/4',self::$USER_AGENT)) || (self::contains('opera 4',self::$USER_AGENT))) {self::$Browser_Version = '4';}
			}
			elseif (self::contains('oracle',self::$USER_AGENT))
			{
				self::$Browser = 'Oracle PowerBrowser';
				self::$Type = 'browser';
				if (self::contains('(tm)/1.0a',self::$USER_AGENT)) {self::$Browser_Version = '1.0a';}
				elseif (self::contains('oracle 1.5',self::$USER_AGENT)) {self::$Browser_Version = '1.5';}
			}
			elseif (self::contains('phoenix',self::$USER_AGENT))
			{
				self::$Browser = 'Phoenix';
				self::$Type = 'browser';
				if (self::contains('phoenix/0.4',self::$USER_AGENT)) {self::$Browser_Version = '0.4';}
				elseif (self::contains('phoenix/0.5',self::$USER_AGENT)) {self::$Browser_Version = '0.5';}
			}
			elseif (self::contains('planetweb',self::$USER_AGENT))
			{
				self::$Browser = 'PlanetWeb';
				self::$Type = 'browser';
				if (self::contains('planetweb/2.606',self::$USER_AGENT)) {self::$Browser_Version = '2.6';}
				elseif (self::contains('planetweb/1.125',self::$USER_AGENT)) {self::$Browser_Version = '3';}
			}
			elseif (self::contains('powertv',self::$USER_AGENT))
			{
				self::$Browser = 'PowerTV';
				self::$Type = 'browser';
				if (self::contains('powertv/1.5',self::$USER_AGENT)) {self::$Browser_Version = '1.5';}
			}
			elseif (self::contains('prodigy',self::$USER_AGENT))
			{
				self::$Browser = 'Prodigy';
				self::$Type = 'browser';
				if (self::contains('wb/3.2e',self::$USER_AGENT)) {self::$Browser_Version = '3.2e';}
				elseif (self::contains('rv: 1.',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
			}
			elseif ((self::contains('voyager',self::$USER_AGENT)) || ((self::contains('qnx',self::$USER_AGENT))) && (self::contains('rv: 1.',self::$USER_AGENT))) // aj voyager je trosku zlozitejsi na detekciu
			{
				self::$Browser = 'Voyager';
        self::$Type = 'browser';
				if (self::contains('2.03b',self::$USER_AGENT)) {self::$Browser_Version = '2.03b';}
				elseif (self::contains('wb/win32/3.4g',self::$USER_AGENT)) {self::$Browser_Version = '3.4g';}
			}
			elseif (self::contains('quicktime',self::$USER_AGENT))
			{
				self::$Browser = 'QuickTime';
				self::$Type = 'browser';
				if (self::contains('qtver=5',self::$USER_AGENT)) {self::$Browser_Version = '5.0';}
				elseif (self::contains('qtver=6.0',self::$USER_AGENT)) {self::$Browser_Version = '6.0';}
				elseif (self::contains('qtver=6.1',self::$USER_AGENT)) {self::$Browser_Version = '6.1';}
				elseif (self::contains('qtver=6.2',self::$USER_AGENT)) {self::$Browser_Version = '6.2';}
				elseif (self::contains('qtver=6.3',self::$USER_AGENT)) {self::$Browser_Version = '6.3';}
				elseif (self::contains('qtver=6.4',self::$USER_AGENT)) {self::$Browser_Version = '6.4';}
				elseif (self::contains('qtver=6.5',self::$USER_AGENT)) {self::$Browser_Version = '6.5';}
			}
			elseif(self::contains('chrome', self::$USER_AGENT)){
				self::$Browser = 'Chrome';
				self::$Type = 'browser';
				if (self::contains('Chrome/21',self::$USER_AGENT)) {self::$Browser_Version = '21';}
				elseif (self::contains('Chrome/20',self::$USER_AGENT)) {self::$Browser_Version = '20';}
				elseif (self::contains('Chrome/19',self::$USER_AGENT)) {self::$Browser_Version = '19';}
				elseif (self::contains('Chrome/18',self::$USER_AGENT)) {self::$Browser_Version = '18';}
				elseif (self::contains('Chrome/17',self::$USER_AGENT)) {self::$Browser_Version = '17';}
				elseif (self::contains('Chrome/16',self::$USER_AGENT)) {self::$Browser_Version = '16';}
				elseif (self::contains('Chrome/3',self::$USER_AGENT)) {self::$Browser_Version = '3';}				
			}
			elseif (self::contains('safari',self::$USER_AGENT))
			{
				self::$Browser = 'Safari';
				self::$Type = 'browser';
				if (self::contains('safari/48',self::$USER_AGENT)) {self::$Browser_Version = '0.48';}
				elseif (self::contains('Version/5',self::$USER_AGENT)) {self::$Browser_Version = '5';}
				elseif (self::contains('safari/49',self::$USER_AGENT)) {self::$Browser_Version = '0.49';}
				elseif (self::contains('safari/51',self::$USER_AGENT)) {self::$Browser_Version = '0.51';}
				elseif (self::contains('safari/60',self::$USER_AGENT)) {self::$Browser_Version = '0.60';}
				elseif (self::contains('safari/61',self::$USER_AGENT)) {self::$Browser_Version = '0.61';}
				elseif (self::contains('safari/62',self::$USER_AGENT)) {self::$Browser_Version = '0.62';}
				elseif (self::contains('safari/63',self::$USER_AGENT)) {self::$Browser_Version = '0.63';}
				elseif (self::contains('safari/64',self::$USER_AGENT)) {self::$Browser_Version = '0.64';}
				elseif (self::contains('safari/65',self::$USER_AGENT)) {self::$Browser_Version = '0.65';}
				elseif (self::contains('safari/66',self::$USER_AGENT)) {self::$Browser_Version = '0.66';}
				elseif (self::contains('safari/67',self::$USER_AGENT)) {self::$Browser_Version = '0.67';}
				elseif (self::contains('safari/68',self::$USER_AGENT)) {self::$Browser_Version = '0.68';}
				elseif (self::contains('safari/69',self::$USER_AGENT)) {self::$Browser_Version = '0.69';}
				elseif (self::contains('safari/70',self::$USER_AGENT)) {self::$Browser_Version = '0.70';}
				elseif (self::contains('safari/71',self::$USER_AGENT)) {self::$Browser_Version = '0.71';}
				elseif (self::contains('safari/72',self::$USER_AGENT)) {self::$Browser_Version = '0.72';}
				elseif (self::contains('safari/73',self::$USER_AGENT)) {self::$Browser_Version = '0.73';}
				elseif (self::contains('safari/74',self::$USER_AGENT)) {self::$Browser_Version = '0.74';}
				elseif (self::contains('safari/80',self::$USER_AGENT)) {self::$Browser_Version = '0.80';}
				elseif (self::contains('safari/83',self::$USER_AGENT)) {self::$Browser_Version = '0.83';}
				elseif (self::contains('safari/84',self::$USER_AGENT)) {self::$Browser_Version = '0.84';}
				elseif (self::contains('safari/85',self::$USER_AGENT)) {self::$Browser_Version = '0.85';}
				elseif (self::contains('safari/90',self::$USER_AGENT)) {self::$Browser_Version = '0.90';}
				elseif (self::contains('safari/92',self::$USER_AGENT)) {self::$Browser_Version = '0.92';}
				elseif (self::contains('safari/93',self::$USER_AGENT)) {self::$Browser_Version = '0.93';}
				elseif (self::contains('safari/94',self::$USER_AGENT)) {self::$Browser_Version = '0.94';}
				elseif (self::contains('safari/95',self::$USER_AGENT)) {self::$Browser_Version = '0.95';}
				elseif (self::contains('safari/96',self::$USER_AGENT)) {self::$Browser_Version = '0.96';}
				elseif (self::contains('safari/97',self::$USER_AGENT)) {self::$Browser_Version = '0.97';}
				elseif (self::contains('safari/125',self::$USER_AGENT)) {self::$Browser_Version = '1.25';}
			}
			elseif (self::contains('sextatnt',self::$USER_AGENT))
			{
				self::$Browser = 'Tango';
				self::$Type = 'browser';
				if (self::contains('sextant v3.0',self::$USER_AGENT)) {self::$Browser_Version = '3.0';}
			}
			elseif (self::contains('sharpreader',self::$USER_AGENT))
			{
				self::$Browser = 'SharpReader';
				self::$Type = 'browser';
				if (self::contains('sharpreader/0.9.5',self::$USER_AGENT)) {self::$Browser_Version = '0.9.5';}
			}
			elseif (self::contains('elinks',self::$USER_AGENT))
			{
				self::$Browser = 'ELinks';
				self::$Type = 'browser';
				if (self::contains('0.3',self::$USER_AGENT)) {self::$Browser_Version = '0.3';}
				elseif (self::contains('0.4',self::$USER_AGENT)) {self::$Browser_Version = '0.4';}
				elseif (self::contains('0.9',self::$USER_AGENT)) {self::$Browser_Version = '0.9';}
			}
			elseif (self::contains('links',self::$USER_AGENT))
			{
				self::$Browser = 'Links';
				self::$Type = 'browser';
				if (self::contains('0.9',self::$USER_AGENT)) {self::$Browser_Version = '0.9';}
				elseif (self::contains('2.0',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
				elseif (self::contains('2.1',self::$USER_AGENT)) {self::$Browser_Version = '2.1';}
			}
			elseif (self::contains('lynx',self::$USER_AGENT))
			{
				self::$Browser = 'Lynx';
				self::$Type = 'browser';
				if (self::contains('lynx/2.3',self::$USER_AGENT)) {self::$Browser_Version = '2.3';}
				elseif (self::contains('lynx/2.4',self::$USER_AGENT)) {self::$Browser_Version = '2.4';}
				elseif ((self::contains('lynx/2.5',self::$USER_AGENT)) || (self::contains('lynx 2.5',self::$USER_AGENT))) {self::$Browser_Version = '2.5';}
				elseif (self::contains('lynx/2.6',self::$USER_AGENT)) {self::$Browser_Version = '2.6';}
				elseif (self::contains('lynx/2.7',self::$USER_AGENT)) {self::$Browser_Version = '2.7';}
				elseif (self::contains('lynx/2.8',self::$USER_AGENT)) {self::$Browser_Version = '2.8';}
			}
			elseif (self::contains('webexplorer',self::$USER_AGENT))
			{
				self::$Browser = 'WebExplorer';
				self::$Type = 'browser';
				if (self::contains('dll/v1.1',self::$USER_AGENT)) {self::$Browser_Version = '1.1';}
			}
			elseif (self::contains('wget',self::$USER_AGENT))
			{
				self::$Browser = 'WGet';
				self::$Type = 'browser';
				if (self::contains('Wget/1.9',self::$USER_AGENT)) {self::$Browser_Version = '1.9';}
				if (self::contains('Wget/1.8',self::$USER_AGENT)) {self::$Browser_Version = '1.8';}
			}
			elseif (self::contains('webtv',self::$USER_AGENT))
			{
				self::$Browser = 'WebTV';
				self::$Type = 'browser';
				if (self::contains('webtv/1.0',self::$USER_AGENT)) {self::$Browser_Version = '1.0';}
				elseif (self::contains('webtv/1.1',self::$USER_AGENT)) {self::$Browser_Version = '1.1';}
				elseif (self::contains('webtv/1.2',self::$USER_AGENT)) {self::$Browser_Version = '1.2';}
				elseif (self::contains('webtv/2.2',self::$USER_AGENT)) {self::$Browser_Version = '2.2';}
				elseif (self::contains('webtv/2.5',self::$USER_AGENT)) {self::$Browser_Version = '2.5';}
				elseif (self::contains('webtv/2.6',self::$USER_AGENT)) {self::$Browser_Version = '2.6';}
				elseif (self::contains('webtv/2.7',self::$USER_AGENT)) {self::$Browser_Version = '2.7';}
			}
			elseif (self::contains('yandex',self::$USER_AGENT))
			{
				self::$Browser = 'Yandex';
				self::$Type = 'browser';
				if (self::contains('/1.01',self::$USER_AGENT)) {self::$Browser_Version = '1.01';}
				elseif (self::contains('/1.03',self::$USER_AGENT)) {self::$Browser_Version = '1.03';}
			}
			elseif ((self::contains('mspie',self::$USER_AGENT)) || ((self::contains('msie',self::$USER_AGENT))) && (self::contains('windows ce',self::$USER_AGENT)))
			{
				self::$Browser = 'Pocket Internet Explorer';
				self::$Type = 'browser';
				if (self::contains('mspie 1.1',self::$USER_AGENT)) {self::$Browser_Version = '1.1';}
				elseif (self::contains('mspie 2.0',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
				elseif (self::contains('msie 3.02',self::$USER_AGENT)) {self::$Browser_Version = '3.02';}
			}
			elseif (self::contains('UP.Browser/',self::$USER_AGENT))
			{
				self::$Browser = 'UP Browser';
				self::$Type = 'browser';
				if (self::contains('Browser/7.0',self::$USER_AGENT)) {self::$Browser_Version = '7.0';}
			}
			elseif (self::contains('msie',self::$USER_AGENT))
			{
				self::$Browser = 'Internet Explorer';
				self::$Type = 'browser';
				if (self::contains('msie 9.0',self::$USER_AGENT)) {self::$Browser_Version = '9.0';}
				elseif (self::contains('msie 8.0',self::$USER_AGENT)) {self::$Browser_Version = '8.0';}
				elseif (self::contains('msie 7.0',self::$USER_AGENT)) {self::$Browser_Version = '7.0';}
				elseif (self::contains('msie 6.0',self::$USER_AGENT)) {self::$Browser_Version = '6.0';}
				elseif (self::contains('msie 5.5',self::$USER_AGENT)) {self::$Browser_Version = '5.5';}
				elseif (self::contains('msie 5.01',self::$USER_AGENT)) {self::$Browser_Version = '5.01';}
				elseif (self::contains('msie 5.23',self::$USER_AGENT)) {self::$Browser_Version = '5.23';}
				elseif (self::contains('msie 5.22',self::$USER_AGENT)) {self::$Browser_Version = '5.22';}
				elseif (self::contains('msie 5.2.2',self::$USER_AGENT)) {self::$Browser_Version = '5.2.2';}
				elseif (self::contains('msie 5.1b1',self::$USER_AGENT)) {self::$Browser_Version = '5.1b1';}
				elseif (self::contains('msie 5.17',self::$USER_AGENT)) {self::$Browser_Version = '5.17';}
				elseif (self::contains('msie 5.16',self::$USER_AGENT)) {self::$Browser_Version = '5.16';}
				elseif (self::contains('msie 5.12',self::$USER_AGENT)) {self::$Browser_Version = '5.12';}
				elseif (self::contains('msie 5.0b1',self::$USER_AGENT)) {self::$Browser_Version = '5.0b1';}
				elseif (self::contains('msie 5.0',self::$USER_AGENT)) {self::$Browser_Version = '5.0';}
				elseif (self::contains('msie 5.21',self::$USER_AGENT)) {self::$Browser_Version = '5.21';}
				elseif (self::contains('msie 5.2',self::$USER_AGENT)) {self::$Browser_Version = '5.2';}
				elseif (self::contains('msie 5.15',self::$USER_AGENT)) {self::$Browser_Version = '5.15';}
				elseif (self::contains('msie 5.14',self::$USER_AGENT)) {self::$Browser_Version = '5.14';}
				elseif (self::contains('msie 5.13',self::$USER_AGENT)) {self::$Browser_Version = '5.13';}
				elseif (self::contains('msie 4.5',self::$USER_AGENT)) {self::$Browser_Version = '4.5';}
				elseif (self::contains('msie 4.01',self::$USER_AGENT)) {self::$Browser_Version = '4.01';}
				elseif (self::contains('msie 4.0b2',self::$USER_AGENT)) {self::$Browser_Version = '4.0b2';}
				elseif (self::contains('msie 4.0b1',self::$USER_AGENT)) {self::$Browser_Version = '4.0b1';}
				elseif (self::contains('msie 4',self::$USER_AGENT)) {self::$Browser_Version = '4.0';}
				elseif (self::contains('msie 3',self::$USER_AGENT)) {self::$Browser_Version = '3.0';}
				elseif (self::contains('msie 2',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
				elseif (self::contains('msie 1.5',self::$USER_AGENT)) {self::$Browser_Version = '1.5';}
			}
			elseif (self::contains('iexplore',self::$USER_AGENT))
			{
				self::$Browser = 'Internet Explorer';
				self::$Type = 'browser';
			}
			elseif (self::contains('mozilla',self::$USER_AGENT)) // (2) netscape nie je prilis detekovatelny....
			{
				self::$Browser = 'Netscape';
				self::$Type = 'browser';
				if (self::contains('mozilla/4.8',self::$USER_AGENT)) {self::$Browser_Version = '4.8';}
				elseif (self::contains('mozilla/4.7',self::$USER_AGENT)) {self::$Browser_Version = '4.7';}
				elseif (self::contains('mozilla/4.6',self::$USER_AGENT)) {self::$Browser_Version = '4.6';}
				elseif (self::contains('mozilla/4.5',self::$USER_AGENT)) {self::$Browser_Version = '4.5';}
				elseif (self::contains('mozilla/4.0',self::$USER_AGENT)) {self::$Browser_Version = '4.0';}
				elseif (self::contains('mozilla/3.0',self::$USER_AGENT)) {self::$Browser_Version = '3.0';}
				elseif (self::contains('mozilla/2.0',self::$USER_AGENT)) {self::$Browser_Version = '2.0';}
			}
		}
		
		/**
		 * Get browser info
		 * @param string $UA
		 */
		public static function get($UA){
			self::$USER_AGENT = $UA;
			self::Resolve();
			self::$Resolved = true;

			$arr=array();
			$arr['user_agent']=self::$USER_AGENT;
			$arr['os']=self::$OS;
			$arr['os_version']=self::$OS_Version;
			$arr['browser']=self::$Browser;
			$arr['browser_version']=self::$Browser_Version;
			$arr['net_clr']=self::$NET_CLR;
			$arr['resolved']=self::$Resolved;
			$arr['type']=self::$Type;

			return $arr;
		}
		
		/**
		 * Get user ip
		 */
		public static function get_user_ip(){
			 if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		    {
		      $ip=$_SERVER['HTTP_CLIENT_IP'];
		    }
		    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		    {
		      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		    }
		    else
		    {
		      $ip=isset_or($_SERVER['REMOTE_ADDR']);
		    }
		    return $ip;
		}
	}
?>
