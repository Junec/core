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
    private $adapter;

    /**
	 * 构造函数
     *
	 * @return void
	 */
    public function __construct(){
        if(!$this->dbConfig) $this->dbConfig = core::getConfig('db_config');
        $this->adapter = core_database_factory::getInstance('pdo',$this->dbConfig);
    }

    public function getAdapter(){
        return $this->adapter;
    }


    /**
	 * 解析filter
	 * 
	 * @param array or string $filter
	 * @return string filter
	 */
    public function filter( $filter = array() ){
        return $this->getAdapter()->filter($filter);
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
        return $this->getAdapter()->getList($this->table,$filter,$field,$offset,$limit,$orderby,$groupby);
    }


    /**
     * 查询单条数据
     *
     * @param array $filter
     * @return miexd
     */
    public function getOne($filter = '',$field = '*',$orderby='',$groupby = ''){
         return $this->getAdapter()->getOne($this->table,$filter,$field,$orderby,$groupby);
    }


    /**
	 * 保存数据
	 * 
     * 判断传入标准结构内是否含有主键，如有主键则update，无主键则insert。
     * @param array $data     标准结构
	 * @return mixed
	 */
    public function save(&$data=array()){
        #return $this->getAdapter()->save(&$data);
    }

    /**
     * 统计
     *
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function count($filter = array()){
        return $this->getAdapter()->count($this->table,$filter);
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
            $insertId[] = $this->getAdapter()->insert($this->table,$data);
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
        return $this->getAdapter()->update($this->table,$data,$filter);
    }

    /**
     * 删除
     *
     * @param array $filter 过滤条件
     * @return miexd
     */
    public function delete($filter = array()){
        return $this->getAdapter()->delete($this->table,$filter);
    }

    

}
