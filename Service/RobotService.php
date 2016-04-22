<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/22
 * Time: 下午1:51
 */
namespace Robotx\Service;

use Robotx\Lib\CommonDB;
use Robotx\Lib\GetConfig;
use Robotx\Lib\Log;
use Robotx\Lib\Robotx;

class RobotService extends CommonDB{


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
     * 生成多个问题
     * @param $words
     * @return array|bool
     */
    private function _getQuestion($words)
    {
        $words = $words[0];
        $word = $words["Word"];
        $type = $words["Type"];
        //地名
        if($type == 304){
             return [
                [
                    'type' => '0',
                    'orignal'=>"{$word}怎么样？",
                ],
                [
                    'type' => '0',
                    'orignal'=>"帮我订一张到{$word}的火车票",
                ],
                [
                    'type' => '0',
                    'orignal'=>"帮我查询下{$word}的天气",
                ],
            ];
        //人名
        } else if ($type == 307){
            return false;
//            return [
//                [
//                    'type' => '0',
//                    'orignal'=>"{$word}是谁？",
//                ],
//                [
//                    'type' => '0',
//                    'orignal'=>"{$word}是那个国家的人？",
//                ],
//                [
//                    'type' => '0',
//                    'orignal'=>"介绍下{$word}",
//                ]
//            ];

        }

        return false;
    }

    /**
     * 判断是否是无意义的短句
     * @param $question
     * @param $event_element
     * @param Robotx $robotx
     * @return array|bool
     */
    public function isShort($question,$event_element,$robotx)
    {
        //没动词 只有一个名词的情况下
        if(empty($event_element->Event) && empty($event_element->Type) && count($event_element->Object) == 1 ){
            //是短词语
            $sql = "select * from `main_words` where `Word` = '{$event_element->Object[0]}' ";
            $word = $this->select($sql);
            if($word){
                $res = $this->_getQuestion($word);
                if($res){
                    return $res;
                }else{
                    return $this->getListQuestion($robotx);
                }

            }
            return false;
            //没有名词,只有动词,但是不是疑问句
        }else if(count($event_element->Object) == 0 && !empty($event_element->Event) && ($event_element->Type < 501 || $event_element->Type > 509)){
            $sql = "select * from `main_words` where `Word` = '{$event_element->Event}' ";
            $word = $this->select($sql);
            if($word){
                $res = $this->_getQuestion($word);
                if($res){
                    return $res;
                }else{
                    return $this->getListQuestion($robotx);
                }
            }
            return false;
        }

        return false;

    }


    /**
     * 根据idlist获取category
     * @param $arr_id
     * @return array|bool
     */
    public function getContextCategoryByIdList($arr_id)
    {
        $sql = sprintf("select * from `{$this->_com_id}youke_context_category` where `type` in (%s)",implode(",",$arr_id));
        return $this->select($sql);
    }

    /**
     * 模糊触发上下文
     * @param $sql
     * @return array|bool
     */
    public function canFuzzyTrigger($sql)
    {
        $sql = str_replace(":table_name",$this->_com_id."youke_context_trigger",$sql);
        return $this->select($sql);
    }

    /**
     * 是否可以精准触发上下文
     * @param $question
     * @return array|bool
     */
    public function canTrigger($question)
    {
        filter_str($question);
        $sql = sprintf("select * from `{$this->_com_id}youke_context_category` where `orignal` = '%s' ",$question);
        return $this->select($sql);
    }

    /**
     * 添加未知问题
     * @param $question
     * @param $web_answer
     * @param bool $addBase
     * @throws \Exception
     */
    public function addUnknown($question,$web_answer,$addBase=true)
    {
        $sql = sprintf("insert into `{$this->_com_id}unknown_question` (`id`,`Question`,`Time`,`CreateTime`,`Orignal`) VALUES (null,'%s','%s','%s','%s')",$question,date("Y-m-d H:i:s",time()),time(),$web_answer);
        $this->insert($sql);
        if($addBase){
            $sql = sprintf("insert into `unknown_question` (`id`,`Question`,`Time`,`CreateTime`,`Orignal`) VALUES (null,'%s','%s','%s','%s')",$question,date("Y-m-d H:i:s",time()),str_replace("_","",$this->_com_id),$web_answer);
            $this->insert($sql);
        }

    }

    /**
     * 获取相似问题
     * @param Robotx $robotx
     * @param int $maxCount
     * @return array
     */
    public function getListQuestion($robotx,$maxCount=6){

        $words = $robotx->getWord_element();
        $keyWord = array();
        $event_element = $robotx->getEventElement();
        if(count($event_element->Object) == 0 && count($event_element->Object2) == 0 ){
            return null;
        }
        foreach($words as $word){
            if($word["Type"] == 503 || $word["Type"] == 502 || $word["Type"] == 504){
                $keyWord[] = $word["Word"]."%";
            }else{
                $keyWord[] = "%".$word["Word"]."%";
                $keyWord[] = "%".$word["Prototype"]."%";
            }
        }
        $keyWord = array_unique($keyWord);//删除重复
        foreach($keyWord as $key=>$word){
            if(mb_strlen($word) == 1){
                unset($keyWord[$key]);//删除一个字的
            }
        }
        $keyWord = array_values($keyWord); //重新建立索引

        //组合
        $this->handleArray = $keyWord;
        array_push($this->handleArray,0);
        $select_word = array();
        $hand_length =  count($this->handleArray)-1;
       
        for($i = $hand_length; $i > 0; $i--){
            $this->_comb(0,$hand_length,$i);
            $select_word[] = $this->result_arr;
            $this->result_arr = array();
        }

        $sql_array = array();
        foreach($select_word as $words){
            foreach($words as $word){
                $condition = "";
                foreach($word as $w){
                    $condition.= " `Orignal` like '{$w}' and ";
                }
                $condition.=" `StartTime` < NOW() and `EndTime` > NOW() and `Status` = 1 order by HitTimes asc";
                $sql = sprintf("select * from `{$this->_com_id}basic_dialog` where %s ",$condition);
                $sql_array[] = $sql;
            }
        }
        
        $question_array = array();
        foreach($sql_array as $sql){
            $question_array = array_merge($question_array,$this->_getQuestionByDb($sql));
            if(count($question_array) >= $maxCount){
                $question_array = array_slice($question_array,0,$maxCount);
                break;
            }
        }


        /////删除重复数组,创建二级数组...需要优化
        $q_array = array();
        foreach($question_array as $q){
            $q_array[] = $q["Orignal"];
        }
        $q_array = array_unique($q_array);
        $res_array = array();
        foreach($q_array as $q){
            $res_array[]["orignal"] = $q;
        }
        return $res_array;
    }

    private function _getQuestionByDb($sql)
    {
        return $this->select($sql);
    }

    //用于获取所有自合类型算法
    private $top = 0;
    private $queue = array();
    private $handleArray = array();
    private $result_arr = array();

    private function _comb($s,$n,$m)
    {
        if($s > $n){
            return ;
        }
        if($this->top == $m){
            $arr = array();
            for($i = 0; $i<$m;$i++){
                $arr[] = $this->queue[$i];
            }
            array_push($this->result_arr,$arr);
            return ;
        }
        $this->queue[$this->top] = $this->handleArray[$s];
        $this->top++;
        $this->_comb($s+1,$n,$m);
        $this->top--;
        $this->_comb($s+1,$n,$m);
    }



}