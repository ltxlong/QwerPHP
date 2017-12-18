<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 18:56
 */

/**
 * [halt]
 * 错误提示函数
 * @param $error
 * @param string $level
 * @param int $type
 * @param null $dest
 */
function halt($error,$level='ERROR',$type=3,$dest=NULL)
{
    if(is_array($error)){
        Log::write($error['message'],$level,$type,$dest);
    }else{
        Log::write($error,$level,$type,$dest);
    }

    $e = array();

    if(DEBUG){
        //开启DEBUG的操作
        if(!is_array($error)){
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['line'] = $trace[0]['line'];
            $e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
            $e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';
            ob_start();//打开缓冲区
            debug_print_backtrace();//将错误信息打印到缓存区
            $e['trace'] = htmlspecialchars(ob_get_clean());//实体化，并将错误信息存到这里
        }else{
            $e = $error;
        }

    }else{
        //关闭DEBUG的操作
        if($url = C('ERROR_URL')){
            go($url);
        }else{
            $e['message'] = C('ERROR_MSG');
        }
    }
    include DATA_PATH . '/Tpl/halt.php';
    die;
}
/**
 * [p]
 * 打印函数
 * @param $arr
 */
function p($arr)
{
    if(is_bool($arr)){
        var_dump($arr);
    }elseif (is_null($arr)){
        var_dump(NULL);
    }else{
        echo '<pre style="padding: 10px;border-radius: 5px;background: #f5f5f5;border: 1px solid #ccc;font-size: 16px;">' . print_r($arr,true) . '</pre>';
    }
}

/**
 * [go]
 * 跳转函数
 * @param $url
 * @param int $time
 * @param string $msg
 */
function go($url, $time=0, $msg='')
{
    //如果没有头部发送，则用header跳转;否则用meta标签http-equiv
    if(!headers_sent()){
        $time == 0 ? header('Location:' . $url) : header("refresh:{$time};url={$url}");
        die($msg);
    }else{
        echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time) die($msg);
    }
}
/**
 * [C]
 * 1.加载配置项
 * C($sysConfig)  C($userConfig)
 * 2.读取某个配置项
 * C('CODE_LEN')
 * 3.临时动态改变配置项
 * C('CODE_LEN',20)
 * 4.C();读取所有的配置项
 */
function C($var = NULL, $value = NULL)
{
    static $config = array();
    //加载配置项
    if(is_array($var)){
        $config = array_merge($config,array_change_key_case($var,CASE_UPPER));//后加载的优先级更高
        return;//return为了在这里结束，不走下面
    }
    //读取或者动态改变配置项
    if(is_string($var)){
        $var = strtoupper($var);//字符串参数转大写
        //两个参数传递
        if(!is_null($value)){
            $config[$var] = $value;
            return;//return为了在这里结束，不走下面
        }
        //一个参数传递
        return isset($config[$var]) ? $config[$var] : NULL;

    }
    //不传参，则返回所有配置项
    if(is_null($var) && is_null($value)){
        return $config;
    }
}

/**
 * [print_const]
 * 打印框架定义的所有常量
 */
function print_const()
{
    $const = get_defined_constants(true);
    p($const['user']);
}

/**
 * [M]
 * 返回实例化的Model对象
 * @param $table
 * @return Model
 */
function M($table)
{
    return new Model($table);
}

/**
 * [K]
 * 返回实例化的扩展Model对象
 * @param $model
 * @return mixed
 */
function K($model)
{
    $model .= 'Model';
    return new $model;
}

