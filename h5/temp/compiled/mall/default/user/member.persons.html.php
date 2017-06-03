<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1, user-scalable=no, minimal-ui" >
<title>个人资料</title>
<link rel="stylesheet" type="text/css" href="/static/css/mobile_style.css" media="screen">
<link type="text/css" href="public/static/wap/css/productlist.css" rel="stylesheet" />
</head>

<body>
<div class="container">
    <header class="hdtop topBar">
        <div class="edit fl">
                <p class="p1"><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'index')); ?>"><img src="public/static/wap/images/tw_03.png"></a></p>
                <p class="p2">我的资料</p>
            </div>
    </header>
    <p class="jgt"></p>
   <form method="POST" >
    <div class="personal">
        <div class="cell" style="padding:10px 0;"><p class="fl">头像</p><p class="tx fr"><img src="<?php echo $this->_var['user']['avatar']; ?>"></p></div>        
        <div class="cell"><p class="fl">昵称</p><input type="text" name="nickname" placeholder="<?php echo $this->_var['user']['nickname']; ?>"  class="cellinp fr"></div>
        <div class="cell"><p class="fl">性别</p><select name='gender'><?php echo $this->html_options(array('options'=>$this->_var['sex'],'selected'=>$this->_var['user']['gender'])); ?></select></div>
    	<div class="cell"><p class="fl">手机</p><input type="text" name="phone_mob" placeholder="<?php echo $this->_var['user']['phone_mob']; ?>"  class="cellinp fr"></div>
        <div class="cell"><p class="fl">邮箱</p><input type="text" name="email" placeholder="<?php echo $this->_var['user']['email']; ?>"  class="cellinp fr"></div>
        
    </div>
    <input type="submit" value="保存" class="tijiao">
    </form>  
</div>
<script src="public/global/jquery-1.8.3.min.js"></script>
<script src="public/global/mdate/mobiscroll.date.js"></script>
<link rel="stylesheet" href="public/global/mdate/mobiscroll.date.css">
<script src="public/global/luck/mobile/luck.js"></script>
<script src="public/static/wap/js/public.js"></script>
<script language="javascript">
	function toVaild(){
		var phone_mob = $("input[name=phone_mob]").val();
		if(!phone_mob)
		{
			return true;
		}
		
		if(/^1\d{10}$/g.test(phone_mob))              /*1开头后面10位，test返回值是true或false*/
		{      
			  return true;
		}
		else
		{
			 luck.open({
				content: '请输入正确的手机号',
				time:2000
			});
			   return false;
			
		 }
	
	}
	$('#shengri').mobiscroll().date({
			theme: 'android', 
			mode: 'scroller',
			display: 'modal',
			lang: 'zh',
			//minDate:d,
			maxDate:(function(){
				var date=new Date()
					date.setMonth(date.getMonth()-1);
				return date	
			})()
		});
</script>
</body>

</html>
