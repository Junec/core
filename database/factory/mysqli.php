<?php 
/**
 * Mysqli
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_factory_mysqli extends core_database_factory_mysql{


    /**
     * 数据库连接
     * 
     * @param array $option 数据库连接信息
     * @return bool
     */
    public function connect(){
        $hashkey = core_debug::info('connect mysqli: '.$this->server.' , connection ...');
        $this->resource = new mysqli();
        $this->resource->connect($this->server,$this->username,$this->password,$this->database);
        if($this->resource->connect_error){
            $this->error($this->resource->connect_errno,$this->resource->connect_error);
        }
        core_debug::info('connect mysqli: '.$this->server.' , ok.',$hashkey);
        $this->resource->query("SET NAMES ".$this->language);
        return true;
    }


    public function getInsertId(){
        return $this->resource->insert_id;
    }


    /**
	 * 执行查询
	 * 
	 * @param string $sql
	 * @return resource
	 */
    public function query($sql = ''){
        if(!$sql) return false;
        $hashkey = core_debug::info($sql,'','sql');
        $result = $this->resource->query($sql);
        if(!$result){
            $this->error($this->resource->errno,$this->resource->error);
        }
        core_debug::upTime($hashkey,'sql');
        return $result;
    }
    
    /**
	 * 格式化资源数据为数组
	 * 
     * @param resource $resource
	 * @return array
	 */
    public function fetchArray($resource){
        $formatArray = array();
        if($resource->num_rows > 0){
            while( $temp= $resource->fetch_array(MYSQL_ASSOC)){
                if($temp){
                    foreach($temp as $k=>$v) $data[$k] = $v;
                    $formatArray[] = $data;
                    $data = null;
                }
            }
            $resource->close();
        }
        return $formatArray;
    }
}
