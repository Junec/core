<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

function smarty_function_editor($params, $template)
{
	return Core::single('Admin_Lib_Input_Html')->editor($params['id'],$params['name'],$params['default'],$params['width'],$params['height']);
}

?>