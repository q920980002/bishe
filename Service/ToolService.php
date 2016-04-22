<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/24
 * Time: 下午6:06
 */
namespace Robotx\Service;
use Robotx\Lib\CommonDB;
use Robotx\Lib\GetConfig;
use Robotx\Lib\Log;

class ToolService extends CommonDB{

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
     * 获取相似问题
     * @param $sql
     * @return array|bool
     */
    public function getLike($sql){
        $sql = str_replace(":table_name",$this->_com_id."basic_dialog",$sql);
        $sql .=" order by id desc limit 50";
        return $this->select($sql);
    }

    /**
     * 关键词是否存在
     * @param $value
     * @return array
     */
    public function isExistWord($value){
        $db_table = $this->_com_id.'main_words';
        $sql = "select * from {$db_table} where `Word` = '{$value}' ";
        $res = $this->select($sql);
        if($res){
            return ["code"=>1,"msg"=>"该词已经存在","data"=>json_encode($res)];
        }else{
            return ["code"=>0,"msg"=>"该词不存在"];
        }

    }



}