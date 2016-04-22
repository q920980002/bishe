<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/16
 * Time: 下午1:45
 */

function p($arr){
    echo "<pre>";
    print_r($arr);
    echo "<pre>";
}

/**
 * 查找字符串中是否包含
 * @param $str
 * @param $value
 * @return bool
 */
function contains($str,$value){
    return mb_strpos($str,$value) != -1;
}

/**
 * 查找字符
 * @param $str
 * @param $value
 * @return bool|int
 */
function find_str($str,$value){
    return mb_strpos($str,$value);
}

/**
 * 获取字符串长度
 * @param $str
 * @return int
 */
function get_str_length($str){
    return mb_strlen($str);
}

/**
 * 获取中文第一次出现的位置
 * @param $str
 * @param $index
 * @return string
 */
function get_char_by_index($str,$index){
    return mb_substr($str,$index,1);
}

/**
 * 中文转数组
 * @param $string
 * @param int $len
 * @return array
 */
function mbStrSplit ($string, $len=1) {
    $start = 0;
    $strlen = mb_strlen($string);
    while ($strlen) {
        $array[] = mb_substr($string,$start,$len,"utf8");
        $string = mb_substr($string, $len, $strlen,"utf8");
        $strlen = mb_strlen($string);
    }
    return $array;
}

/**
 * 检查数组中是否包含另一个连续的数组
 * @param $arr
 * @param $value
 * @return 位置, -1没找到
 */
function arr_contains($arr,$value){

    $i = 0;
    $il = count($value);

    $n = 0;
    $nl = count($arr);

    $is_start = false;
    while($n < $nl){
        //表明已经匹配完成
        if($i == $il){
            return $n - $il;
        }
        //开始匹配
        if($arr[$n] == $value[$i]){
            //p($arr[$n]);
            $is_start = true;
        }else{
            //没有匹配,重置
            $i=0;
            $is_start = false;
        }
        //匹配数组开始迭代
        if($is_start){
            $i++;
        }
        $n++;
    }
    if($is_start && $i == $il){
        return $nl-$il;
    }else{
        return -1;
    }
}

/**
 *
 * @param $search
 * @param $replace
 * @param $subject
 * @return array
 */
function arr_str_replace($search, $replace, $subject){
    $index = arr_contains($subject,$search);
    if($index != -1){
        $start_arr = array_splice($subject,0,$index);
        $end_arr = array_splice($subject,count($search),count($subject));
        return array_merge($start_arr,$replace,$end_arr);
    }
}

/**
 * 值是否在数组中 返回索引
 * @param $haystack
 * @param $value
 * @return int|string
 */
function arr_in_array($haystack,$value){
    foreach($haystack as $key=>$v){
        if($v == $value){
            return $key;
        }
    }
    return -1;
}

/**
 * 过滤字符串
 */
function filter_str(&$input){
    $regex  =  '/\/|\~|\s|\!|\￥|\……|\、|\《|\》|\’|\‘|\！|\？|\。|\，|\”|\：|\；|\|\‘|\【|\】|\）|\（|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/';
    $input = preg_replace($regex,"",$input);
}

/**
 * 聊天引擎
 * @param $question
 * @param $com_id
 * @return string
 */
function robot_response($question,$com_id=0){
    try{
        /** @var string json返回数据 */
        $res = \Robotx\Lib\YhTool::curl(ROBOTX_URL,["m"=>"ask","q"=>$question,"com_id"=>$com_id],"get");
        $res = json_decode($res,true);
        if($res["code"] == 100000){
            if(mb_strpos($res["text"],"俞志晨")!==false){
                return "我们公司很厉害的!!!";
            }else{
                return $res["text"];
            }
        }else{
            return null;
        }
    }catch(\Exception $e){
        /** @error 网络异常 写日志*/
        //return ["code"=>50000,"text"=>$e->getMessage()];
        return null;
    }
}

function web_response($question,$com_id=0){
    try{
        /** @var string json返回数据 */
        $res = \Robotx\Lib\YhTool::curl(TULING_URL,["key"=>TULING_KEY,"info"=>$question],"post");
        $res = json_decode($res,true);
        if($res["code"] == "100000"){
            $text = $res["text"];
            if(mb_strpos($text,"图灵") !== false){
                //替换
                $text = mbStrSplit($text);
                foreach($text as $key=>$t){
                    if($t == "图"){
                        $text[$key] = "小";
                    }
                    if($t == "灵"){
                        $text[$key] = "云";
                    }
                }
                $text = implode("",$text);
            }
            if(mb_strpos($text,"俞志晨") !==false ){
                $text = "我们公司很厉害的哦!!!";
            }
            return $text;
        }else{
            return null;
        }
    }catch(\Exception $e){
        /** @error 网络异常 写日志*/
        //return ["code"=>50000,"text"=>$e->getMessage()];
        return null;
    }
}

