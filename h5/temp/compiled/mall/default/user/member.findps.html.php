<!DOCTYPE html>
<html lang="zh_cn">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<title>找回密码</title>
<link href="/static/css/mobile_style.css" rel="stylesheet" type="text/css">
<link href="public/static/wap/css/slx_style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="main">
    
    <div class="header clearfix head">
        <p class="p1"><a href="javascript:history.go(-1)"><img src="public/static/wap/images/tw_03.png" /></a></p>
        <p class="p2">找回密码</p>
    </div>
    
    <div class="logins" style="margin-top:65px;">
    	<input type="text" value="" placeholder="手机号码"  class="logyx phone"/>
        <div class="yanzheng">
        	<input type="text" value="" placeholder="输入验证码" class="yanzm fl	">
            <input type="button" value="获取验证码" class="fr huoq" id="getCode">
        </div>
      
        <a href="javascript:void(0)" id="okBtn" class="zhmmnext">下一步</a>
    </div>
</div>
<script src="public/global/jquery-1.8.3.min.js"></script>
<script src="public/global/luck/mobile/luck.js"></script>
<script src="public/static/wap/js/public.js"></script>
<script>
cottewap.pageFindPwd();
 function phone(date){
	 luck.open({
		  content: date,
		  btn: [
		    '取消',
		    '呼叫'
		  ],
		  yes: function () {
			  luck.close()
		  },
		  no: function () {
			  window.location.href='tel:'+date;
		  }
		});
 }
</script>
</body>
</html>
