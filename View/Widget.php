<?php

class Core_View_Widget extends Core_View_Simple{
    public function vars(){
        return array();
    }
    public function tpl(){
        
    }

    public function render( $params = array() ){
    	$this->setTplPath( APP_PATH . "/widget" );
    	$vars = $this->vars();
    	$_vars = array_merge($vars,$params);
    	if( is_array($_vars) ){
    		foreach($_vars as $var => $value){
	    		$this->$var = $value;
	    	}
    	}
    	return $this->fetch( $this->tpl() );
    }

    public function getPath(){
        $widgetName = get_class($this);
        $length = strlen($widgetName);
        $widgetName = substr($widgetName,0,$length - 6);
        $widgetName = str_replace("_","/",$widgetName);
        $path = BASE_PATH . "/widget";
        $path .= "/" . $widgetName;
        return $path;
    }
}

?>