<?php
/**
 * DEBUG
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_debug{

	private static $info = array();
    private static $hashkeyTimeline = array();
    private static $sql = array();
    private static $counter = array();


    protected static function getTime(){
        return microtime(true);
    }

    protected static function getIncludedFiles(){
        return get_included_files();
    }

    public static function getFomatTime($beginTime = 0,$endTime = 0){
        return number_format( round(($endTime - $beginTime),3),3 ).'s';
    }

    public static function getSizeUsage($usage = 0){
        $unit = array('b','kb','mb','gb'); 
        return round($usage/pow(1024,($i=floor(log($usage,1024)))),2).''.$unit[$i]; 
    }

    public static function info($info = '',$hashkey = '',$type = 'info'){
        if($info == '') return false;
        $execTime = self::getFomatTime();
        $time = self::getTime();
        $newHashkey = md5($time.$info);

        if($hashkey != '' && isset(self::$hashkeyTimeline[$hashkey])){
            $execTime = self::getFomatTime(self::$hashkeyTimeline[$hashkey],$time);
        }else{
            self::$hashkeyTimeline[$newHashkey] = $time;
        }

        $debugInfo = array(
            'info' => $info,
            'time' => $execTime,
            'memory' => self::getSizeUsage(memory_get_usage()),
        );

        if( core::isCli() == true ){
            if(core::getConfig('core_debug') == true) echo $debugInfo['info'].' (Time: '.$debugInfo['time'].' , Memory: '.$debugInfo['memory'].")\n";
        }else{
            if($type == 'info'){
                self::$info[$newHashkey] = $debugInfo;
            }elseif($type == 'sql'){
                self::$sql[$newHashkey] = $debugInfo;
            }
            
        }
        return $newHashkey;
    }


    public static function upTime($hashkey = '',$type = 'info'){
        if($hashkey == '') return false;
        $time = self::getTime();
        $execTime = self::getFomatTime(self::$hashkeyTimeline[$hashkey],$time);
        unset(self::$hashkeyTimeline[$hashkey]);
        if( core::isCli() == false ){
            if($type == 'info'){
                self::$info[$hashkey]['time'] = $execTime;
            }elseif($type == 'sql'){
                self::$sql[$hashkey]['time'] = $execTime;
            }
        }
        return $execTime;
    }
    

    public static function setCounter($type = ''){
        self::$counter[$type] += 1;
    }

    public static function getCounter($type = ''){
        if($type == ''){
            return self::$counter;
        }else{
            if(isset(self::$counter[$type])) return self::$counter[$type];
            else return 0;
        }
    }


    public static function output(){
        $included = self::getIncludedFiles();
        $endinfo = end(self::$info);
        $sql = self::$sql;
        $sqlQueryTimes = 0;
        foreach(self::$sql as $v) $sqlQueryTimes += str_replace('s', '', $v['time']);
        $sqlQueryTimes = self::getFomatTime(0,$sqlQueryTimes);

        $html = "<style>.core-debug-output{ font:12px Tahoma,Consolas;border:1px #ccc solid;margin-top:10px; }.core-debug-output span{display:block;height:20px;line-height:20px;text-align:center;background:#F3F3F3;}.core-debug-output p{margin:0;padding:3px 5px;border-top:1px solid #ddd;}.core-debug-output p font{background:#ddd;}</style>";
        //Basic
        $html .= "<div class='core-debug-output'><span><b>Basic</b></span>";
        $html .= "<p><b>RunTime: </b><font>".$endinfo['time']."</font></p>";
        $html .= "<p><b>Memory: </b><font>".$endinfo['memory']."</font></p>";
        $html .= "<p><b>SQL: </b><font>QueryTime=".$sqlQueryTimes."</font>　<font>SELECT=".self::getCounter('sql_select')."</font>　<font>INSERT=".self::getCounter('sql_insert')."</font>　<font>UPDATE=".self::getCounter('sql_update')."</font>　<font>DELETE=".self::getCounter('sql_delete')."</font></p>";
        $html .= "<p><b>Include: </b><font>".count($included)."</font></p>";
        $html .= "<p><b>Cache: </b><font>SET=".self::getCounter('cache_set')."</font>　<font>GET=".self::getCounter('cache_get')."</font>　<font>DELETE=".self::getCounter('cache_delete')."</font>　<font>FLUSH=".self::getCounter('cache_flush')."</font></p>";
        $html .= "<p><b>Request: </b><font>GET=".self::getCounter('request_get')."</font>　<font>POST=".self::getCounter('request_post')."</font></p>";
        $html .= "</div>";

        //Flow
        $html .= "<div class='core-debug-output'><span><b>Flow</b>  (".count(self::$info).")</span>";
        foreach(self::$info as $v){
            $html .= '<p>'.$v['info']."　<font>Time: ".$v['time']."</font>　<font>Memory: ".$v['memory']."</font></p>";
        }
        $html .= "</div>";

        //Sql
        $html .= "<div class='core-debug-output'><span><b>SQL</b> (".count(self::$sql).")</span>";
        foreach(self::$sql as $v){
            $html .= '<p>'.$v['info']."　<font>QueryTime: ".$v['time']."</font></p>";
        }
        $html .= "</div>";

        //Include
        $html .= "<div class='core-debug-output'><span><b>Include</b> (".count($included).")</span>";
        foreach($included as $v){
            $fileinfo = core::i('core_file')->getFileInfo($v);
            $html .= '<p>'.$v."　<font>Size: ".self::getSizeUsage($fileinfo['size'])."</font></p>";
        }
        $html .= "</div>";
        echo $html;
    }
}