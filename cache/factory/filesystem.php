<?php
/**
 * 文件系统缓存
 *
 * @author chenjun <594553417@qq.com>
 * @copyright  Copyright (c) 2008-2015 Technologies Inc.
*/
class core_cache_factory_filesystem extends core_cache_abstract implements core_cache_interface{
    private $prefix;
    private $savepath;
    private $obj;

    public function getSavePath(){
        return $this->savepath;
    }

    public function __construct($params = array()){
        $this->savepath = $params['cache_dir'];
        core_debug::info('set filesystem dir: '.$this->getSavePath());
        $this->obj = core::instance('core_file');
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
        core_debug::setCounter('cache_set');
        $key = $this->getKey($key);
        $array = serialize(array(
            'value'=>$value,
            'dateline' => time(),
            'overdueTime' => $overdueTime,
        ));

        $value = '<?php exit; ?>'.$array;
        $file = $this->getSavePath().$key.'.php';

        if( $this->obj->isDir($this->getSavePath()) || $this->obj->mk($this->getSavePath()) ){
            $result = $this->obj->write($file,$value);
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
        core_debug::setCounter('cache_get');
        $key = $this->getKey($key);
        $file = $this->getSavePath().$key.'.php';
        if( file_exists($file) ){
            $value = $this->obj->read($file);
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
        core_debug::setCounter('cache_del');
        $key = $this->getKey($key);
        $file = $this->getSavePath().$key.'.php';
        if( file_exists($file) ){
            $this->obj->del($file);
        }
        return true;
    }

    /**
	 * 清空缓存
	 * 
	 * @return bool
	 */
    public function flush(){
       core_debug::setCounter('cache_flush');
       return $this->obj->clearRecur($this->getSavePath());
    }

}
?>