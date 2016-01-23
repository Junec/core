<?php
/**
 * PAGE渲染类
 * 
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_page{

	public function getRender(){
        $render = core::instance('core_template');
        $render->template_dir    = core::getConfig('template_dir');
        $render->compile_dir     = core::getConfig('compile_dir');
        $render->tpl_left_delim  = core::getConfig('tpl_left_delim');
        $render->tpl_right_delim = core::getConfig('tpl_right_delim');
        return $render;
    }

    /**
     * 模板变量赋值
     * 
     * @param string $var 变量名
     * @param string $val 变量值
     * @return void
     */
    public function assign($var = '',$val = ''){
    	return $this->getRender()->assign($var,$val);
    }


    /**
     * 模板渲染
     * 
     * @param string $template 模板文件
     * @return htmlcode
     */
    public function fetch($template = ''){
        $hashkey = core_debug::info('compile template: '.$template);
        $html = $this->getRender()->fetch($template);
        core_debug::upTime($hashkey);
        return $html;
    }


    /**
     * 模板渲染输出
     * 
     * @param string $template 模板文件
     * @return html
     */
    public function display($template = ''){
        echo $template."<br>";
        echo $this->fetch($template);
    }




}
?>