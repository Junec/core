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
    public static function autoLoad($class){
        self::loadFile( self::getPath($class) );
    }

    public static function getPath($class){
        $path = '';
        if(empty($class)) return false;
        $classLen = strlen($class);

        if(strpos($class,'core_') === 0){
            $classType = 'core';
            $class = substr($class, 5);

        }elseif(substr($class, $classLen - 11) == '_controller'){
            $classType = 'controller';
            $class = substr($class, 0, $classLen - 11);

        }elseif(substr($class, $classLen - 6) == '_model'){
            $classType = 'model';
            $class = substr($class, 0, $classLen - 6);

        }elseif(substr($class, $classLen - 7) == '_widget'){
            $classType = 'widget';
            $class = substr($class, 0, $classLen - 7);

        }elseif(substr($class, $classLen - 9) == '_template'){
            $classType = 'template';
            $class = substr($class, 0, $classLen - 9);

        }else{
            $classType = 'lib';

        }
        
        $params = explode('_',$class);
        if($classType == 'core'){
            $path = CORE_DIR.'/'.join('/',$params).'.php';
        }else{
            $app_dir = rtrim(core::getConfig('app_dir'),'/');
            $path = core::getConfig('app_dir').'/'.$classType.'/'.join('/',$params).'.php';
        }
        return $path;
    }


    public static function loadFile($file){
        if( file_exists($file) ){
            return include_once $file;
        }else{
            return false;
        }
    }

}