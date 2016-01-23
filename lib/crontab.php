<?php
class core_crontab{
    protected $crontabsModel;
    protected $crontabsLogsModel;
    private $threadRunning = 0;
    private $running = array();
    private $sleepTime= 1;

    public function __construct(){
        $this->crontabsModel = core::instance('crontabs_model');
        $this->crontabsLogsModel = core::instance('crontabs_logs_model');
    }

    public function addScript($jobId = '', $script = '', $nextExecTime = 0){
        $this->scripts[$jobId] = $script;
        $this->scriptsNextExecTime[$jobId] = $nextExecTime;
        $this->crontabsModel->update(array('status'=>1),array('id'=>$job['id']));
    }

    public function exec(){
        foreach($this->scripts as $jobId => $script) {
            $this->running[$jobId] = new core_thread($script);
            $this->threadRunning++;
        }
        //检查已经完成的任务
        while(true){
            if ($this->threadRunning == 0) {
                break;
            }
            sleep( $this->sleepTime );
            $time = time();
            foreach ($this->running as $jobId => $thread) {
                $thredRunStatus = $thread->getProcStatus();
                $thredRunTime = $thread->getStartTime();
                if ( !$thredRunStatus['running'] ) {//是否运行中
                    if( $thredRunStatus['exitcode'] == 0 ) $status = 0;//成功
                    else $status = 1;//异常
                    $thread->close();//关闭线程
                    $this->update($jobId, $status, $thredRunTime, $time-$this->sleepTime );
                    unset($this->running[$jobId]);
                    $this->threadRunning--;
                }
            }
        }
    }

    public function update($jobId, $status = 0, $startTime, $endTime){
        $this->crontabsModel->update(array('status'=>0,'time'=>$this->scriptsNextExecTime[$jobId]),array('id'=>$jobId));
        $this->crontabsLogsModel->insert(array('jobId'=>$jobId,'status'=>$status,'startTime'=>$startTime,'endTime'=>$endTime));
    }

}
?>