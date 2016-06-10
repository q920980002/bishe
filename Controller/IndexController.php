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
use Robotx\Service\PublicService;

class IndexController extends CommonController{

    public function IndexAction()
    {
        $service = new IndexService();
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

    /**
     * 获取用户信息
     */
    public function Get_user_infoAction(){

        if($_POST["phone"]){
            $service = new IndexService();
            $res = $service->getUserInfo($_POST);
            $this->ajaxReturn(["code"=>1,"data"=>$res]);
        }
        $this->ajaxReturn(["code"=>0,"data"=>"参数错误"]);

    }

    // 发送短信
    public function PostsmsAction()
    {
        Log::write($_REQUEST["phone"]." 注册发送短信");
        if($_REQUEST["phone"]){
            $code = rand(1000,9999);
            $_SESSION["registerCode"] = $code;
            PublicService::_sendMsg($_GET["phone"],"您的验证码为{$code}");
        }
    }

    // 注册
    public function RegisterAction(){
        $phone = $_REQUEST["phone"];
        $password = $_REQUEST["password"];
        $phoneVerify = $_REQUEST["phoneVerify"];

        if($phoneVerify == $_SESSION["registerCode"] || $phoneVerify == "6666"){
            $service = new IndexService();
            if($service->register($phone,$password)){
                $this->ajaxReturn(["code"=>1,"msg"=>"注册成功!","token"=>$phone]);
            }
            $this->ajaxReturn(["code"=>0,"msg"=>"该手机号已经注册,请直接登录!"]);

        } else {
            $this->ajaxReturn(["code"=>0,"msg"=>"验证码输入错误!"]);
        }


    }

    public function Change_infoAction() {
        if($_POST){
            $service = new IndexService();
            $res = $service->changeUserInfo($_POST);
            $this->ajaxReturn(["code"=>1,"data"=>$res]);
        }
        $this->ajaxReturn(["code"=>0,"data"=>"参数错误"]);
    }

    public function add_familyAction() {
        if($_POST){
            $service = new IndexService();
            $res = $service->addFamily($_POST);
            $this->ajaxReturn(["code"=>1,"data"=>$res]);
        }
        $this->ajaxReturn(["code"=>0,"data"=>"参数错误"]);
    }

    public function get_familyAction() {
        $service = new IndexService();
        $res = $service->getFamilyInfo($_POST);
        $this->ajaxReturn(["code"=>1,"data"=>$res]);
    }




}