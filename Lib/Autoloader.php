<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/16
 * Time: 上午8:54
 */
namespace Robotx\Lib;

class Autoloader
{

    /**
     * Autoload root path.
     * @var string
     */
    protected static $_autoloadRootPath = '';

    /**
     * Set autoload root path.
     * @param string $root_path
     * @return void
     */
    public static function setRootPath($root_path)
    {
        self::$_autoloadRootPath = $root_path;
    }

    /**
     * Load files by namespace.
     * @param string $name
     * @return boolean
     */
    public static function loadByNamespace($name)
    {

        $class_path = str_replace('\\', DIRECTORY_SEPARATOR ,$name);

        if(strpos($name, 'Robotx\\') === 0){
            $class_file = dirname(__DIR__).substr($class_path, strlen('Robotx')).'.php';
        }else{
            if(self::$_autoloadRootPath)
            {
                $class_file = self::$_autoloadRootPath . DIRECTORY_SEPARATOR . $class_path.'.php';
            }
            if(empty($class_file) || !is_file($class_file))
            {
                $class_file = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR . "$class_path.php";
            }
        }

        if(is_file($class_file))
        {
            require_once($class_file);
            if(class_exists($name, false))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 致命错误捕获
     */
    public static function fatalErrorHandle()
    {
        if ($e = error_get_last())
        {
            $errorStr = "错误原因:{$e['message']} 错误文件:{$e['file']} 第 {$e['line']} 行.";
            Log::write($errorStr,Log::LEVEL_ERROR);
        }
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    public static function errorHandle($errno, $errstr, $errfile, $errline)
    {
        $errorStr = "错误原因:$errstr 错误文件:".$errfile." 第 $errline 行.";
        Log::write($errorStr,Log::LEVEL_ERROR);
    }

    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    public static function exceptionHandler($e)
    {
        $error = array();
        $error['message']   =   $e->getMessage();
        $trace              =   $e->getTrace();
        if('E'==$trace[0]['function']) {
            $error['file']  =   $trace[0]['file'];
            $error['line']  =   $trace[0]['line'];
        }else{
            $error['file']  =   $e->getFile();
            $error['line']  =   $e->getLine();
        }
        $error['trace']     =   $e->getTraceAsString();
        $errorStr = "异常错误:{$error['message']} 错误文件:{$error['file']} 错误行号:{$error['line']}";
        Log::write($errorStr,Log::LEVEL_ERROR);
    }


}
spl_autoload_register('\Robotx\Lib\Autoloader::loadByNamespace');
// 捕获致命错误
register_shutdown_function('\Robotx\Lib\Autoloader::fatalErrorHandle');
// 自定义错误处理
set_error_handler('\Robotx\Lib\Autoloader::errorHandle');
// 自定义异常处理
set_exception_handler('\Robotx\Lib\Autoloader::exceptionHandler');

