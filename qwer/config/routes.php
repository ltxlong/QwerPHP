<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2018/1/8
 * Time: 1:21
 */
use NoahBuscher\Macaw\Macaw;

/*
//示例：
define('INDEX_CONTROLLER','app\index\controllers\\');
Macaw::get('',INDEX_CONTROLLER .'indexController@index');
*/

Macaw::get('', function (){
    echo '第三方路由配置实例~';
});
Macaw::$error_callback = function (){
    throw new Exception("路由无匹配项 404 Not Found");
};

Macaw::dispatch();

