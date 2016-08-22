<?php
	/**
	 * History Manager Class
	 */
	/**
	 * Url History Manager Class
	 * @package WebLauncher\Managers
	 */
	class HistoryManager
	{
		/**
		 * @var int $dimension Dimension of history log
		 */
		var $dimension=10;
		/**
		 * @var array $session Session var
		 */
		var $session=array();
		
		/**
		 * Constructor
		 * @param object $session
		 * @param string $url
		 */
		function __construct(&$session,$url='')
		{
			$this->session=&$session;
			if($url)
			{
				$this->add($url);
			}
		}
		
		/**
		 * Add url
		 * @param string $url
		 */
		function add($url)
		{
			$hist=array();
			$this->save_in_session($url);
		}
		
		/**
		 * Save in session
		 * @param string $url
		 */
		function save_in_session($url)
		{
			if(isset($this->session['history']))
			{
				$str=$this->session['history'];
				$hist=unserialize(base64_decode($str));
				if(is_array($hist))
				{
					if(count($hist)>$this->dimension)
						array_splice($hist,1,1);
					if($hist[count($hist)-1]!=$url)
						$hist[count($hist)]=$url;
				}
				else
				{
					$hist[0]=$url;
				}
			}
			else
			{
				$hist[0]=$url;
			}
			$this->session['history']=base64_encode(serialize($hist));
		}
		
		/**
		 * get history
		 */
		function get_history()
		{
			return $this->get_from_session();
		}
		
		/**
		 * get from session
		 */
		function get_from_session()
		{
			if(isset($this->session['history']))
			{
				$hist=unserialize(base64_decode($this->session['history']));
				if(is_array($hist))
				{
					return array_reverse($hist);
				}
				else return array();
			}
			else
			{
				return array();
			}
		}

	}

?>
