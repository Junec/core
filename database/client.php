<?php 
/**
 * 数据库工厂
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_client{
    private $instance;

    private function __construct(){}
    public  function __clone(){}

    static public function factory($type = '',$params = array()){
    	$client = "core_database_factory_{$type}";
        if(class_exists($client)){
        	return core::instance($client,$params);
        }else{
        	throw new core_exception("database client error: ".$client." is not exists.");
        }

    }

}

?>