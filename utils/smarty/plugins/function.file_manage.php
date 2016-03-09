<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

function smarty_function_file_manage($params, $template)
{
	return Core::single('Admin_Lib_Input_Html')->fileManage($params['id'],$params['name'],$params['default']);
}

?>