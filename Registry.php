<?php

class Core_Registry{
	
	static private $instance;

	private function __clone(){}
	private function __construct(){}

	static public function set($name = '', $value = ''){
		self::$instance[ $name ] = $value;
		return true;
	}

	static public function get($name = ''){
		return self::$instance[ $name ];
	}

	static public function del($name = ''){
		if( self::has( $name ) ){
			unset( self::$instance[ $name ] );
			return true;
		}else{
			return false;
		}
	}

	static public function has($name = ''){
		if( isset(self::$instance[ $name ]) ) return true;
		else return false;
	}
}

?>