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
        //如果smarty开启，则用smarty的assi方法，否则用本框架的assign方法
        if(C('SMARTY_ON')){
            parent::assign($varName,$varValue);
        }else{
            $this->_vars[$varName] = $varValue;
        }

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

