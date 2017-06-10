<?php

class Core_Controller_Abstract{
	
	protected $request;

	public function exec($action){
		if( method_exists($this, $action) ){
			$this->$action();
		}else{
			$controller = get_class($this);
			throw new Core_Exception("Action not found: {$controller}::{$action}()");
		}
	}

	public function getView(){
		return Core_Loader::getInstance("Core_View_Simple");
	}

	public function getRequest(){
		return Core_Loader::getInstance("Core_Request_Abstract");
	}

}

?>