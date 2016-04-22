<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/16
 * Time: 上午8:55
 */
namespace Robotx\Controller;


use Robotx\Lib\Log;
use Robotx\Lib\Robotx;
use Robotx\Service\IndexService;

class IndexController extends CommonController{

    public function IndexAction()
    {
        $service = new IndexService();


//        $nickname = "魏亚恒";
//        $username = "q920980002";
//        $password = "w6855961";
//        $phone = "15600065570";
//        $status   = 1;
//        $create_time = time();
//
//        $service->beginTrans();
//        $sql = sprintf("insert into `user` (`id`,`nickname`,`username`,`password`,`phone`,`status`,`create_time`,`update_time`) VALUES  (null,'%s','%s','%s','%s','%s','%s','')",11,11,11,11,11,11);
//        $service->insert($sql);
//        $sql = sprintf("insert into `user` (`id`,`nickname`,`username`,`password`,`phone`,`status`,`create_time`,`update_time`) VALUES  (null,'%s','%s','%s','%s','%s','%s','')",$nickname,$username,$password,$phone,$status,$create_time);
//        $service->insert($sql);
//        $service->commit();
//        try{
//            $service->insert($sql);
//        }catch(\Exception $e){
//            p($e->getMessage());
//        }



        echo "hello world!";
    }

    /**
     * 用户登录
     */
    public function LoginAction()
    {
        if($_POST["username"] && $_POST["password"]){
            $service = new IndexService();
            $token = $service->login($_POST);
            if($token){
                $this->ajaxReturn(array("phone"=>$_POST["username"],"token"=>$token));
            }
        }
        echo 0;
    }

    /**
     * 上传数据
     */
    public function UpdataAction()
    {
        //Log::write($_POST);
        if($_POST["data"]){
            $service = new IndexService();
            $res = $service->updata($_POST);
            if($res){
                $this->ajaxReturn(["code"=>"1","msg"=>$res]);
            }

        }
        echo 0;
    }

    public function Get_weightAction(){

        if($_POST["phone"]){
            $service = new IndexService();
            $res = $service->getWeight($_POST);
            $this->ajaxReturn(["code"=>1,"data"=>$res]);
        }
        $this->ajaxReturn(["code"=>0,"data"=>"参数错误"]);

    }





}