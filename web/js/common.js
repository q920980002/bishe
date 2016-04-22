/**
 * 首页聊天
 * @author weiyaheng
 * @constructor
 * @time 2016年03月16日
 * @lastChange 2016年03月17日15:15:51
 */
function Robotx(){
    /**初始化参数*/
    this.sendUrl = window.location.origin+"/?c=robot&m=ask";
    this.sendType = "post";
    this.dataType = "json";
    /**程序参数*/
    this.msgInput = $("#msg");
    this.msg = this.msgInput.val();
    this.answer = "";
    this.code = 0;

    this.com_id = "1000000248"; //企业id

}
/**聊天 原型方法*/
Robotx.prototype = {
    /**发送信息*/
    sendMsg : function(){
        if(!this.sendMsgBefore()){
            return;
        }
        //,data_type:"json"
        var _this = this;
        $.ajax({
            type: this.sendType,
            url: this.sendUrl,
            data: {q:this.msg,com_id:this.com_id},
            dataType: this.dataType,
            success: function (data) {
                console.log(data);
                if(data){
                    _this.code = data.code;
                    _this.answer = data.text;
                    _this.sendMsgAfter();
                }
            },
            error: function (e) {
                console.log(e.responseText);
            }
        });
    },

    /**发送信息前的回调函数*/
    sendMsgBefore : function(){
        if(this.msg == " " || this.msg.length < 1 || this.msg == ""){
            this.msgInput.focus().val("");
            return false;
        }
        var innerHtml = '<div class="user-msg-item"><div class="user-head"></div><div class="user-msg">';
        innerHtml += this.msg;
        innerHtml += '</div><div class="clear"></div></div>';
        $("#chatBox").append(innerHtml).scrollTop($("#chatBox")[0].scrollHeight);
        if(this.msgInput){
            this.msgInput.focus().val("");
        }
        return true;
    },

    /**发送完信息后的回调函数*/
    sendMsgAfter : function(){
        if(this.code == 100000){
            var innerHtml = '<div class="robotx-msg-item"><div class="robotx-head"></div><div class="robotx-msg">';
            innerHtml += this.answer;
            innerHtml += '</div><div class="clear"></div></div>';
            $("#chatBox").append(innerHtml).scrollTop($("#chatBox")[0].scrollHeight);

        }
    }
};

/**工具*/
function Tool(type){
    /**工具类型*/
    this.toolType = type;
    /**获取聊天初始化*/
    Robotx.call(this);
    /**初始化为null覆盖默认值*/
    this.msgInput = null;
}
Tool.prototype = new Robotx();
Tool.prototype.chatMsg = [
    "你好呀",
    "公司投资人都有谁？",
    "你们公司上市了吗？",
    "呼呼呼呼",
    "给我买张北京到上海的飞机票"
];//bug 此处要改成服务器获得
Tool.prototype.start = function(){
    this[this.toolType]();
};
/**随便聊聊*/
Tool.prototype.chat = function(){
    this.msg = this.chatMsg[Math.round(Math.random()*(this.chatMsg.length-1))];
    this.dialog("给我讲个笑话",this.msg,"历史上的今天");
};
/**试用产品*/
Tool.prototype.trial = function(){
    this.msg = "请问如何试用你们的产品?";
    this.sendMsg();
};
/**天气*/
Tool.prototype.weather = function(){
    this.dialog("北京天气","帮我查询下天气","帮我查询下邯郸的天气");

};
/**火车票*/
Tool.prototype.railway = function(){
    this.dialog("帮我订下火车票","帮我订一张明天去北京的火车票","帮我订一张北京到上海的火车票","帮我订一张明天北京到上海的火车票");
};
/**飞机票*/
Tool.prototype.airplane = function(){
    this.msg = "帮我订张飞机票";
    this.sendMsg();
};
/**菜谱*/
Tool.prototype.menu = function(){
    var menu_name = [
        "宫保鸡丁菜谱",
        "鱼香肉丝菜谱",
        "土豆丝菜谱",
        "水煮鱼菜谱",
        "北京烤鸭菜谱",
        "馄饨菜谱",
        "排骨菜谱",
    ];
    this.msg = menu_name[Math.round(Math.random()*(menu_name.length-1))];
    this.sendMsg();
};
/**菜谱*/
Tool.prototype.travel = function(){
    var travel_name = [
        "大理游记",
        "北京游记",
        "西藏游记",
        "济州岛游记",
        "大理游记",
        "游记"
    ];
    this.msg = travel_name[Math.round(Math.random()*(travel_name.length-1))];
    this.sendMsg();
};
/**对话框*/
Tool.prototype.dialog = function(){
    var defaultMsg = arguments[0];
    var innerHtml = '<div class="shade">';
    innerHtml+='<div class="dialog">';
    innerHtml+='<div class="dialog-content">';
    innerHtml+='<h1>您可以这样提问</h1>';
    innerHtml+='<ul>';
    for(var i=0; i<arguments.length;i++){
        innerHtml+='<li><a href="javascript:;">'+arguments[i]+'</a></li>';
    }
    innerHtml+='</ul>';
    innerHtml+='</div>';
    innerHtml+='<div class="dialog-footer">';
    innerHtml+='<span class="dialog-btn close-btn" >取消</span>';
    innerHtml+='<span class="dialog-btn yes-btn" >确定</span>';
    innerHtml+='</div>';
    innerHtml+='</div>';
    innerHtml+='</div>';
    $(".wrap").append(innerHtml);

    function hideDialog(){
        $(".shade").remove();
    }
    $(".dialog").find("a").each(function(){
        $(this).click(function(){
            var robotx = new Robotx();
            robotx.msgInput = null;
            robotx.msg = $(this).text();
            robotx.sendMsg();
            robotx=null;
            hideDialog();
        });
    });
    $(".dialog").find(".close-btn").click(function(){
        hideDialog();
    });
    $(".dialog").find(".yes-btn").click(function(){
        var robotx = new Robotx();
        robotx.msgInput = null;
        robotx.msg = defaultMsg;
        robotx.sendMsg();
        robotx=null;
        hideDialog();
    });
}

$(function(){
    /**解决移动端 click延迟300毫秒*/
    FastClick.attach(document.body);

    /**解决移动端 键盘拉出 聊天内容没到底部*/
    var isr = true;
    $(window).resize(function() {
        if(isr){
            isr = false;
            setTimeout(function(){
                isr = true;
                $("#msg").focus();
                $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
            },300);
        }

    });

    /** @event 发送消息*/
    $("#sendBtn").click(function(){
        var robotx = new Robotx();
        robotx.sendMsg();
        robotx=null;
    });

    /** @event 键盘回车发送*/
    //$(document).keydown(function(event){
    //    if(event.keyCode == 13){
    //        $("#sendBtn").click();
    //    }
    //});

    $("#test1").keydown(function(){
        if(event.keyCode == 13){
           return false;
        }
    });

    /**输入框获得焦点 隐藏工具栏*/
    $("#msg").focus(function(){
        hideTool();
    });

    /**移动端输入框更加容易获取焦点*/
    $(".chat-content").click(function(){
        $("#msg").focus();
    });

    /** @event 更多按钮 显示工具栏*/
    $("#openTollBtn").click(function(){
        showTool();
    });

    /** @event 隐藏工具栏*/
    $("#chatBox").click(function(){
        hideTool();
    });

    /** @event 工具栏事件*/
    $(".tool-item").each(function(){
        $(this).click(function(){
            (new Tool($(this).attr("data-type"))).start();
        });
    });


});

/**发送问题*/
function sendMultiple(msg){
    var robotx = new Robotx();
    robotx.msgInput = null;
    robotx.msg = msg;
    robotx.sendMsg();
    robotx=null;
}
/**显示工具栏*/
function showTool(){
    $("#toolBox").addClass("show-tool-box");
    $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
}
/**隐藏工具栏*/
function hideTool(){
    $("#toolBox").removeClass("show-tool-box");
}

