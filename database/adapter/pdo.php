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


    /**
     * 插入
     *
     * @param array $table 表名
     * @param array $data 数据
     * @return miexd
     */
    public function insert($table = '',$data = array()){
        if( !$data ) return false;
        $fieldSql = array();
        $valuePrepareSql = array();
        $valueReal = array();
        foreach($data as $field=>$value){
            $fieldSql[] =  '`'.trim($field).'`';
            $valuePrepareSql[] = '?';
            $valueReal[] =  $value === NULL ? null : $value;
        }
        $fieldSql = join(',',$fieldSql);
        $valuePrepareSql = join(',',$valuePrepareSql);
        $sql = "INSERT INTO {$table}({$fieldSql}) VALUES({$valuePrepareSql})";
        $statement = $this->client->prepare($sql);
        $result = $statement->execute($valueReal);
        $insertId = $this->client->getInsertId();
        return $insertId;
    }


    /**
     * 更新
     *
     * @param array $table 表名
     * @param array $data 数据
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function update($table = '',$data = array(),$filter = array()){
        $where = $this->filter($filter);
        if(!$data) return false;
        $fieldSql = array();
        $valueReal = array();
        foreach($data as $k=>$v){
            $fieldSql[] = "`{$k}` = ?";
            $valueReal[] = $v === NULL ? 'null' : $v;
        }
        $fieldSql = join(',',$fieldSql);
        $sql = "UPDATE {$table} SET {$fieldSql} WHERE {$where}";
        $statement = $this->client->prepare($sql);
        return $statement->execute($valueReal);
    }


    /**
     * 删除
     *
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function delete($table = '',$filter = array()){
        $where = $this->filter($filter);
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $result = $this->client->exec($sql);
        return empty($result)? false : $result;
    }


    /**
     * 统计
     *
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function count($table = '',$filter = array()){
        $where = $this->filter($filter);
        $sql = "SELECT COUNT(*) AS _count FROM {$table} WHERE {$where}";
        $result = $this->select($sql);
        return $result[0]['_count'];
    }


    /**
     * 查询列表数据
     * 
     * @param string $field     返回字段
     * @param string $filter    filter过滤条件
     * @param number $offset    offset
     * @param number $limit     limit
     * @param string $orderby   排序 如：id DESC
     * @param string $groupby
     * @return mixed
     */
    public function getList($table = '',$filter = '',$field = '*',$offset = 0,$limit = '-1',$orderby = '',$groupby = ''){
        $field = empty($field)?'*':$field;
        $where = $this->filter( $filter );
        $offset = empty($offset) ? 0: $offset;
        if( !empty($limit) && $limit != '-1' ) $_limit = $offset.','.$limit;
        $sql = "SELECT {$field} FROM {$table} WHERE {$where}";
        if(!empty($orderby)) $sql .= " ORDER BY {$orderby}";
        if(!empty($groupby)) $sql .= " GROUP BY {$groupby}";
        if(!empty($_limit))  $sql .= " LIMIT {$_limit}";

        $result = $this->select($sql);
        return $result;
    }

    
    /**
     * 查询单条数据
     *
     * @param array $filter
     * @return miexd
     */
    public function getOne($table = '',$filter = '',$field = '*',$orderby='',$groupby = ''){
        if(is_array($filter) || is_string($filter)){
            $filter = $filter;
        }elseif(is_numeric($filter)){
            $filter = array($this->pri => $filter);
        }
        $data = $this->getList($table,$filter,$field,0,1,$orderby,$groupby);
        if(is_array($data) && isset($data[0])) return $data[0];
        else return array();
    }

}

?>