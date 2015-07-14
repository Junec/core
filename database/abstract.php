<?php
/**
 * 数据库操作公共方法类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
abstract class core_database_abstract{
	/**
	 * 数据转义
	 * 
	 * @param string $string 字符串数据
	 * @return string
	 */
    public function escapeString($string = ''){
    	return $string;
        #return mysql_real_escape_string($string);
    }


    protected function error($errno = '' , $error = ''){
    	$message = $errno.' : '.$error;
    	core_handler::errorlog($message);
        exit;
    }
}