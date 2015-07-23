<?php 
/**
 * Mysql
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_client_mysql{

    protected $server = 'localhost';
    protected $username = 'root';
    protected $password = '';
    protected $language = 'utf8';
    protected $port = 3306;
    protected $database = '';


    public function __construct($options = array()){
        $this->server   = $options['server'];
        $this->username = $options['username'];
        $this->password = $options['password'];
        $this->language = $options['language'] == '' ? $this->language : $options['language'];
        $this->port     = $options['port'] == '' ? $this->port : $options['port'];
        $this->database = $options['database'];
        $this->connect();
    }


    /**
     * 数据库连接
     * 
     * @param array $option 数据库连接信息
     * @return bool
     */
    protected function connect(){
        $this->resource = mysql_connect($this->server,$this->username,$this->password,MYSQL_CLIENT_COMPRESS);
        if(!$this->resource){
            $this->error(mysql_errno(),mysql_error());
        }
        mysql_select_db($this->database,$this->resource);
        $this->query("SET NAMES ".$this->language);
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
        $result = mysql_query($sql);
        if(!$result){
            $this->error(mysql_errno(),mysql_error());
        }
        return $result;
    }
    

    /**
	 * 开启事务
	 * 
	 * @return bool
	 */
    public function begin(){
        $this->query('START TRANSACTION');
        return true;
    }


    /**
	 * 回滚事务
	 * 
	 * @return bool
	 */
    public function rollback(){
        $this->query('ROLLBACK');
        $this->query('END');
        return true;
    }


    /**
	 * 提交事务
	 * 
	 * @return bool
	 */
    public function commit(){
        $this->query('COMMIT');
        $this->query('END');
        return true;
    }



    /**
     * 格式化资源数据为数组
     * 
     * @param resource $resource
     * @return array
     */
    public function fetch($resource){
        $result = array();
        if(is_resource($resource)){
            $temp = mysql_fetch_array($resource,MYSQL_ASSOC);
            if($temp){
                foreach($temp as $k=>$v) $data[$k] = $v;
                $result = $data;
                $data = null;
            }else{
                mysql_free_result($resource);
            }
        }
        return $result;
    }

}
