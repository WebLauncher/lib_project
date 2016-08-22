<?php
/**
 * Cache Manager Class
 */
/**
 * Cache manager class
 * @package WebLauncher\Managers
 */
class CacheManager
{
    /**
     * Pools objects
     */
    private $_pools = array();
    
    /**
     * Default pool
     */
    private $_default = 0;

    /**
     * Constructor
     *
     * @return none
     */
    public function __construct($options = array())
    {
        foreach ($options as $k => $v) {
            $obj = null;
            switch($v['type']) {            
            case 'composite' :
                if(isset($v['drivers'])){
                    $drivers=array();
                    foreach($v['drivers'] as $driver)
                        $drivers[]=$this->_initDriver($driver);
                    $opts=array('drivers'=>$drivers);
                    $obj=new Stash\Driver\Composite($opts);
                }
                break;
            default:
                $obj=$this->_initDriver($v);
                break;
            }
            if(isset($v['default']))
                $this->_default=$k;
            $this->_pools[$k] = new Stash\Pool($obj);
        }
    }

    /**
     * Init cache driver
     * 
     * @param array $driver Driver array configurations
     * 
     * @return object
     */
    private function _initDriver($driver)
    {
        $obj = null;
        switch($driver['type']) {
        case 'file' :
            $obj = new Stash\Driver\FileSystem();
            break;
        case 'sqlite' :
            $obj = new Stash\Driver\Sqlite();
            break;
        case 'apc' :
            $obj = new Stash\Driver\Apc();
            break;
        case 'memcached' :
            $obj = new Stash\Driver\Memcache();
            break;
        case 'redis' :
            $obj = new Stash\Driver\Redis();
            break;
        }
        if (isset($driver['options']))
            $obj->setOptions($driver['options']);
        return $obj;
    }

    /**
     * Get cache engine
     * 
     * @param string $name Name of the cache engine
     * 
     * @return object
     */
    public function getEngine($name){
        if($name){
            return $this->_pools[$name];
        } else {
            return $this->_pools[$this->_default];
        }
    }
    
    /**
     * Invoke method to get cache engine
     * 
     * @param string $name Name of the cache engine
     * 
     * @return object
     */
    public function __invoke($name){
        return $this->getEngine($name);
    }
    
    /**
     * Call magic method
     * 
     * @param string $name Method name
     * @param mixed $arguments Method arguments
     * 
     * @return object
     */
    public function __call($name, $arguments){
        if($this->_default)
        {
            return call_user_func_array(array(
                        $this->_pools[$this->_default],
                        $name
                    ), $arguments);
        }
    }
}
?>