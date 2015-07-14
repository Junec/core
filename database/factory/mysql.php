<?php 
/**
 * Mysql
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_database_factory_mysql extends core_database_abstract implements core_database_interface{

    protected $server = 'localhost';
    protected $username = 'root';
    protected $password = '';
    protected $language = 'utf8';
    protected $port = 3306;
    protected $database = '';
    private $options = array();


    public function __construct($options = array()){
        $this->options = $options;
        $this->server = $this->options['server'];
        $this->username = $this->options['username'];
        $this->password = $this->options['password'];
        $this->language = $this->options['language'] == '' ? $this->language : $this->options['language'];
        $this->port = $this->options['port'] == '' ? $this->port : $this->options['port'];
        $this->database = $this->options['database'];
        $this->connect();
    }


    /**
     * 数据库连接
     * 
     * @param array $option 数据库连接信息
     * @return bool
     */
    public function connect(){
        $hashkey = core_debug::info('connect mysql: '.$this->server.' , connection ...');
        $this->resource = mysql_connect($this->server,$this->username,$this->password,MYSQL_CLIENT_COMPRESS);
        if(!$this->resource){
            $this->error(mysql_errno(),mysql_error());
        }
        mysql_select_db($this->database,$this->resource);
        mysql_query("SET NAMES ".$this->language);
        core_debug::info('connect mysql: '.$this->server.' , ok.',$hashkey);
        return true;
    }


    /**
     * 插入数据
     * 
     * @param string $table 表名
     * @param array $data 插入数据数组
     * @return bool or insert_id
     */
    public function insert($table = '',$data = array()){
        if( $table == '' || !$data ) return false;
        $valueSql = $fieldSql = array();
        foreach($data as $field=>$value){
            $fieldSql[] =  '`'.trim($field).'`';
            $valueSql[] =  $value===NULL?'null':"'".$this->escapeString($value)."'";
        }
        $fieldSql = join(',',$fieldSql);
        $valueSql = join(',',$valueSql);
        $sql = 'INSERT INTO '.'`'.$table.'`('.$fieldSql.') '.'VALUES('.$valueSql.')';
        core_debug::setCounter('sql_insert');
        $result = $this->query($sql);

        if($result) return $this->getInsertId();
        else return $result;
    }


    public function getInsertId(){
        return mysql_insert_id();
    }


    /**
     * 查询数据
     * 
     * @param string $table 表名
     * @param array $field 查询字段
     * @param string $where 过滤条件
     * @param string $groupby 分组
     * @param string $orderby 排序条件
     * @param string $limit 偏移值
     * @param string $join 联合查询
     * @return array
     */
    public function select($table = '',$field = '*',$where = '',$orderby = '',$limit = '',$groupby = '',$join = ''){
        if( $table == '' || $field == '') return false;
        if($join != ''){
            $tableSql = $table.' '.$join;
        }else{
            $tableSql = $table;
        }
        core_debug::setCounter('sql_select');
        $sql = 'SELECT '.$field.' FROM '.'`'.$tableSql.'`';
        if($where != ''){
            $sql .= ' WHERE '.$where;
        }
        if($groupby != ''){
            $sql .= ' GROUP BY '.$groupby;
        }
        if($orderby != ''){
            $sql .= ' ORDER BY '.$orderby;
        }
        if($limit != ''){
            $sql .= ' LIMIT '.$limit;
        }
        return $this->fetchArray( $this->query($sql) );
    }

    /**
	 * 统计数据
	 * 
	 * @param string $table 表名
     * @param string $where 过滤条件
     * @param string $groupby 分组
     * @param string $join 联合查询
	 * @return number
	 */
    public function count($table = '',$where = '',$groupby = '',$join = ''){
        $rs = $this->select($table,'COUNT(*) AS _count',$where,'','',$groupby,$join);
        return $rs[0]['_count'];
    }

    /**
     * 删除数据
     * 
     * @param string $table 表名
     * @param string $where 过滤条件
     * @return bool
     */
    public function delete($table = '',$where = ''){
        if( $table == '' || $where == '' ) return false;
        $where = empty($where)?'':' WHERE '.$where;
        $sql = 'DELETE FROM '.'`'.$table.'`'.$where;

        return $this->query($sql);
    }
    

    /**
     * 更新数据
     * 
     * @param string $table 表名
     * @param array $data 更新数据数组
     * @param string $where 过滤条件
     * @return bool
     */
    public function update($table = '',$data = array(),$where = ''){
        if( $table == '' || !$data || $where == '' ) return false;
        $fieldSql = '';
        foreach($data as $k=>$v){
            $fieldSql[] = $v==NULL?'`'.$k.'`'.'= null':'`'.$k.'`'.'=\''.$this->escapeString($v).'\'';
        }
        $fieldSql = join(',',$fieldSql);
        $where = empty($where)?'':' WHERE '.$where;
        $sql = 'UPDATE `'.$table.'` SET '.$fieldSql.$where;
        
        return $this->query($sql);
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
        $result = mysql_query($sql);
        if(!$result){
            $this->error(mysql_errno(),mysql_error());
        }
        core_debug::upTime($hashkey,'sql');
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
    public function fetchArray($resource){
        $formatArray = array();
        if(is_resource($resource)){
            while($temp = mysql_fetch_array($resource,MYSQL_ASSOC)){
                if($temp){
                    foreach($temp as $k=>$v) $data[$k] = $v;
                    $formatArray[] = $data;
                    $data = null;
                }
            }
            mysql_free_result($resource);
        }
        return $formatArray;
    }

    /**
	 * 获取表主键
	 * 
     * @param string $table 表名
	 * @return string
	 */
    public function getTablePri($table = ''){
        $sql = 'DESC `'.$table.'`';
        $resource = $this->query($sql);
        $result = $this->fetchArray($resource);
        foreach($result as $v){
            if( $v['Key'] == 'PRI' ){
                return $v['Field'];
                break;
            }
        }
    }
}
