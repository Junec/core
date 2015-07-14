<?php
/**
 * handler	
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_handler{
	
	/**
     * 捕获错误
     * 
     * @return void
     */
    public static function error(){
        $error = error_get_last();
        if (in_array($error['type'],array(E_ERROR,E_WARNING,E_PARSE))){
            $errorType = array('1'=>'Fatal Error','2'=>'Warning','4'=>'Parse Error');
            $errorMsg = $errorType[$error['type']]. ': ' .$error['message']." in file ".$error['file']." on line ".$error['line'];
            self::errorlog( $errorMsg );
        }
    }

    /**
     * 捕获异常
     * 
     * @return void
     */
    public static function exception(Exception $e){
        $errorMsg = '[Exception] '.$e->getMessage()."\n".$e->getTraceAsString();
        self::errorlog( $errorMsg );
    }


    public static function notfound(){
        header('HTTP/1.1 404 Not Found'); 
        header("status: 404 Not Found"); 
        echo '404 not found.';
        exit;
    }

    /**
     * 错误处理
     * 
     * @param string $message    错误信息
     * @return void
     */
    public static function errorlog($message){
        $logfile = core::getConfig('errorlog_dir').'/'.date('Ymd').'.txt';
        $ip = $_SERVER['REMOTE_ADDR'];
        if(core::getConfig('core_debug') === true){
            echo $message;
        }else{
            $message = sprintf("#%s# - %s %s\n", date("Y-m-d H:i:s") , $ip , $message);
            self::instance('core_file')->write($logfile,$message);
        }
    }
	

}