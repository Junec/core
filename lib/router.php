<?php
/**
 * 路由解析类
 * 
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_router{
    
    /**
	 * 解析路由
     *
     * @param string $pathinfo
	 * @return QueryString
	 */
    public function parse($pathinfo){
        $queryString = $pathinfo;
        $router = core::getRouter();
        $isPar = strpos($pathinfo,'?');
        if($isPar !== false){
            $urlParams = substr($pathinfo, ($isPar+1));
            $pathinfo = substr($pathinfo, 0,$isPar);
        }
        /* 检查是否满足普通路由 */
        if( isset($router[ $pathinfo ]) ){
            $routerinfo = $router[ $pathinfo ];
            $routerinfo = array_merge($routerinfo ,$this->getUrlParams($urlParams));
            $queryString = http_build_query($routerinfo);
        }else{
            /* 不满足泛路由走泛路由 */
            foreach($router as $rname => $routerinfo){
                if( preg_match("/^\/\^?/", $rname) ){
                    if( preg_match($rname,$pathinfo) ){
                        $routerinfo = array_merge($routerinfo ,$this->getUrlParams($urlParams));
                        foreach($routerinfo as $rik=>$riv){
                            $routerinfo[$rik] = preg_replace($rname, $riv, $pathinfo);
                        }
                        $queryString = http_build_query($routerinfo);
                        break;
                    }
                }
            }
        }
        return $queryString;
    }



    private function getUrlParams($paramsStr = ''){
        $result = array();
        parse_str($paramsStr,$result);
        if(is_array($result) && $result){
            foreach($result as $k=>$v){
                if($v == '') unset($result[$k]);
            }
        }
        return $result;
    }

}
?>