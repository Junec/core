<?php
class core_thread{
    /**
     * 句柄
     * @var resource
     */
    public $resource;

    /**
     * 管道
     * @var resource
     */
    public $pipes;

    /**
     * 脚本开始执行时间
     * @var Integer
     */
    private $startTime;


    /**
     * 析构
     *
     * @param string $executable PHP执行文件名
     * @param string $script PHP脚本名
     * @param string $param 脚本执行参数
     * @param integer $maxExecTime 超时间设置
     * @return void
     */
    public function __construct($script = '',$params = '') {

        $descriptorspec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w')
        );

        $this->resource = proc_open($script." ".$params, $descriptorspec, $this->pipes);
        $this->startTime = time();
    }

    /**
     * 检查任务是否运行中
     *
     * @param void
     * @return boolean
     */
    public function isRunning() {
        $status = proc_get_status($this->resource);
        return $status['status'];
    }

    /**
     * 检查运行是否超时
     *
     * @param void
     * @return boolean
     */
    public function isOverExecuted($maxExecTime) {
        if (($this->startTime + $maxExecTime) < time())
            return true;
        else
            return false;
    }
}
?>