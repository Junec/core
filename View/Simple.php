<?php

class Core_View_Simple implements Core_View_Interface{

	protected $path = '';
	protected $vars = array();

	public function __construct(){
		$this->path = APP_PATH . "/views";

	}

	public function setTplPath( $path  ){
		$this->path = $path;
	}

	public function assign( $var, $value ){
		$this->vars[ $var ] = $value;
	}

	public function fetch( $tpl ){
		extract($this->getVars(), EXTR_OVERWRITE);
		ob_start();
        ob_implicit_flush(0);
        include_once $this->path . "/" . $tpl;
        $fetch = ob_get_clean();
        return $fetch;
	}

	public function display( $tpl ){
		echo $this->fetch($tpl);
	}

	public function __set( $var, $value ){
		$this->vars[ $var ] = $value;
	}

	public function __get( $var ){
		return $this->vars[ $var ];
	}

	public function getVars(){
		return $this->vars;
	}

	public function widget($widget = '', $params = array()){
		return Core_Loader::getInstance($widget)->render( $params );
	}

}

?>