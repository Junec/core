<?php
class core_crontab{
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
    

    public function addScript($script = '',$params = ''){
        $this->scripts[] = array('script'=>$script,'params'=>$params);
    }

    public function exec(){
        foreach($this->scripts as $s) {
            $this->running[] = new core_thread($s['script'],$s['params']);
            $this->threadRunning++;
        }
        while(true){
            if ($this->threadRunning == 0) {
                break;
            }
            //记录关闭的进程
            $threadClose = array();

            //检查已经完成的任务
            foreach ($this->running as $idx => $thread) {
                if ( !$thread->isRunning() ) {
                    $threadClose[] = array(
                        'endTime' => time(),
                        'status' => proc_close($thread->resource)
                    );
                    unset($this->running[$idx]);
                    $this->threadRunning--;
                }
            }
        }
    }

}
?>