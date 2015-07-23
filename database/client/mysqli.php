<?php 
/**
 * Mysqli
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_client_mysqli extends core_database_client_mysql{


    /**
     * 数据库连接
     * 
     * @param array $option 数据库连接信息
     * @return bool
     */
    protected function connect(){
        $this->mysqli = new mysqli();
        $this->mysqli->connect($this->server,$this->username,$this->password,$this->database);
        if($this->mysqli->connect_error){
            $this->error($this->mysqli->connect_errno,$this->mysqli->connect_error);
        }
        $this->mysqli->query("SET NAMES ".$this->language);
        return true;
    }


    /**
	 * 执行查询
	 * 
	 * @param string $sql
	 * @return resource
	 */
    public function query($sql = ''){
        if(!$sql) return false;
        $result = $this->mysqli->query($sql);
        if(!$result){
            $this->error($this->mysqli->errno,$this->mysqli->error);
        }
        return $result;
    }


    /**
     * 格式化资源数据为数组
     * 
     * @param resource $resource
     * @return array
     */
    public function fetch($resource){
        $result = array();
        if($resource->num_rows > 0){
            $temp= $resource->fetch_array(MYSQL_ASSOC);
            if($temp){
                foreach($temp as $k=>$v) $data[$k] = $v;
                $result = $data;
                $data = null;
            }else{
                $resource->close();
            }
        }
        return $result;
    }

}
