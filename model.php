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
    public $db;
    public $pri;

    /**
	 * 构造函数
     *
	 * @return void
	 */
    public function __construct(){
        if(!$this->dbConfig){
            $this->dbConfig = core::getConfig('db_config');
        }
        $this->db = core_database_client::factory('mysqli',$this->dbConfig);
        $this->pri = $this->db->getTablePri($this->table);
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
                    case 'likeBefore':
                        $tsql = ' AND '.$fieldName.' like \'%'.$value.'\'';
                    break;
                    case 'likeAfter':
                        $tsql = ' AND '.$fieldName.' like \''.$value.'%\'';
                    break;
                    case 'like':
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
        }else{
            $where .= $filter;
        }
        return $where;
    }

    /**
	 * 查询列表数据
	 * 
     * @param string $table     操作表名
     * @param string $field     返回字段
     * @param string $filter    filter过滤条件
     * @param number $offset    offset
     * @param number $limit     limit
     * @param string $order     排序 如：id DESC
	 * @return mixed
	 */
    public function getList($field = '*',$filter = '',$offset = 0,$limit = '-1',$orderby = '',$groupby = ''){
        $field = empty($field)?'*':$field;
        $_filter = $this->filter( $filter );
        $filter = (empty($_filter) ? ' 1 ' : $_filter);
        $offset = empty($offset) ? 0: $offset;
        if( !empty($limit) and $limit != '-1' ) $_limit = $offset.','.$limit;
        $result = $this->db->select($this->table,$field,$filter,$orderby,$_limit,$groupby);
        return $result;
    }


    /**
	 * 保存数据
	 * 
     * 判断传入sdf标准结构内是否含有主键，如有主键则update，无主键则insert。
     * @param array $sdf     标准结构
	 * @return mixed
	 */
    public function save(&$sdf=array()){
        $rs = true;
        $pri_id = $sdf[$this->pri];
        if(isset($sdf[$this->pri]) && $sdf[$this->pri]!= ''){
            $filter = array($this->pri=>$pri_id);
            $_s = $this->getOne($filter,$this->pri);
            if( isset($_s[$this->pri]) && $_s[$this->pri]!='' ){
                if( $this->update($sdf,$filter) ) $rs = true;
                else $rs = false;
            }else{
                if( $this->insert($sdf) === false ) $rs = false;
                else $rs = true;
            }
        }else{
            if( $this->insert($sdf) ){
                $sdf[$this->pri] = $this->db->getInstance()->getInsertId();
                $rs = true;
            }else{
                $rs = false;
            }
        }
        return $rs;
    }

    /**
     * 统计
     *
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function count($filter = array()){
        return $this->db->count( $this->table ,$this->filter( $filter ));
    }

    /**
     * 插入
     *
     * @param array $data 数据
     * @return miexd
     */
    public function insert($data = array()){
        return $this->db->insert( $this->table ,$data);
    }

    /**
     * 更新
     *
     * @param array $data 数据
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function update($data = array(),$filter = array()){
        return $this->db->update( $this->table ,$data,$this->filter( $filter ));
    }

    /**
     * 删除
     *
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function delete($filter = array()){
        return $this->db->delete( $this->table ,$this->filter( $filter ));
    }

    /**
     * 查询单条数据
     *
     * @param array $filter
     * @return miexd
     */
    public function getOne($filter = array(),$field = '*',$orderby=''){
        if(is_array($filter)){
            $filter = $filter;
        }else{
            $filter = array(
                $this->pri => $filter,
            );
        }
        $data = $this->getList($field,$filter,0,1,$orderby);
        if(is_array($data) && isset($data[0])) return $data[0];
        else return array();
    }

}
