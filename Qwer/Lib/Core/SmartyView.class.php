<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 19:04
 */
namespace core;
/**
 * Class SmartyView
 * 此类为本框架与Smarty的桥梁
 */

class SmartyView
{

    /**
     * @var null
     * 用于保存Smarty对象
     */
    private static $smarty = NULL;

    /**
     * SmartyView constructor.
     */
    public function __construct()
    {
        if(!is_null(self::$smarty)) return;
        $smarty = new \Smarty();

        if(C('ANOTHER_ROUTE_ON')){
            //如果开启了第三方路由组件
            $cNameArr = array_reverse(explode('\\',get_called_class()));
            $controllerName = substr($cNameArr[0],0,-10);
        }else{
            $controllerName = CONTROLLER;
        }
        //模板目录
        $smarty->template_dir = APP_VIEWS_PATH . '/' . strtolower($controllerName) . '/';
        //编译目录
        $smarty->compile_dir = APP_COMPILE_PATH;
        //定界符
        $smarty->left_delimiter = C('LEFT_DELIMITER');
        $smarty->right_delimiter = C('RIGHT_DELIMITER');
        //缓存
        $smarty->cache_dir = APP_CACHE_PATH;
        //缓存开启开关
        $smarty->caching = C('CACHE_ON');
        //缓存时间
        $smarty->cache_lifetime = C('CACHE_TIME');

        self::$smarty = $smarty;
    }

    /**
     * [display]
     * 调用Smarty的display方法
     * @param $tpl
     */
    protected function display($tpl)
    {
        self::$smarty->display($tpl);
    }

    /**
     * [assign]
     * 调用Smarty的assign方法
     * @param $varName
     * @param $varValue
     */
    protected function assign($varName,$varValue)
    {
        self::$smarty->assign($varName,$varValue);
    }

    protected function isCached($tpl=NULL)
    {
        if(!C('SMARTY_ON')) halt('请先开启Smarty！');
        $tpl = $this->get_tpl($tpl);
        //请注意，旧版本的smarty判断是否开启缓存的方法是is_cached，新版本改为isCached
        return self::$smarty->isCached($tpl,$_SERVER['REQUEST_URI']);
    }
}

