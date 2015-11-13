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

        }elseif(substr($class, $classLen - 10) == 'Controller'){
            $classType = 'controller';
            $class = substr($class, 0, $classLen - 10);

        }elseif(substr($class, $classLen - 5) == 'Model'){
            $classType = 'model';
            $class = substr($class, 0, $classLen - 5);

        }else{
            $classType = 'lib';

        }

        $params = explode('_',$class);
        $path = join('/',$params);
        
        switch($classType){
            case 'core':
                $path = CORE_DIR.'/'.$path.'.php';
            break;
            case 'controller':
                $path = core::getConfig('controller_dir').'/'.$path.'.php';
            break;
            case 'model':
                $path = core::getConfig('model_dir').'/'.$path.'.php';
            break;
            default:
                $path = core::getConfig('lib_dir').'/'.$path.'.php';
            break;
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