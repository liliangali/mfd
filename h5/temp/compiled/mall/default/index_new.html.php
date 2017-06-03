<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta content="telephone=no" name="format-detection" />
<meta content="email=no" name="format-detection" />
<title>选择您的爱宠</title>
<script type="text/javascript" src="/static/js/jquery-1.8.3.min.js"></script>
<link rel="stylesheet" type="text/css" href="public/static/wap/css/footer.css" />
<style>
/*====公共/S====*/
body, div, p, ul, ol, li, nav, footer, dl, dt, dd, h1, h2, h3, h4, h5, h6, form, img {padding: 0; margin: 0;}
body {min-width: 320px; font-size:12px; background:#f6f6f6; color:#333; font-family:"微软雅黑", "Arial";}
body a{outline:none;blr:expression(this.onFocus=this.blur());}
button {font-size:12px;}
ul, ol, li {list-style:none;vertical-align:0;}
img {vertical-align: middle;border: none; max-width:100%;}
a{text-decoration:none; color:#333;}
i {font-style: normal;}
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
.topBar .wrap{border-bottom: solid 1px #dfdfdf;position:fixed;width:100%;max-width:640px;background:#fff;height:45px;}
.topBar .back{background:url(/static/images/sybpic.png) no-repeat center center;display: block;background-size: auto 18px;width: 45px;height: 45px;position:relative;z-index: 2;}
.topBar h1{font: normal 16px/45px '';position: absolute;left:0;top:0;width:100%;text-align:center;z-index:0;color:#000;}
/*====头部/E====*/
.search{margin:10px 15px;}
.search img{width:100%;}
.mold{background:#fff;padding:20px 18px 3px 18px;border-top:1px solid #dfdfdf;}
.mold ul{margin-left:-11%; overflow:hidden;}
.mold ul li{width:15%;float:left; text-align:center;line-height:26px;margin-bottom:12px;margin-left:10%;height:70px;overflow:hidden;}
@media (min-width:321px) and (max-width:375px){.mold ul li{height:77px;}}
@media (min-width:376px) and (max-width:414px){.mold ul li{height:83px;}}
@media (min-width:415px) and (max-width:640px){.mold ul li{height:121px;}}
@media (min-width:667px) and (max-width:1920px){.mold ul li{height:140px;}}
.mold ul li a{display:block;color:#222;}
.mold ul li img{border-radius:50%;}
.letter{height:20px;line-height:20px;border-bottom:1px solid #dfdfdf;padding-left:15px;}
.contacts{background:#fff;border-bottom:1px solid #dfdfdf;overflow:hidden;}
.group{display:flex;padding:7px 0;display: box; display: -webkit-box; display: -moz-box;}
.touxiang{width:10%;margin:0 15px;}
.touxiang img{border-radius:50%;}
.group_name{flex:1;height:34px;line-height:34px;border-bottom:1px solid #dfdfdf;box-flex: 1; -webkit-box-flex: 1; -moz-box-flex: 1;}
@media (min-width:321px) and (max-width:375px){.group_name{height:36px;line-height:36px;}}
@media (min-width:376px) and (max-width:414px){.group_name{height:41px;line-height:41px;}}
@media (min-width:415px) and (max-width:640px){.group_name{height:64px;line-height:64px;}}
@media (min-width:667px) and (max-width:1920px){.group_name{height:64px;line-height:64px;}}
.group_name i{width:6px;height:6px;display:inline-block;background:#f74c30; border-radius:50%;margin-left:20px;margin-top:-2px;}
.border{border:none;}
.sort{width:10px;position:fixed;right:0px;top:50%;-webkit-transform:translateY(-50%);-moz-transform: translateY(-50%);-ms-transform: translateY(-50%);-o-transform: translateY(-50%);transform: translateY(-50%); width:45px;}
.sort ul li{text-align:center; font-size:13px; line-height:20px;}
.sort ul li a{color:#999; display:block; padding:1px 0 1px 20px;}
.sort ul .cur a{color:#f74c30;}
.zhezhao{background:#fff; position:absolute;left:0;top:0;width:100%;height:100%; z-index:10;}

.really{height:34px;line-height:32px; display:box; display:-webkit-box; display:-moz-box; position: relative;}
.really p {-moz-box-flex:1; -webkit-box-flex:1; box-flex:1;}
.really input{height:34px;line-height:34px;border:1px solid #dfdfdf; width:100%; border-radius:15px; box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing:border-box; padding:0 15px 0 40px;}

.quxiao{width:40px; margin-left:10px; height:34px;line-height:34px;display:block;text-align:center;}
.ssbut{width:32px;height:32px; display:block; position:absolute;top:0;left:10px; text-align:center;}
.ssbut img{width:13px;margin:0 auto;}

.sslist{position:absolute;top:15px;z-index:20;width:100%;}
.souslist{overflow:hidden;margin-top:13px;}
.touxiangs{width:30px;height:30px;margin-right:15px;}
.touxiangs img{border-radius:50%;}

/*购物车浮动层单独样式*/

</style>
</head>

<body>
	<div class="main">
        
        <header class="topBar" id="header">
            <div class="wrap">
                <a class="back" href="/" onclick="goIndex()" ></a>
                <h1><?php echo $this->_var['title']; ?></h1>
            </div>
        </header>

        <script>
            function goIndex(){
                app.done();
            }

            //检测是否是微信浏览器
            function is_weixin() {
                var ua = navigator.userAgent.toLowerCase();
                if (ua.match(/MicroMessenger/i) == "micromessenger") {
                    return true;
                } else {
                    return false;
                }
            }
            if (is_weixin()) {
                document.getElementById("header").style.display = 'none';
            }
        </script>
        




        <section class="search"><img src="/static/images/sspic.png"></section>
        <section class="mold">
        	<ul>
                <?php $_from = $this->_var['common_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'commons');$this->_foreach['common'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['common']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['commons']):
        $this->_foreach['common']['iteration']++;
?>
                    <?php if ($this->_foreach['common']['iteration']): ?>
                    <li><a href="<?php echo $this->build_url(array('app'=>'fdiy','act'=>'index3','arg'=>$this->_var['commons']['cate_id'])); ?>"><img src="<?php echo $this->_var['commons']['small_img']; ?>"><?php echo $this->_var['commons']['cate_name']; ?></a></li>
                    <?php endif; ?>
                    
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
        </section>
        <section>
            <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'lists');$this->_foreach['cate'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['cate']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['lists']):
        $this->_foreach['cate']['iteration']++;
?>
                <?php if ($this->_var['k'] != ''): ?>
                <div class="letter"><a name="<?php echo $this->_var['k']; ?>F"><?php echo $this->_var['k']; ?></a></div>
                <?php else: ?>
                <div class="letter"><a name="0F">#</a></div>
                <?php endif; ?>
                <section class="contacts">
                    <?php $_from = $this->_var['lists']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'info');$this->_foreach['cate'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['cate']['total'] > 0):
    foreach ($_from AS $this->_var['info']):
        $this->_foreach['cate']['iteration']++;
?>
                        <div class="group" onClick="window.location.href='/fdiy-index3-<?php echo $this->_var['info']['cate_id']; ?>.html'">
                            <p class="touxiang"><img src="<?php echo $this->_var['info']['small_img']; ?>"></p>

                            <p <?php if (($this->_foreach['cate']['iteration'] == $this->_foreach['cate']['total'])): ?>class="group_name border"<?php else: ?>class="group_name"<?php endif; ?>><?php echo $this->_var['info']['cate_name']; ?></p>
                        </div>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </section>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </section>
        <section class="sort">
        	<ul>
                <?php $_from = $this->_var['letters']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'letter');$this->_foreach['let'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['let']['total'] > 0):
    foreach ($_from AS $this->_var['letter']):
        $this->_foreach['let']['iteration']++;
?>
                    <?php if ($this->_var['letter'] != ''): ?>
                    <li><a href="#<?php echo $this->_var['letter']; ?>F" name="<?php echo $this->_var['letter']; ?>F"><?php echo $this->_var['letter']; ?></a></li>
                    <?php else: ?>
                    <li><a href="#<?php echo $this->_var['letter']; ?>F" name="<?php echo $this->_var['letter']; ?>F">#</a></li>
                    <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
        </section>
        <section class="zhezhao" style="display:none;"></section>
        <section class="sslist" style="display:none;">
        	<div style="margin:0 15px;">


                <div class="really">
                    <p><input type="text" placeholder="找找您的爱宠" id='search'/></p>
                    <a href="javascript:;" class="quxiao">取消</a>
                    <div class="ssbut"><img src="/static/images/ssico.png"></div>
                </div>

                <section class="souslist">
                </section>
        	</div>
        </section>
    </div>
    <?php echo $this->fetch('footer_footer.html'); ?>
    <?php echo $this->fetch('fudong1.html'); ?>
</body>
</html>
<script type='text/javascript' src='/public/static/m/js/luck/luck.js'></script>
<script type='text/javascript' src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>
<script>

function gotodetail(id) {
  window.location.href="fdiy-index3-"+id+".html";
}

$(".search").click(function(){
	$(".zhezhao").show();
	$(".sslist").show();
});
$(".quxiao").click(function(){
	$(".zhezhao").hide();
	$(".sslist").hide();
});
$("#search").bind('input propertychange',function(){
    var obj=$('.ssbut')
    getList(obj)
});
//读取cookies
function getCookie(name)
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
    if(arr=document.cookie.match(reg))
    return unescape(arr[2]);
    else
    return null;
}
//删除cookies
function delCookie(name)
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null)
    document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}
function getList(obj){
//    var search=$(obj).siblings('input').val()
    var search=$('#search').val()
    $('.souslist').children().remove()

    if(search.length>0){
        $.get('/fdiy-ajaxGetCateList.html',{s:search},function(res){
            if(res.done){
                $('.souslist').html('')
                $.each(res.retval,function(n,i){
                    $('.souslist').append('<div class="group" onClick="window.location.href=&apos;/fdiy-index3-'+i.cate_id+'.html&apos;">\
                    <p class="touxiang"><img src="'+i.small_img+'"></p>\
                    <p class="group_name">'+i.cate_name+'</p>\
                </div>');
                });
            }
        },'json');
    }
    
}
</script>