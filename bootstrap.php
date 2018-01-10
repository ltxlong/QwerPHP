<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2018/1/8
 * Time: 1:27
 */
//载入框架核心类
require __DIR__  . "/qwer/qwer.php";

if(DEBUG && C('ANOTHER_HALT_ON')){
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

use Illuminate\Database\Capsule\Manager as Capsule;
if(C('ANOTHER_ORM_ON')){
    $capsule = new Capsule;
    $capsule->addConnection($ormDatabaseConfig);
    $capsule->bootEloquent();
}

