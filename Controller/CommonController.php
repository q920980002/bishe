<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/16
 * Time: 上午8:56
 */
namespace Robotx\Controller;

class CommonController{

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function ajaxReturn($data,$type='') {
        if(empty($type)) $type  =   "JSON";
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                //header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'XML'  :
                // 返回xml格式数据
                //header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                //header('Content-Type:application/json; charset=utf-8');
                $handler  =   "callback";
                exit($handler.'('.json_encode($data).');');
            case 'EVAL' :
                // 返回可执行的js脚本
                //header('Content-Type:text/html; charset=utf-8');
                exit($data);
        }
    }

}