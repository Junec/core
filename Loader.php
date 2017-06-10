<?php

class Core_Loader{

	static protected $instance;
	

	static public function autoload( $class ){
		$path = "";
		$classLen = strlen($class);

		if(substr($class, 0, 5) == 'Core_'){
			$path = CORE_PATH . "/" . str_replace("_", "/", substr($class, 5));

		}elseif(substr($class, $classLen - 10) == 'Controller'){
			$path = APP_PATH . "/controllers/" . str_replace("_", "/", substr($class, 0, $classLen - 10));

		}elseif(substr($class, $classLen - 5) == 'Model'){
			$path = APP_PATH . "/models/" . str_replace("_", "/", substr($class, 0, $classLen - 5));

		}elseif(substr($class, $classLen - 6) == 'Widget'){
			$path = APP_PATH . "/widget/" . str_replace("_", "/", substr($class, 0, $classLen - 6));

		}else{
			$path = APP_PATH . "/library/" . str_replace("_", "/", $class);

		}

		$path .= ".php";
		if(file_exists($path)){
			include_once $path;
		}else{
			throw new Core_Exception("include file not exists: {$path}");
		}
		
	}

	static public function getInstance( $class , $param = '' ){
        if( !isset(self::$instance[$class]) || self::$instance[$class] == '' ){
        	if(empty($param)){
        		self::$instance[$class] = new $class();
        	}else{
        		self::$instance[$class] = new $class( $param );
        	}
        }
        return self::$instance[$class];
	}


}

?>