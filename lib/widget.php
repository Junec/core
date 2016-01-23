<?php
/**
 * 组件基类
 * 
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
abstract class core_widget{

    public function __construct(){
        $this->render = core::instance('core_page')->getRender();
        $this->render->template_dir    = core::getConfig('widget_dir');
        $this->render->mark = 'widget';
    }

    public function params($params = array()){
        return $params;
    }

    public function display($params = array()){
        $loadWidgetHashkey = core_debug::info('load widget: '.get_class($this));
        $params = $this->params($params);
        $compileWidgetHashkey = core_debug::info('compile widget: '.$this->tpl());
        $this->render->display($this->tpl());
        core_debug::upTime($loadWidgetHashkey);
        core_debug::upTime($compileWidgetHashkey);
    }

}
?>