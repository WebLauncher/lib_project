<?php
/**
 * Captcha generator
 */
/**
 * Signature/Captcha Generator/Manager Class
 * @package WebLauncher\Managers
 */
class SignatureManager
{
	/**
	 * @var array $session Session object
	 */
	var $session='';
	
	/**
	 * Constructor
	 * @param array $session
	 */
	function __construct(&$session)
	{
		$this->session=&$session;
	}
	
	/**
	 * Display captcha
	 * @param int $length
	 * @param string $font_path
	 */
	function display($length=5,$font_path='font.ttf')
	{
		sleep(1);
		if(!isset($this->session['signature']))
			$text=strtolower(substr(md5(microtime()),0,$length));
		else
			$text=$this->session['signature'];
		$this->session['signature']=$text;
		
		global $page;
		$page->save_session();

		$my_img = imagecreate( $length*20, 30 );
		$background = imagecolorallocate( $my_img, 255, 255, 255 );
		$text_colour = imagecolorallocate( $my_img, 0, 0, 0 );
		$line_colour = imagecolorallocate( $my_img, 60, 60, 60 );
		
		imagesetthickness ( $my_img, 1 );
		$i=0;
		while($i<200)
		{
			$x=rand(0,100);
			$y=rand(0,30);
			imageline($my_img,$x,$y,$x,$y,$line_colour);
			$i++;
		}
		imageline( $my_img, 0, 22, 100, 20, $line_colour );
		imageline( $my_img, 0, 12, 100, 18, $line_colour );
		imageline( $my_img, 0, 10, 100, 6, $line_colour );
		imagearc($my_img, 10, 10, 200, 20, 0, 180, $line_colour);
		
		imagefttext($my_img, 18, -5, 10, 22, $text_colour, $font_path, $text);		

		$i=0;
		while($i<100)
		{
			$x=rand(0,100);
			$y=rand(0,30);
			imageline($my_img,$x,$y,$x,$y,$background);
			$i++;
		}

		header( 'Content-Type: image/jpeg' );
		imagejpeg( $my_img );
		imagecolordeallocate( $line_color );
		imagecolordeallocate( $text_color );
		imagecolordeallocate( $background );
		imagedestroy( $my_img );
	}
}

?>