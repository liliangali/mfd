<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta content="telephone=no" name="format-detection" />
<meta content="email=no" name="format-detection" />
<title>物流详情</title>
<script src="js/jquery-1.8.3.min.js"></script>
<style>
/*====公共/S====*/
body, div, p, ul, ol, li, nav, footer, dl, dt, dd, h1, h2, h3, h4, h5, h6, form, img {padding: 0; margin: 0;}
body {min-width: 320px; font-size:12px; background:#fff; color:#333; font-family:"微软雅黑", "Arial";}
body a{outline:none;blr:expression(this.onFocus=this.blur());}
button {font-size:12px;}
ul, ol, li {list-style:none;vertical-align:0;}
img {vertical-align: middle;border: none; max-width:100%;}
a{text-decoration:none; color:#333;}
input,textarea,select {outline:none; list-style-type:none; border:none; padding:0; margin:0;-webkit-appearance: none; -webkit-border-radius: 0;font-size:12px;}
.clearfix:after {content:"."; height:0px; line-height:0px; overflow:hidden; clear:both; display:block; visibility:hidden;}
.clearfix{zoom:1;}
.fl {float: left;}
.fr {float: right;}
.hide{display:none;}
#shadowLayer{width:100%; max-width:640px; height:100%; background:#000; opacity:0.3; z-index:50; position:fixed; top:0; visibility:hidden;left:0;}
input[type="button"], input[type="submit"], input[type="reset"], input[type="text"], input[type="password"] {-webkit-appearance: none;}
/*====公共/E====*/
/*====头部/S====*/
.topBar {position:relative;z-index:10;height:46px;}
.topBar .wrap{border-bottom: solid 1px #dfdfdf;position:fixed;width:100%;max-width:640px;}
.topBar .back{background:url(view/sc-utf-8/mall/default/styles/default/images/sybpic.png) no-repeat center center;display: block;background-size: auto 18px;width: 45px;height: 45px;position:relative;z-index: 2;cursor: pointer;}
.topBar h1{font: normal 16px/45px '';position: absolute;left:0;top:0;width:100%;text-align:center;z-index:0;}
/*====头部/E====*/
.details{margin:20px 15px;height:55px;overflow:hidden;}
.sfpic{width:55px;height:55px;}
.infoword{margin-left:14px; display:inline;}
.word{line-height:19px;color:#686868;}
.word span{color:#e66800;}
.zhanwei{height:12px;border-top:1px solid #dfdfdf;border-bottom:1px solid #dfdfdf; background:#eeeeee;}
.logistics{margin:20px 0 0 15px}
.logistics h4{font-size:14px;font-weight:normal;border-bottom:1px solid #dfdfdf;padding-bottom:7px;}
.gzxinx{border-left:1px solid #dfdfdf;margin:15px 0 15px 4px;}
.wlxxword{margin-left:17px;border-bottom:1px solid #dfdfdf;padding:12px 15px 12px 0;line-height:18px; position:relative;}
.wlxxwords{color:#e66800;}
.wlxxword span{color:#e66800;padding:0 5px;}
.diand{width:7px;height:7px;border:1px solid #ccc; border-radius:50%; position:absolute;left:-22px;top:15px; background:#fff;}
.diands{border:1px solid #e66800;background:#e66800;}
</style>
</head>

<body>
	<div class="main">
    	<header class="topBar" id="header">
            <div class="wrap">
                <span class="back" onClick="history.go(-1)"></span>
                <h1>物流详情</h1>
            </div>
        </header>
		<section class="details">
        	<!-- <p class="sfpic fl"><img src="./styles/default/images/sfpic.png"></p> -->
            <div class="infoword fl">
                <p class="word">承运公司：<?php echo $this->_var['expressInfo']['delivery']['logi_name']; ?></p>
                <p class="word">运单编号：<?php echo $this->_var['expressInfo']['delivery']['logi_no']; ?></p>
            </div>
        </section>
        <section class="zhanwei"></section>
        <section class="logistics">
        	<h4>物流跟踪</h4>
            <div class="gzxinx">
                <?php $_from = $this->_var['expressInfo']['delivery']['wuliu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'express');$this->_foreach['exp'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['exp']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['express']):
        $this->_foreach['exp']['iteration']++;
?>
                <?php if (($this->_foreach['exp']['iteration'] <= 1)): ?><?php endif; ?>
                <div <?php if (($this->_foreach['exp']['iteration'] <= 1)): ?>class="wlxxword wlxxwords"<?php else: ?>class="wlxxword"<?php endif; ?>>
                <?php echo $this->_var['express']['context']; ?><br>
                <?php echo $this->_var['express']['ftime']; ?>
                <p <?php if (($this->_foreach['exp']['iteration'] <= 1)): ?>class="diand diands"<?php else: ?>class="diand"<?php endif; ?>></p>
                </div>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </div>
        </section>
    </div>
</body>
</html>
<script>
//检测是否是微信浏览器
function is_weixin(){
	var ua = navigator.userAgent.toLowerCase();
	if(ua.match(/MicroMessenger/i)

=="micromessenger") {
		return true;
 	} else {
		return false;
	}
}
if(is_weixin()){
	document.getElementById
	("#header").style.display='none';
}
</script>