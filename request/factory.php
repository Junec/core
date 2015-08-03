<?php 
/**
 * 请求类工厂
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_request_factory{
    private $instance;

    private function __construct(){}
    public  function __clone(){}

    static public function getInstance($type = '',$timeout = 0){
    	$client = "core_request_factory_{$type}";
        if(class_exists($client)){
        	return core::instance($client,$timeout);
        }else{
        	throw new core_exception("request client error: ".$client." is not exists.");
        }

    }

}

?>