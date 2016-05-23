<?php
/**
 * Redis
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_cache_factory_redis extends core_cache_abstract implements core_cache_interface{
    private $instance;
    
    public function __construct($params = array()){
        $host = $params['host'];
        $port = !empty($params['port']) ? $params['port'] : 6379;
        if( $host != '' ){
            core_debug::info('add redis server: '.$host.":".$port);
            $this->getInstance()->connect($host,$port);
        }else{
            throw new Exception('can\'t load cache_redis_host, please check it.');
        }
    }

    public function getInstance(){
    	if(!is_object($this->instance) || $this->instance == ''){
    		$this->instance = new Redis();
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
    public function set($key = '',$value = '',$overdueTime = 0){
        $key = $this->getKey($key);
        return $this->getInstance()->setex($key, $overdueTime, $value);
    }

    /**
	 * 获取缓存
	 * 
	 * @param string $key    唯一标识
     * @param string $filed    字段 value、dateline
	 * @return data
	 */
    public function get($key = '',$field = 'value'){
        $key = $this->getKey($key);
        $value = $this->getInstance()->get($key);
        return $value;
    }

    /**
	 * 删除缓存
	 * 
	 * @param string $key    唯一标识
	 * @return bool
	 */
    public function del($key = ''){
        $key = $this->getKey($key);
        return $this->getInstance()->delete($key);
    }

    /**
	 * 清空缓存
	 * 
	 * @return bool
	 */
    public function flush(){
        return $this->getInstance()->flushAll();
    }


}
