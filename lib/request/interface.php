<?php 
/**
 * REQUEST类接口
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
interface core_request_interface{


	/**
	 * 设置超时时间
	 * 
	 * @param numeric $timeOut 超时时间
	 * @return object
	 */
	public function setTimeout($timeOut = 3);


	/**
	 * GET
	 * 
	 * @param string $url 请求地址
	 * @param array $params 请求参数
	 * @return void
	 */
    public function get($url = '',$params = array()); 
 

 	/**
	 * POST
	 * 
	 * @param string $url 请求地址
	 * @param array $params 请求参数
	 * @return void
	 */
    public function post($url = '',$params = array()); 


}
