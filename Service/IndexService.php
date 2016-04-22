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


}