<?php 
/**
 * PDO
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_client_pdo{

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

    }


    /**
	 * 执行查询
	 * 
	 * @param string $sql
	 * @return resource
	 */
    public function query($sql = ''){

    }
    

    /**
	 * 开启事务
	 * 
	 * @return bool
	 */
    public function begin(){

    }


    /**
	 * 回滚事务
	 * 
	 * @return bool
	 */
    public function rollback(){

    }


    /**
	 * 提交事务
	 * 
	 * @return bool
	 */
    public function commit(){

    }



    /**
     * 格式化资源数据为数组
     * 
     * @param resource $resource
     * @return array
     */
    public function fetch($resource){

    }

}
