<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/17
 * Time: 下午10:03
 */
namespace Robotx\Lib;

use Robotx\Model\EventElement;

class Robotx extends CommonDB{

    const QUERY = 1;
    const INSERT = 2;

    /**@var integer|string 企业id号 */
    private $_com_id;

    /**@var string|array 用户输入 */
    private $input;

    /**@var string 原问题*/
    private $orignal;

    /**@var array 分词数组 汉字 */
    private $word_element = array();

    /**@var array 分词数组 数词 */
    private $digits_element = array();

    /**@var array 分词数组 英文 */
    private $en_element = array();

    /**@var array 未知词*/
    private $word_unknown = array();

    /**@var EventElement 上下文对象*/
    private $event_element;

    /**@var array 匹配的名词数组 */
    private $match_object = array();

    /**
     * Robotx constructor.
     * @param array $q
     * @param string $com_id
     * @throws \Exception
     */
    public function __construct($q,$com_id="1000000000")
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

        if($q){
            $this->init($q);
        }else{
            throw  new \Exception("缺少必要的参数");
        }

    }

    public function init($q){

        /**@var EventElement 初始化上下文*/
        $this->event_element = new EventElement();
        $this->input = $q;
        $this->orignal = $q;

        /**首先格式化句子*/
        $this->_beforeSplitWord();

        /**分词*/
        $this->_splitWord();

        /**句式分析 疑问句-非疑问句*/
        $this->_analysisPattern();

        /**语义分析*/
        $this->_analysisSemantic();


        //p($this->_createQuerySql(RobotxService::INSERT));
        //p($this->_createQuerySql(RobotxService::QUERY));
        //p($this->event_element);

    }

    /**
     * @return EventElement
     */
    public function getEventElement(){
        return $this->event_element;
    }

    /**
     * @return array
     */
    public function getMatchObject(){
        return $this->match_object;
    }

    /**
     * @return array
     */
    public function getWord_element(){
        return $this->word_element;
    }

    /**
     * @return array
     */
    public function getDigits_element(){
        return $this->digits_element;
    }

    /**
     * @return array
     */
    public function getEn_element(){
        return $this->en_element;
    }

    /**
     * @return array
     */
    public function getWord_unknown(){
        return $this->word_unknown;
    }


    /**
     * 首先初始化字符串
     */
    private function _beforeSplitWord(){

        if($this->input != ""){

            filter_str($this->input); //替换非汉字,数字,英文的特殊字符
            $this->input = mbStrSplit($this->input);//转换成数组

            /**处理字符串*/
            if(arr_contains($this->input,array("能","否")) != -1) {
                $this->input = arr_str_replace(array("能","否"),array("能","不","能"),$this->input);
            }
            if(arr_contains($this->input,array("可","否")) != -1) {
                $this->input = arr_str_replace(array("可","否"),array("可","不","可"),$this->input);
            }
            /**可不可=>可~~~~~吗?*/
            $i = arr_in_array($this->input,"不");
            if($i != -1){
                if($i - 1 >= 0 && $i + 1 < count($this->input)){
                    if($this->input[$i-1] == $this->input[$i+1]){
                        array_splice($this->input,$i,1);
                        array_splice($this->input,$i,1);
                        array_push($this->input,"吗");
                    }
                }
            }
        }

    }

    /**
     * 分词
     */
    private function _splitWord(){

        /**@var string 数字缓存字符串*/
        $digital = "";

        /**@var string 字母缓存字符串*/
        $en = "";

        for($index = 0; $index < count($this->input);$index++){
            $word = $this->input[$index];
            /**处理数字*/
            if(preg_match("/^[0-9]+$/",$word)){
                //如果英文缓存不为空，则代表前一个char一定是字母，则代表字母与数字是相连的,则直接加入字母缓存，算字母合体名词
                if(strlen($en) != 0){
                    $en.=$word;
                }else{
                    $digital.=$word;
                }
                continue;
            }else if(preg_match("/^[A-Za-z]+$/",$word)){
                if(strlen($digital) != 0){
                    $en.=$digital.$word;
                    $digital="";
                }else{
                    $en.=$word;
                }
                continue;
            }
            if($en != ""){
                array_push($this->en_element,$en);
                $en="";
            }
            if($digital != ""){
                array_push($this->digits_element,$digital);
                $digital="";
            }
            $sql = sprintf("select * from `{$this->_com_id}main_words` where Word like '%s' order by WordLength desc",$word."%");
            $wordElements = $this->select($sql);
            if(!$wordElements){
                $sql = sprintf("select * from `main_words` where Word like '%s' order by WordLength desc",$word."%");
                $wordElements = $this->select($sql);
            }
            if($wordElements){
                foreach($wordElements as $element){
                    //由于使用正向最大化匹配原则，查出来的第一个词是最长的，如果查出来的词都大于原句了就直接过
                    if ($element["WordLength"] > (count($this->input) - $index)) {
                        continue;
                    }
                    $sub_arr = array_slice($this->input,$index,$element["WordLength"]);
                    if(arr_contains($sub_arr,mbStrSplit($element["Word"])) != -1){
                        $this->word_element[] = $element;               //添加到分词数组
                        $index = $index + $element["WordLength"] - 1;
                        break;
                    }
                }
            }else{
                array_push($this->word_unknown,$word);
            }
        }

    }

    /**
     * 句式分析
     */
    private function _analysisPattern(){

        /**@var integer 动词索引*/
        $eventPosition = -1;

        /**@var integer '把'字索引*/
        $baPosition = -1;

        for($i = 0;$i<count($this->word_element);$i++){
            /**@var array 词*/
            $word = $this->word_element[$i];

            /**@var integer 词性*/
            $type = $word["Type"];

            /**过滤不确定词*/
            if (!$this->_isLegal($type)) {
                continue;//遇到忽略词过
            }

            /** 疑问句 type >= 501 && type <= 599 */
            if($this->_isInterrogativeWord($type)){
                $this->event_element->isInterrogative = true; //疑问句
                $this->event_element->Type = $type;
                $this->event_element->Interrogative = $word["Word"];
                $word["Type"] = 99;//在原来的分词列表中置为忽略状态
                break; //如果有多个疑问词，语法倾向于第一个，则只取第一个疑问词。
            /**动词*/
            } else if ($type == 201) {
                $eventPosition = $i;
            /**把*/
            } else if($type == 211){
                $baPosition = $i;
            /**语气词 叹词 拟声词 等等 ： 嘻嘻 哈哈 呸呸*/
            } else if($type == 309 || $type == 605 || $type == 606){
                array_push($this->event_element->Object2,$word["Prototype"]);
            /**否定 不 */
            } else if ($type == 700) {
                $this->event_element->isNegative = !$this->event_element->isNegative; //出现一次 取向反
                $this->event_element->Negative = $word["Prototype"];
                /**漂亮不漂亮 => 漂亮吗?*/
                if($i - 1 >= 0 && $i + 1 < count($this->word_element) && $this->word_element[$i-1]["Prototype"] == $this->word_element[$i+1]["Prototype"]){
                    $this->event_element->isNegative = !$this->event_element->isNegative; //继续取向反
                    array_splice($this->word_element,$i-1,2);
                    if(end($this->word_element)["Prototype"] != "吗"){
                        array_push($this->word_element,array("Type"=>506,"Word"=>"吗","Prototype"=>"吗"));
                    }
                    $i--; //删除两个 所以指针往前移一个
                    /**这种情况也是疑问句*/
                    $this->event_element->isInterrogative = true; //疑问句
                    $this->event_element->Type = 506;             //506 吗的疑问句
                    $this->event_element->Interrogative = "吗";
                }
            }
        } //end for $this->word_element

    }

    /**
     * 语义分析
     */
    private function _analysisSemantic(){

        if($this->event_element->isInterrogative == true){
            $this->_analysisInterrogative();
        }else{
            $this->_analysisNotInterrogative();
        }

        /**没找到动词,从第二动词查找*/
        if(!$this->event_element->Event){
            if($this->event_element->Support3){
                $this->event_element->Event = $this->event_element->Support3;
            }
            if($this->event_element->Support2){
                $this->event_element->Event = $this->event_element->Support2;
            }
            if($this->event_element->Support){
                $this->event_element->Event = $this->event_element->Support;
            }
        }

        /**创建match_object*/
        $temp = "";
        if($this->event_element->Object){
            if(count($this->event_element->Object) > 1){
                foreach($this->event_element->Object as $v){
                    $temp.=$v;
                }
                array_push($this->match_object,$temp);
                $temp = "";
                foreach(array_reverse($this->event_element->Object) as $v){
                    $temp.=$v;
                }
                array_push($this->match_object,$temp);
            }
            foreach($this->event_element->Object as $v){
                array_push($this->match_object,$v);
            }
        }else{
            if ($this->event_element->Object2) {
                if(count($this->event_element->Object2) > 1){
                    foreach($this->event_element->Object2 as $v){
                        $temp.=$v;
                    }
                    array_push($this->match_object,$temp);
                    $temp = "";
                    foreach(array_reverse($this->event_element->Object2) as $v){
                        $temp.=$v;
                    }
                    array_push($this->match_object,$temp);
                }
                foreach($this->event_element->Object2 as $v){
                    array_push($this->match_object,$v);
                }
            }
        }

    }

    /**
     * 疑问句分析
     */
    private function _analysisInterrogative(){

        for($i = 0;$i<count($this->word_element);$i++){
            /**@var array 词*/
            $word = $this->word_element[$i];
            /**@var integer 词性*/
            $type = $word["Type"];

            if ($type >= 201 && $type <= 205) {
                /**动词*/
                if($type == 201){
                    $this->event_element->Event = $word["Prototype"];
                 /**形容词*/
                }else if($type == 202){
                    $this->event_element->Status = $word["Prototype"];
                /**想,有,要.能愿动词 可做动词*/
                }else if($type == 203){
                    $this->event_element->Support = $word["Prototype"];
                /**会*/
                }else if($type == 204){
                    $this->event_element->Support2 = $word["Prototype"];
                /**副词:那是,却是,也是,===是 联系动词*/
                }else if($type == 205){
                    $this->event_element->Support3 = $word["Prototype"];
                }

                /**当吗时  搜索除去吗之外的句子 当陈述搜索， 返回有值则回答 肯定*/
                if($this->event_element->Type == 506 || $this->event_element->isEventJudgement){
                    $this->event_element->Type=$type;
                    //删除否定，在吗时 可以吧否定判定 改为肯定判定
                    $this->event_element->Negative = "";
                    $this->event_element->isNegative = false;
                    $this->event_element->isEventJudgement = true;
                }
            /**名词*/
            }else if($this->_isO($type)){
                array_push($this->event_element->Object,$word["Prototype"]);
                array_push($this->event_element->original_object,$word["Word"]);
            }
        }

    }

    /**
     * 非疑问句分析
     */
    private function _analysisNotInterrogative(){

        for($i = 0;$i<count($this->word_element);$i++){
            /**@var array 词*/
            $word = $this->word_element[$i];
            /**@var integer 词性*/
            $type = $word["Type"];

            if ($type >= 201 && $type <= 205) {
                $this->event_element->Type=$type;
                /**动词*/
                if($type == 201){
                    $this->event_element->Event = $word["Prototype"];
                    /**形容词*/
                }else if($type == 202){
                    $this->event_element->Status = $word["Prototype"];
                    /**想,有,要.能愿动词 可做动词*/
                }else if($type == 203){
                    $this->event_element->Support = $word["Prototype"];
                    /**会*/
                }else if($type == 204){
                    $this->event_element->Support2 = $word["Prototype"];
                    /**副词:那是,却是,也是,===是 联系动词*/
                }else if($type == 205){
                    $this->event_element->Support3 = $word["Prototype"];
                }
            }else if($this->_isO($type)){
                array_push($this->event_element->Object,$word["Prototype"]);
                array_push($this->event_element->original_object,$word["Word"]);
            }
        }
    }

    /**
     * 生成指定sql
     * @param int|string $fruit
     * @param int $context_type
     * @return string
     */
    public function createQuerySql($fruit = self::QUERY,$context_type = 0){

        /**@var integer 类型*/
        $type = empty($this->event_element->Type)?0:$this->event_element->Type;
        /**@var string 动词*/
        $event = empty($this->event_element->Event)?"-":$this->event_element->Event;
        /**@var string 条件*/
        $condition = "";
        /**@var string object字符串*/
        $object_str = "";

        if($this->event_element->Object){
            $object_str = implode('',$this->event_element->Object);
            $object_count = count($this->event_element->Object);
            $condition.=" and (";
            foreach($this->event_element->Object as $key=>$o){
                //end($this->event_element->Object) == $o bug重复元素
                if($key == $object_count - 1){
                    $condition.=" `object` like '%".$o."%' ";
                }else{
                    $condition.=" `object` like '%".$o."%' or ";
                }
            }
            $condition.=")";
        }else{
            if($this->event_element->Object2){
                $condition.=" and (";
                $object_str = implode('',$this->event_element->Object2);
                $object_count = count($this->event_element->Object2);
                foreach($this->event_element->Object2 as $key=>$o){
                    if($object_count-1 == $key){
                        $condition.=" `object` like '%".$o."%' ";
                    }else{
                        $condition.=" `object` like '%".$o."%' or ";
                    }
                }
                $condition.=")";
            }
        }
        if($fruit == self::QUERY){
            return sprintf("select * from `:table_name` where `type` = '%s' and `event` = '%s' %s",$type,$event,$condition);
        }else if($fruit == self::INSERT){
            return sprintf("insert into  `:table_name` (`context_category_type`,`type`,`event`,`object`,`orignal`) VALUES ('%s','%s','%s','%s','%s')",$context_type,$type,$event,$object_str,$this->orignal);
        }

        return "";
    }

    /**词性是否合法*/
    private function _isLegal($t) {
        return ($t >= 201 && $t <= 211) || $t == 301 || ($t >= 303 && $t <= 309) || $t == 401 || ($t > 500 && $t < 600) || $t == 605 || $t == 606 || $t == 700;
    }

    /**是否为名词 401 数词*/
    private function _isO($t) {
        return $t == 301 || ($t >= 303 && $t <= 309) || $t == 401;
    }

    /**是否为疑问句*/
    private function _isInterrogativeWord($t){
        return $t >= 501 && $t <= 599;
    }




}