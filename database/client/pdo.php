<?php 
/**
 * PDO 
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_client_pdo{
    
    protected $pdo;
    protected $statement;
    protected $dsn;
    protected $drive = 'mysql';
    protected $server = 'localhost';
    protected $port = 3306;
    protected $username = 'root';
    protected $password = '';
    protected $database = '';
    protected $charset = 'utf8';
    protected $initsql = array();


    public function __construct($options = array()){
        if( isset($options['drive']) && !empty($options['drive']) )
            $this->drive    = $options['drive'];

        if( isset($options['server']) && !empty($options['server']) )
            $this->server   = $options['server'];

        if( isset($options['port']) && !empty($options['port']) )
            $this->port     = $options['port'];
        
        if( isset($options['charset']) && !empty($options['charset']) )
            $this->charset  = $options['charset'];

        $this->username = $options['username'];
        $this->password = $options['password'];
        $this->database = $options['database'];

        $this->parsedsn();
        $this->connect();
    }

    private function parsedsn(){
        switch($this->drive){
            case 'mysql':
                $this->dsn = "{$this->drive}:host={$this->server};port={$this->port};dbname={$this->database}";
                $this->initsql[] = "SET NAMES {$this->charset}";
            break;
        }
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
            foreach($this->initsql as $sql) $this->exec($sql);

        }catch(PDOException $e){
            throw new core_exception( $e->getMessage() );
        }
    }


    /**
	 * Query
	 * 
	 * @param string $sql
	 * @return PDOStatement
	 */
    public function query($sql = ''){
        if(!$sql) return false;
        $result = $this->pdo->query($sql);
        if ($result instanceof PDOStatement) {
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $this->statement = $result;
        }else{
            if($this->pdo->errorCode() !== '00000'){
                throw new core_exception( join(',',$this->pdo->errorInfo()) );
            }
        }
        return $result;
    }


    /**
     * Exec
     * 
     * @param string $sql
     * @return resource
     */
    public function exec($sql = ''){
        if(!$sql) return false;
        $result = $this->pdo->exec($sql);
        if($this->pdo->errorCode() !== '00000'){
            throw new core_exception( join(',',$this->pdo->errorInfo()) );
        }
        return $result;
    }
    

    /**
     * 格式化数据
     * 
     * @return array
     */
    public function fetch(){
        $result = array();
        $data = $this->statement->fetch();
        if($data){
            $result = $data;
            $data = null;
        }else{
            $this->statement->closeCursor();
        }
        return $result;
    }


    public function getInsertId(){
        return $this->pdo->lastInsertId();
    }

    public function prepare($sql = '', $driver_options = array()){
        return $this->pdo->prepare($sql,$driver_options);
    }


}
