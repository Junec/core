<?php

class Core_Cache_Filesystem extends Core_Cache_Abstract{
	public function __construct(){
		$this->fileObj = Core_Loader::getInstance("Core_Library_File");
	}

	public function getPath(){
		$cacheDir = Core_Registry::get("config")->cache['filesystem']['cache_dir'];
		return rtrim($cacheDir,"/") . "/";
	}

	public function set($key = '', $value = '',$overdueTime = ''){
		if($key == '') return false;
        $key = $this->hashKey($key);
        $array = serialize(array(
            'value'=>$value,
            'dateline' => time(),
            'overdueTime' => $overdueTime,
        ));
        $value = $array;
        $file = $this->getPath().$key;
        if( $this->fileObj->isDir($this->getPath()) || $this->fileObj->mk($this->getPath()) ){
            $result = $this->fileObj->write($file,$value);
            if( $result ) return true;
            else return false;
        }else{
            return false;
        }
	}

	public function get($key = ''){
		if( $key == '' ) return false;
		$oriKey = $key;
        $key = $this->hashKey($key);
        $file = $this->getPath().$key;
        if( file_exists($file) ){
            $value = $this->fileObj->read($file);
            $value = unserialize($value);
            if($value["overdueTime"] > 0){
            	if(time() - $value["dateline"] >= $value["overdueTime"]){
	            	$this->delete($oriKey);
	            	return false;
	            }
            }
            return $value["value"];
        }else{
            return false;
        }
	}

	public function delete($key = ''){
		if($key == '') return false;
        $key = $this->hashKey($key);
        $file = $this->getPath().$key;
        if( file_exists($file) ){
            $this->fileObj->del($file);
        }
        return true;
	}

	public function flush(){
       return $this->fileObj->clearRecur($this->getPath());
    }

}

?>