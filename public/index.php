<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 18:40
 */
//默认应用的入口文件
define('DEBUG',true);//开启调试模式，默认为关闭
define('APP_NAME','index');//必须声明应用名称

//注册composer自动加载
require dirname(__DIR__) . '/vendor/autoload.php';

//第三方ORM组件的配置
//示例文件在qwer/config目录中
//要用第三方ORM组件除了ormDatabase.php外，还要开启 ANOTHER_ORM_ON 配置项，默认开启
$ormDatabaseConfig = get_ormDatabaseConfig('/app/' . APP_NAME . '/config/ormDatabase.php');

//加载框架启动器
require dirname(__DIR__) . '/bootstrap.php';

//第三方路由组件的配置
//示例文件在qwer/config目录中
//要用第三方路由组件除了要加载routes.php外，还要开启 ANOTHER_ROUTE_ON 配置项，默认开启
require dirname(__DIR__) . '/app/' . APP_NAME . '/config/routes.php';
