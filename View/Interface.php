<?php

interface Core_View_Interface{
	
	public function assign( $name, $value );

	public function display( $tpl );

	public function fetch( $template );

	public function setTplPath( $path );
}

?>