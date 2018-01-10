<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 17:25
 */
use core\Application;
/**
 * 框架核心类
 * Class Qwer
 */
final class qwer
{
    public static function run()
    {
        self::_set_const();//设置常量
        defined('DEBUG') || define('DEBUG',false);//看调试参数是否有定义，有则拿用户的值，否则默认为关闭调试模式
        if(DEBUG){
            //开发阶段，开启调试模式
            self::_create_dir();//创建文件夹
        }else{
            //上线阶段，关闭错误提示
            error_reporting(0);
        }
        Application::run();//执行应用类
    }

    /**
     * [_set_const]
     * 设置框架所需常量
     */
    private static function _set_const()
    {
        $path = str_replace('\\','/',__FILE__);//将反斜杠\替换为正斜杠/，这样Windows、Mac、Linux等都适用
        define('QWER_PATH',dirname($path));//框架目录
        define('CONFIG_PATH',QWER_PATH . '/config');
        define('TPL_PATH',QWER_PATH . '/tpl');
        define('LIB_PATH',QWER_PATH . '/lib');
        define('CORE_PATH',LIB_PATH . '/core');
        define('FUNCTION_PATH',LIB_PATH . '/function');
        define('ROOT_PATH',dirname(QWER_PATH));//根目录
        //临时目录
        define('RUNTIME_PATH',ROOT_PATH . '/runtime');
        //日志目录
        define('LOG_PATH',RUNTIME_PATH . '/log');
        //应用目录
        define('APP_PATH',ROOT_PATH . '/app/' . APP_NAME);
        define('APP_CONFIG_PATH',APP_PATH . '/config');
        define('APP_CONTROLLERS_PATH',APP_PATH . '/controllers');
        define('APP_VIEWS_PATH',APP_PATH . '/views');
        define('APP_MODELS_PATH',APP_PATH . '/models');
        define('APP_PUBLIC_PATH',APP_VIEWS_PATH . '/public');
        define('APP_COMPILE_PATH',RUNTIME_PATH . '/' . APP_NAME . '/compile');
        define('APP_CACHE_PATH',RUNTIME_PATH . '/' . APP_NAME . '/cache');
        define('APP_DEFAULT_THEME_PATH',APP_PUBLIC_PATH . '/default');
        define('APP_DEFAULT_CSS_PATH',APP_DEFAULT_THEME_PATH . '/css');
        define('APP_DEFAULT_IMAGES_PATH',APP_DEFAULT_THEME_PATH . '/images');
        define('APP_DEFAULT_JS_PATH',APP_DEFAULT_THEME_PATH . '/js');
        //公共目录
        define('COMMON_PATH',ROOT_PATH . '/common');
        //公共配置项文件夹
        define('COMMON_CONFIG_PATH',COMMON_PATH . '/config');
        //公共库文件夹
        define('COMMON_SERVICES_PATH',COMMON_PATH . '/services');
        //框架版本
        define('QWER_VERSION','1.0');

        define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false);
        define('IS_GET', ($_SERVER['REQUEST_METHOD'] == 'GET') ? true : false);
        define('IS_PUT', ($_SERVER['REQUEST_METHOD'] == 'PUT') ? true : false);
        define('IS_DELETE', ($_SERVER['REQUEST_METHOD'] == 'DELETE') ? true : false);
        define('IS_AJAX', (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false);


    }

    /**
     * [_create_dir]
     * 创建文件夹（创建用户应用和公共的文件夹）
     */
    private static function _create_dir()
    {
        $arr = array(
            COMMON_CONFIG_PATH,
            COMMON_SERVICES_PATH,
            APP_PATH,
            APP_CONFIG_PATH,
            APP_CONTROLLERS_PATH,
            APP_VIEWS_PATH,
            APP_MODELS_PATH,
            APP_PUBLIC_PATH,
            APP_DEFAULT_THEME_PATH,
            APP_DEFAULT_CSS_PATH,
            APP_DEFAULT_IMAGES_PATH,
            APP_DEFAULT_JS_PATH,
            RUNTIME_PATH,
            APP_COMPILE_PATH,
            APP_CACHE_PATH,
            LOG_PATH

        );
        foreach ($arr as $v){
            is_dir($v) || mkdir($v,0777,true);//判断目录是否已经存在，不存在则创建
        }
    }
}
qwer::run();