<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta content="telephone=no" name="format-detection" />
<meta content="email=no" name="format-detection" />
<title>订单评价</title>
<link rel="stylesheet" type="text/css" href="/static/css/mobile_style.css" media="screen">
<link type="text/css" href="public/static/wap/css/productlist.css" rel="stylesheet" />
<style>
/*====公共/S====*/
body {min-width: 320px; font-size:14px; background:#fff; color:#333; font-family:"微软雅黑", "Arial";}
body a{outline:none;blr:expression(this.onFocus=this.blur());}
button {font-size:12px;}
ul, ol, li {list-style:none;vertical-align:0;}
img {vertical-align: middle;border: none; max-width:100%;}
a{text-decoration:none; color:#333;}
img, a {-webkit-touch-callout: none}
input,textarea,select,button{outline:none; list-style-type:none; border:none; padding:0; margin:0;-webkit-appearance: none; -webkit-border-radius: 0;font-size:12px;}
.clearfix:after {content:"."; height:0px; line-height:0px; overflow:hidden; clear:both; display:block; visibility:hidden;}
.clearfix{zoom:1;}
.fl {float: left;}
.fr {float: right;}
.hide{display:none;}
#shadowLayer{width:100%; max-width:640px; height:100%; background:#000; opacity:0.3; z-index:50; position:fixed; top:0; visibility:hidden;left:0;}
input[type="button"], input[type="submit"], input[type="reset"], input[type="text"], input[type="password"] {-webkit-appearance: none;}
/*====公共/E====*/
/*====布局/S====*/
.main{min-width: 320px;max-width:640px;width:100%;margin:0 auto; overflow:hidden; position:relative;}
/*====布局/E====*/
/*====头部/S====*/
.topBar {position:relative;z-index:10;height:46px;background:#fff;}
.topBar .wrap{border-bottom: solid 1px #dfdfdf;position:fixed;width:100%;max-width:640px;background:#fff;}
.topBar .back{background:url(/static/images/sybpic.png) no-repeat center center;display: block;background-size: auto 18px;width: 45px;height: 45px;position:relative;z-index: 2;}
.topBar h1{font: normal 16px/45px '';position: absolute;left:0;top:0;width:100%;text-align:center;z-index:0;color:#000;}
/*====头部/E====*/
.djpj_order{margin-left:12px;padding-right:12px;}
.ddpj_product{border-bottom:1px solid #e5e5e5;overflow:hidden;display:-webkit-box;display:-moz-box;display:flex;padding:16px 0 10px 0;}
.cpypic{width:106px;}
.ddpj_suggest{color:#454545;margin-left:12px;-webkit-box-flex:1; flex-grow:1;}
.ddpj_suggest .jsword{height:20px;line-height:20px; overflow:hidden;}
.ddpj_suggest .jsword .mom{width:38%;display:inline-block;}
.ddpj_suggest .jsword .taste{margin-left:30px;}
.ddpj_judge{border-bottom:1px solid #e5e5e5;overflow:hidden;padding:9px 0 10px 0;}
.pj_user{overflow:hidden;}
.pj_user span{display:inline-block;vertical-align:middle;}
.ddpj_yhtxpic{width:23px;height:25px; border-radius:50%;}
.ddpj_yhtxpic img{border-radius:50%;}
.yhname{padding-left:9px;}
.yhstar{width:57px;height:23px;margin-left:9px;}
.pj_content{color:#686868;padding-top:4px;}
.pjbuy{overflow:hidden;display:-webkit-box;display:-moz-box;display:flex;padding:10px 0;}
.pjtada{text-align:left;color:#aaa;-webkit-box-flex:1; flex-grow:1;}
.gmkey{text-align:right;-webkit-box-flex:1; flex-grow:1;}
.gmkey a{color:#e66800;}
.pj_zhanwei{height:12px;background:#eee;}
</style>
</head>

<body>
	<div class="main">
    	<header class="topBar" id="header">
            <div class="wrap">
                <span class="back" onClick="history.go(-1)"></span>
                <h1>订单评价</h1>
            </div>
        </header>
		<section class="djpj_order">
        	<div class="ddpj_product">
            	<p class="cpypic"><img src="/public/images/cpypic.png"></p>
                <div class="ddpj_suggest">
                	<p class="jsword"><span class="mom">阶段: 怀孕期</span><span class="taste">口味:鸡肉</span></p>
                    <p class="jsword"><span class="mom">规格: 4kg</span><span class="taste">包装: 桶装</span></p>
                    <p class="jsword">功效: 胎儿发育/增强免疫/亮泽胎儿发育/增强免疫/亮泽被毛被毛</p>
                </div>
            </div>
            <div class="ddpj_judge">
            	<div class="pj_user">
                	<span class="ddpj_yhtxpic"><img src="/public/images/zwepic.png"></span><span class="yhname">雌和尚</span><span class="yhstar"><img src="/public/images/zwpic.png"></span>
                </div>
                <p class="pj_content">麦富迪宠物食品定制果然不错，个性化的包装、功能 化的多样选择，最主要的是狗狗很喜欢吃。</p>
            </div>
            <div class="pjbuy">
            	<p class="pjtada">2015-05-25&nbsp;&nbsp;&nbsp;&nbsp;09:30:15</p>
                <p class="gmkey"><a href="javascript:;">一键购买</a></p>
            </div>
        </section>
        <section class="pj_zhanwei"></section>  
        <section class="djpj_order">
        	<div class="ddpj_product">
            	<p class="cpypic"><img src="/public/images/cpepic.png"></p>
                <div class="ddpj_suggest">
                	<p class="jsword"><span class="mom">阶段: 怀孕期</span><span class="taste">口味:鸡肉</span></p>
                    <p class="jsword"><span class="mom">规格: 4kg</span><span class="taste">包装: 桶装</span></p>
                    <p class="jsword">功效: 胎儿发育/增强免疫/亮泽胎儿发育/增强免疫/亮泽被毛被毛</p>
                </div>
            </div>
            <div class="ddpj_judge">
            	<div class="pj_user">
                	<span class="ddpj_yhtxpic"><img src="/public/images/zwepic.png"></span><span class="yhname">雌和尚</span><span class="yhstar"><img src="/public/images/zwpic.png"></span>
                </div>
                <p class="pj_content">麦富迪宠物食品定制果然不错，个性化的包装、功能 化的多样选择，最主要的是狗狗很喜欢吃。</p>
            </div>
            <div class="pjbuy">
            	<p class="pjtada">2015-05-25&nbsp;&nbsp;&nbsp;&nbsp;09:30:15</p>
                <p class="gmkey"><a href="javascript:;">一键购买</a></p>
            </div>
        </section>
        <section class="pj_zhanwei"></section>      
    </div>
</body>
</html>