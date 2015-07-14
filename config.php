<?php
/**
 * 全局配置文件
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/

return array(

#DEBUG模式
    'core_debug'                    => true,
    'core_debug_info'               => false,

#运行内存设置
    'core_memory_limit'             => '32M',

#时区
    'core_timezone'                 => 'Etc/GMT-8',


#框架默认控制器变量/方法变量
    'mvc_controller'                => 'ctl',
    'mvc_action'                    => 'act',

#框架默认控制器名/方法名
    'default_controller'            => 'index',
    'default_action'                => 'index',

#是否开启页面压缩
    'gzip_on'                       => true,

#URL路由开关
    'url_router'                    => true,

#URL路由规则
    'router_rules'                  => array(),

#缓存引擎，默认filesystem。
#共三种： filesystem、database、memcache
    'cache_engine'                  => 'filesystem', 

#filesystem目录
    'cache_filesystem_dir'          => APP_DIR.'/data/cache/',

#memcache连接信息
    'cache_memcache_host'           => "127.0.0.1:11211",

#database连接信息
    'cache_database_options'        => array(),

#模版引擎
    'tpl_client'                    => 'template',
    'tpl_left_delim'                => '{#',
    'tpl_right_delim'               => '#}',

#目录名称
    'controller_dir'                => APP_DIR.'/controller',
    'lib_dir'                       => APP_DIR.'/lib',
    'model_dir'                     => APP_DIR.'/model',
    'data_dir'                      => APP_DIR.'/data',
    'template_dir'                  => APP_DIR.'/template',
    'compile_dir'                   => APP_DIR.'/data/compile',
    'errorlog_dir'                  => APP_DIR.'/data/errorlog',

#数据库配置信息
    'db_config' => array(
		'client' => '',
        'medoo_config' => array(//medoo配置信息
            'database_type' => '',
         ),
		'server' => '',
		'username' => '',
		'password' => '',
		'port' => 3306,
		'database' => '',
	),

#API KEY
    'api_key'                      => 'Iu1ye83Gha2Bce',
);

?>