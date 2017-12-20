<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 19:06
 */

/**
 * Class Application
 * 应用类
 */
final class Application
{
    public static function run()
    {
        self::_init();//初始化框架
        set_error_handler(array(__CLASS__,'error'));//美化错误处理页面
        register_shutdown_function(array(__CLASS__,'fatal_error'));//美化致命错误页面
        self::_user_import();//载入用户自定义功能
        self::_set_url();//设置外部路径
        spl_autoload_register(array(__CLASS__,'_autoload'));//注册自动载入
        self::_create_demo();//创建欢迎使用框架页面
        self::_app_run();//实例化应用管理器
    }

    /**
     * [fatal_error]
     * 抓取致命错误
     */
    public static function fatal_error()
    {
        if($e = error_get_last()){
            self::error($e['type'],$e['message'],$e['file'],$e['line']);
        }
    }
    /**
     * [error]
     * 加载自己美化的错误处理页面
     * @param $errno
     * @param $error
     * @param $file
     * @param $line
     */
    public static function error($errno, $error, $file, $line)
    {
        switch ($errno){
            //以下五个是致命错误
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $msg = $error . $file . " 第{$line}行";
                halt($msg);

            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                if(DEBUG){
                    include DATA_PATH . '/Tpl/notice.php';
                }
                break;
        }
    }
    /**
     * [_autoload]
     * 自动载入功能
     * @param $className
     */
    private static function _autoload($className)
    {
        switch (true){
            //判断是否是控制器controller，控制器的固定格式是XxxController.class.php
            case strlen($className) > 10 && substr($className,-10) == 'Controller':
                $path = APP_CONTROLLER_PATH . '/'. $className . '.class.php';//固定类的后缀是.class.php
                if(!is_file($path)){
                    $emptyPath = APP_CONTROLLER_PATH . '/EmptyController.class.php';
                    if(is_file($emptyPath)){
                        include $emptyPath;
                        return;
                    }else{
                        halt($path . '控制器未找到');
                    }

                }
                include $path;
                break;
            //判断是否是模型Model，模型的固定格式是XxxModel.class.php
            case strlen($className) > 5 && substr($className, -5) == 'Model':
                $path = COMMON_MODEL_PATH . '/' . $className . '.class.php';//固定类的后缀是.class.php
                is_file($path) || halt($path . '模型未找到');
                include $path;
                break;
            default:
                //默认是工具类
                $path = TOOL_PATH . '/' . $className . '.class.php';//固定类的后缀是.class.php
                is_file($path) || halt($path . '类未找到');
                include $path;
                break;
        }

    }
    /**
     * [_init]
     * 初始化框架
     */
    private static function _init()
    {
        //加载配置项，并且使用户配置的优先级最高(用户应用配置>公共配置>框架配置)
        //加载框架配置项
        C(include CONFIG_PATH . '/config.php');

        //公共配置项
        $commonPath = COMMON_CONFIG_PATH . '/' .'/config.php';

        $commonConfig = <<<str
<?php
return array(
    //配置项 => 配置值
);
str;

        is_file($commonPath) || file_put_contents($commonPath,$commonConfig);//如果公共配置项文件不存在，则创建
        //加载公共配置项
        C(include $commonPath);

        //用户配置项
        $userPath = APP_CONFIG_PATH . '/config.php';

        $userConfig = <<<str
<?php
return array(
    //配置项 => 配置值
);
str;
        is_file($userPath) || file_put_contents($userPath,$userConfig);//如果用户配置项文件不存在，则创建
        //加载用户配置项
        C(include $userPath);

        //设置默认时区
        date_default_timezone_set(C('DEFAULT_TIME_ZONE'));

        //是否开启session
        C('SESSION_AUTO_START') && session_start();

        //创建默认header和footer文件
        $defaultHeaderPath = APP_DEFAULT_THEME_PATH . '/' . C('DEFAULT_HEADER') . C('TEMPLATE_SUFFIX');
        $defaultHeaderCode = <<<str
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
str;
        is_file($defaultHeaderPath) || file_put_contents($defaultHeaderPath,$defaultHeaderCode);//如果默认主题没有header文件则创建

        $defaultFooterPath = APP_DEFAULT_THEME_PATH . '/' . C('DEFAULT_FOOTER') . C('TEMPLATE_SUFFIX');
        $defaultFooterCode = <<<str
</body>
</html>
str;
        is_file($defaultFooterPath) || file_put_contents($defaultFooterPath,$defaultFooterCode);//如果默认主题没有footer文件则创建


    }

    /**
     * [_set_url]
     * 设置外部路径
     */
    private static function _set_url()
    {
        //p($_SERVER);
        $path = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        $path = str_replace('\\','/',$path);
        define('__APP__', $path);
        define('__ROOT__', dirname(__APP__));

        define('__TPL__', __ROOT__ . '/' . APP_NAME . '/Tpl');
        define('__PUBLIC__', __TPL__ . '/Public');
    }

    /**
     * [_create_demo]
     * 创建默认控制器
     */
    private static function _create_demo()
    {
        $path = APP_CONTROLLER_PATH . '/IndexController.class.php';
        $str = <<<str
<?php
class IndexController extends Controller
{
    public function index()
    {
        header('charset=utf-8');
        echo '<h1 style="text-align: center;margin-top: 90px;">'.'欢迎使用QwerPHP框架！'.'</h1>';
    }
}
str;

        is_file($path) || file_put_contents($path, $str);//如果默认控制器不存在，则创建（用于初次运行框架时显示的欢迎页）

    }

    /**
     * [_app_run]
     * 实例化应用控制器
     */
    private static function _app_run()
    {
        if(C('RewriteRule_ON')){
            //如果开启了rewrite解析开关
            if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '/'){
                $rPath = $_SERVER['REQUEST_URI'];
                $rPathArr = explode('/',trim($rPath,'/'));
                $c = ucfirst($rPathArr[0]);
                unset($rPathArr[0]);
                $a = empty($rPathArr[1]) ? 'index' : $rPathArr[1];
                unset($rPathArr[1]);

                //url 多余部分转换成 GET
                $countNum = count($rPathArr) + 2;
                $i = 2;
                while ($i < $countNum){
                    if(isset($rPathArr[$i + 1])){
                        $_GET[$rPathArr[$i]] = $rPathArr[$i + 1];
                    }
                    $i = $i + 2;
                }
            }else{
                $c = 'Index';
                $a = 'index';
            }
        }else{
            $c = isset($_GET[C('VAR_CONTROLLER')]) ? $_GET[C('VAR_CONTROLLER')] : 'Index';
            $a = isset($_GET[C('VAR_ACTION')]) ? $_GET[C('VAR_ACTION')] : 'index';
        }

        define('CONTROLLER', $c);//定义访问的控制器常量
        define('ACTION', $a);//定义访问的方法常量

        $c .= 'Controller';//固定了控制器类名格式为xxxController
        if(class_exists($c)){
            $obj = new $c();
            if(!method_exists($obj, $a))
            {
                //如果方法不存在
                if(method_exists($obj, '__empty')){
                    $obj->__empty();
                }else{
                    //如果__empty()都不存在
                    halt($c . '控制器中' . $a . '方法不存在！');
                }
            }else{
                $obj->$a();
            }

        }else{
            $obj = new EmptyController();
            $obj->index();
        }

    }

    /**
     * [_user_import]
     * 自动加载Common/Lib目录下的文件，可以载入多个
     * 可以实现用户自定义扩展功能，比如扩展自己定义的函数
     */
    private static function _user_import()
    {
        $fileArr = C('AUTO_LOAD_FILE');
        if(is_array($fileArr) && !empty($fileArr)){
            foreach ($fileArr as $v)
            {
                $path = COMMON_LIB_PATH . '/' . $v;
                is_file($path) || halt($path . '文件不存在');
                require_once $path;
            }
        }
    }
}

