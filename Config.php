<?php

class Core_Config{
	protected $config;
	
	public function __construct( $config ){
		$this->config = include_once $config;

	}

	public function __get( $var ){
		return $this->config[ $var ];
	}

	public function get( $key ){
		return $this->config[ $key ];
	}
}

?>