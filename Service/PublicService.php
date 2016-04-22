<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/20
 * Time: 上午11:29
 */

namespace Robotx\Service;
use Robotx\Lib\CommonDB;
use Robotx\Lib\DB;
use Robotx\Lib\GetConfig;
use Robotx\Lib\Log;
use Robotx\Lib\Snoopy;
use Robotx\Model\EventElement;

class PublicService extends CommonDB{

    /**@var integer|string 企业id号 */
    private $_com_id;

    /**
     * RobotService constructor.
     * @param integer|string $com_id
     * @throws \Exception
     */
    public function __construct($com_id)
    {
        try{
            if($com_id == BASE_DB_ID){
                $this->_com_id = '';
            }else{
                $this->_com_id = $com_id."_";
            }
            $config = GetConfig::getConfig($com_id);
            parent::__construct($config);
        }catch(\Exception $e){
            Log::write($e->getMessage());
            throw new \Exception("配置文件读取错误");
        }
    }

    /**
     * 发送短信
     * @param $phone
     * @param $content
     * @return string
     */
    public static function _sendMsg($phone,$content)
    {
        if ($phone != null && $content != null) {
            $smsapi = "api.smsbao.com"; //短信网关
            $user = "youkesdk"; //短信平台帐号
            $pass = md5("y0uke5dk"); //短信平台密码
            $snoopy = new Snoopy();
            $sendurl = "http://{$smsapi}/sms?u={$user}&p={$pass}&m={$phone}&c=" . urlencode($content);
            $snoopy->fetch($sendurl);
            $result = $snoopy->results;
            return $result;
        } else {
            return "post not null !";
        }
    }

    /**
     * 查找城市
     * @param $name
     * @return array
     */
    public static function searchCity($name)
    {
        if(mb_strlen($name) < 2){
            return null;
        }
        $publicService = new PublicService(BASE_DB_ID);
        $sql = "select * from `city` where `name` like '{$name}%' ";
        return $publicService->select($sql);
    }

    /**
     * 获取时间
     * 例如 今天:返回2016-03-27 明天2016-03-28 后天:2016-03-29 一个星期后2016-03-27+7
     * @param $param
     * @param EventElement $event_element
     * @param string $format
     * @return bool
     */
    public static function getTime($param,$event_element,$format="Y-m-d"){

        $param = preg_replace('/[^\d]/',"",$param);
        $time = strtotime($param);
        if($time !== false && $time != -1){
            return date($format,$time);
        }
        foreach($event_element->Object as $obj){
            if($obj == "今天"){
                return date($format,time());
            }else if($obj == "明天"){
                return date($format,strtotime("+1 day"));
            }else if($obj == "后天"){
                return date($format,strtotime("+2 day"));
            }else if($obj == "一周后"){
                return date($format,strtotime("+1 week"));
            }else if($obj == "二周后"){
                return date($format,strtotime("+2 week"));
            }else if($obj == "三周后"){
                return date($format,strtotime("+3 week"));
            }else if($obj == "四周后"){
                return date($format,strtotime("+4 week"));
            }else if($obj == "一个月后"){
                return date($format,strtotime("+1 month"));
            }else if($obj == "二个月后"){
                return date($format,strtotime("+2 month"));
            }else if($obj == "三个月后"){
                return date($format,strtotime("+3 month"));
            }else if($obj == "四个月后"){
                return date($format,strtotime("+4 month"));
            }else if($obj == "五个月后"){
                return date($format,strtotime("+5 month"));
            }else if($obj == "半年"){
                return date($format,strtotime("+6 month"));
            }else if($obj == "一年"){
                return date($format,strtotime("+1 year"));
            }else if($obj == "半个月"){
                return date($format,strtotime("+30 day"));
            }
        }
        return false;
    }


}