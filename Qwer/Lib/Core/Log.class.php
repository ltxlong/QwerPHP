<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/18
 * Time: 19:02
 */

/**
 * Class Log
 * 日志类
 */
class Log
{
    public static function write($msg,$level='ERROR',$type=3,$dest=NULL)
    {
        if(!C('SAVE_LOG')) return;//看日志配置参数是否开启
        if(is_null($dest)){
            $dest = LOG_PATH . '/' . date('Y-m-d') . '.log';
        }
        if(is_dir(LOG_PATH)) error_log("[TIME]:" . date('Y-m-d H:i:s') . " {$level}:{$msg}\r\n",$type,$dest);
    }
}

