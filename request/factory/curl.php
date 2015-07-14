<?php 
/**
 * CURL
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_request_factory_curl extends core_request_abstract implements core_request_interface{

	public function __construct($timeout = 10){
		$this->client = curl_init();
		$this->setTimeout($timeout);
		core_debug::info("curl set request timeout: {$timeout} , ok.");
	}

    /**
	 * GET
	 * 
	 * @param string $url 请求地址
	 * @param array $params 请求参数
	 * @return void
	 */
	public function get($url = '',$params = array()){
		core_debug::setCounter('request_get');
		return $this->_request($url,'GET',$params);
	}

    /**
	 * POST
	 * 
	 * @param string $url 请求地址
	 * @param array $params 请求参数
	 * @return void
	 */
	public function post($url = '',$params = array()){
		core_debug::setCounter('request_post');
        return $this->_request($url,'POST',$params);
	}

    /**
	 * 发起请求
	 * 
	 * @param string $url 请求地址
     * @param array $method 请求方式
	 * @param array $params 请求参数
	 * @return void
	 */
	private function _request($url = '',$method = 'POST',$params = array()){
		$hashkey = core_debug::info("curl {$method} request: {$url} , requesting ...");
		curl_setopt($this->client, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($this->client, CURLOPT_URL, $url);
        curl_setopt($this->client, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->client, CURLOPT_CONNECTTIMEOUT, $this->getTimeout());
        curl_setopt($this->client, CURLOPT_TIMEOUT, $this->getTimeout());
        if($method == 'POST'){
        	curl_setopt($this->client, CURLOPT_POST, count($params));
        	curl_setopt($this->client, CURLOPT_POSTFIELDS, $params);
        }
        $this->response = curl_exec($this->client);
        curl_close($this->client);
        if($this->response === false) $debugInfo = "curl {$method} request: {$url} , timeout(".$this->getTimeout()." Seconds).";
        else $debugInfo = "curl {$method} request: {$url} , ok.";
        core_debug::info($debugInfo,$hashkey);
        return $this->response;
	}
}
