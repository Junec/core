<?php
/**
 * 文件系统缓存
 *
 * @author chenjun <594553417@qq.com>
 * @copyright  Copyright (c) 2008-2015 Technologies Inc.
*/
class core_cache_factory_filesystem extends core_cache_abstract implements core_cache_interface{
    private $savepath;
    private $instance;

    public function getSavePath(){
        return $this->savepath;
    }

    public function __construct($params = array()){
        if(!isset($params['cache_dir']) || !$this->getInstance()->isDir($params['cache_dir'])){
            $this->savepath = core::getConfig('cache_filesystem_dir');
        }else{
            $this->savepath = $params['cache_dir'];
        }
        core_debug::info('set filesystem dir: '.$this->getSavePath());     
    }

    public function getInstance(){
        if(!is_object($this->instance) || $this->instance == ''){
            $this->instance = core::instance('core_file');
        }
        return $this->instance;
    }

    /**
	 * 设置缓存
	 * 
	 * @param string $key    唯一标识
     * @param $value    缓存数据
     * @param $overdueTime 过期时间 单位:秒[如为空则永不过期]
	 * @return bool
	 */
    public function set($key = '',$value = '',$overdueTime = ''){
        if($key == '') return false;
        $key = $this->getKey($key);
        $array = serialize(array(
            'value'=>$value,
            'dateline' => time(),
            'overdueTime' => $overdueTime,
        ));

        $value = '<?php exit; ?>'.$array;
        $file = $this->getSavePath().$key.'.php';
        if( $this->getInstance()->isDir($this->getSavePath()) || $this->getInstance()->mk($this->getSavePath()) ){
            $result = $this->getInstance()->write($file,$value);
            if( $result ) return true;
            else return false;
        }else{
            return false;
        }
    }

    /**
	 * 获取缓存
	 * 
	 * @param string $key    唯一标识
     * @param string $filed    字段 value、dateline
	 * @return data
	 */
    public function get($key = '',$field = 'value'){
        if( $key == '' ) return null;
        $key = $this->getKey($key);
        $file = $this->getSavePath().$key.'.php';
        if( file_exists($file) ){
            $value = $this->getInstance()->read($file);
            $value = substr($value,14);
            $value = unserialize($value);
            return $field == '*' ? $value : $value[$field];
        }else{
            return null;
        }
    }

    /**
	 * 删除缓存
	 * 
	 * @param string $key    唯一标识
	 * @return bool
	 */
    public function del($key = ''){
        if($key == '') return false;
        $key = $this->getKey($key);
        $file = $this->getSavePath().$key.'.php';
        if( file_exists($file) ){
            $this->getInstance()->del($file);
        }
        return true;
    }

    /**
	 * 清空缓存
	 * 
	 * @return bool
	 */
    public function flush(){
       return $this->getInstance()->clearRecur($this->getSavePath());
    }

}
?>