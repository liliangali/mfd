<?php echo $this->fetch('member.header.html'); ?>
<link href="/public/static/pc/css/slx_dlzc.css" type="text/css" rel="stylesheet">
<div class="login">
    <form id="loginBox" action="member-login.html" method="post">
        <p class="p1">会员登录</p>
        <div class="p2">
            <div class="inputBox">
                <label class="placeholder" for="user_name">手机号码</label>
                <input type="text" name="user_name" id="user_name" validate="required|phone_emall" autocomplete="off">
            </div>
        </div>
        <div class="p2">
            <div class="inputBox">
                <label class="placeholder" for="password">登录密码</label>
                <input type="password" validate="required" name="password" id="password" autocomplete="off">
            </div>
        </div>
        <input type="hidden" value="<?php echo $this->_var['ret_url']; ?>" id="ret_url" name="ret_url">

        <p class="p4">
            <input type="submit" value="登录" id="loginIn" style="margin-top:10px;">
        </p>

        <p class="p3"><a href="/member-register.html" class="fl">注册为麦富迪会员</a>
            <!--    <a href="/member-find_password.html" class="fr">忘记密码？</a>-->
            <a href="javascript:void(0)" onclick="forget()"  class="fr">忘记密码？</a>
        </p>

        <!--<div class="dlnx">-->
        	<!--<a href="#" class="qq">QQ登录</a>-->
        	<!--<a href="#" class="wx">微信登录</a>-->
        	<!--<a href="#" class="zfb">支付宝登录</a>-->
        <!--</div>-->
    </form>
</div>

<!--底部开始1-->
<?php echo $this->fetch('footer.html'); ?>
<!--底部结束-->

<script src="/public/global/jquery-1.8.3.min.js"></script>
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/global/jquery.form.js"></script>
<script src="/public/static/pc/js/user.js"></script>
<script>
function forget(){
    var user_name = $('#user_name').val();
    window.location.href="/member-find_password.html?user_name="+user_name;
}
cotteFn.login()
</script>

<!--<script src="/public/global/luck/pc/luck.js"></script>-->
<!--<script>-->
<!--//登录弹层调用方法-->
<!--//loginIn.show(function(res){-->
<!--//    alert('登录成功');-->
<!--//})-->


<!--////弹层登录-->
<!--//var loginIn={-->
<!--//    callback:null,-->
<!--//    show:function(fn){-->
<!--//        luck.open({-->
<!--//            title:'用户登录',-->
<!--//            width:'580px',-->
<!--//            height:'480px',-->
<!--//            content:'<iframe width="580" height="500" style="display:block" src="view/sc-utf-8/mall/default/user/ajax_login.html" frameborder="0"></iframe>',-->
<!--//            class:'mfd-luck',-->
<!--//        });-->
<!--////        /html/cotte/pc/弹层登录.html-->
<!--//        loginIn.callback=fn;-->
<!--//    }-->
<!--//}-->
<!--//loginIn.show(function(){-->
<!--//    alert('登录成功');-->
<!--//})-->

<!--</script>-->

</body>
</html>
