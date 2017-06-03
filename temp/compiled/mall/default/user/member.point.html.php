<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<div class="user_box">
<?php echo $this->fetch('member.menu.html'); ?>
    <div class="cardright fr">
		<div class="coolcard">
        	<h4>我的积分</h4>
			<div class="ktmoney">
            	<div class="ktble fl">
                	<p class="blep fl">可用积分：<span><?php echo $this->_var['user']['point']; ?></span></p>
                </div>
            </div>
        </div>
        <div class="history">
        	<div class="ktbrecord">
        		<h4>积分明细</h4>
            </div>
            <table width="100%" frame="void" rules="none" cellspacing="0">
              <tr class="kktab">
                <td width="30%">获取</td>
                <td width="48%">详细说明</td>
                <td width="22%">日期</td>
              </tr>
              <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
              <tr>
                <td><?php echo $this->_var['item']['mark']; ?><?php echo $this->_var['item']['cash_money']; ?></td>
                <td><?php echo $this->_var['item']['name']; ?></td>
                <td><?php echo local_date("Y-m-d H:i:s",$this->_var['item']['add_time']); ?></td>
              </tr>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </table>
            <div class="ktbpage fr">
             <?php echo $this->fetch('page.bottom.html'); ?>
            </div>
        </div>
    </div>
</div>
<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/global/jquery.swipe.js"></script> 
<script src="/public/static/pc/js/public.js"></script> 
<script>
//cotteFn.index()
//分享
window._bd_share_config = {
	common : {
		bdText : document.title,	
		bdDesc : '',	
		bdUrl : window.location.href, 	
		bdPic : ''
	},
	share : [{
		"bdSize" : 16
	}]
}
with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];
</script>
<?php echo $this->fetch('footer.html'); ?>
