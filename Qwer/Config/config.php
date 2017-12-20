<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 18:50
 */

return array(
    //验证码位数
    'CODE_LEN' => 4,
    //默认时区
    'DEFAULT_TIME_ZONE' => 'PRC',
    //session自动开启
    'SESSION_AUTO_START' => true,
    //伪静态Url解析开关(支持controller/action的方式，至于index.php的隐藏要在相应的服务器配置文件进行配置)
    'RewriteRule_ON' => true,
    //url的控制器访问变量
    'VAR_CONTROLLER' => 'c',
    //url的方法访问变量
    'VAR_ACTION' => 'a',
    //是否开启日志
    'SAVE_LOG' => true,
    //错误跳转的地址
    'ERROR_URL' => '',
    //错误提示的信息
    'ERROR_MSG' => '网站出错了，请稍后再试...',
    //自动加载Common/Lib目录下的文件，可以载入多个，比如array('func1.php','func2.php','func3.php'),array()则为不加载
    'AUTO_LOAD_FILE' => array(),
    //视图模板默认后缀
    'TEMPLATE_SUFFIX' => '.html',
    //默认主题路径
    'DEFAULT_THEME' => 'default',
    //默认主题头部
    'DEFAULT_HEADER' => 'header',
    //默认主题尾部
    'DEFAULT_FOOTER' => 'footer',

    //数据库配置项
    'DB_CHARSET' => 'utf8',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_USER' => 'root',
    'DB_PASSWORD' =>'',
    'DB_DATABASE' =>'',
    'DB_PREFIX' =>'',

    //Smarty配置项
    'SMARTY_ON' => true,
    'LEFT_DELIMITER' => '{',
    'RIGHT_DELIMITER' => '}',
    'CACHE_ON' => false,
    'CACHE_TIME' => 60
);

