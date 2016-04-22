<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/18
 * Time: 上午9:43
 */
namespace Robotx\Model;
/** 上下文对象 */
class EventElement{
    public  $isInterrogative = false;	 //是否为疑问句
    public $isNegative = false;			//是否为否定句
    public $isEventJudgement = false;	//判断句
    public $isObjectJudgement = false;  //事务判断句  漂亮不漂亮
    public $isWaitConfirm = false;		//等待确认
    public $isMatch = false;				//是否匹配
    public $isPreciseMatch = false;//是否精确匹配

    public $isNeedAnswer = false;



    public $Event;             //谓语 常用动词  weight:999
    public $Object = array();  //宾语 常用名词  weight:99
    public $Object2 = array(); //语气词,象声词,情感词等 如嘻嘻 哈哈 呸呸 weight:9
    public $ID;
    public $CategoryID;
    public $Type;
    public $Time;
    public $DBObject;          //数据库object 只在查询数据库时有效；
    public $Location;
    public $Return;
    public $Negative;			// 否定 不！
    public $Status;
    public $Command;
    public $Support;
    public $Support2;
    public $Support3;
    public $Interrogative;
    public $Orignal;
    public $Digit;              //主要针对纯数字的问答
    public $Object3;


    public $original_object = array();  //原句对象



}