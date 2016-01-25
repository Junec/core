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

        if( strpos(PHP_SAPI,'cli') === true ){
            if(core::getConfig('core_debug') == true) echo $debugInfo['info'].' (Time: '.$debugInfo['time'].' , Memory: '.$debugInfo['memory'].")\n";
        }else{
            if($type == 'info'){
                self::$info[$newHashkey] = $debugInfo;
            }
        }
        return $newHashkey;
    }


    public static function upTime($hashkey = '',$type = 'info'){
        if($hashkey == '') return false;
        $time = self::getTime();
        $execTime = self::getFomatTime(self::$hashkeyTimeline[$hashkey],$time);
        unset(self::$hashkeyTimeline[$hashkey]);
        if( strpos(PHP_SAPI,'cli') === false ){
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

        $html = "<style>.core-debug-output{ font:12px Consolas,Courier;color:#333;}.core-debug-output b{color:#2B82C7}.core-debug-output span{display:block;height:20px;line-height:20px;text-align:left;padding-left:5px;background:#F3F3F3;}.core-debug-output p{margin:0;padding:3px 5px;border-bottom:0px solid #ddd;}.core-debug-output p font{}.core-debug-table{font-size:12px;}.core-debug-table tr td{background:#F4F4F4;}</style>";
        //Basic
        $html .= "<div class='core-debug-output'><span><b>Basic</b></span>";
        $html .= "<table class='core-debug-table'>
            <tr>
                <td>RunTime</td>
                <td>".$endinfo['time']."</td>
            </tr>
            <tr>
                <td>Memory</td>
                <td>".$endinfo['memory']."</td>
            </tr>
            <tr>
                <td>Include</td>
                <td>".count($included)."</td>
            </tr>
            <tr>
                <td>Cache</td>
                <td>SET=".self::getCounter('cache_set')." GET=".self::getCounter('cache_get')." DELETE=".self::getCounter('cache_delete')." FLUSH=".self::getCounter('cache_flush')."</td>
            </tr>
        </table>";
        $html .= "</div>";

        //Flow
        $html .= "<div class='core-debug-output'><span><b>Flow</b>  (".count(self::$info).")</span><table class='core-debug-table'><tr><td width=15%>*Time</td><td width=15%>*Memory</td><td>*Info</td></tr>";
        foreach(self::$info as $v){
            $html .= "<tr>
            <td>".$v['time']."</td>
            <td>".$v['memory']."</td>
            <td>".$v['info']."</td></tr>";
        }
        $html .= "</table></div>";

        //Include
        $html .= "<div class='core-debug-output'><span><b>Include</b> (".count($included).")</span><table class='core-debug-table'><tr><td width=15%>*Size</td><td>*File</td></tr>";
        foreach($included as $v){
            $fileinfo = core::instance('core_file')->getFileInfo($v);
            $html .= "<tr>
            <td>".self::getSizeUsage($fileinfo['size'])."</td>
            <td>".$v."</td></tr>";
        }
        $html .= "</table></div>";

        echo $html;
    }
}