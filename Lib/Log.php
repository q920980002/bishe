<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/22
 * Time: 上午11:21
 */
namespace Robotx\Lib;

class Log{

    const LEVEL_ERROR = 'error';

    const LEVEL_WARNING = 'warning';

    const LEVEL_INFO = 'info';

    const LEVEL_TRACE = 'trace';


    /**
     * @param $message
     * @param $level
     */
    public static function write($message, $level = self::LEVEL_ERROR)
    {
        if(is_array($message) || is_object($message)){
            $message = var_export($message,true);
        }

        $msg = sprintf("[%s] [%s] %s\r\n",date("Y-m-d H:i:s",time()),$level,$message);
        file_put_contents(LOG_PATH."/log_".date("Ymd").".log",$msg,FILE_APPEND);

    }

}