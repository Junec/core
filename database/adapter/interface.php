<?php 
/**
 * 适配器接口
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
interface core_database_adapter_interface{

    /**
	 * 执行查询
	 * 
	 * @param string $sql 原生查询语句
	 * @return resource
	 */
    public function query($sql = '');


    /**
	 * 开启事务
	 * 
	 * @return bool
	 */
    public function begin();
    

    /**
	 * 回滚事务
	 * 
	 * @return bool
	 */
    public function rollback();
    

    /**
	 * 提交事务
	 * 
	 * @return bool
	 */
    public function commit();


    /**
	 * 
	 * 
	 * @return array
	 */
    public function fetch($resource = '');


    /**
	 * 
	 * 
	 * @return array
	 */
    public function fetchAll($resource = '');

}

?>