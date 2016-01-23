<?php
class core_thread{

    public $resource;
    public $pipes;
    public $descriptorspec;
    private $startTime;

    
    /**
     * 析构
     *
     * @param string $script PHP脚本名
     * @return void
     */
    public function __construct($script = ''){
        $this->descriptorspec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w')
        );
        $this->resource = proc_open($script, $this->descriptorspec, $this->pipes);
        $this->startTime = time();
    }

    /**
     * 获取线程状态信息
     *
     * @param void
     * @return boolean
     */
    public function getProcStatus(){
        return proc_get_status($this->resource);
    }

    /**
     * 关闭线程
     *
     * @param void
     * @return boolean
     */
    public function close(){
        return proc_close($this->resource);
    }

    /**
     * 检查运行是否超时
     *
     * @param void
     * @return boolean
     */
    public function isOverExecuted($maxExecTime){
        if (($this->startTime + $maxExecTime) < time())
            return true;
        else
            return false;
    }

    /**
     * 获取脚本开始执行时间
     *
     * @param void
     * @return boolean
     */
    public function getStartTime() {
        return $this->startTime;
    }
}
?>