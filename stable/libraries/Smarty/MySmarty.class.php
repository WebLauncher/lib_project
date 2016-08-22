<?php
class TemplatesManager
{
	protected static $smarty=null;
	protected static $ver='v2';
	public static function get_engine($ver='v2',$template_dir='',$cache_dir='',$trace=false,$debug=false,$cache_enabled=false)
	{
		self::$ver=$ver;
		switch($ver)
		{
			case 'v2':
				require_once dirname(__FILE__).'/v2/libs/Smarty.class.php';
				self::$smarty = new Smarty;
				self::$smarty->template_dir=$template_dir;
				self::$smarty->compile_dir=$cache_dir;
				
				self::$smarty->cache_dir=$cache_dir.'smarty_cache/';
				if(!is_dir($cache_dir.'smarty_cache/'))
					mkdir($cache_dir.'smarty_cache/');
				if($cache_enabled)
				{
					self::enable_cache();
				}
				if($debug)
					self::$smarty->error_reporting=E_ALL ^ E_NOTICE;			
				if(!$trace)
					self::$smarty->load_filter('output', 'trimwhitespace');
			break;
			case 'v3':
				require_once dirname(__FILE__).'/v3/libs/Smarty.class.php';
				self::$smarty= new Smarty();
				self::$smarty->setTemplateDir($template_dir);
				self::$smarty->setCompileDir($cache_dir);	
				self::$smarty->allow_php_templates=true;
				self::$smarty->auto_literal = false;
				self::$smarty->error_unassigned = true;
				if($debug)
				{
					self::$smarty->debugging=false;
					self::$smarty->compile_check = true;
					self::$smarty->error_reporting=E_ALL & ~E_NOTICE;
				}
				else
				{
					self::$smarty->debugging=false;
					self::$smarty->compile_check = false;
					self::$smarty->error_reporting=0;
				}
				if(!$trace)
					self::$smarty->loadFilter('output', 'trimwhitespace');			
			break;		
		}
		return self::$smarty;
	}
	
	public static function set_template_dir($dir='')
	{
		switch(self::$ver){
			case 'v2':
				self::$smarty->template_dir=$dir;
			break;
			case 'v3':
				self::$smarty->setTemplateDir($dir);
			break;
		}
	}

	public static function get_template_dir()
	{
		switch(self::$ver){
			case 'v2':
				return self::$smarty->template_dir;
			break;
			case 'v3':
				$dirs=self::$smarty->getTemplateDir();
				return $dirs[0];
			break;
		}
	}
	
	public static function set_compile_dir($dir)
	{
		switch(self::$ver){
			case 'v2':
				self::$smarty->compile_dir=$dir;
			break;
			case 'v3':
				self::$smarty->setCompileDir($dir);
			break;
		}
	}
	
	public static function get_compile_dir($dir)
	{
		switch(self::$ver){
			case 'v2':
				return self::$smarty->compile_dir;
			break;
			case 'v3':
				self::$smarty->getCompileDir();
			break;
		}
	}
	
	public static function get_template_var($var)
	{
		switch(self::$ver)
		{
			case 'v2':
				return self::$smarty->get_template_vars($var);
			break;
			case 'v3':
				return isset_or(self::$smarty->tpl_vars[$var]->value);
			break;
		}
	}
	
	public static function enable_cache(){
		switch(self::$ver)
		{
			case 'v2':
				self::$smarty->caching=2;
				self::$smarty->cache_lifetime = 3600;
				self::$smarty->compile_check = false;
			break;
			case 'v3':				
			break;
		}
	}
	
	public static function disable_cache(){
		switch(self::$ver)
		{
			case 'v2':
				self::$smarty->caching=0;	
				self::$smarty->compile_check = true;			
			break;
			case 'v3':				
			break;
		}
	}
	
	public static function set_cache($enabled=true){
		if($enabled)
			self::enable_cache();
		else 
			self::disable_cache();		
	}
	
	public static function clear_cache($cache_id){
		switch(self::$ver)
		{
			case 'v2':
				self::$smarty->clear_cache(null,$cache_id);					
			break;
			case 'v3':				
			break;
		}
	}
	
	public static function is_cached($template,$cache_id='',$compile_id=null){
		switch(self::$ver)
		{
			case 'v2':
				$old=self::$smarty->caching;
				self::$smarty->caching = true;
				$is_cached= self::$smarty->is_cached($template,$cache_id,$compile_id);
				self::$smarty->caching = $old;
				return $is_cached;					
			break;
			case 'v3':				
				return self::$smarty->isCached($template,$cache_id,$compile_id);
			break;
		}
	}
}

?>