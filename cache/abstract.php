<?php
/**
 * 缓存公共方法类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
abstract class core_cache_abstract{
    protected function getKey($key = ''){
        return md5($key);
    }
}