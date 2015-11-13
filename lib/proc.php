<?php
class core_proc{
    /**
     * 当前运行的线程数
     * @var Integer
     */
    private $threadRunning = 0;


    /**
     * 运行中的线程对象
     * @var Array
     */
    private $running = array();
    
    
    /**
     * 子进程最大可执行时间，单位：秒，默认1小时
     * @var int
     */
    private $maxExecTime = 10;
     
    
    /**
     * 设置子进程最大可执行时间，单位：秒
     *
     * @param int $sec
     */
    public function setMaxExecTime($sec){
        $this->maxExecTime = $sec;
    }
    
    /**
     * 获取子进程最大可执行时间，单位：秒
     *
     * @return int $sec
     */
    public function getMaxExecTime(){
        $sec = $this->maxExecTime;
        return $sec;
    }


    /**
     * 多进程
     *
     * @param string $script 脚本路径
     * @param string $params 脚本参数
     * @param string $process 子进程数
     * @param int $max
     */
    public function exec($script = '',$params = '',$process = 5){
        while(true) {
            //创建子进程
            while ($this->threadRunning < $process) {
                $this->running[] = new core_thread($script,$params);
                $this->threadRunning++;
            }

            //检查是否已经结束
            if ($this->threadRunning == 0) {
                break;
            }

            //等待代码执行完成
            usleep(1000);

            //记录进程的关闭状态
            $thread_close = array();

            //检查已经完成的任务
            foreach ($this->running as $idx => $thread) {
                if ( !$thread->isRunning() || $thread->isOverExecuted($this->maxExecTime) ) {
                    $thread_close[] = proc_close($thread->resource);//记录进程的关闭状态
                    unset($this->running[$idx]);
                    $this->threadRunning--;
                }
            }
        }
    }

}
?>