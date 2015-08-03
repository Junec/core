<?php
/**
 * 模型基类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
abstract class core_model{
    public $table = '';
    public $dbConfig = array();
    public $pri;
    private $db;

    /**
	 * 构造函数
     *
	 * @return void
	 */
    public function __construct(){
        if(!$this->dbConfig) $this->dbConfig = core::getConfig('db_config');
        $this->db = core_database_factory::getInstance('pdo',$this->dbConfig);
    }

    public function getdb(){
        return $this->db;
    }


    /**
	 * 解析filter
	 * 
	 * @param array or string $filter
	 * @return string filter
	 */
    public function filter( $filter = array(),$tablePrefix = null ){
        $where = ' 1 ';
        if( is_array($filter) ){
            foreach($filter as $field=>$value){
                $isIn = false;
                if(is_array($value)){
                    $value = '\''.join('\',\'',$value).'\'';
                    $isIn = true;
                }
                if( stripos($field,'|') === false){
                    if($isIn == true)    $field = $field.'|in';
                    else $field = $field.'|=';
                }
                $field = explode('|',$field);
                if($tablePrefix != null) $fieldName = $tablePrefix.'.'.$field[0];
                else $fieldName = $field[0];

                $link = $field[1];
                switch($link){
                    case '=':
                        if(is_null($value)) $tsql = ' AND '.$fieldName.' is null';
                        else $tsql = ' AND '.$fieldName.' '.$link.' \''.$value.'\'';
                    break;
                    case '!=':
                        if(is_null($value)) $tsql = ' AND '.$fieldName.' is not null';
                        else $tsql = ' AND '.$fieldName.' '.$link.' \''.$value.'\'';
                    break;
                    case '<=':
                    case '>=':
                    case '<':
                    case '>':
                        $tsql = ' AND '.$fieldName.' '.$link.' \''.$value.'\'';
                    break;
                    case '%~':
                        $tsql = ' AND '.$fieldName.' like \'%'.$value.'\'';
                    break;
                    case '~%':
                        $tsql = ' AND '.$fieldName.' like \''.$value.'%\'';
                    break;
                    case '%':
                        $tsql = ' AND '.$fieldName.' like \'%'.$value.'%\'';
                    break;
                    case 'in':
                        $tsql = ' AND '.$fieldName.' in ('.$value.')';
                    break;
                    case 'notin':
                        $tsql = ' AND '.$fieldName.' not in ('.$value.')';
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
        $where = $this->filter( $filter );
        $offset = empty($offset) ? 0: $offset;
        if( !empty($limit) && $limit != '-1' ) $_limit = $offset.','.$limit;
        $sql = "SELECT {$field} FROM {$this->table} WHERE {$where}";
        if(!empty($orderby)) $sql .= " ORDER BY {$orderby}";
        if(!empty($groupby)) $sql .= " GROUP BY {$groupby}";
        if(!empty($_limit))  $sql .= " LIMIT {$_limit}";
        $result = $this->db->select($sql);
        return $result;
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
     * @param array $data     标准结构
	 * @return mixed
	 */
    public function save(&$data=array()){
        if( !$data ) return false;
        if(isset($data[$this->pri]) && $data[$this->pri]!= ''){
            $filter = array($this->pri=>$data[$this->pri]);
            $result = $this->update($data,$filter);
        }else{
            if( $result = $this->insert($data) ){
                $data[$this->pri] = $result;
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
        $where = $this->filter($filter);
        $sql = "SELECT COUNT(*) AS _count FROM {$this->table} WHERE {$where}";
        $result = $this->getdb()->select($sql);
        return $result[0]['_count'];
    }

    /**
     * 插入
     *
     * @param array $datas 数据(支持批量插入)
     * @return miexd
     */
    public function insert($datas = array()){
        if( !$datas ) return false;
        $insertId = array();
        if( !isset($datas[0]) ){
            $datas = array($datas);
        }
        foreach($datas as $data){
            $valueSql = $fieldSql = array();
            foreach($data as $field=>$value){
                $fieldSql[] =  '`'.trim($field).'`';
                $valueSql[] =  $value===NULL?'null':"'".$value."'";
            }
            $fieldSql = join(',',$fieldSql);
            $valueSql = join(',',$valueSql);
            $sql = "INSERT INTO {$this->table}({$fieldSql}) VALUES({$valueSql})";
            $this->getdb()->exec($sql);
            $insertId[] = $this->getdb()->getInsertId();
        }
        return count($insertId) > 1 ? $insertId : $insertId[0];
    }

    /**
     * 更新
     *
     * @param array $data 数据
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function update($data = array(),$filter = array()){
        $where = $this->filter($filter);
        if(!$data) return false;
        $fieldSql = '';
        foreach($data as $k=>$v){
            $fieldSql[] = $v==NULL?'`'.$k.'`'.'= null':'`'.$k.'`'.'=\''.$v.'\'';
        }
        $fieldSql = join(',',$fieldSql);
        $sql = "UPDATE {$this->table} SET {$fieldSql} WHERE {$where}";
        $result = $this->getdb()->exec($sql);
        return empty($result)? false : $result;
    }

    /**
     * 删除
     *
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function delete($filter = array()){
        $where = $this->filter($filter);
        $sql = "DELETE FROM {$this->table} WHERE {$where}";
        $result = $this->getdb()->exec($sql);
        return empty($result)? false : $result;
    }

    

}
