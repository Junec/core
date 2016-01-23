<?php
/**
 * 框架核心类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/

define('CORE_VERSION', '5.0');
define('CORE_DIR',realpath(dirname(__FILE__)));
header("Content-type:text/html;charset=utf-8");
if(extension_loaded('zlib')) {
    ini_set('zlib.output_compression', 'On');
    ini_set('zlib.output_compression_level', '3');
}
class core{
    /* 启动信息 */
    private static $boot = array();
    /* 配置信息 */
    private static $config;
    /* 路由信息 */
    private static $router;
    /* 单例对象 */
    private static $instance = array();

    private function __construct(){}
    

    /**
	 * 单例
	 * 
	 * @param string $class    类名
	 * @return object
	 */
    public static function instance($class,$param = ''){
        if(is_array($param)) $md5par = serialize($param);
        else $md5par = $param;
        $hashClass = md5(md5($class).md5($md5par));
        if( !isset(self::$instance[$hashClass]) || self::$instance[$hashClass] == '' ){
            self::$instance[$hashClass] = new $class($param);
        }
        return self::$instance[$hashClass];
    }


    /**
	 * 初始化
	 * 
	 * @return void
	 */
    public static function init($config = array()){
        if (function_exists('spl_autoload_register')){
            include_once 'load.php';
            spl_autoload_register(array('core_load','autoLoad'));
        }
        $coreConfig = include_once CORE_DIR.'/config.php';
        self::$config = array_merge($coreConfig,$config);
        self::$router = self::$config['router_rules'];

        /* settings */
        ini_set('memory_limit',self::$config['core_memory_limit']);
        error_reporting( E_ALL^E_NOTICE );
        date_default_timezone_set(self::$config['core_timezone']);
        set_exception_handler(array('core_handler','exception'));
        self::setBoot();
    }


    /**
     * 分派执行
     * 
     * @return void
     */
    public static function dispatch(){
        $controller = self::$boot[ self::getConfig('mvc_controller') ];
        $action = self::$boot[ self::getConfig('mvc_action') ];
        $controllerClassName = $controller.'_controller';
        $controllerClass = core::instance($controllerClassName);
        $hashkey = core_debug::info('controller: '.$controllerClassName.'::'.$action.'() , run ...');
        $controllerClass->exec($action);
        core_debug::info('controller: '.$controllerClassName.'::'.$action.'() , end .',$hashkey);
        if(self::getConfig('core_debug') && strpos(PHP_SAPI,'cli') === false){
            core_debug::output();
        }
    }


    /**
	 * 设置参数信息
	 * 
	 * @return void
	 */
    private static function setBoot(){
        $pathinfo = self::instance('core_url')->getPathInfo();
        $queryString = self::instance('core_router')->parse($pathinfo);
        $get = self::instance('core_url')->getQueryParams($queryString);

        $controller = $get[ self::$config['mvc_controller'] ];
        $action = $get[ self::$config['mvc_action'] ];
        if($controller == '') $controller = self::$config['default_controller'];
        if($action == '') $action = self::$config['default_action'];

        self::$boot[ self::$config['mvc_controller'] ] = $controller;
        self::$boot[ self::$config['mvc_action'] ] = $action;
        self::$boot['pathinfo'] = $pathinfo;
        core_debug::info('path info: '.$pathinfo);
        core_debug::info('query string: '.$queryString);
    }


    /**
     * 获取配置信息
     * 
     * @param $key 配置项
     * @return string || array
     */
    public static function getConfig($key = ''){
        if($key == '') return self::$config;
        else return self::$config[$key];
    }


    /**
     * 获取启动信息
     * 
     * @return array
     */
    public static function getBoot(){
        return self::$boot;
    }


    /**
     * 获取路由信息
     * 
     * @return array
     */
    public static function getRouter(){
        return self::$router;
    }


}
?>