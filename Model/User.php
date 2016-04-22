<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/18
 * Time: 下午5:49
 */
namespace Robotx\Model;

use Robotx\Business\CommonBusiness;
use Robotx\Lib\Robotx;

class User{

    /**@var boolean 业务是否开启上下文*/
    public $is_open_business = false;

    /**@var CommonBusiness 业务对象*/
    public $business;

    public function setIsOpenBusiness($is_open_business){
        $this->is_open_business = $is_open_business;
    }

    public function setBusiness($business){
        $this->business = $business;
    }

    /**
     * 事务入口
     * @param $question
     * @param $format
     * @param int $com_id
     * @return mixed
     */
    public function talk($question,$format,$com_id=0){
        $robotx_service = new Robotx($question);
        return $this->business->talk($question,$robotx_service->getEventElement(),$format);
    }

    /**
     * 事务完成 更新事务
     */
    function __destruct(){
        if($this->business){
            $isUnset = true;
            foreach($this->business->step as $c){
                if(!$c["finish"]){
                    $_SESSION["user"] = serialize($this);
                    $isUnset = false;
                    break;
                }
            }
            if($isUnset){
                unset($_SESSION["user"]);
            }
        }
    }


}