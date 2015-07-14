<?php 
/**
 * Memcached Queue
 *
 * @author chenjun <594553417@qq.com>
 * @copyright  Copyright (c) 2008-2015 Technologies Inc.
*/
class core_queue{

	private $cache;
	private $prefix 	= '-coreQueue-Key-';
	private $beginKey   = 'begin';
	private $endKey     = 'end';
	private $maxLimit 	= 1000;


	public function __construct($host = '',$block = 'mcq1'){
		$this->cache = core_cache_client::factory('memcached',array('host' => $host));
		$this->prefix = $block.$this->prefix;

		$this->add($this->beginKey, 0);
		$this->add($this->endKey, 0);
	}


	/**
	 * 写入缓存
	 * 
	 * @param $key
	 * @param $value
	 * @return bool
	 */
	private function add($key,$value){
		return $this->cache->add( $this->prefix.$key, $value );
	}


	/**
	 * 读取缓存
	 * 
	 * @param $key
	 * @return bool || result
	 */
	private function get($key){
		return $this->cache->get( $this->prefix.$key );
	}


	/**
	 * 删除缓存
	 * 
	 * @param $key
	 * @return bool || result
	 */
	private function delete($key){
		return $this->cache->del( $this->prefix.$key );
	}


	/**
	 * 设置指针
	 * 
	 * @param $key
	 * @return bool || number index
	 */
	private function setHand($key){
		return $this->cache->increment( $this->prefix.$key );
	}


	/**
	 * 入栈
	 * 
	 * @param $value
	 * @return bool
	 */
	public function push($value){
		$end = (int)($this->get($this->endKey) - $this->get($this->beginKey));
		if($end >= $this->maxLimit) return false;
		return $this->add($this->setHand($this->endKey), $value);
	}


	/**
	 * 出栈
	 * 
	 * @param 
	 * @return bool || result
	 */
	public function pop(){
		if($this->get($this->beginKey) >= $this->maxLimit) return false;
		$index = $this->setHand($this->beginKey);
		if($index !== false){
			$pop = $this->get($index);
			$this->delete($index);
		}else{
			$pop = false;
		}
		return $pop;
	}


	public function getList(){
		$beginKey = (int)$this->get($this->beginKey);
		$endKey = (int)$this->get($this->endKey);
		$list = array();
		for($i=$beginKey+1;$i<=$endKey;$i++){
			$list[$this->prefix.$i] = $this->get($i);
		}
		$list[$this->prefix.$this->beginKey] = $this->get($this->beginKey);
		$list[$this->prefix.$this->endKey] = $this->get($this->endKey);
		return $list;
	}


	/**
	 * 清空队列
	 * 
	 * @param 
	 * @return bool
	 */
	public function flush(){
		$beginKey = (int)$this->get($this->beginKey);
		$endKey = (int)$this->get($this->endKey);
		for($i=$beginKey;$i<=$endKey;$i++){
			$this->delete($i);
		}
		$this->delete($this->beginKey);
		$this->delete($this->endKey);
		return true;
	}

}
