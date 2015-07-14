<?php 
/**
 * 缓存类工厂
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_cache_client{
    private $instance;

    private function __construct(){}
    public  function __clone(){}

    static public function factory($type = '',$params = array()){
    	$client = "core_cache_factory_{$type}";
        if(class_exists($client)){
        	return core::instance($client,$params);
        }else{
        	throw new core_exception("cache client error: ".$client." is not exists.");
        }

    }

}

?>