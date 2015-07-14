<?php 
/**
 * 数据库接口
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
interface core_database_interface{


	/**
	 * 数据库连接
	 * 
	 * @param array $option 数据库连接信息
	 * @return bool
	 */
    public function connect();


    /**
	 * 执行查询
	 * 
	 * @param string $sql 原生查询语句
	 * @return resource
	 */
    public function query($sql = '');


    /**
	 * 插入数据
	 * 
	 * @param string $table 表名
     * @param array $data 插入数据数组
	 * @return bool or insert_id
	 */
    public function insert($table = '',$data = array());


    /**
	 * 查询数据
	 * 
	 * @param string $table 表名
     * @param array $field 查询字段
     * @param string $where 过滤条件
     * @param string $groupby 分组
     * @param string $orderby 排序条件
     * @param string $limit 偏移值
     * @param string $join 联合查询
	 * @return bool or insert_id
	 */
    public function select($table = '',$field = '*',$where = '',$orderby = '',$limit = '',$groupby = '',$join = '');

    /**
	 * 统计数据
	 * 
	 * @param string $table 表名
     * @param string $where 过滤条件
     * @param string $groupby 分组
     * @param string $join 联合查询
	 * @return number
	 */
    public function count($table = '',$where = '',$groupby = '',$join = '');

    /**
	 * 删除数据
	 * 
	 * @param string $table 表名
	 * @param string $where 过滤条件
	 * @return bool
	 */
    public function delete($table = '',$where = '');
    

    /**
	 * 更新数据
	 * 
	 * @param string $table 表名
     * @param array $data 更新数据数组
     * @param string $where 过滤条件
	 * @return bool
	 */
    public function update($table = '',$data = array(),$where = '');
    

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
	 * 获取表主键
	 * 
	 * @return string
	 */
    public function getTablePri($table = '');


}

?>