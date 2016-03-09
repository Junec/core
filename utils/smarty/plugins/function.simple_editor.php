<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

function smarty_function_simple_editor($params, $template)
{
	return Core::single('Admin_Lib_Input_Html')->simpleEditor($params['id'],$params['name'],$params['default'],$params['width'],$params['height']);
}

?>