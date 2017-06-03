<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta content="telephone=no" name="format-detection" />
<meta content="email=no" name="format-detection" />
<title>专家在线</title>
<link rel="stylesheet" type="text/css" href="/static/css/mobile_style.css" media="screen">
<style>

.hide{display:none;}
#shadowLayer{width:100%; max-width:640px; height:100%; background:#000; opacity:0.3; z-index:50; position:fixed; top:0; visibility:hidden;left:0;}
input[type="button"], input[type="submit"], input[type="reset"], input[type="text"], input[type="password"] {-webkit-appearance: none;}
.jj_box {display: box; display: -webkit-box; display: -moz-box; orient: horizontal; width: 100%;}
.jj_1 {box-flex: 1; -webkit-box-flex: 1; -moz-box-flex: 1; display: block;}
/*====公共/E====*/

/*====头部/S====*/
.topBar {position:relative;z-index:10;height:46px;background:#fff;}
.topBar .wrap{border-bottom: solid 1px #dfdfdf;position:fixed;width:100%;max-width:640px;background:#fff;}
.topBar .back{background:url(/static/images/sybpic.png) no-repeat center center;display: block;background-size: auto 18px;width: 45px;height: 45px;position:relative;z-index: 2;}
.topBar h1{font: normal 16px/45px '';position: absolute;left:0;top:0;width:100%;text-align:center;z-index:0;color:#000;}
/*====头部/E====*/
.expert{margin-left:12px;padding-right:12px;}
.zjinfo{margin-top:20px;border-bottom:1px solid #dfdfdf;padding-bottom:12px;}
.zjpic{width:100px; height:100px;}
.zjpic img {width:100px; height:100px;}
.zjpresent{margin-left:15px;}
.zjname{font-size:16px; height:20px; line-height:20px; overflow:hidden; position:relative;}
.zjname span{font-size:14px;color:#999;padding-left:8px;}
.zjname i{width:20px;height:15px; position:absolute; right:0; top:0;}
.zjname i img {vertical-align: middle;border: none; max-width:100%;}
.zjdepict{color:#666; max-height:60px; line-height:20px;padding-top:5px; overflow:hidden;}
.zjzixun{text-align:right;margin-top:10px;}
.zjzixun span {display:block; float:left; height:26px; line-height:26px; font-size:12px; text-align:center; overflow:hidden; margin-right:15px; color:#ef6000;}
.zjconsult{width:80px; height:26px;line-height:26px; font-size:12px; text-align:center;color:#fff; background:#ef6000; border-radius:4px; display:block; float:right;}
</style>
</head>

<body>
	<div class="main">


        <?php echo $this->fetch('headernb.html'); ?>


		<section class="expert">
        <?php if ($this->_var['adminList']): ?>
            <?php $_from = $this->_var['adminList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'kefu');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['kefu']):
?>
            <div class="zjinfo jj_box">
                <p class="zjpic"><img src="<?php echo $this->_var['kefu']['face']; ?>"></p>
                <div class="zjpresent jj_1">
                    <div class="zjname"><?php echo $this->_var['kefu']['name']; ?><span><?php echo $this->_var['kefu']['nick']; ?></span><i><img src="/static/images/rzpic.png"></i></div>
                    <p class="zjdepict"><?php echo $this->_var['kefu']['remark']; ?></p>
                    <p class="zjzixun"><a href="<?php echo $this->_var['kefu']['url']; ?>" class="zjconsult">立即咨询</a><span><?php if ($this->_var['kefu']['status'] == 1): ?>在线<?php else: ?>忙碌<?php endif; ?></span><p>
                </div>
            </div>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <?php endif; ?>
        	 <!-- <div class="zjinfo">
            	<p class="zjpic"><img src="/static/images/zj1.png"></p>
                <div class="zjpresent">
                	<div class="zjname">手术刀<span>专家在线</span><i><img src="/static/images/rzpic.png"></i></div>
                    <p class="zjdepict">擅长宠物内科，宠物产科，给狗狗接 生，剖腹产等手术。擅长处理各种疑 难杂症是我国做早的宠物医生...</p>
                    <p class="zjzixun"><a href="http://www.sobot.com/chat/h5/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf&amp;modulType=2&amp;groupid=fa3e8bfdceaf4d4bb1c0eae7a8f6a917" class="zjconsult">立即咨询</a><p>
                </div>
            </div> -->
            <!--<div class="zjinfo">
            	<p class="zjpic"><img src="/static/images/zj1.png"></p>
                <div class="zjpresent">
                	<div class="zjname">王静怡<span>主治医师</span><i><img src="/static/images/rzpic.png"></i></div>
                    <p class="zjdepict">擅长宠物内科，宠物产科，给狗狗接 生，剖腹产等手术。擅长处理各种疑 难杂症是我国做早的宠物医生...</p>
                    <p class="zjzixun"><a href="javascript:;" class="zjconsult">立即咨询</a><p>
                </div>
            </div>
            <div class="zjinfo">
            	<p class="zjpic"><img src="/static/images/zj1.png"></p>
                <div class="zjpresent">
                	<div class="zjname">王静怡<span>主治医师</span><i><img src="/static/images/rzpic.png"></i></div>
                    <p class="zjdepict">擅长宠物内科，宠物产科，给狗狗接 生，剖腹产等手术。擅长处理各种疑 难杂症是我国做早的宠物医生...</p>
                    <p class="zjzixun"><a href="javascript:;" class="zjconsult">立即咨询</a><p>
                </div>
            </div> -->
        </section>     
    </div>
</body>

<?php echo $this->_var['scripts']; ?>
<script src="public/global/luck/mobile/luck.js"></script>
<script src="public/global/jquery-1.8.3.min.js"></script>
<script src="public/static/wap/js/public.js"></script>

</html>