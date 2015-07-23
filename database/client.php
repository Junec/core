<?php 
/**
 * 
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_client{
    private $instance;

    private function __construct(){}
    public  function __clone(){}

    static public function factory($type = '',$options = array()){
    	$client = "core_database_adapter_{$type}";
        if(class_exists($client)){
        	return core::instance($client,$options);
        }else{
        	throw new core_exception("database client error: ".$client." is not exists.");
        }

    }

}

?>