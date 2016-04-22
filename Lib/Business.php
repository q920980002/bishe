<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/18
 * Time: 下午5:46
 */

namespace Robotx\Lib;

interface Business{

    /**
     * 事务对话
     * @param $question
     * @param $event_element
     * @param $format
     * @return mixed
     */
    public function talk($question,$event_element,$format);



}