<?php

class Core_Request_Abstract{

	private $get;
	private $post;
	private $request;
	private $server;

	public function __construct(){
		$this->get = $_GET;
		$this->post = $_POST;
		$this->request = $_REQUEST;
		$this->server = $_SERVER;
	}

	public function getScriptName(){
		return $this->getServer('SCRIPT_NAME');
	}

	public function getRequestUri(){
		return $this->getServer('REQUEST_URI');
	}

	public function getPathInfo(){
		$scriptName = $this->getScriptName();
    	$scriptDirName = dirname($scriptName);
    	$scriptBaseName = basename($scriptName);
		$pathInfo = "";

		$PATH_INFO = $this->getServer('PATH_INFO');
		$ORIG_PATH_INFO = $this->getServer('ORIG_PATH_INFO');
		$REQUEST_URI = $this->getServer('REQUEST_URI');
		$PHP_SELF = $this->getServer('PHP_SELF');

		if(!empty($PATH_INFO)){
            $pathInfo = $this->getServer('PATH_INFO');

        }elseif(!empty($ORIG_PATH_INFO)){
            $pathInfo = $this->getServer('ORIG_PATH_INFO');

        }elseif(!empty($REQUEST_URI)){
            $pathInfo = str_replace(array($scriptDirName,$scriptBaseName),array('',''),$this->getServer('REQUEST_URI'));

        }elseif(!empty($PHP_SELF)){
            $pathInfo = str_replace(array($scriptDirName,$scriptBaseName),array('',''),$this->getServer('PHP_SELF'));

        }

        $pathInfo = trim($pathInfo,"/");
        return $pathInfo;
	}

	public function setGet($key = '', $value = ''){
		$this->get[$key] = $value;
		$_GET[$key] = $value;
	}

	public function setPost($key = '', $value = ''){
		$this->post[$key] = $value;
		$_POST[$key] = $value;
	}

	public function get($key = ''){
		if( isset($this->get[$key]) ) return $this->get[$key];
		return false;		
	}

	public function getPost($key = ''){
		if( isset($this->post[$key]) ) return $this->post[$key];
		return false;	
	}

	public function gets(){
		return $this->get;
	}

	public function getPosts(){
		return $this->post;
	}

	public function getServer($key = ''){
		if( isset($this->server[$key]) ) return $this->server[$key];
		return false;
	}

	


}

?>