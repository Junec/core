<?php 
/**
 * 
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_factory{
    private $instance;

    private function __construct(){}
    public  function __clone(){}

    static public function getInstance($type = '',$options = array()){
    	$client = "core_database_adapter_{$type}";
        if(class_exists($client)){
        	return core::instance($client,$options);
        }else{
        	throw new core_exception("database client error: ".$client." is not exists.");
        }

    }

}

?>