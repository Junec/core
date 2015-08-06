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

    public function filter( $filter = array() ){
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
                $fieldName = $field[0];

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