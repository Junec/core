<?php
/**
 * 数据库操作公共方法类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
abstract class core_database_adapter_abstract{
    public $client;

    public function query($sql = ''){
        return $this->client->query($sql);
    }

    public function exec($sql = ''){
        return $this->client->exec($sql);
    }

    public function fetch(){
        return $this->client->fetch();
    }

    public function fetchAll(){
        $result = array();
        while($temp = $this->fetch()){
            if($temp){
                foreach($temp as $k=>$v) $data[$k] = $v;
                $result[] = $data;
                $data = null;
            }
        }
        return $result;
    }

    public function select($sql = ''){
        $this->query($sql);
        return $this->fetchAll();
    }

    public function getInsertId(){
        return $this->client->getInsertId();
    }

    public function filter( $filter = array() ){}

    public function begin(){}

    public function rollback(){}

    public function commit(){}
    
    abstract function insert($table = '',$data = array());

    abstract function update($table = '',$data = array(),$filter = array());

    abstract function delete($table = '',$filter = array());

    abstract function count($table = '',$filter = array());

    abstract function getList($table = '',$filter = '',$field = '*',$offset = 0,$limit = '-1',$orderby = '',$groupby = '');

    abstract function getOne($table = '',$filter = '',$field = '*',$orderby='',$groupby = '');
}