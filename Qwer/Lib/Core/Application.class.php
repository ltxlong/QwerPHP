<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 19:06
 */
namespace core;
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
        self::_set_url();//设置外部路径
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
                    include TPL_PATH . '/notice.php';
                }
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
        $commonPath = COMMON_CONFIG_PATH . '/config.php';

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

        //创建框架默认扩展模型的示例文件
        $app_name = APP_NAME;
        $exampleModelPath = APP_MODELS_PATH . '/exampleModel.php';
        $exampleModelStr = <<<str
<?php
namespace app\\$app_name\models;

use core\Model;
//框架默认扩展模型的示例文件
class exampleModel extends Model
{
    //配置对应的表名，一个扩展模型对应一个张表
    public \$table = 'example';
    //自定义扩展业务逻辑函数
    public function get_all_data()
    {
        //可以在数据存取前增加业务逻辑等等
        return \$this->all();
    }
}

str;
        if(C('CREATE_EXAMPLE_MODEL_ON')){
            //如果开启了创建框架默认扩展模型示例文件的配置项
            is_file($exampleModelPath) || file_put_contents($exampleModelPath,$exampleModelStr);//如果框架默认扩展模型示例文件不存在，则创建
        }

        //创建第三方路由默认配置文件
        $routesPath = APP_CONFIG_PATH . '/routes.php';
        $routesConfig = <<<str
<?php
use NoahBuscher\Macaw\Macaw;

define('INDEX_CONTROLLER','app\index\controllers\\\');
Macaw::get('',INDEX_CONTROLLER .'IndexController@index');
Macaw::error(function (){
    throw new Exception("路由无匹配项 404 Not Found");
});
Macaw::dispatch();

str;
        is_file($routesPath) || file_put_contents($routesPath,$routesConfig);//如果第三方路由配置文件不存在，则创建

        //创建第三方ORM组件默认配置文件
        $ormPath = APP_CONFIG_PATH . '/ormDatabase.php';
        $ormConfig = <<<str
<?php
return [
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => '',
    'username'  => '',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => ''
];

str;
        is_file($ormPath) || file_put_contents($ormPath,$ormConfig);//如果第三方ORM配置文件不存在，则创建
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
        $path = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        $path = str_replace('\\','/',$path);
        define('__APP__', $path);
        define('__ROOT__', dirname(__APP__));

        define('__VIEWS__', __ROOT__ . '/' . APP_NAME . '/views');
        define('__PUBLIC__', __VIEWS__ . '/public');
    }

    /**
     * [_create_demo]
     * 创建默认控制器
     */
    private static function _create_demo()
    {
        $app_name = APP_NAME;
        $path = APP_CONTROLLERS_PATH . '/IndexController.php';
        $str = <<<str
<?php
namespace app\\$app_name\controllers;

use core\Controller;

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
        if(C('ANOTHER_ROUTE_ON')){
            //如果开启了第三方路由组件
            return;
        }
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
                $c = 'index';
                $a = 'index';
            }
        }else{
            $c = isset($_GET[C('VAR_CONTROLLER')]) ? ucfirst($_GET[C('VAR_CONTROLLER')]) : 'index';
            $a = isset($_GET[C('VAR_ACTION')]) ? $_GET[C('VAR_ACTION')] : 'index';
        }

        define('CONTROLLER', $c);//定义访问的控制器常量
        define('ACTION', $a);//定义访问的方法常量

        $c .= 'Controller';//固定了控制器类名格式为xxxController

        $classPath = '\\app\\' . APP_NAME . '\controllers\\'. $c;

        if(class_exists($classPath)){//不用is_file判断，改为用class_exist

            $ref = new \ReflectionClass($classPath);

            //检查类是否可实例化, 排除抽象类abstract和对象接口interface
            if(!$ref->isInstantiable()){
                halt('实例化异常！不能实例化该类：'.$c);
            }

            $instance = $ref->newInstance();

            if(!$ref->hasMethod($a))
            {
                //如果方法不存在
                if($ref->hasMethod('__empty')){
                    $instance->__empty();
                }else{
                    //如果__empty()都不存在
                    halt($c . '控制器中' . $a . '方法不存在！');
                }
            }else{
                $instance->$a();
            }

        }else{
            $empthClass = '\\app\\'.APP_NAME . '\controllers\EmptyController';
            if(class_exists($empthClass)){//不用is_file判断，改为用class_exist

                $ref = new \ReflectionClass($empthClass);

                if(!$ref->isInstantiable()){
                    halt('实例化异常！不能实例化该类：'.$c);
                }

                $instance = $ref->newInstance();

                if($ref->hasMethod('index')){
                    $instance->index();
                    return;
                }else{
                    if($ref->hasMethod('__empty')){
                        $instance->__empty();
                        return;
                    }else{
                        halt($c . '控制器未找到');
                    }
                }
            }else{
                halt($c . '控制器未找到');
            }
        }

    }

}

