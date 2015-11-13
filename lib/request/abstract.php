<?php
/**
 * REQUEST公共方法类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
abstract class core_request_abstract{
	protected $client;
	protected $response;
	private $timeOut = 0;

    /**
	 * 设置超时时间
	 * 
	 * @param numeric $timeOut 超时时间
	 * @return object
	 */
	public function setTimeout($timeOut = 10){
		$this->timeOut = $timeOut;
		return $this;
	}

    /**
	 * 获取超时时间
	 * 
	 * @return object
	 */
	public function getTimeout(){
		return $this->timeOut;
	}
}