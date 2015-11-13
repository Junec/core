<?php 
/**
 * 缓存类接口
 *
 * @author chenjun <594553417@qq.com>
 * @copyright  Copyright (c) 2008-2015 Technologies Inc.
*/
interface core_cache_interface{

    /**
	 * 设置缓存
	 * 
	 * @param string $key    唯一标识
     * @param $value    缓存数据
     * @param $overdueTime 过期时间 单位:秒[如为空则永不过期]
	 * @return bool
	 */
    public function set($key = '',$value = '',$overdueTime = '');


    /**
	 * 获取缓存
	 * 
	 * @param string $key    唯一标识
     * @param string $filed    字段 value、dateline
	 * @return data
	 */
    public function get($key = '');


    /**
	 * 删除缓存
	 * 
	 * @param string $key    唯一标识
	 * @return bool
	 */
    public function del($key = '');


    /**
	 * 清空缓存
	 * 
	 * @return bool
	 */
    public function flush();
}
