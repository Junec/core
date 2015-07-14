<?php
/**
 * Load
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_load{

	/**
     * 框架类自动加载
     * 
     * @return void
     */
    public static function autoLoad($className){
        $params = explode('_',$className);
        $oriParams = $params;
        $type = array_shift($params);
        $path = join('/',$params).'.php';
        
        if($type == 'core'){
            $file = CORE_DIR.'/'.$path;
        }elseif($type == 'ctl'){
            $file = core::getConfig('controller_dir').'/'.$path;
        }elseif($type == 'mdl'){
            $file = core::getConfig('model_dir').'/'.$path;
        }elseif($type == 'lib'){
            $file = core::getConfig('lib_dir').'/'.$path;
        }
        if( file_exists($file) ) include_once $file;
    }

}