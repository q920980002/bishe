<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/22
 * Time: 上午10:58
 */
namespace Robotx\Service;

use Robotx\Lib\CommonDB;
use Robotx\Lib\GetConfig;
use Robotx\Lib\Log;
use Robotx\Lib\Robotx;
use Robotx\Lib\YhTool;

class IndexService extends CommonDB{



    public function __construct($com_id="1000000000")
    {
        try{
            $config = GetConfig::getConfig($com_id);
            parent::__construct($config);
        }catch(\Exception $e){
            Log::write($e->getMessage());
            throw new \Exception("配置文件读取错误");
        }
    }

    public function register($phone,$password)
    {
        $time = time();
        $sql = "insert into `user` (`id`,`username`,`password`,`phone`,`status`,`create_time`) VALUES (null,'{$phone}','{$password}','{$phone}','1','{$time}')";
        try{
            $res = $this->insert($sql);
            return $res;
        }catch(\Exception $e){
            return false;
        }

    }
    public function login($param)
    {
        $sql = "select * from `user` where `phone` = '{$param['username']}' and `password` = '{$param['password']}'";
        $res = $this->select($sql);
        if($res){
            return md5(time());
        }
        echo false;
    }

    public function updata($param)
    {
        $sql = "select * from `user` where `phone` = '{$param['phone']}'";
        $res = $this->select($sql);
        if($res){
            $res = $res[0];
            $uid = $res["id"];
            $username = $res["phone"];
            $procedure_id = $param["procedure_id"];
            $sex = $res["sex"];
            $age = $res["age"];
            $weight = $param["data"];
            $bmi = 1;
            $create_time = time();

            $sql = "select `weight` from `body_general_data` where `u_id` = '{$uid}' order by id DESC limit 1";
            $nextTime = $this->count($sql);
            $sql = "insert into `body_general_data` (`id`,`u_id`,`username`,`procedure_id`,`sex`,`age`,`weight`,`bmi`,`create_time`) VALUES (null,'{$uid}','{$username}','{$procedure_id}','{$sex}','{$age}','{$weight}','{$bmi}','{$create_time}')";
            $res = $this->insert($sql);
            if($res){
                return $nextTime;
            }
        }

        echo false;
    }

    public function getWeight($param){

        $sql = "select * from `body_general_data` where `username` = '{$param['phone']}' order by id DESC limit 5";
        return $this->select($sql);

    }

    public function getUserInfo($param) {
        $sql = "select * from `user` where `phone` = '{$param['phone']}'";
        $res = $this->select($sql);
        $res = $res[0];
        $sql = "select max(weight) from `body_general_data` where `username` = '{$param['phone']}'";
        $max = $this->count($sql);
        $sql = "select min(weight) from `body_general_data` where `username` = '{$param['phone']}'";
        $min = $this->count($sql);
        $sql = "select weight from `body_general_data` where `username` = '{$param['phone']} order by id desc'";
        $new = $this->count($sql);
        $res['max'] = $max;
        $res['min'] = $min;
        $res['new'] = $min;
        return $res;
    }

    public function changeUserInfo($param) {

        $sql = "update `user` set `sex` = {$param['sex']},`age` = {$param['age']},`height` = {$param['height']} where `phone` = {$param['phone']} ";

        return $this->sql($sql);
    }

    public function addFamily($param) {

        $sql = "INSERT INTO `family` (`id`, `u_id`,`family_id`,`phone`,  `username`, `appellation`, `sex`, `age`, `height`) VALUES (NULL, '0', '0', '{$param['phone']}', '{$param['username']}','{$param['appellation']}', '{$param['sex']}', '{$param['age']}', '{$param['height']}')";
        return $this->insert($sql);
    }

    public function getFamilyInfo($param) {
        $sql = "select * from `family` where `username` = '{$param['phone']}'";
        return $this->select($sql);
    }

}