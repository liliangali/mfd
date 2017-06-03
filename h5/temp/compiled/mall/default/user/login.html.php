<!DOCTYPE html>
<html lang="zh_cn">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<title>账户登录</title>
<link href="/static/css/mobile_style.css" rel="stylesheet" type="text/css">
<link href="public/static/wap/css/slx_style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="main">
    <div class="header clearfix head topBar">
        <p class="p1"><a href="javascript:history.go(-1)"><img src="public/static/wap/images/tw_03.png" /></a></p>
        <p class="p2"><?php if ($this->_var['invite_nickname'] != ''): ?><?php echo $this->_var['invite_nickname']; ?>邀您注册麦富迪<?php else: ?>用户登录<?php endif; ?></p>
    </div>
    <div class="logins" style="margin-top:80px;">
    	<input type="text" value="" placeholder="手机号/邮箱"  class="logyx input_1 jj_1 username"/>
        <input id="password"  type="password" placeholder="登录密码" name="password" class="logyx input_2 jj_1 userpwd">
        <!--<a href="/member-find_password.html" class="wjpass">忘记密码？</a>-->
        <a href="javascript:void(0)" id="loginBtn" class="delu">登 录</a>
        <input type="hidden" name='ret_url' value="<?php echo $this->_var['ret_urll']; ?>" class="ret_urll"/>
        <div class="fudongdb">
            <!--<p class="dlor"><img src="public/static/wap/images/orpic.png" /></p>-->
            <!--<a href="/member-register.html?ret_url=<?php echo $this->_var['ret_url']; ?>" class="logzc">注册为麦富迪用户</a>-->
        </div>
    </div>
</div>
<script src="/public/global/luck/mobile/luck.js"></script>
<script src="/public/global/jquery-1.8.3.min.js"></script>
<script src="/public/global/jquery.form.js"></script>
<script src="/public/static/wap/js/public.js?v=1"></script>
<script>
cottewap.pageLogin()
</script>
</body>
</html>
