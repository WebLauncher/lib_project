<?php	
	class AdminPage extends Page
	{
		public static $menu = array();
		public static $breadcrumb=array();

		var $version = "1.0.0";

		function _on_init()
		{
			parent::_on_init();

			if($this -> system -> logged)
			{
				// get users permissions
				if(isset_or($this->user['type_id']) && method_exists($this->models->administrators, 'get_permissions'))
					$this->user['permissions']=$this->models->administrators->get_permissions($this->user['type_id']);
				
				// get menu
				$xmlp = new Xml_Parser();
				$xmlp -> load($this -> system -> paths['root_dir'] . 'modules/admin/menu.xml');
				$admin_menu = $xmlp -> xmlToArray();
				self::$menu = $admin_menu['menu']['item'];	
				self::$breadcrumb= $this -> _getMap();
				$this->title=$this -> _getTitle();
				$this -> assign("page_title",$this->title);
			}
		}
		
		function _on_load()
		{
			parent::_on_load();
			if($this->system->logged)
			{
				$this -> assign("admin_menu", $this -> _getMenu());
				$this -> assign("admin_menu_page", $this -> _currentMenu());
				$this -> assign("page_map", self::$breadcrumb);
				$this->system->title=$this->system->title.' - '.tr($this->title);
			}
		}

		function _getMenu()
		{
			$menu = self::$menu;
			usort($menu, function($a, $b)
			{
				if($a['order'] == $b['order'])
				{
					return 0;
				}
				return ($a['order'] < $b['order']) ? -1 : 1;
			});			
			// filter master user menu
			$menu=$this->__filter_menu($menu);
			return $menu;
		}
		
		function __filter_menu($menu){
			foreach($menu as $k=>$v){
				if(isset($v['submenu']['item']))
					$menu[$k]['submenu']['item']=$this->__filter_menu($menu[$k]['submenu']['item']);
			}	
			if(isset($menu[0]))
				$menu=array_filter($menu,array($this,'__menu_filter'));
			return $menu;
		}
		
		function __menu_filter($var){
			$visible=(!$this->user['is_master'] && !isset_or($var['master']));			
			if(isset($this->user['permissions']) && isset($var['permissions'])){
				$perms=explode(',', $var['permissions']);
				$found=false;
				foreach($perms as $v){
					if(isset($this->user['permissions'][trim($v)]))
						$found=true;
				}
				$visible=$found;
			}
            if(isset($this -> user['permissions']) && (array_key_exists('cms', $this->user['permissions']) || array_key_exists('employee_logins', $this->user['permissions'])))
            {
               return true;
            }else{			
			return $visible || $this->user['is_master'];
            }
		}

		function _currentMenu()
		{
			$current = $this -> _getSubmenu(self::$menu, $this -> system -> content);
			if($this -> system -> component && isset($current['submenu']['item']))
			{
				$current = $this -> _getSubmenu($current['submenu']['item'], $this -> system -> component);
				if(isset($current['submenu']['item']))
				{
					foreach($current['submenu']['item'] as $k => $v)
					{
						$current['submenu']['item'][$k]['link'] = $v['link'];
					}
				}
			}
			$current['levels'] = $this -> _getMenuLevel($current);
			return $current;
		}

		function _getSubmenu($menu, $link)
		{
			foreach($menu as $v)
				if(isset($v['link']) && $link && strpos($v['link'], $link) !== false)
					return $v;
		}

		function _getMenuLevel($menu)
		{
			if(isset($menu['submenu']['item']))
				return $this -> _getMenuLevel($menu['submenu']['item'][0]) + 1;
			return 0;
		}

		function _getTitle()
		{
			$current = $this -> _currentMenu();
			return isset($current['name']) ? $current['name'] : "Home";
		}

		function _getMap()
		{
			$map = array( array(
					"link" => $this -> system -> paths['root_module'],
					"name" => "Home"
				));
			$current = self::$menu;
			$link = $this -> system -> paths['root_module'];

			foreach($this->system->subquery as $k => $sub)
			{
				if($k > 0)
				{
					$cr = $this -> _getSubmenu($current, $sub);				
					if(isset($cr['name']) && strpos($cr['link'], $sub) !== false)
					{
						$link .= ($cr['link'] . "/");
						$map[] = array(
							"link" => $this -> system -> paths['root_module'] . $cr['link'],
							"name" => $cr['name']
						);
						if(isset($cr['submenu']))
							$current = $cr['submenu']['item'];
					}
				}
			}

			return $map;
		}

	}
?>