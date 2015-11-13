<?php
/**
 * Memcached缓存
 *
 * @author chenjun <594553417@qq.com>
 * @copyright  Copyright (c) 2008-2015 Technologies Inc.
*/
class core_cache_factory_memcached extends core_cache_abstract implements core_cache_interface{
    private $prefix;
    private $obj;
    
    public function __construct($params = array()){
        if(!isset($this->obj) || $this->obj == ''){
            $hosts = array();
            $this->obj = new Memcached;
            if( $params['host'] != '' ){
                $config = explode(',',$params['host']);
                foreach($config as $v){
                    $v = trim($v);
                    $tmp = explode(':', $v);
                    $hosts[] = array($tmp[0],$tmp[1]);
                    core_debug::info('add memcached server: '.$v);
                }
                $this->obj->addServers($hosts);
            }else{
                throw new Exception('can\'t load cache_memcache_host, please check it.');
            }
        }
    }

    public function getObj(){
        return $this->obj;
    }

    /**
	 * 设置缓存
	 * 
	 * @param string $key    唯一标识
     * @param $value    缓存数据
     * @param $overdueTime 过期时间 单位:秒[如为空则永不过期]
	 * @return bool
	 */
    public function set($key = '',$value = '',$overdueTime = 0){
        core_debug::setCounter('cache_set');
        $key = $this->getKey($key);
        return $this->getObj()->set($key, $value, $overdueTime);
    }

    /**
	 * 获取缓存
	 * 
	 * @param string $key    唯一标识
     * @param string $filed    字段 value、dateline
	 * @return data
	 */
    public function get($key = '',$field = 'value'){
        core_debug::setCounter('cache_get');
        $key = $this->getKey($key);
        $value = $this->getObj()->get($key);
        return $value;
    }

    /**
	 * 删除缓存
	 * 
	 * @param string $key    唯一标识
	 * @return bool
	 */
    public function del($key = ''){
        core_debug::setCounter('cache_del');
        $key = $this->getKey($key);
        return $this->getObj()->delete($key);
    }

    /**
	 * 清空缓存
	 * 
	 * @return bool
	 */
    public function flush(){
        core_debug::setCounter('cache_flush');
        return $this->getObj()->flush();
    }


    public function increment($key = '', $offset = 1){
        $key = $this->getKey($key);
        return $this->getObj()->increment($key,$offset);
    }

    public function add($key = '',$value = '',$overdueTime = 0){
        core_debug::setCounter('cache_set');
        $key = $this->getKey($key);
        return $this->getObj()->add($key, $value, $overdueTime);
    }

}
?>