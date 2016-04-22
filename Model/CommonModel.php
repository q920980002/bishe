<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/18
 * Time: 下午5:51
 */
namespace Robotx\Model;

class CommonModel{


    public function __set($property_name, $value){

        $this->$property_name = $value;
    }

    public function __get($property_name){

        if(isset($this->$property_name)){
            return $this->$property_name;
        }
    }


}
