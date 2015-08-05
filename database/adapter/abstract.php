<?php
/**
 * 数据库操作公共方法类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
abstract class core_database_adapter_abstract{
    public $client;

    public function addslashes($string = ''){
        if(!get_magic_quotes_gpc()){
            $string = addslashes($string);
        }
        return $string;
    }

    public function stripslashes($string = ''){
        if(get_magic_quotes_gpc()){
            $string = stripslashes($string);
        }
        return $string;
    }

    public function begin(){
        $this->exec('START TRANSACTION');
        return true;
    }

    public function rollback(){
        $this->exec('ROLLBACK');
        $this->exec('END');
        return true;
    }

    public function commit(){
        $this->exec('COMMIT');
        $this->exec('END');
        return true;
    }

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

}