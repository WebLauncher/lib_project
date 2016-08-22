<?php
	/**
	 * Tables Manager Class
	 */
	/**
	 * DB Tables Manager
	 * @package WebLauncher\Managers
	 */
	class TablesManager implements ArrayAccess{
		/**
		 * ArrayAccess set
		 * @param string $offset 
		 * @param string $value
		 */
		public function offsetSet($offset, $value) {
			global $page;        
	        $page->tables->{$offset} = $value;        
	    }
		/**
		 * ArrayAccess exists
		 * @param string $offset
		 */
	    public function offsetExists($offset) {
	    	global $page;
			if(!isset($page->tables->{$offset}))
				if(strpos($offset,'tbl_')==0) $page->tables->{$offset}=substr ($offset , 4 , strlen($offset)-4);
	        return isset($page->tables->{$offset});
	    }
		/**
		 * ArrayAccess unset
		 * @param string $offset
		 */
	    public function offsetUnset($offset) {
	    	global $page;
	        unset($page->tables->{$offset});
	    }
		/**
		 * ArrayAccess get
		 * @param string $offset
		 */
	    public function offsetGet($offset) {
	    	global $page;
	        return isset($page->tables->{$offset}) ? $page->tables->{$offset} : (strpos($offset,'tbl_')==0 ?substr ($offset , 4 , strlen($offset)-4):null);
	    }
		/**
		 * Magic method get
		 * @param string $name
		 */
		public function __get($name){
			global $page;
			return $this[$name];
		}
		/**
		 * Magic method set
		 * @param string $name
		 * @param string $value
		 */
		public function __set($name,$value){
			$this[$name]=$value;
		}	
	}
?>
