<?php
/**
 * 分页类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class Core_Library_Pager{
    public $firstName = '首页';
    public $prevName = '上一页';
    public $nextName = '下一页';
    public $lastName = '末页';

    public function pageList($params){
        if( isset($params['key']) and $params['key']!='' ) $_page = $params['key'];
        else $_page = 'page';

        $page = empty($_GET[$_page])?1:$_GET[$_page];
        $pagenums = ceil($params['record']/$params['limit']);
        if($page>$pagenums) $page = $pagenums;
        if($page<=0) $page = 1;

        $offset = ($page-1)*$params['limit'];
        
        $b='';
        $e='';
        //构造分页列表
        if($pagenums<=10){
            $b = 1;$e = $pagenums;
        }else{
            if($page<=10){
                $b = 1;$e = 10;
            }else{
                $b = $page-4;$e = $page+4;
            }
            if($page>$pagenums-10){
                $b = $pagenums-9;    $e = $pagenums;
            }
        }
        $_params = array('key'=>$_page,'page'=>$page,'pagenums'=>$pagenums);
        if($pagenums>0){
            $list = '<div class="pages">';
            $list .= "<a href='".$this->_geturl($_params,'begin')."'>".$this->firstName."</a><a href='";
            if($page>1){
                $list .= $this->_geturl($_params,'prev')."' id='pager_prev'>".$this->prevName."</a>";
            }else{
                $list .= "#' type='normal' >".$this->prevName."</a>";
            }
            #$list .= $page>10?"<a href='".$this->_geturl($_params,'begin',true,$params['url_mode'])."'>1...</a>":'';
            for($i=$b;$i<=$e;$i++){
                $list .= "<a href='".$this->_geturl($_params,$i)."'";
                $list .= $page==$i?"class='active'":"";
                $list .= ">".$i."</a>";
            }
            #$list .= $page<=($pagenums-10)?"<a href='".$this->_geturl($_params,'end',true,$params['url_mode'])."'>".$pagenums."...</a>":'';
            $list .= "<a href='";
            if($page<$pagenums){
                $list .= $this->_geturl($_params,'next')."'  id='pager_next'>".$this->nextName."</a>";
            }else{
                $list .= "#' type='normal'>".$this->nextName."</a>";
            }
            $list .= "<a href='".$this->_geturl($_params,'end')."'>".$this->lastName."</a></div>";
        }
        
        $result = array(
            'list' => $list,
            'offset' => $offset,
            'limit' => $params['limit'],
            'record' => $params['record'],
            'pagenums' => $pagenums,
            'page' => $page,
            'prev' => ($page-1)<=0?'':($page-1),
            'next' => ($page+1)>$pagenums?'':($page+1),

        );
        return $result;
    }

    public function get($params){
        if( isset($params['key']) and $params['key']!='' ) $_page = $params['key'];
        else $_page = 'page';

        $page = empty($_GET[$_page])?1:$_GET[$_page];
        $pagenums = ceil($params['record']/$params['limit']);
        if($page>$pagenums) $page = $pagenums;
        if($page<=0) $page = 1;

        $offset = ($page-1)*$params['limit'];

        $b='';
        $e='';
        //构造分页列表
        if($pagenums<=10){
            $b = 1;$e = $pagenums;
        }else{
            if($page<=10){
                $b = 1;$e = 10;
            }else{
                $b = $page-5;$e = $page+4;
            }
            if($page>$pagenums-10){
                $b = $pagenums-9;    $e = $pagenums;
            }
        }
        $_params = array('key'=>$_page,'page'=>$page,'pagenums'=>$pagenums);
        $number = array();
        if($pagenums>0){
            $list .= $page>10?$number[] = array('num'=>$this->_geturl($_params,'begin')):'';
            for($i=$b;$i<=$e;$i++){
                $number[] = array('num'=>$this->_geturl($_params,$i),'active'=>$page==$i?true:false);
            }
            $list .= $page<=($pagenums-10)?$number[] = array('num'=>$this->_geturl($_params,'end')):'';
        }

        $result = array(
            'number' => $number,
            'offset' => $offset,
            'limit' => $params['limit'],
            'record' => $params['record'],
            'pagenums' => $pagenums,
            'page' => $page,
            'prev' => ($page-1)<=0?'':($page-1),
            'next' => ($page+1)>$pagenums?'':($page+1),

        );
        return $result;
    }

    private function _geturl($_params,$sign){
        switch($sign){
            case 'begin':
                $go_page = 1;
            break;
            case 'end':
                $go_page = $_params['pagenums'];
            break;
            case 'prev':
                $go_page = $_params['page']-1;
            break;
            case 'next':
                $go_page = $_params['page']+1;
            break;
            case is_numeric($sign):
                $go_page = $sign;
            break;
            default:
                $go_page = '#';
            break;
        }
        $get = $_GET;
        $get[$_params['key']] = $go_page;
        $url = '?'.http_build_query($get);
        
        return $url;
    }

}
?>