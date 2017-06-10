<?php

class Core_Cache_Memcached extends Core_Cache_Abstract{
	public function __construct(){
		
	}

	public function getMemcached( $host = array() ){
		$this->obj = Core_Loader::getInstance("Memcached");
        foreach($host as $v){
            $v = trim($v);
            $tmp = explode(':', $v);
            $hosts[] = array($tmp[0],$tmp[1]);
        }
        $this->obj->addServers($hosts);
        return $this->obj;
	}
}

?>