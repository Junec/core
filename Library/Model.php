<?php
/**
 * Model
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2016.
 */
class Core_Library_Model{
    
    public $pri;
    public $table;
    public $masterConfig = array();
    public $slaveConfig = array();

    private $master;
    private $slave;

    public function getMaster(){
        return $this->master;
    }

    public function getSlave(){
        return $this->slave;
    }
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct(){
        if(!$this->masterConfig) $this->masterConfig = Core_Registry::get("config")->database['master'];
        if(!$this->slaveConfig) $this->slaveConfig = Core_Registry::get("config")->database['slave'];

        $this->master = Core_Loader::getInstance("Core_Library_Pdo", $this->masterConfig );
        $this->slave = Core_Loader::getInstance("Core_Library_Pdo", $this->slaveConfig );
    }

    public function filter( $filter = array(), &$filterValue = array() ){
        $where = ' 1 ';
        if( is_array($filter) ){
            foreach($filter as $field=>$value){
                $isIn = false;
                if(is_array($value)){
                    $isIn = true;
                }
                if( stripos($field,'|') === false){
                    if($isIn == true)    $field = $field.'|in';
                    else $field = $field.'|=';
                }
                $field = explode('|',$field);
                $fieldName = $field[0];
                $link = $field[1];
                if($isIn){
                    foreach($value as $vv){
                        $filterValue[] = $vv;
                        $filterSql[] = '?';
                    }
                    $filterSql = join(',',$filterSql);
                }else{
                    $filterValue[] = $value;
                    $filterSql = '?';
                }
                switch($link){
                    case '=':
                        $tsql = ' AND '.$fieldName.' = ?';
                    break;
                    case '!=':
                        $tsql = ' AND '.$fieldName.' != ?';
                    break;
                    case '<=':
                    case '>=':
                    case '<':
                    case '>':
                        $tsql = ' AND '.$fieldName.' '.$link.' ?';
                    break;
                    
                    case '%~':
                        $fv = array_pop($filterValue);
                        $fv = "%{$fv}";
                        $filterValue[] = $fv;
                        $tsql = " AND {$fieldName} LIKE ?";
                    break;
                    case '~%':
                        $fv = array_pop($filterValue);
                        $fv = "{$fv}%";
                        $filterValue[] = $fv;
                        $tsql = " AND {$fieldName} LIKE ?";
                    break;
                    case '%':
                        $fv = array_pop($filterValue);
                        $fv = "%{$fv}%";
                        $filterValue[] = $fv;
                        $tsql = " AND {$fieldName} LIKE ?";
                    break;
                    
                    case 'in':
                        $tsql = ' AND '.$fieldName.' in ('.$filterSql.')';
                    break;
                    case 'notin':
                        $tsql = ' AND '.$fieldName.' not in ('.$filterSql.')';
                    break;
                }
                $where .= $tsql;
            }
        }elseif(!empty($filter)){
            $where .= ' AND '.$filter;
        }
        return $where;
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
    public function getList($filter = '',$field = '*',$offset = 0,$limit = '-1',$orderby = '',$groupby = ''){
        $field = empty($field)?'*':$field;
        $filterValue = array();
        $where = $this->filter( $filter ,$filterValue);
        $offset = empty($offset) ? 0: $offset;
        if( !empty($limit) && $limit != '-1' ) $_limit = $offset.','.$limit;

        $sql = "SELECT {$field} FROM {$this->table} WHERE {$where}";
        if(!empty($orderby)) $sql .= " ORDER BY {$orderby}";
        if(!empty($groupby)) $sql .= " GROUP BY {$groupby}";
        if(!empty($_limit))  $sql .= " LIMIT {$_limit}";

        
        $this->getSlave()->prepareExecute($sql,$filterValue);
        return $this->getSlave()->fetchAll();
    }

    /**
     * 查询单条数据
     *
     * @param array $filter
     * @return miexd
     */
    public function getOne($filter = '',$field = '*',$orderby='',$groupby = ''){
        if(is_array($filter) || is_string($filter)){
            $filter = $filter;
        }elseif(is_numeric($filter)){
            $filter = array($this->pri => $filter);
        }
        $data = $this->getList($filter,$field,0,1,$orderby,$groupby);
        if(is_array($data) && isset($data[0])) return $data[0];
        else return array();
    }

    /**
     * 保存数据
     * 
     * 判断传入标准结构内是否含有主键，如有主键则update，无主键则insert。
     * @param array $data 标准结构
     * @return mixed
     */
    public function save(&$data = array()){
        $result = true;
        if(isset($data[$this->pri]) && $data[$this->pri]!= ''){
            $pri_id = $data[$this->pri];
            $filter = array($this->pri=>$pri_id);
            $oldData = $this->getOne($filter,$this->pri);
            if( isset($oldData[$this->pri]) && $oldData[$this->pri]!='' ){
                if( $this->update($data,$filter) ) $result = true;
                else $result = false;
            }else{
                if( $this->insert($data) === false ) $result = false;
                else $result = true;
            }
        }else{
            if( $insertId = $this->insert($data) ){
                $data[$this->pri] = $insertId;
                $result = true;
            }else{
                $result = false;
            }
        }
        return $result;
    }

    /**
     * 统计
     *
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function count($filter = array()){
        $filterValue = array();
        $where = $this->filter( $filter ,$filterValue);
        $sql = "SELECT COUNT(*) AS count FROM {$this->table} WHERE {$where}";
        $this->getSlave()->prepareExecute($sql,$filterValue);
        $result = $this->getSlave()->fetch();
        return $result['count'];
    }

    /**
     * 插入
     *
     * @param array $data 数据
     * @return miexd
     */
    public function insert($data = array()){
        if(!$data) return false;

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

        $sql = "INSERT INTO {$this->table}({$fieldSql}) VALUES({$valuePrepareSql})";
        $result = $this->getMaster()->prepareExecute($sql, $valueReal);
        $insertId = $this->getMaster()->getInsertId();
        return $insertId;
    }

    /**
     * 更新
     *
     * @param array $data 数据
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function update($data = array(),$filter = array()){
        if(!$data) return false;

        $filterValue = array();
        $where = $this->filter( $filter ,$filterValue);
        $fieldSql = array();
        $valueReal = array();
        foreach($data as $k=>$v){
            $fieldSql[] = "`{$k}` = ?";
            $valueReal[] = $v === NULL ? 'null' : $v;
        }
        $paramsValue = array_merge($valueReal,$filterValue);
        $fieldSql = join(',',$fieldSql);

        $sql = "UPDATE {$this->table} SET {$fieldSql} WHERE {$where}";
        return $this->getMaster()->prepareExecute($sql,$paramsValue);
    }

    /**
     * 删除
     *
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function delete($filter = array()){
        $filterValue = array();
        $where = $this->filter( $filter ,$filterValue);
        $sql = "DELETE FROM {$this->table} WHERE {$where}";
        return $this->getMaster()->prepareExecute($sql,$filterValue);
    }

}

?>