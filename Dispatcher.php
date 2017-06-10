<?php

class Core_Dispatcher{
	
	protected function getRouter(){
		return Core_Loader::getInstance("Core_Router");
	}

	public function dispatch(){
		$router = $this->getRouter();
		$controller = $router->getController();
		$controllers = explode("_",$controller);
		foreach($controllers as $k => $v){
			$controllers[$k] = ucfirst($v);
		}
		$controllerName = join("_",$controllers)."Controller";
		$actionName = $router->getAction();

		$controller = Core_Loader::getInstance($controllerName);
		$controller->exec($actionName);
	}



}

?>