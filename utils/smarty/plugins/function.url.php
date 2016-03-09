<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

function smarty_function_url($params=array(), $template){
    $url = Core::single('Core_Lib_Base_Url')->setUrl($params['params'],$params['dispatch'],$params['url_mode']);
    return $url;
}

?>