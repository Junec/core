<?php

class Core_Cache_Redis extends Core_Cache_Abstract{
	public function __construct(){

	}

	public function getRedis( $host = "", $port = 6379 ){
		$this->obj = Core_Loader::getInstance("Redis");
		$this->obj->connect($host, $port);
		return $this->obj;
	}
}

?>