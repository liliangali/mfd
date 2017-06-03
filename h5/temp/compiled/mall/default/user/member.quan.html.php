<!DOCTYPE html>
<html lang="zh_cn">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<title>我的麦券</title>
<script type="text/javascript" src="/static/css/layer_mobile/layer.js"></script>
<script type="text/javascript" src="/static/js/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/base.css">
<link rel="stylesheet" type="text/css" href="/static/css/style.css">

<script type="text/javascript" src="/static/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript">
function activ(){
	layer.open({
		width:'660px',
		height:'490px',
		addclass:'cotte-luck',
		content:$('#window004').html()
	})
}

/*$(document).ready(function(){
	var $tab_li = $('#tab ul li');
	$tab_li.click(function(){
		$(this).addClass('selected').siblings().removeClass('selected');
		var index = $tab_li.index(this);
		$('div.tab_box > div').eq(index).show().siblings().hide();
	});	
});*/
</script>
</head>
<body>
	<div class="mian">
    	<div class="header">
			<div class="p1"><a href="javascript:history.go(-1)"></a></div>
			<div class="p2">我的麦券</div>
            <div class="p3"><a href="javascript:;" onclick="activ()">使用说明</a></div>
		</div>
        <div id="tab">
            <ul class="tab_menu c_db">
                <li <?php if (! $this->_var['status']): ?>class="selected"<?php endif; ?> ><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'quan','arg0'=>'1')); ?>">未使用</a>（<?php echo $this->_var['num_list']['notUse']; ?>）</li>
                <li <?php if ($this->_var['status'] == 'hu'): ?>class="selected"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'quan','arg0'=>'2','arg1'=>'hu')); ?>">已使用（<?php echo $this->_var['num_list']['haveUsed']; ?>）</a></li>
                <li <?php if ($this->_var['status'] == 'hi'): ?>class="selected"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'quan','arg0'=>'3','arg1'=>'hi')); ?>">已过期（<?php echo $this->_var['num_list']['haveInvalid']; ?>）</a></li>
            </ul>
            <div class="tab_box">
            	<div class="onediv">
            	<?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'li');if (count($_from)):
    foreach ($_from AS $this->_var['li']):
?>
                    <div class="not" style="position:relative;">
                        <p class="kzwpic"><?php if (! $this->_var['status']): ?><img src="/static/img/bjpic.png"><?php else: ?><img src="/static/img/hbjpic.png"><?php endif; ?></p>
                        <div class="notquan">
                            <div <?php if (! $this->_var['status']): ?>class="c_db give"<?php else: ?> class="c_db give gives"<?php endif; ?>>
                                <p <?php if (! $this->_var['status']): ?>class="c_bf1 ybword"<?php else: ?> class="c_bf1"<?php endif; ?>><?php if ($this->_var['li']['source'] == 1): ?>激活码激活<?php endif; ?></p>
                                <p class="c_bf1 c_txr"><?php echo $this->_var['li']['code']; ?></p>
                            </div>
                            <div <?php if (! $this->_var['status']): ?>class="money"<?php else: ?> class="money gives"<?php endif; ?> style=padding:6px 0 10px 0;>¥<span><?php echo $this->_var['li']['money']; ?></span>
                      <?php if ($this->_var['xianshi'] == '1'): ?>  <span style="border:1px solid #dfdfdf; height:28px; line-height:28px; padding:0 18px; display:inline-block; border-radius:16px; font-size:12px; float:right; vertical-align: middle; margin-top:5px;"><a href="<?php if ($this->_var['li']['category'] == '限普通商品使用'): ?>product.html<?php else: ?>fdiy-1-3.html<?php endif; ?>">立即使用</a></span><?php endif; ?>
                    </div>



                            <div <?php if (! $this->_var['status']): ?>class="c_db limit"<?php else: ?> class="c_db limit gives"<?php endif; ?>>
                                <p class="c_bf1"><?php echo $this->_var['li']['category']; ?></p>


                   <?php if ($this->_var['xianshi'] == '2'): ?><p style="width:54px; height:54px; position:absolute; right:11px; bottom:11px; z-index:10;"><img src="/static/img/yisy.png" style="width:100%; height:100%;"></p><?php else: ?> <p class="c_bf1 c_txr"<?php if ($this->_var['xianshi'] == '3'): ?>style="padding-right:62px;"<?php endif; ?>><?php echo $this->_var['li']['end_time']; ?> 到期<span><?php if ($this->_var['xianshi'] == '1'): ?><?php if ($this->_var['li']['datetime']): ?>(仅剩<?php echo $this->_var['li']['datetime']; ?>天)<?php endif; ?><?php endif; ?></span></p><?php endif; ?>
                    <?php if ($this->_var['xianshi'] == '3'): ?><p style="width:54px; height:54px; position:absolute; right:11px; bottom:11px; z-index:10;"><img src="/static/img/yigq.png" style="width:100%; height:100%;"></p><?php endif; ?>
                            </div>
                        </div>
                    </div>
               <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              
                </div>
<div id="window004" style="display:none;">
	<div class="wdktbword">
	<?php echo $this->_var['article_info']; ?>
	</div>
</div>
            </div>
        </div>
    </div>
</body>
</html>