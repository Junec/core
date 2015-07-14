<?php
/**
 * URL解析类
 * 
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_url{

    /**
	 * 获取根目录url
     *
	 * @return string
	 */
    public function getBaseUrl(){
        $host = $this->getHttpHost();
        $dir = $this->getFilePath('dir');
        return $host.$dir;
    }
    

    /**
	 * 获取域名
     *
	 * @return string
	 */
    public function getHttpHost(){
        return 'http://'.$_SERVER ['HTTP_HOST'];
    }

    
    /**
	 * 获取执行文件路径
     *
     * @param string $type    dir:只获取目录路径 2:获取目录文件完整路径
	 * @return string
	 */
    public function getFilePath($type){
        $path = '';
        $filename = basename($_SERVER['SCRIPT_FILENAME']);
        switch($type){
            case 'dir':
                if(basename($_SERVER['SCRIPT_NAME']) === $filename){
                    $path = dirname($_SERVER['SCRIPT_NAME']);
                }else if(basename($_SERVER['PHP_SELF']) === $filename){
                    $path = dirname($_SERVER['PHP_SELF']);
                }else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename){
                    $path = dirname($_SERVER['ORIG_SCRIPT_NAME']);
                }
            break;
            case 'file';
                if(basename($_SERVER['SCRIPT_NAME']) === $filename){
                    $path = basename($_SERVER['SCRIPT_NAME']);
                }else if(basename($_SERVER['PHP_SELF']) === $filename){
                    $path = basename($_SERVER['PHP_SELF']);
                }else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename){
                    $path = basename($_SERVER['ORIG_SCRIPT_NAME']);
                }
            break;
            default:
                $path = $_SERVER['SCRIPT_NAME'];
            break;
        }
        return rtrim($path,DIRECTORY_SEPARATOR);
    }
    

    /**
	 * 兼容获取pathinfo
     *
	 * @return string
	 */
    public function getPathInfo(){
        $filename = basename($_SERVER['SCRIPT_FILENAME']);
        $path_info = '';
        if(isset($_SERVER['PATH_INFO']) and !empty($_SERVER['PATH_INFO']) and basename($_SERVER['PATH_INFO']) != $filename){    
            $path_info = $_SERVER['PATH_INFO'];
        }else if(isset($_SERVER['ORIG_PATH_INFO']) and !empty($_SERVER['ORIG_PATH_INFO']) and basename($_SERVER['ORIG_PATH_INFO']) != $filename){
            $path_info = $_SERVER['ORIG_PATH_INFO'];
        }else if(isset($_SERVER['REQUEST_URI']) and !empty($_SERVER['REQUEST_URI']) and basename($_SERVER['REQUEST_URI']) != $filename){
            $path_info = str_replace(array($this->getFilePath('dir'),$this->getFilePath('file')),array( '','' ),$_SERVER['REQUEST_URI']);
        }else if(isset($_SERVER['PHP_SELF']) and !empty($_SERVER['PHP_SELF']) and basename($_SERVER['PHP_SELF']) != $filename){
            $path_info = str_replace(array($this->getFilePath('dir'),$this->getFilePath('file')),array( '','' ),$_SERVER['PHP_SELF']);
        }
        $path_info = trim($path_info,'/');
        $path_info = trim($path_info,'?');
        return  empty($path_info) ? '' : $path_info;
    }
    
    
    /**
	 * 获取当前地址参数信息
     *
	 * @return array
	 */
    public function getQueryParams($string = ''){
        $queryArray = array();
        parse_str($string,$array);
        $queryArray = array_merge($array,$_GET);
        $_GET = $queryArray;
        return $_GET;
    }

    /**
	 * 
     *
	 * @return string
	 */
    public function getUrl(){
        $url = 'index.php?'.http_build_query($_GET);
        return $url;
    }

    /**
	 * 
     *
	 * @return array
	 */
    public function parseQueryString($get = array()){
        $queryString = http_build_query($get);
        return $queryString;
    }
}
?>