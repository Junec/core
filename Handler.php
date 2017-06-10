<?php

class Core_Handler{
    /**
     * 处理错误
     * 
     * @return void
     */
    public function error(){
        $error = error_get_last();
        if (in_array($error['type'],array(E_ERROR,E_WARNING,E_PARSE))){
            $errorType = array('1'=>'Fatal Error','2'=>'Warning','4'=>'Parse Error');
            $errorMsg = $errorType[$error['type']]. ': ' .$error['message']." in file ".$error['file']." on line ".$error['line'];
        }
    }

    /**
     * 处理异常
     * 
     * @return void
     */
    public function exception($e){
        $exception = get_class($e);
		$msg = '['.$exception.'] '.$e->getMessage()."\n".$e->getTraceAsString();
        echo $msg;
        exit;
    }

}

?>