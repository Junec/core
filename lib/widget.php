<?php
/**
 * 组件
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
        $params = $this->params($params);
        $this->render->display($this->tpl());
    }


}
?>