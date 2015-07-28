<?php 
/**
 * PDO操作类适配器
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_adapter_pdo extends core_database_adapter_abstract implements core_database_adapter_interface{

    public function __construct($options = array()){
        $this->client = core::instance('core_database_client_pdo',$options);
    }

}

?>