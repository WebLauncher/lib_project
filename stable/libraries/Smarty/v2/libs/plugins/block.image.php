<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {image original width height class alt}{/image} block plugin
 *
 * Type:     block function<br>
 * Name:     image<br>
 * Purpose:  displays the image from the db table images at the given id
 * @author Mihai Varga
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content translated
 */
function smarty_block_image($params, $content, &$smarty)
{
	global $page;
	$alt=isset_or($params['alt'])?$params['alt']:$page->title;
	$default=isset_or($params['default'],'no default');
	$width=isset_or($params['width']);
	$height=isset_or($params['height']);
	$fit=isset_or($params['fit']);
	$resize=isset($params['resize'])?$params['resize']=="true":false;
	$class=isset($params['class'])?"class='".$params['class']."'":"";
	$title=isset($params['title'])?"title='".$params['title']."'":"";
	$align=isset($params['align'])?"align='".$params['align']."'":"";
	$watermark=isset_or($params['watermark']);
	$watermark_left=isset_or($params['watermark_left'],'left');
	$watermark_top=isset_or($params['watermark_top'],'top');
	
	global $page;
	if(!$page->models){
		if($page->trace)
			return 'Init DAL';
		else
			$content='';
	}
	if(!$content && isset_or($default))
		$content=$default;
	
	$content=trim($content);
	if(is_numeric($content)){	
		$obj=$page->models->images->get($content);
		$image_path=$obj['original_http_path'];
	}
	else
		$image_path=$content;

	if($resize)
	{		
		// get cache path
		$path = str_replace($page -> paths['root'], $page -> paths['dir'], $image_path);
		$img_cache = $page -> paths['root_cache'] . 'img_mod/';		
		$cache_filename=generate_seo_link($alt).'_'.sha1($path . $width . $height . $fit.$watermark.$watermark_left.$watermark_top) ;
		$cache_path = $img_cache .$cache_filename . '.jpg';
		if(is_file($cache_path))
			$image_path=$page->paths['root'].$page->cache_folder.'/img_mod/'.$cache_filename. '.jpg';
		else{
			$image_path=$page->paths['root']."img_mod/?_file=".urlencode($image_path)."&_width=".$width."&_height=".$height.'&_fit='.$fit;
			if($watermark)
				$image_path.=('&_w='.urlencode($watermark).'&_w_left='.$watermark_left.'&_w_top='.$watermark_top);
			$image_path.=('&name='.$cache_filename.'.jpg');
		}
	}
	else
	{
		$style="";
		if($width)
			$style.="width:".$width."px;";
		if($height)
			$style.="height:".$height."px";
		if($style)
			$style='style="'.$style.'"';
	}
	if(isset_or($params['get_path']))
		return $image_path;
	else
		return "<img src='".$image_path."' $align $title alt='".$alt."' border='0' $style $class/>";	
}

/* vim: set expandtab: */

?>
