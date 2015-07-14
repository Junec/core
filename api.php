<?php
/**
 * API基类
 * 
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_api extends core_controller{

	/**
     * 控制器初始化
     * 
     * @return void
     */
    public function __construct(){
        parent::__construct();
        $apiKey = $this->get['apiKey'];
        if(md5($apiKey) !== md5(core::getConfig('api_key'))){
            $this->result('fail','permission error.');
        }
    }

}
?>