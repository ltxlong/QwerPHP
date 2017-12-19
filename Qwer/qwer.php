<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 17:25
 */

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
            self::_import_file();//载入必须类
        }else{
            //上线阶段，关闭错误提示
            error_reporting(0);
            //载入合并的必须类
            require TEMP_PATH . '/~boot.php';
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
        define('CONFIG_PATH',QWER_PATH . '/Config');
        define('DATA_PATH',QWER_PATH . '/Data');
        define('LIB_PATH',QWER_PATH . '/Lib');
        define('CORE_PATH',LIB_PATH . '/Core');
        define('EXTENDS_PATH',QWER_PATH . '/Extends');
        define('TOOL_PATH',EXTENDS_PATH . '/Tool');
        define('ORG_PATH',EXTENDS_PATH . '/Org');
        define('FUNCTION_PATH',LIB_PATH . '/Function');
        define('ROOT_PATH',dirname(QWER_PATH));//根目录
        //临时目录
        define('TEMP_PATH',ROOT_PATH . '/Temp');
        //日志目录
        define('LOG_PATH',TEMP_PATH . '/Log');
        //应用目录
        define('APP_PATH',ROOT_PATH . '/' . APP_NAME);
        define('APP_CONFIG_PATH',APP_PATH . '/Config');
        define('APP_CONTROLLER_PATH',APP_PATH . '/Controller');
        define('APP_TPL_PATH',APP_PATH . '/Tpl');
        define('APP_PUBLIC_PATH',APP_TPL_PATH . '/Public');
        define('APP_COMPILE_PATH',TEMP_PATH . '/' . APP_NAME . '/Compile');
        define('APP_CACHE_PATH',TEMP_PATH . '/' . APP_NAME . '/Cache');
        define('APP_DEFAULT_THEME_PATH',APP_PUBLIC_PATH . '/default');
        define('APP_DEFAULT_CSS_PATH',APP_DEFAULT_THEME_PATH . '/css');
        define('APP_DEFAULT_IMAGES_PATH',APP_DEFAULT_THEME_PATH . '/images');
        define('APP_DEFAULT_JS_PATH',APP_DEFAULT_THEME_PATH . '/js');
        //公共目录
        define('COMMON_PATH',ROOT_PATH . '/Common');
        //公共配置项文件夹
        define('COMMON_CONFIG_PATH',COMMON_PATH . '/Config');
        //公共模型文件夹
        define('COMMON_MODEL_PATH',COMMON_PATH . '/Model');
        //公共库文件夹
        define('COMMON_LIB_PATH',COMMON_PATH . '/Lib');
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
            COMMON_MODEL_PATH,
            COMMON_LIB_PATH,
            APP_PATH,
            APP_CONFIG_PATH,
            APP_CONTROLLER_PATH,
            APP_TPL_PATH,
            APP_PUBLIC_PATH,
            APP_DEFAULT_THEME_PATH,
            APP_DEFAULT_CSS_PATH,
            APP_DEFAULT_IMAGES_PATH,
            APP_DEFAULT_JS_PATH,
            TEMP_PATH,
            APP_COMPILE_PATH,
            APP_CACHE_PATH,
            LOG_PATH

        );
        foreach ($arr as $v){
            is_dir($v) || mkdir($v,0777,true);//判断目录是否已经存在，不存在则创建
        }
    }

    /**
     * [_import_file]
     * 载入框架所需文件
     */
    private static function _import_file()
    {
        //加载顺序固定
        $fileArr = array(
            CORE_PATH . '/Log.class.php',
            FUNCTION_PATH . '/functions.php',
            ORG_PATH . '/Smarty/Smarty.class.php',
            CORE_PATH . '/SmartyView.class.php',
            CORE_PATH . '/Controller.class.php',
            CORE_PATH . '/Application.class.php'
        );
        $str = '';
        foreach ($fileArr as $v){
            $str .= trim(substr(file_get_contents($v), 5));//把以上所有必须的文件压到一个字符串里面
            $str .= PHP_EOL;
            $str .= PHP_EOL;
            require_once $v;
        }

        $str = "<?php" . PHP_EOL . $str;
        file_put_contents(TEMP_PATH . '/~boot.php', $str) || die('access not allow');//将这个字符串写入到临时文件夹的~boot.php中（上线的时候，加载这个文件就可以了），如果写入失败则提示没权限
    }
}
qwer::run();