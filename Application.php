<?php

class Core_Application{
	
	protected $config;

	public function __construct( $config ){
		$this->config = $config;
	}

	public function getConfig(){
		return Core_Loader::getInstance("Core_Config", $this->config );
	}

	public function init(){
		include_once "Loader.php";
		header("Content-type:text/html;charset=utf-8");
		spl_autoload_register(array('Core_Loader','autoload'));
		date_default_timezone_set('Asia/Shanghai');
		Core_Registry::set("config", $this->getConfig() );

		define("BASE_PATH",rtrim(Core_Registry::get("config")->application['base_path'],"/"));
	}

	public function run(){
		try{
			$this->init();
			Core_Loader::getInstance("Core_Dispatcher")->dispatch();

		}catch(Exception $e){
			Core_Loader::getInstance("Core_Handler")->exception($e);
			
		}
		
	}
}

?>