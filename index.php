<?php
/**
 * Created by PhpStorm.
 * @author: weiyaheng
 */

if(isset($_POST["user_id"])){
    session_id($_POST["user_id"]);
}
session_start();
header("Access-Control-Allow-Origin:*");
require_once './Lib/Autoloader.php';
require_once './Config/config.php';
require_once './Config/common.php';

/**
 * 单入口文件
 * url模式 xxx/index.php?c=index&m=index
 */
$controller=isset($_GET['c'])?$_GET['c']:"index";
$action=isset($_GET['m'])?$_GET['m']:"index";
$controller = ucfirst($controller).C_SUFFIX;
$method = ucfirst($action).A_SUFFIX;
$controller =  "\\Robotx\\Controller\\".$controller;
$instance = new $controller();
if(method_exists($instance,$method)){
    call_user_func(array($instance,$method));
}else{
    echo "没找到".$method."方法";
}





