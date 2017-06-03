<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">

<style>
    .luck-layer .luck-box {width:600px !important; height: 300px !important; line-height:30px !important;}
</style>

<div class="user_box">
<?php echo $this->fetch('member.menu.html'); ?>
       <div class="user_right user_rights fr">
		<h4>我的麦券</h4>
        <div class="voucher">
        	<ul class="kjul fl">
            	<li ><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'debit')); ?>?type=0" <?php if ($this->_var['type'] == 0): ?> class="jwsy"<?php endif; ?>>未使用（<?php echo $this->_var['num_list']['notUse']; ?>）</a></li>
                <li><span></span></li>
                <li><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'debit')); ?>?type=1" <?php if ($this->_var['type'] == 1): ?> class="jwsy"<?php endif; ?>>已使用（<?php echo $this->_var['num_list']['haveUsed']; ?>）</a></li>
                <li><span></span></li>
                <li><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'debit')); ?>?type=2" <?php if ($this->_var['type'] == 2): ?> class="jwsy"<?php endif; ?>>已过期（<?php echo $this->_var['num_list']['haveInvalid']; ?>）</a></li>
            </ul>
            <p class="kjrhsy fr"><a href="javascript:;" id="kqrhsy">如何使用？</a></p>
        </div>
        <div class="recently recentlyfs">
            <table width="100%" frame="void" rules="none" cellspacing="0" class="mekqwsy">
              <tr class="first">
                <td width="320">麦券</td>
                <td>麦券编号</td>
                <td>有效期</td>
                <td>来源</td>
              </tr>
              
              <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
              
              <tr>
                <td width="320">
                	<div class="tjtion <?php if ($this->_var['type'] != 0): ?>tjtions<?php endif; ?>">
                	    <p class="kjwordtj" style="padding:12px 0 6px 30px; font-size:14px; font-weight:bold;"><?php echo $this->_var['item']['name']; ?></p>
                    	<p class="kjwordje" style="padding:5px 30px;">￥<?php echo $this->_var['item']['money']; ?></p>
                        <p class="kjwordtj" style="padding:6px 0 0 30px; font-size:12px;">使用条件：<?php echo $this->_var['item']['category']; ?></p>
                    </div>
                </td>
                <td><?php echo $this->_var['item']['code']; ?></td>
                <td><?php echo $this->_var['item']['end_time']; ?>到期(仅剩<?php echo $this->_var['item']['datetime']; ?>天)</td>
                <td><?php echo $this->_var['item']['name']; ?></td>
			
              </tr>
             <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
             
            </table>
            <div class="flip fr">
            <?php echo $this->fetch('page.bottom.html'); ?>
            </div>
        </div>
    </div>
</div>

<div id="window004" style="display:none; width:500px; height:300px;">
	<div class="wdktbword">
	<?php echo $this->_var['article_info']; ?>
	</div>
</div>
<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/global/jquery.swipe.js"></script> 
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/static/pc/js/usercenter.js"></script>
<script src="/public/global/luck/pc/luck.js"></script> 
<script>
cotteFn.customer004()
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
