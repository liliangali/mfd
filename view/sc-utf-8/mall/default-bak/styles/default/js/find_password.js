//倒计时效果
down_time.i=61;
down_time.c=null;
var array_value=[];
function down_time(obj)
{
    var num=obj.find(".b_number");
    var txt=obj.find(".btext");
    var val=txt.text();
    array_value.push(val);

    if(down_time.i==0||down_time.c==64){
        obj.attr("e_click","true");
        txt.text(array_value[0]);
        array_value=[];
        num.text("");
        obj.removeClass("g_color");
        down_time.i=61;
        down_time.c=null;
        clearTimeout(down_time.c);
        return true;
    }
    if(down_time.i>0){
        obj.addClass("g_color");
        txt.text("后重发");
        down_time.i--;
        num.text(down_time.i+"秒");
        down_time.c=setTimeout(function(){
            down_time(obj)
        },1000);
    }
}

find_password={
    //初始化函数
    init:function(){
        this.tab_select();
        this.step1();
        this.step2();
        this.step3();
        this.step4();
    },
    //身份验证选择方式tab功能
    tab_select:function(){
        $("#select_way").change(function(){
            if(down_time.c!=null){
                down_time.c=64;
            }
            $("#select_way option:selected").each(function(){
                var index=$("#select_way option").index(this);
                $("#tab_wrap .select_tab").eq(index).addClass("current").siblings(".select_tab").removeClass("current");
            });
        });
    },
    //发送验证码
    send_verifycode:function(){
        var obj=$("#send_sms");
        var mybol=obj.attr("e_click");
        if(mybol=="true")
        {
            down_time(obj);
        }
        obj.attr("e_click","false");
    },
    //发送验证链接
    send_verifylink:function()
    {
        var obj=$("#send_email");
        var mybol=obj.attr("e_click");
        if(mybol=="true")
        {
            down_time(obj);
        }
        obj.attr("e_click","false");
    },
    step1:function(){
        //第1步-更新验证码
        $("#change_captcha").live("click",function()
        {
            $(this).find("img").attr('src','/index.php/captcha-check_captcha.html?t='+new Date().getTime());
        });
        //第1步-检查帐号
        $("#captcha").focus(function(){
            $(".tip_email").hide();
        });

        $("#account").focus(function(){
            $("#tip_info").removeClass("error").text("");
        });
        $("#form_find_password_step1").submit(function()
        {
            //检查帐号是否为空
            if(!$("#account").val())
            {
                $("#tip_info").html("请输入用户名/手机号/邮箱").addClass("error").show();
                return false;
            }
            //检查验证码是否为空
            if(!$("#captcha").val())
            {
                $("#tip_info").html("请输入验证码").addClass("error").show();
                return false;
            }
            //检查帐号和验证码
            var data=check_form("/index.php/member-find_password.html?step=1&opt=check_account","form_find_password_step1");
            if(data.flag<0)
            {
                $("#tip_info").html(data.info).addClass("error").show();
                $("#change_captcha").trigger("click");
                $("#captcha").val("");
                return false;
            }
        });
    },
    step2:function(){
        //第2步-发送短信
        $("#send_sms").bind("click",function()
        {
            find_password.send_verifycode();
            $.post("/index.php/member-find_password.html?step=2&opt=send_sms",$("#form_find_password_step2").serialize(),function(data)
            {
                if(data.flag)
                {
                    $("#tip_verifycode").html('验证码短信已发送').removeClass("error").addClass("ok").show();
                }
                else
                {
                    $("#tip_verifycode").html(data.info).removeClass("ok").addClass("error").show();
                }
            },"json");
        });

        //第2步-发送邮件
        $("#send_email").bind("click",function()
        {
            find_password.send_verifylink();
            $.post("/index.php/member-find_password.html?step=2&opt=send_email",$("#form_find_password_step2").serialize(),function(data)
            {
                if(data)
                {
                    $("#tip_email").html('邮件已发送，<a target="_blank" href="'+get_email_site($("#email").val())+'">前往邮箱>></a>');
                }
            });
        });

        //第2步-提交短信验证码验证
        $("#submit_verifycode").click(function()
        {
            //检查验证码是否为空
            if(!$("#verifycode").val())
            {
                $("#tip_verifycode").html("请输入验证码").removeClass("ok").addClass("error").show();
                return false;
            }
            //检查验证码是否正确
            var data=check_form("/index.php/member-find_password.html?step=2&opt=check_verifycode","form_find_password_step2");
            if(!data)
            {
                $("#tip_verifycode").html("验证码错误").removeClass("ok").addClass("error").show();
                $("#verifycode").val("");
                return false;
            }
            //提交表单
            $("#form_find_password_step2").submit();
        });

        //第2步-提交密保问题验证
        $("#submit_problem_password").click(function()
        {
            //检查密保问题答案是否为空
            if(!$("#answer").val())
            {
                $("#tip_problem_password").html("请输入密保问题答案").addClass("error").show();
                return false;
            }
            //检查密保问题答案是否正确
            var data=check_form("/index.php/member-find_password.html?step=2&opt=check_problem_password","form_find_password_step2");
            if(!data)
            {
                $("#tip_problem_password").html("密保问题答案错误").addClass("error").show();
                return false;
            }
            //提交表单
            $("#form_find_password_step2").submit();
        });
    },
    //第3步,密码验证
    step3:function(){
        function paw_strength_check(object){
            value=object.val();
            var regEXP_number=/[0-9]/;
            var regEXP_letter=/[a-zA-Z]/;
            var regEXP_sign=/[_\-+=*.!@#$%^&()]/;
            var regEXP_blank=/\s/g;

            var new_str1=regEXP_number.test(value);
            var new_str2=regEXP_letter.test(value);
            var new_str3=regEXP_sign.test(value);
            var new_str4=regEXP_blank.test(value);

            var n=0;
            var m=[];
            new_str1==true&&(n=1);
            new_str2==true&&(n++);
            new_str3==true&&(n++);
            m.push(n);
            if(new_str4==true){
                alert("不能输入空格！");
                value=value.replace(regEXP_blank,"");
                m.push(value);
                object.val(m[1]);
            }
            return m;
        }

        $("#new_password").keyup(function(){
            var m=paw_strength_check($(this));
            if(m[0]==0)
                $(".pwd_check span").removeClass("current");
            if(m[0]==1)
                $(".pwd_check span:eq(0)").addClass("current").siblings().removeClass("current");
            if(m[0]==2)
                $(".pwd_check span:eq(1)").addClass("current").siblings().removeClass("current");
            if(m[0]==3)
                $(".pwd_check span:eq(2)").addClass("current").siblings().removeClass("current");
            ;
        });

        $("#new_password").blur(function(){
            var m=paw_strength_check($(this));
            var value=$(this).val();
            var regEXP_length=/^.{6,16}$/;
            var regEXP_sign=/^[\w\-+=*.!@#$%^&()]+$/;
            if(!value){
                $(this).siblings(".error").removeClass("ok").text("新登录密码不能为空").show();
            }
            else if(!regEXP_sign.test(value)){
                $(this).siblings(".error").removeClass("ok").text("登录密码包含非法字符").show();
            }
            else if(m[0]==1){
                $(this).siblings(".error").removeClass("ok").text("密码过于简单").show();
            }
            else if(!regEXP_length.test(value)){
                $(this).siblings(".error").removeClass("ok").text("请输入6-16个字符").show();
            }
            else{
                $(this).siblings(".error").addClass("ok").text("").show();
            }
        });
        $("#confirm_password").blur(function(){
            var value1=$("#new_password").val();
            var value2=$(this).val();
            if(!value2){
                $(this).siblings(".error").removeClass("ok").text("确认密码不能为空").show();
            }
            else if(value1!=value2&&value2!=""){
                $(this).siblings(".error").removeClass("ok").text("密码不一致！").show();
            }else{
                $(this).siblings(".error").addClass("ok").text("").show();
            }

        });

        $("#form_find_password_step3").submit(function(){
            $("#new_password").blur();
            $("#confirm_password").blur();
            if($(".error").text() !="") {
                return false;
            }else{
            $("#submit").attr("disable","disable");
            $(this).find("#submit").val("提交中...");
            }
        })

    },
    //第4步-倒计时跳转
    step4:function(){
        if($("#down_time").find("p").find("font").length>0)
        {
            var timer=setInterval(function()
            {
                var s=parseInt($("#down_time").find("p").find("font").text());
                if(s>1)
                {
                    $("#down_time").find("p").find("font").text(--s);
                }
                else if(s===1)
                {
                    clearInterval(timer);
                    location.href="index.php?app=member&act=login";
                }
            },
                    1000);
        }
    }

};


//检查表单
function check_form(url,form_id)
{
    var result;
    $.ajax(
            {
                type:"post",
                url:url,
                data:$("#"+form_id).serialize(),
                dataType:"json",
                async:false,
                success:function(data)
                {
                	 console.log(data);
                    result=data;
                }
            });
    return result;
}

//获取邮箱网址
function get_email_site(email){
    var email_site=['http://mail.qq.com','http://gmail.com','http://126.com','http://mail.163.com','http://hotmail.com','http://mail.yahoo.com','http://mail.live.com','http://mail.sohu.com','http://mail.sina.com'];
    var suffix=email.split('@')[1];
    var address;
    var n=email_site.length;
    for(var i=0;i<n;i++)
    {
        if(email_site[i].indexOf(suffix)!==-1)
        {
            address=email_site[i];
            break;
        }
    }
    return address?address:('http://mail.'+suffix);
}
$(function(){
    find_password.init();
    //身份验证方式第一个TAB默认显示
    $("#tab_wrap .select_tab:first-child").addClass("current");
});

//辅助邮箱输入的下拉框
$(".tip_email p").live("mouseover",function(){
        $(this).addClass("p_hover");
    });

$(".tip_email p").live("mouseout",function(){
        $(this).removeClass("p_hover");
    });

    $("#form_find_password_step1 .tip_email p").live("click",function(){
        var nameValue=$(this).text();
        $("#account").val(nameValue);
        $(".tip_email p").remove();
    });

var emai_address=['qq.com','gmail.com','126.com','163.com','hotmail.com','yahoo.com','live.com','sohu.com','sina.com'];
    $('#account').live('input',function()
    {
        $(".tip_email").html('');
        var value=$(this).val();
        var index=value.indexOf('@');
        if(index!==-1&&index!==0)
        {
            var n=emai_address.length;
            if(index<value.length-1)
            {
                var address=value.substring(index+1,value.length);
                for(var i=0;i<n;i++)
                {
                    if(!address||emai_address[i].indexOf(address)==0)
                    {
                        $(".tip_email").show().append('<p><span style="color:#FF0000;">'+value.substring(0,index)+'</span>@'+emai_address[i]+'</p>');
                    }
                }
            }
            else
            {
                for(var i=0;i<n;i++)
                {
                    $(".tip_email").show().append('<p><span style="color:#FF0000;">'+value.substring(0,index)+'</span>@'+emai_address[i]+'</p>');
                }
            }
        }
    });
