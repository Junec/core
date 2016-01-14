<?php
class core_crontab{

    private $threadRunning = 0;
    private $running = array();
    private $sleepTime= 1;
    

    public function addScript($jobId = '', $script = ''){
        $this->scripts[$jobId] = $script;
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
        $pdo = new core_database_client_pdo(array(
            'server' => 'localhost',
            'username' => 'root',
            'password' => '...',
            'port' => 3306,
            'database' => 'web',
            'charset' => 'utf8',
        ));
        #$time = (int)strtotime(date('Y-m-d H:i:00',$endTime));
        $pdo->exec("UPDATE crontabs SET status = 0,time = time+space*60 WHERE id =".$jobId);
        $pdo->exec("INSERT INTO crontabs_logs(jobId,status,startTime,endTime) VALUES({$jobId},{$status},{$startTime},{$endTime})");
    }

}
?>