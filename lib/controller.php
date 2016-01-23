<?php
/**
 * 控制器基类
 * 
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
abstract class core_controller{
    protected $get = array();
    protected $post = array();
    protected $request = array();
    protected $render;

	/**
     * 控制器初始化
     * 
     * @return void
     */
    public function __construct(){
        $this->get = $_GET;
        $this->post = $_POST;
        $this->request = $_REQUEST;
    }


    /**
     * 执行
     * 
     * @return void
     */
    public function exec($action = ''){
        $return = $this->$action();

    }

    /**
     * 模版对象
     * 
     * @return object
     */
    protected function getRender(){
        return core::instance('core_page')->getRender();
    }

    /**
     * 模板变量赋值
     * 
     * @param string $var 变量名
     * @param string $val 变量值
     * @return void
     */
    protected function assign($var = '',$val = ''){
    	return core::instance('core_page')->assign($var,$val);
    }


    /**
     * 模板渲染
     * 
     * @param string $template 模板文件
     * @return htmlcode
     */
    protected function fetch($template = ''){
        return core::instance('core_page')->fetch($template);
    }


    /**
     * 模板渲染输出
     * 
     * @param string $template 模板文件
     * @return html
     */
    protected function display($template = ''){
        return core::instance('core_page')->display($template);
    }


    /**
     * 页面跳转
     * 
     * @param string $url 跳转地址 为空则后退
     * @param string $alert 弹出信息
     * @return html
     */
    protected function redirect($url = '',$alert = ''){
        if($alert != '') echo "<script>alert('$alert');</script>";
        if($url == '') echo "<script>window.history.back();</script>";
        else echo "<script>window.location.href = '{$url}';</script>";
    }

    /**
     * JSON RESULT
     * 
     * @param string $status 状态
     * @param string/array $response 返回值
     * @return html
     */
    protected function result($status = 'succ',$response = ''){
        $_status = $status == 'succ' ? 'succ' : 'fail';
        $result = array(
            'status' => $_status,
            'response' => $response,
        );
        print_r(json_encode($result));
        exit;
    }

}
?>