<?php 
/**
 * 数据库适配器
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_adapter{
    private $adapter;

    public function __construct($adapter){
        $this->adapter = $adapter;
    }


    /**
	 * 执行查询
	 * 
	 * @param string $sql 原生查询语句
	 * @return resource
	 */
    public function query($sql = ''){
    	return $this->adapter->query($sql);
    }


    /**
	 * 开启事务
	 * 
	 * @return bool
	 */
    public function begin(){
    	return $this->adapter->begin();
    }
    

    /**
	 * 回滚事务
	 * 
	 * @return bool
	 */
    public function rollback(){
    	return $this->adapter->rollback();
    }
    

    /**
	 * 提交事务
	 * 
	 * @return bool
	 */
    public function commit(){
    	return $this->adapter->commit();
    }



}

?>