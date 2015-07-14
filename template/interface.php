<?php 
/**
 * 模版引擎接口
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
interface core_template_interface{


	/**
     * 模板变量赋值
     * 
     * @param string $var 变量名
     * @param string $val 变量值
     * @return void
     */
    public function assign($var = '',$val = '');


    /**
     * 模板渲染
     * 
     * @param string $template 模板文件
     * @return htmlcode
     */
    public function fetch($template = '');


    /**
     * 模板渲染输出
     * 
     * @param string $template 模板文件
     * @return html
     */
    public function display($template = '');


}

?>