<?php
/**
 * 数据库操作公共方法类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
abstract class core_database_adapter_abstract{
   
    /**
     * 开启事务
     * 
     * @return bool
     */
    public function begin(){
        $this->exec('START TRANSACTION');
        return true;
    }


    /**
     * 回滚事务
     * 
     * @return bool
     */
    public function rollback(){
        $this->exec('ROLLBACK');
        $this->exec('END');
        return true;
    }


    /**
     * 提交事务
     * 
     * @return bool
     */
    public function commit(){
        $this->exec('COMMIT');
        $this->exec('END');
        return true;
    }


}