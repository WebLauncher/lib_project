<?php
/**
 * Image processing
 */
 
/**
 * Image Processing Manager Class
 * @package WebLauncher\Managers
 */
class ImageManager
{
	/**
	 * Save resized image proportionally to file
	 * @param string $file_path File path of the image
	 * @param string $filetype File type of the image
	 * @param string $tofile Location where to save the file
	 * @param int $width Width of the resized image
	 * @param int $height Height of the resized image
	 */
	function output_resized_image_proportional($file_path,$filetype,$tofile,$width,$height)
	{
		$img=WideImage::load($file_path,$filetype);
		$img->resize($width,$height)->saveToFile($tofile);
	}
	
	/**
	 * Display resuze image proportionally
	 * @param string $file_path File path of the image
	 * @param int $width Width of the image
	 * @param int $height Height of the image
	 * @param string $fit ['crop','canvas','inside','outside','fill']
	 * @param string $cache_path Path where image should be cached
	 * @param bool $display Flag if image should be displayed directly
	 */
	static function get_resized_image_proportional($file_path,$width,$height,$fit='',$cache_path='',$display=true)
	{
		// check cache
		if(is_file($cache_path))
		{
			if($display)
				self::output($cache_path);
		}
		try{
			$img=WideImage::load($file_path);
			if($fit=='crop'){
				$img=$img->resize($width,$height,'outside')->crop('center','middle',$width,$height);
			}
			elseif($fit=='canvas'){
				$img=$img->resize($width,$height,'inside')->resizeCanvas($width, $height, 'center', 'center');
			}
			else{
				$img=$img->resize($width,$height,$fit);
			}
			if($cache_path){
				$img->saveToFile($cache_path);
			}
			if($display)
				$img->output('png');
			$img->destroy();
			
		}
		catch(Exception $ex)
		{
			echo $ex->getMessage();
			echo $ex->getTrace();
		}
	}
	
	/**
	 * Output image
	 * @param string $file_path File path of the image
	 * @param string $format Format of the image
	 */
	static function output($file_path,$format='png'){
		try{
			$img=WideImage::load($file_path);
			$img->output($format);
		}
		catch(Exception $ex){
			echo $ex->getMessage();
			echo $ex->getTrace();
		}
	}
	
	/**
	 * Get image handle 
	 * @param string $file_path File path of the image
	 * @return object Image 
	 */
	static function get_handle($file_path){
		$img=WideImage::load($file_path);
		return $img->get_handle();
	}
	
	/**
	 * Apply watermark to file
	 * @param string $file_path
	 * @param string $watermark_path
	 * @param string $pos_left
	 * @param string $pos_top
	 * @param int $opacity
	 */
	static function apply_watermarked($file_path,$watermark_path,$pos_left='left',$pos_top='top',$opacity=100){
		$img = WideImage::load($file_path);
		$watermark = WideImage::load($watermark_path);
		$new = $img->merge($watermark, $pos_left, $pos_top, $opacity);
		$new->saveToFile($file_path);
		$img->destroy();
		$new->destroy();
		$watermark->destroy();
	}
	
	/**
	 * Import image from url
	 * @param string $url
	 * @param string $file
	 */
	function import_url($url,$file)
	{
		$img=WideImage::load($url);
		$img->saveToFile($file);
	}
}
?>