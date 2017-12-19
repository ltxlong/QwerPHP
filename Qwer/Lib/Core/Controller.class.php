<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 19:05
 */

/**
 * Class Controller
 * 控制器父类
 */
class Controller extends SmartyView
{
    /**
     * 变量保存器
     * @var array
     */
    private $_vars = array();
    /**
     * Controller constructor.
     * 控制器父类的构造方法
     */
    public function __construct()
    {
        if(C('SMARTY_ON')){
            //如果smarty开启，则执行SmartyView类的构造方法
            parent::__construct();
        }
        //为了父类和子类的构造方法都执行而不被同名覆盖
        //子类的构造方法要默认写为__init()
        if(method_exists($this, '__init')){
            $this->__init();
        }
        //子类的子类的构造方法要默认写为__auto()
        if(method_exists($this, '__auto')){
            $this->__auto();
        }
        //框架默认只有两层，如果有子类的子类的子类要构造方法，则要增加新的

    }

    /**
     * [get_tpl]
     * 获取模板路径方法
     * @param $tpl
     * @return string
     */
    protected function get_tpl($tpl)
    {
        if(is_null($tpl)){
            $path = APP_TPL_PATH . '/' . CONTROLLER . '/' . ACTION . C('TEMPLATE_SUFFIX');
        }else{
            $suffix = strrchr($tpl,'.');//判断传的参数有没有后缀
            $tpl = empty($suffix) ? $tpl . C('TEMPLATE_SUFFIX') : $tpl;//如果传的参数有后缀（比如'.html','.php','.tpl'），则按照传参的后缀
            $path = APP_TPL_PATH . '/' . CONTROLLER . '/' .$tpl;
        }
        return $path;
    }

    /**
     * [display]
     * 展示模板方法
     * @param null $tpl
     */
    protected function display($tpl=NULL)
    {
        $path = $this->get_tpl($tpl);
        if(!is_file($path)) halt($path . '模板文件不存在');
        //如果smarty开启，则用smarty的display方法，否则用本框架的display方法
        if(C('SMARTY_ON')){
            parent::display($path);
        }else{
            extract($this->_vars);//解包变量
            include $path;//载入模板
        }

    }

    /**
     * [assign]
     * 分配变量给模板
     * 改进方向：将assign和display合成一个方法
     * @param $varName
     * @param $varValue
     */
    protected function assign($varName,$varValue)
    {
        //设置变量 $varName 必须是字符串 $varValue可以是字符串 也可以是对象
        //如果smarty开启，则用smarty的assign方法，否则用本框架的assign方法
        if(C('SMARTY_ON')){
            parent::assign($varName,$varValue);
        }else{
            $this->_vars[$varName] = $varValue;
        }

    }

    /**
     * [render]
     * 整合assign和display两个方法
     * 可以不传参，或传一个参数，或传两个参数
     * @param null $tpl
     * @param array $data
     */
    protected function render($tpl=NULL, $data=array())
    {
        $num_args = func_num_args();
        if($num_args == 0){
            //如果不传参
            $this->display();
        }elseif ($num_args == 1){
            if(is_array(func_get_arg(0))){//如果只传$data
                $data = func_get_arg(0);
                foreach ($data as $k => $v){
                    $this->assign($k, $v);
                }
                $this->display();
            }elseif (is_string(func_get_arg(0))){//如果只传$tpl
                $this->display($tpl);
            }else{
                halt('render方法参数异常！');
            }
        }elseif ($num_args == 2){
            //如果传两个参数
             if((is_array(func_get_arg(0)) && is_string(func_get_arg(1))) || (is_string(func_get_arg(0)) && is_array(func_get_arg(1)))){
                foreach ($data as $k => $v){
                    $this->assign($k, $v);
                }
                $this->display($tpl);
             }else{
                 halt('render方法参数异常！');
             }
        }else{
            halt('render方法参数异常！');
        }

    }

    /**
     * [show]
     * 整合assign和display两个方法
     * 可以不传参，或传一个参数，或传两个参数
     * 和render方法的区别：show方法分离了header和footer
     * 注意：
     * 用show方法的时候，相应的模板要去掉body以上和以下的所有标签，
     * 这样header+模板+footer拼接为一个完整的页面
     * @param null $tpl
     * @param array $data
     */
    protected function show($tpl=NULL, $data=array())
    {
        $num_args = func_num_args();
        $themePath = APP_PUBLIC_PATH . '/' . C('DEFAULT_THEME');
        is_dir($themePath) || halt('主题路径配置错误！相应文件夹不存在！');
        $headerPath = $themePath . '/' . C('DEFAULT_HEADER') . C('TEMPLATE_SUFFIX');
        is_file($headerPath) || halt($headerPath . ' 文件不存在');
        $footerPath = $themePath . '/' . C('DEFAULT_FOOTER') . C('TEMPLATE_SUFFIX');
        is_file($footerPath) || halt($footerPath . ' 文件不存在');

        include_once $headerPath;
        if($num_args == 0){
            //如果不传参
            $this->display();
        }elseif ($num_args == 1){
            if(is_array(func_get_arg(0))){//如果只传$data
                $data = func_get_arg(0);
                foreach ($data as $k => $v){
                    $this->assign($k, $v);
                }
                $this->display();
            }elseif (is_string(func_get_arg(0))){//如果只传$tpl
                $this->display($tpl);
            }else{
                halt('render方法参数异常！');
            }
        }elseif ($num_args == 2){
            //如果传两个参数
            if((is_array(func_get_arg(0)) && is_string(func_get_arg(1))) || (is_string(func_get_arg(0)) && is_array(func_get_arg(1)))){
                foreach ($data as $k => $v){
                    $this->assign($k, $v);
                }
                $this->display($tpl);
            }else{
                halt('render方法参数异常！');
            }
        }else{
            halt('render方法参数异常！');
        }
        include_once $footerPath;
    }

    /**
     * [success]
     * 成功提示方法
     */
    protected function success()
    {
        echo 'success';
    }

    /**
     * [error]
     * 错误提示方法
     */
    protected function error()
    {
        echo 'error';
    }
}

