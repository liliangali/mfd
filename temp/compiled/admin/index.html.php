<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Use IE7 mode -->
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7 charset=<?php echo $this->_var['charset']; ?>" />
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $this->_var['charset']; ?>" />
<title>Mfd管理中心</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<link href="templates/style/dialog.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'jquery.min.js'; ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'rctailor.js'; ?>" charset="utf-8"></script>
<script type="text/javascript">
   var menu = <?php echo $this->_var['menu_json']; ?>;
</script>

<script type="text/javascript" src="<?php echo $this->res_base . "/" . 'js/index.js'; ?>" charset="utf-8"></script>
</head>
<body>
<div class="back_nav">
    <div class="back_nav_list">
    <?php $_from = $this->_var['back_nav']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'menu');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['menu']):
?>
        <dl>
            <dt><?php echo $this->_var['menu']['text']; ?></dt>
            <?php $_from = $this->_var['menu']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('sub_key', 'sub_menu');if (count($_from)):
    foreach ($_from AS $this->_var['sub_key'] => $this->_var['sub_menu']):
?>
            <dd><a href="javascript:;" onclick="openItem('<?php echo $this->_var['sub_key']; ?>','<?php echo $this->_var['key']; ?>');none_fn();"><?php echo $this->_var['sub_menu']['text']; ?></a></dd>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </dl>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </div>
    <div class="shadow"></div>
    <div class="close_float"><img src="templates/style/images/close2.gif" /></div>
</div>
<div id="head">
    <div id="logo"><a href="index.php"><img src="templates/style/images/logo.png"></a></div>
    <div id="menu">
    	<span><a href="#" class="yfm">您好，<strong><?php echo $this->_var['visitor']['user_name']; ?></strong></a><b>|</b><a href="index.php?act=logout"> [退出]</a><b>|</b> <a href="index.php?app=sysmessage" target="workspace" class="notice">站内信<font>[0]</font></a><b>|</b><a href="<?php echo $this->_var['site_url']; ?>/index.php" target="_blank" class="sy">首页</a><b>|</b>
    <a href="javascript:;" class="menu_btn1" id="iframe_refresh">刷新</a><b>|</b>
    <a href="javascript:;" class="menu_btn2" id="clear_cache">更新缓存</a><b>|</b>
    <a href="#" id="back_btn" class="menu_btn3">后台导航</a></span>
    </div>
    <ul id="nav">
    </ul>
    <p class="clear"></p>
</div>


<div id="content">

    <div id="left">
        <div id="leftMenus">
            <dl id="submenu">
                <dt><b><a class="ico1" id="submenuTitle" href="javascript:;"></a></b></dt>
            </dl>
            <dl id="history" class="history">
                <dt>
                    <a class="ico2" id="historyText" href="javascript:;"><b>操作历史</b></a>
                </dt>
            </dl>
            <p class="clear"></p>
         </div>
    </div>
    
    <div id="right">
        <iframe frameborder="0" style="display:none;" width="100%" id="workspace" name="workspace"></iframe>
    </div>
    
</div>
<script src="../static/expand/jquery.form.js"></script>
<script>

$(document).ready(function(){
//	setInterval(function(){
//	    $.get("index.php?app=sysmessage",{act:"notice"},function(res){
//	        var res = eval("("+res+")");
//	        $(".notice").html(res.retval);
//		})
//	},90000);
})

</script>
</body>
</html>
