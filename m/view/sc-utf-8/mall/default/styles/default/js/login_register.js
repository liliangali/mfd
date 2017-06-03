//通用-开始------------------------------------------------------------------------------------------------------------------------

//判断是否为手机号
function is_mobile(string)
{
    return string.match(/^1[3|4|5|8][0-9]{9}$/) ? true : false;
}

//判断是否为邮箱
function is_email(string)
{
    return string.match(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/) ? true : false;
}

//获取邮箱网址
function get_email_site(email)
{
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
    return address ? address : ('http://mail.'+suffix);
}

//检查图形验证码
function check_captcha(name,value)
{
    var result;
    $.ajax(
            {
                url:"captcha-check_captcha.html?",
                data:{name:name,captcha:value},
                async:false,
                dataType:"json",
                success:function(data)
                {
                    result=data;
                }
            });
    return result;
}

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
                    result=data;
                }
            });
    return result;
}

//检查表单数据
function check_form_data(url,data)
{
    var result;
    $.ajax(
            {
                type:"post",
                url:url,
                data:data,
                dataType:"json",
                async:false,
                success:function(data)
                {
                    result=data;
                }
            });
    return result;
}

//检查帐号
function check_account(account)
{
    if(!account||account=="手机/邮箱")
    {
        return {flag:false,info:"请输入手机/邮箱！"};
    }
    else if(!is_mobile(account)&&!is_email(account))
    {
        return {flag:false,info:"手机/邮箱格式错误！"};
    }
    else
    {
        return {flag:true,info:""};
    }
}

//检查动态验证码
function check_verifycode(verifycode)
{
    var regEXP=/^\d{4,6}$/;
    if(!verifycode)
    {
        return {flag:false,info:"请输入验证码！"};
    }
    else if(!regEXP.test(verifycode))
    {
        return {flag:false,info:"请输入正确的验证码！"};
    }
    else
    {
        return {flag:true,info:""};
    }
}

//检查密码
function check_password(password)
{
    var m=paw_strength_check(password);
    var regEXP_sign=/^[\w\-+=*.!@#$%^&()]+$/;
    var regEXP_length=/^.{6,16}$/;
    if(!password)
    {
        return {flag:false,info:"登录密码不能为空！"};
    }
    else if(!regEXP_sign.test(password))
    {
        return {flag:false,info:"登录密码包含非法字符！"};
    }
    else if(m===1)
    {
        return {flag:false,info:"登录密码过于简单！"};
    }
    else if(!regEXP_length.test(password))
    {
        return {flag:false,info:"请输入6-16个字符！"};
    }
    else
    {
        return {flag:true,info:""};
    }
}

//登录密码强度判断
function paw_strength_check(value)
{
    var regEXP_number=/[0-9]/;
    var regEXP_letter=/[a-zA-Z]/;
    var regEXP_sign=/[_\-+=*.!@#$%^&()]/;

    var new_str1=regEXP_number.test(value);
    var new_str2=regEXP_letter.test(value);
    var new_str3=regEXP_sign.test(value);

    var n=0;
    new_str1===true&&(n=1);
    new_str2===true&&(n++);
    new_str3===true&&(n++);
    return n;
}

//检查用户名
function check_username(username)
{
    var regEXP=/^[\u0391-\uFFE5\w]+$/;
    var regEXP_number=/^[\d]+$/;
    var regEXP_name=/eelly|衣联/;

    var length=username.length;
    for(var i=0;i<username.length;i++)
    {
        if(username.charCodeAt(i)>127)
        {
            length++;
        }
    }

    if(!username||username=="用户名")
    {
        return {flag:false,info:"用户名不能为空！"};
    }
    else if(regEXP_name.test(username))
    {
        return {flag:false,info:"该用户名包含限制词汇！"};
    }
    else if(!regEXP.test(username))
    {
        return {flag:false,info:"只能输入中英文、数字和下划线！"};
    }
    else if(length<5||length>15)
    {
        return {flag:false,info:"请输入5-15个字符，一个汉字算2个字符！"};
    }
    else if(regEXP_number.test(username))
    {
        return {flag:false,info:"用户名不能是纯数字！"};
    }
    else
    {
        return {flag:true,info:""};
    }
}

//记录手机号申请发送登录验证码的次数，如果次数大于等于2，给出相关提示
var verifycode_count_login=0;//手机登录申请验证码次数
function tip_verifycode_count_login()
{
    verifycode_count_login++;
    if(verifycode_count_login>=2)
    {
        $("#tip_verifycode_count_login").show();
    }
}

//记录手机号申请发送注册验证码的次数，如果次数大于等于2，给出相关提示
var verifycode_count_register=0;//手机注册申请验证码次数
function tip_verifycode_count_register()
{
    verifycode_count_register++;
    if(verifycode_count_register>=2)
    {
        $("#tip_verifycode_count_register").show();
    }
}

//倒计时-快速登录
var n_login_fast=60;
var timer_login_fast=null;
var timing_login_fast=false;
function timer_start_login_fast()
{
    if(!timing_login_fast)
    {
        timer_login_fast=setInterval(function()
        {
            $("#send_verifycode_login_fast").text(n_login_fast+"重新发送").addClass('g_color');
            n_login_fast===0&&timer_stop_login_fast();
            n_login_fast--;
        },1000);
        timing_login_fast=true;
    }
}
function timer_stop_login_fast()
{
    clearInterval(timer_login_fast);
    n_login_fast=60;
    timing_login_fast=false;
    $("#send_verifycode_login_fast").text("重新发送").removeClass('g_color');
}

//倒计时-快速注册
var n_register_fast=60;
var timer_register_fast=null;
var timing_register_fast=false;
function timer_start_register_fast()
{
    if(!timing_register_fast)
    {
        timer_register_fast=setInterval(function()
        {
            $("#send_verifycode_register_fast").text('('+n_register_fast+')'+'重新发送').addClass('g_color');
            n_register_fast===0&&timer_stop_register_fast();
            n_register_fast--;
        },1000);
        timing_register_fast=true;
    }
}
function timer_stop_register_fast()
{
    clearInterval(timer_register_fast);
    n_register_fast=60;
    timing_register_fast=false;
    $("#send_verifycode_register_fast").text("重新发送").removeClass('g_color');
}

//倒计时-普通注册
var n_register=60;
var timer_register=null;
var timing_register=false;
function timer_start_register()
{
    if(!timing_register)
    {
        timer_register=setInterval(function()
        {
            $("#send_verifycode_register").text('('+n_register+')'+'重新发送').addClass('g_color');
            n_register===0&&timer_stop_register();
            n_register--;
        },1000);
        timing_register=true;
    }
}
function timer_stop_register()
{
    clearInterval(timer_register);
    n_register=60;
    timing_register=false;
    $("#send_verifycode_register").text("重新发送").removeClass('g_color');
}

//普通登录加载图形验证码
function load_captcha_login()
{
    $("#captcha_login_box").show().find("img").attr('src','/index.php?app=captcha&name=captcha_login&t='+new Date().getTime());
}

//快速登录加载图形验证码
function load_captcha_login_fast()
{
    $("#captcha_login_fast_box").show().find("img").attr('src','/index.php?app=captcha&name=captcha_login_fast&t='+new Date().getTime());
}

//注册加载图形验证码
function load_captcha_register()
{
    $("#captcha_register_box").show().find("img").attr('src','/index.php?app=captcha&name=captcha_register&t='+new Date().getTime());
}

//普通登录更新图形验证码
$("#change_captcha_login").live("click",function()
{
    load_captcha_login();
});

//快速登录更新图形验证码
$("#change_captcha_login_fast").live("click",function()
{
    load_captcha_login_fast();
});

//注册更新图形验证码
$("#change_captcha_register").live("click",function()
{
    load_captcha_register();
});

//通用-结束------------------------------------------------------------------------------------------------------------------------

$(function()
{
//普通登录-开始------------------------------------------------------------------------------------------------------------------------
    //普通登录提交表单
    $("#form_login").submit(function()
    {
        var a_login=$.trim($("#account_login").val());
        //帐号为空检查
        if(!a_login||a_login=="用户名/手机/邮箱")
        {
            $("#error_login").html("<span>请输入已注册用户名或手机或邮箱</span>");
            return false;
        }
        //密码为空检查
        if(!$("#password").val())
        {
            $("#error_login").html("<span>请输入登录密码</span>");
            return false;
        }
        //图形验证码检查
        if($("#captcha_login_box").is(":visible")===true)
        {
            if(!$("#captcha_login").val()||$("#captcha_login").val()=="验证码")
            {
                $("#error_login").html("<span>请输入图形验证码</span>");
                return false;
            }
            else
            {
                if(!check_captcha("captcha_login",$("#captcha_login").val()))
                {
                    $("#error_login").html("<span>图形验证码不正确</span>");
                    load_captcha_login();
                    $("#captcha_login").val("");
                    return false;
                }
            }
        }
        //普通登录检查普通登录表单
        var data=check_form("/index.php?app=member&act=login&opt=check","form_login");
        if(data.flag<0)
        {
            $("#account_login")[0].focus();
            $("#error_login").html('<span>帐号名和密码不匹配，<a target="_blank" href="/index.php?app=member&act=find_password">忘记密码？</a></span>');
            if(data.block)
            {
                load_captcha_login();
            }
            return false;
        }
        //提交普通登录表单效果
        $("#submit_login").val("登录中...").attr("disabled","disabled");
    });

//普通登录-结束------------------------------------------------------------------------------------------------------------------------

//快速登录-开始------------------------------------------------------------------------------------------------------------------------
    //快速登录提交表单
    $("#form_login_fast").submit(function()
    {
        $("#login_cart .tip").hide();
        //获取帐号和验证码
        var account=$.trim($("#account_login_fast").val());
        var verifycode=$("#verifycode_login_fast").val();
        //帐号为空检查
        if(!account||account=="手机/邮箱")
        {
            $("#error_login_fast").html("<span>请输入已验证手机/邮箱</span>");
            return false;
        }
        //帐号格式检查
        if(!is_mobile(account)&&!is_email(account))
        {
            $("#error_login_fast").html("<span>手机/邮箱格式错误</span>").show();
            return false;
        }
        //验证码为空检查
        if(!verifycode||verifycode=="验证码")
        {
            $("#error_login_fast").html("<span>请输入验证码</span>");
            return false;
        }
        //验证码格式检查
        if(!verifycode.match(/^\d{6}$/))
        {
            $("#error_login_fast").html("<span>验证码错误</span>");
            return false;
        }
        //图形验证码检查
        if($("#captcha_login_fast_box").is(":visible")===true)
        {
            if(!$("#captcha_login_fast").val()||$("#captcha_login_fast").val()=="图形验证码")
            {
                $("#error_login_fast").html("<span>请输入图形验证码</span>");
                return false;
            }
            else
            {
                if(!check_captcha("captcha_login_fast",$("#captcha_login_fast").val()))
                {
                    $("#error_login_fast").html("<span>图形验证码不正确</span>");
                    load_captcha_login_fast();
                    $("#captcha_login_fast").val("");
                    return false;
                }
            }
        }
        //检查快速登录表单
        var data=check_form("/index.php?app=member&act=login&mode=fast&opt=check","form_login_fast");
        if(data.flag<0)
        {
            $("#error_login_fast").html('<span>'+data.info+'</span>');
            $("#login_cart .tip").hide();
            if(data.block)
            {
                load_captcha_login_fast();
            }
            return false;
        }
        //提交普通登录表单效果
        $("#submit_login_fast").val("登录中...").attr("disabled","disabled");
    });

    //快速登录获取验证码
    $("#send_verifycode_login_fast").live("click",function()
    {
        //检查是否正在发送
        if(timing_login_fast)
        {
            return false;
        }
        //获取帐号
        var account=$.trim($("#account_login_fast").val());
        //帐号为空检查
        if(!account)
        {
            $("#error_login_fast").html("<span>请输入已验证手机/邮箱</span>").show();
            return false;
        }
        //帐号格式检查
        if(!is_mobile(account)&&!is_email(account))
        {
            $("#error_login_fast").html("<span>手机/邮箱格式错误</span>").show();
            return false;
        }
        timer_start_login_fast();
        //获取验证码
        $.ajax(
                {
                    type:"post",
                    url:"/index.php?app=member&act=login&mode=fast&opt=verifycode",
                    data:{"account_login_fast":$("#account_login_fast").val()},
                    dataType:"json",
                    success:function(data)
                    {
                        switch(data.flag)
                        {
                            case -1:
                                $("#error_login_fast").html('<span>该手机号未注册，<a target="_blank" href="/index.php?app=member&act=register">马上注册衣联帐号>></a></span>').show();
                                timer_stop_login_fast();
                                break;
                            case -3:
                                $("#error_login_fast").html('<span>该邮箱未注册，<a target="_blank"  href="/index.php?app=member&act=register">马上注册衣联帐号>></a></span>').show();
                                timer_stop_login_fast();
                                break;
                            case -5:
                                $("#error_login_fast").html('<span>帐号格式错误</span>').show();
                                timer_stop_login_fast();
                                break;
                            case 1:
                                $("#error_login_fast").html('<span class="ok">验证码短信已发送</span>').show();
                                tip_verifycode_count_login();
                                break;
                            case 2:
                                $("#error_login_fast").html('<span>验证码短信发送失败</span>').show();
                                timer_stop_login_fast();
                                break;
                            case 3:
                                $("#error_login_fast").html('<span class="ok">验证码邮件已发送，<a target="_blank" href="'+get_email_site($("#account_login_fast").val())+'">前往邮箱>></a></span>').show();
                                break;
                            case 4:
                                $("#error_login_fast").html('<span>邮件发送失败</span>').show();
                                timer_stop_login_fast();
                                break;
                            case 5:
                                $("#error_login_fast").html('<span>发送验证码申请太频繁</span>').show();
                                timer_stop_login_fast();
                                break;
                            case 6:
                                $("#error_login_fast").html('<span>发送验证码申请太频繁，请稍后再试</span>').show();
                                timer_stop_login_fast();
                                break;
                        }
                    }
                });
    });
//快速登录-结束------------------------------------------------------------------------------------------------------------------------

//普通注册-开始------------------------------------------------------------------------------------------------------------------------
    $.fn.handle_register=function()
    {
        //失去焦点手机邮箱验证
        $("#account_register").blur(function()
        {
            //检查帐号
            var account=$.trim($("#account_register").val());
            ck_account=check_account(account);
            if(!ck_account.flag)
            {
                $("#tip_account_register").text(ck_account.info).removeClass("ok").addClass("error").show();
            }
            else
            {
                $.ajax(
                        {
                            type:"post",
                            url:"/index.php/member-check_account.html",
                            data:{"account_register":$("#account_register").val()},
                            dataType:"json",
                            success:function(data)
                            {
                                if(data.flag==='-1')
                                {
                                    $("#tip_account_register").html('<span>该手机号已注册，<a id="ap_tab2" target="_blank" href="/index.php?app=member&act=login">马上登录>></a></span>').addClass("error").removeClass("ok").show();
                                }
                                else if(data.flag==='-2')
                                {
                                    $("#tip_account_register").html('<span>该邮箱已注册，<a id="ae_tab2" target="_blank" href="/index.php?app=member&act=login">马上登录>></a></span>').addClass("error").removeClass("ok").show();
                                }
                                else
                                {
                                    $("#tip_account_register").addClass("ok").removeClass("error").text("");
                                }
                            }
                        });
            }
        });

        //失去焦点密码验证
        $(".reg_pe_password").blur(function()
        {
            $("#tip_pwd").hide();
            var password=$(this).val();
            ck_password=check_password(password);
            if(!ck_password.flag)
            {
                $(this).siblings(".tip").removeClass("ok").text(ck_password.info).show();
            }
            else
            {
                $(this).siblings(".tip").addClass("ok").text("").show();
            }
        });

        //密码强度提示
        $(".reg_pe_password").keyup(function()
        {
            var m=paw_strength_check($(this).val());
            if(m===0)
            {
                $("#tip_pwd .leve").removeClass("leve1 leve2 leve3");
                $("#leve_txt").text("");
            }
            if(m===1)
            {
                $("#tip_pwd .leve").addClass("leve1").removeClass("leve2 leve3");
                $("#leve_txt").text("弱");
            }
            if(m===2)
            {
                $("#tip_pwd .leve").addClass("leve2").removeClass("leve1 leve3");
                $("#leve_txt").text("中");
            }
            if(m===3)
            {
                $("#tip_pwd .leve").addClass("leve3").removeClass("leve1 leve2");
                $("#leve_txt").text("强");
            }
        });

        //失去焦点图形验证码验证
        $("#captcha_register").blur(function()
        {
            var captcha=$.trim($("#captcha_register").val());
            if(!captcha||captcha=="图形验证码")
            {
                $("#tip_captcha_register").text("请填写图形验证码！").removeClass("ok").addClass("error").show();
            }
            else
            {
                $.ajax(
                        {
                            url:"captcha-check_captcha.html?",
                            data:{name:"captcha_register",captcha:$("#captcha_register").val()},
                            dataType:"json",
                            success:function(data)
                            {
                                if(!data)
                                {
                                    $("#tip_captcha_register").text("图形验证码错误！").removeClass("ok").addClass("error").show();
                                    load_captcha_register();
                                }
                                else
                                {
                                    $("#tip_captcha_register").text("").removeClass("error").addClass("ok").show();
                                }
                            }
                        });
            }
        });

        //提交普通注册表单-第1步
        $("#form_register").submit(function()
        {
            //检查是否同意衣联协议
            if($("#agree").is(":checked")===false)
            {
                alert("请阅读并同意《衣联用户服务协议》");
                return false;
            }
            //检查帐号格式
            var account=$.trim($("#account_register").val());
            ck_account=check_account(account);
            if(!ck_account.flag)
            {
                $("#tip_account_register").text(ck_account.info).removeClass("ok").addClass("error").show();
                return false;
            }
            //检查密码格式
            var password=$("#password").val();
            ck_password=check_password(password);
            if(!ck_password.flag)
            {
                $("#tip_password").text(ck_password.info).removeClass("ok").addClass("error").show();
                return false;
            }
            //检查图形验证码是否正确
            var data=check_captcha("captcha_register",$("#captcha_register").val());
            if(!data)
            {
                $("#tip_captcha_register").text("图形验证码错误！").removeClass("ok").addClass("error").show();
                load_captcha_register();
                return false;
            }
            //检查帐号是否占用
            var data=check_form_data("/index.php?app=member&act=check_account",{"account_register":$("#account_register").val()});
            if(data.flag===-1)
            {
                $("#tip_account_register").html('<span>该手机号已注册，<a id="ap_tab2" target="_blank" href="/index.php?app=member&act=login">马上登录>></a></span>').removeClass("ok").addClass("error").show();
                return false;
            }
            else if(data.flag===-2)
            {
                $("#tip_account_register").html('<span>该邮箱已注册，<a id="ae_tab2" target="_blank" href="/index.php?app=member&act=login">马上登录>></a></span>').removeClass("ok").addClass("error").show();
                return false;
            }
            
            //提交注册表单效果
            $("#submit_register").val("下一步...").attr("disabled","disabled");
        });
    }

    $.fn.handle_register_confirm=function()
    {
        //失去焦点动态验证码验证
        $("#verifycode_register").blur(function()
        {
            var account=$.trim($("#account_register").val());
            var verifycode=$.trim($("#verifycode_register").val());
            ck_verifycode=check_verifycode(verifycode);
            if(!ck_verifycode.flag)
            {
                $("#tip_verifycode_register").text(ck_verifycode.info).removeClass("ok").addClass("error").show();
            }
            else{
                $.ajax(
                        {
                            type:"post",
                            url:"/index.php?app=member&act=check_verifycode",
                            data:{account_register:account,verifycode_register:verifycode},
                            dataType:"json",
                            success:function(data)
                            {
                                if(data.flag<0)
                                {
                                    $("#tip_verifycode_register").addClass("error").removeClass("ok").text(data.info).show();
                                }
                                else
                                {
                                    $("#tip_verifycode_register").addClass("ok").removeClass("error").text("");
                                }
                            }
                        });
            }
        });

        //普通注册发送验证码
        $("#send_verifycode_register").live("click",function()
        {
            //检查是否正在发送
            if(timing_register)
            {
                return false;
            }
            //获取帐号
            var account=$.trim($("#account_register").val());
            //检查帐号
            ck_account=check_account(account);
            if(!ck_account.flag)
            {
                $("#tip_account_register").text(ck_account.info).removeClass("ok").addClass("error").show();
                return false;
            }
            else
            {
                //倒计时
                timer_start_register();
                //获取验证码
                $.ajax(
                        {
                            type:"post",
                            url:"/index.php/member-verifycode.html?",
                            data:{"account_register":$("#account_register").val(),"password":$("#password").val()},
                            dataType:"json",
                            success:function(data)
                            {
                                switch(data.flag)
                                {
                                    case -3:
                                        $("#tip_verifycode_register").html('<span>该手机号已注册，<a id="ap_tab2" target="_blank" href="/index.php?app=member&act=login">马上登录>></a></span>').addClass("error").removeClass("ok").show();
                                        timer_stop_register();
                                        break;
                                    case -4:
                                        $("#tip_verifycode_register").html('<span>该邮箱已注册，<a id="ae_tab2" target="_blank" href="/index.php?app=member&act=login">马上登录>></a></span>').addClass("error").removeClass("ok").show();
                                        timer_stop_register();
                                        break;
                                    case -5:
                                        $("#tip_verifycode_register").html('<span>帐号格式错误</span>').addClass("error").removeClass("ok").show();
                                        timer_stop_register();
                                        $("#login_cart .tip").hide();
                                        break;
                                    case 1:
                                        $("#tip_verifycode_register").html('<span class="ok">验证码短信已发送</span>').addClass("ok").removeClass("error").show();
                                        tip_verifycode_count_register();
                                        break;
                                    case -13:
                                        $("#tip_verifycode_register").html('<span>验证码短信发送失败</span>').addClass("error").removeClass("ok").show();
                                        timer_stop_register();
                                        break;
                                    case 2:
                                        $("#tip_verifycode_register").html('<span class="ok">验证邮件已发送，<a target="_blank" href="'+get_email_site($("#account_register").val())+'">前往邮箱>></a></span>').addClass("ok").removeClass("error").show();
                                        break;
                                    case -14:
                                        $("#tip_verifycode_register").html('<span>邮件发送失败</span>').addClass("error").removeClass("ok").show();
                                        timer_stop_register();
                                        break;
                                    case -11:
                                        $("#tip_verifycode_register").html('<span>发送验证码申请太频繁</span>').addClass("error").removeClass("ok").show();
                                        timer_stop_register();
                                        break;
                                    case -12:
                                        $("#tip_verifycode_register").html('<span>发送验证码申请太频繁，请稍后再试</span>').addClass("error").removeClass("ok").show();
                                        timer_stop_register();
                                        break;
                                }
                            }
                        });
            }
        });


        //提交普通注册表单-第2步
        $("#form_register_confirm").submit(function()
        {
            //检查动态验证码格式
            var verifycode=$.trim($("#verifycode_register").val());
            ck_verifycode=check_verifycode(verifycode);
            if(!ck_verifycode.flag)
            {
                $("#tip_verifycode_register").text(ck_verifycode.info).removeClass("ok").addClass("error").show();
                return false;
            }

            //检查动态验证码是否正确
            var result=check_form_data("/index.php?app=member&act=check_verifycode",{account_register:$("#account_register").val(),verifycode_register:$("#verifycode_register").val()});
            if(result.flag<0)
            {
                $("#tip_verifycode_register").text("验证码错误或已失效！").removeClass("ok").addClass("error").show();
                return false;
            }

            //提交注册表单效果
            $("#submit_register_confirm").val("提交中...").attr("disabled","disabled");
        });
    }

    //前往邮箱网址
    $("#goto_email_site").click(function()
    {
    	//$("#goto_email_site").attr('target',"_blank");
    	var hh = get_email_site($("#account_register").val());
    	$("#goto_email_site").attr('href',hh);
        //location.href=get_email_site($("#account_register").val());
    });

    $("#form_register").handle_register();
    $("#form_register_confirm").handle_register_confirm();

//普通注册-结束------------------------------------------------------------------------------------------------------------------------

//快速注册-开始------------------------------------------------------------------------------------------------------------------------
    $.fn.handle_register_fast=function()
    {
        //快速注册提交表单
        $("#form_register_fast").submit(function()
        {
            $("#login_cart .tip").hide();
            //获取帐号和验证码
            var account=$.trim($("#account_register_fast").val());
            var verifycode=$("#verifycode_register_fast").val();
            //判断是否同意衣联协议
            if($("#form_register_fast #agree").is(":checked")===false)
            {
                $("#login_cart .tip").hide();
                $("#error_register_fast").html('<span>请阅读并同意《衣联用户服务协议》</span>');
                return false;
            }
            //帐号为空检查
            if(!account||account=="手机/邮箱")
            {
                $("#error_register_fast").html("<span>请输入手机/邮箱</span>");
                $("#login_cart .tip").hide();
                return false;
            }
            //帐号格式检查
            if(!is_mobile(account)&&!is_email(account))
            {
                $("#error_register_fast").html("<span>手机/邮箱格式错误</span>").show();
                $("#login_cart .tip").hide();
                return false;
            }
            //验证码为空检查
            if(!verifycode||verifycode=="验证码")
            {
                $("#error_register_fast").html("<span>请输入验证码</span>");
                $("#login_cart .tip").hide();
                return false;
            }
            //验证码格式检查
            if(!verifycode.match(/^\d{6}$/))
            {
                $("#error_register_fast").html("<span>验证码错误</span>");
                $("#login_cart .tip").hide();
                return false;
            }
            //检查快速注册表单
            var data=check_form("/index.php?app=member&act=register&mode=fast&opt=check","form_register_fast");
            if(data.flag<0)
            {
                $("#error_register_fast").html('<span>'+data.info+'</span>');
                $("#login_cart .tip").hide();
                return false;
            }
            //提交快速注册表单效果
            $("#submit_register_fast").val("注册中...").attr("disabled","disabled");
        });

        //快速注册获取验证码
        $("#send_verifycode_register_fast").live("click",function()
        {
            //检查是否正在发送
            if(timing_register_fast)
            {
                return false;
            }
            $("#login_cart .tip").hide();
            //获取帐号
            var account=$.trim($("#account_register_fast").val());
            //帐号为空检查
            if(!account)
            {
                $("#error_register_fast").html("<span>请输入手机/邮箱</span>").show();
                return false;
            }
            //帐号格式检查
            if(!is_mobile(account)&&!is_email(account))
            {
                $("#error_register_fast").html("<span>手机/邮箱格式错误</span>").show();
                return false;
            }
            //检查图形验证码
            var captcha=$.trim($("#captcha_register_fast").val());
            if(!captcha)
            {
                $("#error_register_fast").html("<span>请输入图片内容</span>").show();
                return false;
            }
            //倒计时
            timer_start_register_fast();
            //$("#send_verifycode_register_fast").removeClass("red");
            //获取验证码
            $.ajax(
                    {
                        type:"post",
                        url:"/index.php?app=member&act=register&mode=fast&opt=send_verifycode",
                        data:{"account_register":$("#account_register_fast").val(),"captcha_register":$("#captcha_register_fast").val()},
                        dataType:"json",
                        success:function(data)
                        {
                            switch(data.flag)
                            {
                                case -2:
                                    $("#error_register_fast").html('<span>该手机号已注册，<a id="ap_tab2" href="javascript:void(0)">马上登录>></a></span>').show();
                                    timer_stop_register_fast();
                                    break;
                                case -4:
                                    $("#error_register_fast").html('<span>该邮箱已注册，<a id="ae_tab2" href="javascript:void(0)">马上登录>></a></span>').show();
                                    timer_stop_register_fast();
                                    break;
                                case -5:
                                    $("#error_register_fast").html('<span>帐号格式错误</span>').show();
                                    timer_stop_register_fast();
                                    $("#login_cart .tip").hide();
                                    break;
                                case -6:
                                    $("#error_register_fast").html('<span>请输入正确的图片内容</span>').show();
                                    timer_stop_register_fast();
                                    $("#captcha_register_fast").val("");
                                    load_captcha_register();
                                    break;
                                case 1:
                                    $("#error_register_fast").html('<span class="ok">验证码短信已发送</span>').show();
                                    tip_verifycode_count_register();
                                    load_captcha_register();
                                    break;
                                case 2:
                                    $("#error_register_fast").html('<span>验证码短信发送失败</span>').show();
                                    timer_stop_register_fast();
                                    break;
                                case 3:
                                    $("#error_register_fast").html('<span class="ok">验证码邮件已发送，<a target="_blank" href="'+get_email_site($("#account_register_fast").val())+'">前往邮箱>></a></span>').show();
                                    load_captcha_register();
                                    break;
                                case 4:
                                    $("#error_register_fast").html('<span>邮件发送失败</span>').show();
                                    timer_stop_register_fast();
                                    break;
                                case 5:
                                    $("#error_register_fast").html('<span>发送验证码申请太频繁</span>').show();
                                    timer_stop_register_fast();
                                    break;
                                case 6:
                                    $("#error_register_fast").html('<span>发送验证码申请太频繁，请稍后再试</span>').show();
                                    timer_stop_register_fast();
                                    break;
                            }
                        }
                    });
        });
    }
    $("#form_register_fast").handle_register_fast();

//快速注册-结束------------------------------------------------------------------------------------------------------------------------

//密保注册-开始------------------------------------------------------------------------------------------------------------------------
    $.fn.handle_register_question=function()
    {
        //用户名提示
        $("#user_name").focus(function()
        {
            var error=$(this).siblings(".tip");
            if(error.is(":visible")===true)
                error.hide();
            $("#tip_name").show();
        });

        //失去焦点检查用户名
        $("#user_name").blur(function()
        {
            $("#tip_name").hide();
            var username=$.trim($("#user_name").val());
            ck_username=check_username(username);
            if(!ck_username.flag)
            {
                $("#tip_username").text(ck_username.info).removeClass("ok").addClass("error").show();
            }
            else
            {
                $.ajax(
                        {
                            type:"post",
                            url:"/index.php?app=member&act=register&mode=question&opt=check_username",
                            data:{user_name:$("#user_name").val()},
                            dataType:"json",
                            success:function(data)
                            {
                                if(data>0)
                                {
                                    $("#tip_username").text("该用户名已经被占用！").removeClass("ok").addClass("error").show();
                                }
                                else
                                {
                                    $("#tip_username").text("").removeClass("error").addClass("ok").show();
                                }
                            }
                        });
            }
        });

        $("#ask").change(function()
        {
            if($(this).val()!="")
            {
                $(this).addClass("c666");
            }
        });

        //失去焦点检查密保问题
        $("#ask").blur(function()
        {

            var ask=$.trim($("#ask").val());
            if(!ask)
            {
                $("#tip_ask").text("请选择密保问题！").removeClass("ok").addClass("error").show();
            }
            else
            {
                $("#tip_ask").text("").removeClass("error").addClass("ok").show();
            }
        });

        //失去焦点检查密保问题答案
        $("#answer").blur(function()
        {
            var answer=$.trim($("#answer").val());
            if(!answer||answer=="密保问题答案")
            {
                $("#tip_answer").text("请填写密保问题答案！").removeClass("ok").addClass("error").show();
            }
            else
            {
                $("#tip_answer").text("").removeClass("error").addClass("ok").show();
            }
        });

        //失去焦点检查图形验证码
        $("#captcha_register").blur(function()
        {
            var captcha=$.trim($("#captcha_register").val());
            if(!captcha||captcha=="图形验证码")
            {
                $("#tip_captcha_register").text("请填写图形验证码！").removeClass("ok").addClass("error").show();
            }
            else
            {
                $.ajax(
                        { 
                        	url:"captcha-check_captcha.html?",
                            data:{name:"captcha_register",captcha:$("#captcha_register").val()},
                            dataType:"json",
                            success:function(data)
                            {
                                if(!data)
                                {
                                    $("#tip_captcha_register").text("图形验证码错误！").removeClass("ok").addClass("error").show();
                                    load_captcha_register();
                                }
                                else
                                {
                                    $("#tip_captcha_register").text("").removeClass("error").addClass("ok").show();
                                }
                            }
                        });
            }
        });

        //提交密保注册
        $("#form_register_question").submit(function()
        {
            //检查是否同意衣联协议
            if($("#agree").is(":checked")===false)
            {
                alert("请阅读并同意《衣联用户服务协议》");
                return false;
            }
            //检查用户名
            var username=$.trim($("#user_name").val());
            ck_username=check_username(username);
            if(!ck_username.flag)
            {
                $("#tip_username").text(ck_username.info).removeClass("ok").addClass("error").show();
                return false;
            }
            //检查密码
            var password=$("#password").val();
            ck_password=check_password(password);
            if(!ck_password.flag)
            {
                $("#tip_password").text(ck_password.info).removeClass("ok").addClass("error").show();
                return false;
            }
            //检查密保问题
            var ask=$("#ask").val();
            if(!ask)
            {
                $("#tip_ask").text("请选择密保问题！").removeClass("ok").addClass("error").show();
                return false;
            }
            //检查密保问题答案
            var answer=$.trim($("#answer").val());
            if(!answer)
            {
                $("#tip_answer").text("请填写密保问题答案！").removeClass("ok").addClass("error").show();
                return false;
            }
            //检查图形验证码
            var captcha=$.trim($("#captcha_register").val());
            if(!captcha||captcha=="图形验证码")
            {
                $("#tip_captcha_register").text("请填写图形验证码！").removeClass("ok").addClass("error").show();
                return false;
            }
            //检查图形验证码是否正确
            var data=check_captcha("captcha_register",$("#captcha_register").val());
            if(!data)
            {
                $("#tip_captcha_register").text("图形验证码错误！").removeClass("ok").addClass("error").show();
                load_captcha_register();
                return false;
            }
            //检查用户名是否占用
            var result=check_form_data("/index.php?app=member&act=register&mode=question&opt=check_username",{user_name:$("#user_name").val()});
            if(result>0)
            {
                $("#tip_username").text("该用户名已经被占用！").removeClass("ok").addClass("error").show();
                return false;
            }
            //提交注册表单效果
            $("#submit_register_question").val("注册中...").attr("disabled","disabled");
        });
    }
    $("#form_register_question").handle_register_question();
//密保注册-结束------------------------------------------------------------------------------------------------------------------------

//效果-开始------------------------------------------------------------------------------------------------------------------------

    $(".middler").live("click",function(){
        $(".tip_email p").remove();
    });

    /**Tab选项那竖线显示效果**/
    $(".tab-trigger li#tab1").live("click",function(){
        $(".tip_email p").remove();
        $(this).addClass("tab-trigger-item-current").siblings().removeClass("tab-trigger-item-current");
        $("#lg").show().siblings().hide();
        $(".tab-trigger li#tab2").addClass("reg_bg").removeClass("none_bg");
    });

    $(".tab-trigger li#tab2").live("click",function(){
        $(".tip_email p").remove();
        $(this).addClass("tab-trigger-item-current").siblings().removeClass("tab-trigger-item-current");
        $("#rg").show().siblings().hide();
        $(".tab-trigger li#tab1,.tab-trigger li#tab3").addClass("none_bg").removeClass("reg_bg");
        if($("#is_captcha_login_fast").length>0 && $("#form_login_fast").is(":visible")===true && $("#captcha_login_fast_box").is(":visible")===false)
        {
            load_captcha_login_fast();
        }
    });

    $(".tab-trigger li#tab3").live("click",function(){
        $(".tip_email p").remove();
        $(this).addClass("tab-trigger-item-current").siblings().removeClass("tab-trigger-item-current");
        $("#rg1").show().siblings().hide();
        $(this).prev("li").addClass("none_bg").removeClass("reg_bg");
        $(".tab-trigger li#tab1").addClass("reg_bg").removeClass("none_bg");
        if(!$("#captcha_register_box").find("img").attr("src"))
        {
            load_captcha_register();
        }
    });

    //输入框获取焦点删除提示信息
    $(".login_text").live("focusin",function()
    {
        $(".show_error span:not(.ok),.show_error1 span:not(.ok)").remove();
    });

    $(".tab-cnt").live("click",function(){
        $(".tip_email p").remove();
    });

    $("#password,#verifycode_login_fast,#account_register_fast").live("focusin",function(){
        $(".tip_email p").remove();
    });

    //切换选项卡
    $(".login_top .tab li").live("click",function()
    {
        var index=0;
        index=$(".login_top .tab li").index(this);
        $(this).addClass("current").siblings().removeClass("current");
        $("#form_tab form").eq(index).show().siblings().hide();
        if($("#is_captcha_login_fast").length>0 && $("#form_login_fast").is(":visible")===true && $("#captcha_login_fast_box").is(":visible")===false)
        {
            load_captcha_login_fast();
        }
    });

    $("#form_login_fast  #account_login_fast").focus(function()
    {
        $(".c_send_button").addClass("c_color");
    });

    //马上登录链接调转
    $("#ap_tab2,#ae_tab2").live("click",function(){
        $(".tab-trigger li#tab2").click();
    });

    //输入邮箱提示效果
    $(".tip_email p").live("mouseover",function(){
        $(this).addClass("p_hover");
    });

    $(".tip_email p").live("mouseout",function(){
        $(this).removeClass("p_hover");
    });

    $("#form_login .tip_email p").live("click",function(){
        var nameValue=$(this).text();
        $("#account_login").val(nameValue);
        $(".tip_email p").remove();
    });

    $("#form_login_fast .tip_email p").live("click",function(){
        var nameValue=$(this).text();
        $("#account_login_fast").val(nameValue);
        $(".tip_email p").remove();
    });

    $("#form_register_fast .tip_email p").live("click",function(){
        var nameValue=$(this).text();
        $("#account_register_fast").val(nameValue);
        $(".tip_email p").remove();
    });

    $(document).keydown(function(event)
    {
        var tip1=$('#form_login .tip_email p');
        var tip2=$('#form_login_fast .tip_email p');
        var tip3=$('#form_register_fast .tip_email p');

        var t_hover1=$("#form_login .tip_email p.p_hover");
        var t_hover2=$("#form_login_fast .tip_email p.p_hover");
        var t_hover3=$("#form_register_fast .tip_email p.p_hover");

        switch(event.keyCode)
        {
            case (38):
                if(tip1.eq(0).hasClass("p_hover")){
                    return false;
                }else{
                    t_hover1.removeClass("p_hover").prev("p").addClass("p_hover");
                    t_hover2.removeClass("p_hover").prev("p").addClass("p_hover");
                    t_hover3.removeClass("p_hover").prev("p").addClass("p_hover");
                }
                break;

            case (40):
                if(!tip1.hasClass("p_hover")&&!tip2.hasClass("p_hover")&&!tip3.hasClass("p_hover")){
                    tip1.eq(0).addClass("p_hover");
                    tip2.eq(0).addClass("p_hover");
                    tip3.eq(0).addClass("p_hover");
                }else{
                    if(tip1.eq(8).hasClass("p_hover")){
                        return false;
                    }else{
                        t_hover1.removeClass("p_hover").next("p").addClass("p_hover");
                        t_hover2.removeClass("p_hover").next("p").addClass("p_hover");
                        t_hover3.removeClass("p_hover").next("p").addClass("p_hover");
                    }
                }

                break;

            case (13):
                if($("#form_login .tip_email p").length>0||$("#form_login_fast .tip_email p").length>0||$("#form_register_fast .tip_email p").length>0){
                    var name_value=t_hover1.text();
                    var name_value_fast=t_hover2.text();
                    var name_value_register=t_hover3.text();
                    $("#form_login #account_login").val(name_value);
                    $("#form_login_fast #account_login_fast").val(name_value_fast);
                    $("#form_register_fast #account_register_fast").val(name_value_register);
                    $(".tip_email p").remove();
                }

                if(document.activeElement.id=='account_login'){
                    return false;
                }
                else if(document.activeElement.id=="account_login_fast"){
                    return false;
                }
                else if(document.activeElement.id=="account_register_fast"){
                    return false;
                }

        }
    });

    var emai_address=['qq.com','gmail.com','126.com','163.com','hotmail.com','yahoo.com','live.com','sohu.com','sina.com'];
    $('#account_login,#account_login_fast,#account_register_fast').live('input',function()
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
                    if(!address||emai_address[i].indexOf(address)===0)
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

    $("#password,#account_login").live("focusin",function(){
        $("#password").parent(".pwd_img").addClass("bg_none");
    });

    $("#password,#account_login").live("focusout",function(){
        if($("#password").val()==''){
            $("#password").parent(".pwd_img").removeClass("bg_none");
        }
    });
//效果-结束------------------------------------------------------------------------------------------------------------------------

});


$(function(){
//手机或邮箱注册input获取焦点图标改变
var index=0;
$('.sy-ul .login_text').focus(function(){ 
  index=$('.sy-ul .login_text').index(this);
  switch(index){
    case 0:
         $(this).prev('.icon').addClass('icon-focus1');
         break;
    case 1:
         $(this).prev('.icon').addClass('icon-focus2');
         break;
    case 2:
         $(this).prev('.icon').addClass('icon-focus3');
         break;
    default:
         break;

    }
});

$('.sy-ul .login_text').blur(function(){
    switch(index){
    case 0:
         $(this).prev('.icon').removeClass('icon-focus1');
         break;
    case 1:
         $(this).prev('.icon').removeClass('icon-focus2');
         break;
    case 2:
         $(this).prev('.icon').removeClass('icon-focus3');
         break;
    default:
         break;
    }
});



//登录密码眼睛的功能
    var s_eye=document.getElementById("password");
    $("#eye").mousedown(function(){
        s_eye.setAttribute("type","text");
    });

    $("#eye").mouseup(function(){
        s_eye.setAttribute("type","password");
    });

    $(".reg_pe_password").focus(function(){
        $("#tip_pwd").show();
        $(this).siblings(".error").hide();
    });

    /**IE兼容placeholder属性**/

    var _placeholderSupport=function(){
        var t=document.createElement("input");
        t.type="text";
        return (typeof t.placeholder!=="undefined");
    }();

    window.onload=function(){
        var arrInputs=document.getElementsByTagName("input");
        for(var i=0;i<arrInputs.length;i++){
            var curInput=arrInputs[i];
            if(!curInput.type||curInput.type==""||curInput.type=="text")
                HandlePlaceholder(curInput);
        }
    };

    function HandlePlaceholder(oTextbox){
        if(!_placeholderSupport){
            var curPlaceholder=oTextbox.getAttribute("placeholder");
            if(curPlaceholder&&curPlaceholder.length>0){
                oTextbox.value=curPlaceholder;
                oTextbox.style.color="#b1b1b1";
                oTextbox.onfocus=function(){
                    this.style.color="#666";
                    if(this.value===curPlaceholder)
                        this.value="";
                };
                oTextbox.onblur=function(){
                    if(this.value===""){
                        this.style.color="#b1b1b1";
                        this.value=curPlaceholder;
                    }
                }
            }
        }
    }

    /*兼容密码提示*/
    function pwd_Placeholder(obj){
        if(!_placeholderSupport){
            var tip=document.getElementById("placeholder");
            if(tip){
                tip.style.display="block";
                obj.onfocus=function(){
                    tip.style.display="none";
                };

                obj.onblur=function(){
                    if(pwd.value===""){
                        tip.style.display="block";
                    }
                }
            }
        }
    }
    var pwd=document.getElementById("password");
    pwd_Placeholder(pwd);

    $("#placeholder").click(function(){
        $("#password").focus();
    });

    if(!_placeholderSupport){
        $("#account_register,#user_name,#account_login").keydown(function(){
            $("#placeholder").hide();
        });
    }

    //ajax请求下父容器样式
    var frame_login_register_ajax=$(window.parent.document).find("#frame_login_register_ajax");
    if(frame_login_register_ajax.length>0)
    {
        $(window.parent.document).find('.ui-dialog').addClass('mg2_dialog');
        $(".tab-trigger .close").live("click",function(){
           $(window.parent.document).find('.blockUI').html("").hide();
        });
    }
});


    