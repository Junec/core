<?php 
/**
 * Mysqli操作类适配器
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_adapter_mysqli extends core_database_adapter_mysql{

    public function __construct($options = array()){
        $this->client = core::instance('core_database_client_mysqli',$options);
    }
}

?>