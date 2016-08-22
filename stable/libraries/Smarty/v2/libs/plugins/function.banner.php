<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {banner} function plugin
 *
 * Type:     function<br>
 * Name:     banner<br>
 * Purpose:  display banner in template<br>
 *
 * @author   Mihai Varga
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_banner($params, &$smarty)
{	
	global $page;
    if (empty($params['banner_id']) && empty($params['zone'])) {
        $smarty->trigger_error("validator: missing 'banner_id' or 'zone' parameter");
        return;
    }

    $banner_id = isset_or($params['banner_id']);
    $zone_name=isset_or($params['zone']);
    $class=isset_or($params['class']);

    if($banner_id)
    {

    	$banner=$page->models->banners->get($banner_id);
		$zone=$page->models->banners_zones->get($banner['zone_id']);
    }
    else
    {
		if($zone_name!='')
		{
			if($page->models)
			{
				$banner=$page->models->banners->GetBanner($zone_name);
				$zone=$page->models->banners_zones->get_cond('name="'.$zone_name.'"');
			}
		}
    }
    $response='<div style="overflow:hidden;width:'.$zone['width'].'px;height:'.$zone['height'].'px class="'.$class.'">';
    if (isset($banner['id']))
    {
    	switch($banner['type']['name'])
    	{
    		case 'image':
    			if(!function_exists('smarty_block_image'))
    				require_once dirname(__FILE__).'/block.image.php';
    			$refresh=1;
    			$response.='<center><a href="'.$banner['link'].'" target="'.$banner['target'].'">'.smarty_block_image(array(), $banner['content'], $smarty,$refresh).'</a></center>';
    		break;
    		case 'flash':
    			$response.='<div id="banner_'.$banner['id'].'>';
    			$response.=$page->paths['root'].$banner['content'];
    			$response.='</div>';
    			$response.='<script type="text/javascript">';
    			$response.='swfobject.embedSWF("'.$page->paths['root'].$banner['content'].'", "banner_'.$banner['id'].'", "'.$zone['width'].'", "'.$zone['height'].'", "9.0.0","'.$page->paths['root_scripts'].'swfobject/expressInstall.swf","",{wmode:\'transparent\'});';
    			$response.='</script>';
    		break;
    		case 'script':
    			$response.='<script type="text/javascript">';
    			$response.=$banner['content'];
    			$response.='</script>';
    		break;
    		case 'text':
    		case 'html':
    			$response.=$banner['content'];
    		break;
    	}
    }
    else
    {
		if ($page->live)
			$response.='<div class="banner">Banner Zone</div>';
		else
			$response.='- banner zone ['.$zone_name.'] not found -';
    }
    $response.='</div>';
    return $response;
}

/* vim: set expandtab: */

?>
