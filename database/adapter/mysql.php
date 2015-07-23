<?php 
/**
 * Mysql操作类适配器
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_adapter_mysql extends core_database_adapter_abstract implements core_database_adapter_interface{
    protected $client;

    public function __construct($options = array()){
        $this->client = core::instance('core_database_client_mysql',$options);
    }


    public function select($sql = ''){
        $resource = $this->query($sql);
        return $this->fetchAll($resource);
    }


    /**
	 * 执行查询
	 * 
	 * @param string $sql 原生查询语句
	 * @return resource
	 */
    public function query($sql = ''){
    	return $this->client->query($sql);
    }

    public function exec($sql = ''){
        return $this->client->exec($sql);
    }


    /**
     * 
     * 
     * @return array
     */
    public function fetch($resource = ''){
        return $this->client->fetch($resource);
    }


    /**
	 * 
	 * 
	 * @return array
	 */
    public function fetchAll($resource = ''){
        $result = array();
        while($temp = $this->fetch($resource)){
            if($temp){
                foreach($temp as $k=>$v) $data[$k] = $v;
                $result[] = $data;
                $data = null;
            }
        }
        return $result;
    }

}

?>