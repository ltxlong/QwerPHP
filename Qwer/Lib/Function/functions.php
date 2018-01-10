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
function halt($error,$level='ERROR',$type=3,$dest=null)
{
    if(is_array($error)){
        \core\Log::write($error['message'],$level,$type,$dest);
    }else{
        \core\Log::write($error,$level,$type,$dest);
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

    include TPL_PATH . '/halt.php';
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
        var_dump(null);
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
function C($var = null, $value = null)
{
    static $config = array();
    static $bootstrap_config = array();

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
        if(empty($config)){
            //在bootstrap启动器里面用到读取配置项
            if(empty($bootstrap_config)){
                $root_path = str_replace('\\','/',dirname(dirname(dirname(__DIR__))));
                $qwer_config_path = $root_path . '/qwer/config/config.php';//框架配置项
                $common_config_path = $root_path . '/common/config/config.php';//公共配置项
                $app_config_path = $root_path . '/app/' . APP_NAME . '/config/config.php';//应用配置项

                $qwer_config = include $qwer_config_path;

                $bootstrap_config = array_merge($bootstrap_config,array_change_key_case($qwer_config,CASE_UPPER));//后加载的优先级更高
                if(is_file($common_config_path)){
                    $common_config = include $common_config_path;
                    $bootstrap_config = array_merge($bootstrap_config,array_change_key_case($common_config,CASE_UPPER));//后加载的优先级更高
                }
                if(is_file($app_config_path)){
                    $app_config = include $app_config_path;
                    $bootstrap_config = array_merge($bootstrap_config,array_change_key_case($app_config,CASE_UPPER));//后加载的优先级更高
                }
            }
            return isset($bootstrap_config[$var]) ? $bootstrap_config[$var] : null;
        }

        return isset($config[$var]) ? $config[$var] : null;

    }
    //不传参，则返回所有配置项
    if(is_null($var) && is_null($value)){
        if(empty($config)){
            //在bootstrap启动器里面用到读取配置项
            if(empty($bootstrap_config)){
                $root_path = str_replace('\\','/',dirname(dirname(dirname(__DIR__))));
                $qwer_config_path = $root_path . '/qwer/config/config.php';//框架配置项
                $common_config_path = $root_path . '/common/config/config.php';//公共配置项
                $app_config_path = $root_path . '/app/' . APP_NAME . '/config/config.php';//应用配置项

                $qwer_config = include $qwer_config_path;

                $bootstrap_config = array_merge($bootstrap_config,array_change_key_case($qwer_config,CASE_UPPER));//后加载的优先级更高
                if(is_file($common_config_path)){
                    $common_config = include $common_config_path;
                    $bootstrap_config = array_merge($bootstrap_config,array_change_key_case($common_config,CASE_UPPER));//后加载的优先级更高
                }
                if(is_file($app_config_path)){
                    $app_config = include $app_config_path;
                    $bootstrap_config = array_merge($bootstrap_config,array_change_key_case($app_config,CASE_UPPER));//后加载的优先级更高
                }
            }
            return $bootstrap_config;
        }else{
            return $config;
        }
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
 * @return \core\Model
 */
function M($table)
{
    return new \core\Model($table);
}

/**
 * [K]
 * 返回实例化的扩展Model对象(继承的是框架内置Model类)
 * 扩展模型可以自己封装函数增加功能，可扩展性强
 * 扩展模型拥有框架内置模型的所有功能，并且还可以扩展新的功能（增加业务逻辑等等）
 * 一个表建一个扩展模型
 * 扩展模型放在应用的models文件夹里面，models文件夹只放扩展模型
 * MVC里面，可以没有models文件夹，是为了放扩展模型才建的
 * @param $modelName
 * @return mixed
 */
function K($modelName)
{
    $app_model_path = '\app\\' . APP_NAME . '\models\\';
    $k_model = $app_model_path.$modelName.'Model';
    return new $k_model;
}

/**
 * [get_ormDatabaseConfig]
 * 获取第三方ORM组件的配置
 * @param $ormDatabasePath
 * @return mixed
 */
function get_ormDatabaseConfig($ormDatabasePath)
{
    $root_path = str_replace('\\','/',dirname(dirname(dirname(__DIR__))));
    if(is_file($root_path . $ormDatabasePath)){
        $ormDatabaseConfig = require $root_path . $ormDatabasePath;
    }else{
        $ormDatabaseConfig = require $root_path . '/qwer/config/ormDatabase.php';
    }
    return $ormDatabaseConfig;
}

