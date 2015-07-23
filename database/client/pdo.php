<?php 
/**
 * PDO
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_client_pdo{
    private $pdo;
    private $dsn = '';
    private $username = 'root';
    private $password = '';



    public function __construct($dsn = '',$options = array()){
        $this->dsn      = $dsn;
        $this->username = $options['username'];
        $this->password = $options['password'];
        $this->connect();
    }


    /**
     * 数据库连接
     * 
     * @param array $option 数据库连接信息
     * @return bool
     */
    protected function connect(){
        try{
            $this->pdo = new PDO($this->dsn,$this->username,$this->password);

        }catch(PDOException $e){
            throw new core_exception( $e->getMessage() );
        }
    }


    /**
	 * 执行查询
	 * 
	 * @param string $sql
	 * @return resource
	 */
    public function query($sql = ''){
        if(!$sql) return false;
        $result = $this->pdo->query($sql);
        if ($result instanceof PDOStatement) {
            $result->setFetchMode(PDO::FETCH_ASSOC);
        }else{
            if($this->pdo->errorCode() !== '00000'){
                throw new core_exception( join(',',$this->pdo->errorInfo()) );
            }
        }
        return $result;
    }

    /**
     * 
     * 
     * @param string $sql
     * @return resource
     */
    public function exec($sql = ''){
        if(!$sql) return false;
        $result = $this->pdo->exec($sql);
        if ($result instanceof PDOStatement) {
            $result->setFetchMode(PDO::FETCH_ASSOC);
        }else{
            if($this->pdo->errorCode() !== '00000'){
                throw new core_exception( join(',',$this->pdo->errorInfo()) );
            }
        }
        return $result;
    }
    

    /**
     * 格式化资源数据为数组
     * 
     * @param object $PDOStatement
     * @return array
     */
    public function fetch($PDOStatement){
        $result = array();
        
        $data = $PDOStatement->fetch();
        if($data){
            $result = $data;
            $data = null;
        }else{
            $PDOStatement->closeCursor();
        }
        return $result;
    }

}
