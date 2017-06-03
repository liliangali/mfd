<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<div class="user_box">
<?php echo $this->fetch('member.menu.html'); ?>
<div class="user_right user_rights fr">
		<h4 class="collection">我的收藏</h4>
		<?php if ($this->_var['collect_custom']): ?>
        <ul class="wdzplb">
        <?php $_from = $this->_var['collect_custom']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'custom');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['custom']):
        $this->_foreach['loop']['iteration']++;
?>
        	<li <?php if ($this->_foreach['loop']['iteration'] % 4 == 0): ?>class="wdsc wdscs"<?php else: ?>class="wdsc"<?php endif; ?>>
            	<a href="<?php echo $this->build_url(array('app'=>'goods','arg0'=>$this->_var['custom']['item_id'])); ?>" target="_blank"><img src="<?php echo $this->_var['custom']['ctm']['thumbnail_pic']; ?>" class="zstppic"></a>
                <p class="sctitle"><a href="<?php echo $this->build_url(array('app'=>'goods','arg0'=>$this->_var['custom']['item_id'])); ?>" target="_blank"><?php echo $this->_var['custom']['ctm']['name']; ?></a></p>
                <?php if ($this->_var['custom']['ctm']['woman_price']): ?>
                <p class="sctitle"><span>¥<?php echo $this->_var['custom']['ctm']['woman_price']; ?></span><del>¥<?php echo $this->_var['custom']['ctm']['price']; ?></del></p>
                <?php else: ?>
                <p class="sctitle">¥<?php echo $this->_var['custom']['ctm']['price']; ?></p>
                <?php endif; ?>
                <div class="gwscbot"><a href="javascript:;" onclick="dropCustom(<?php echo $this->_var['custom']['item_id']; ?>,'确认删除？')" class="scshopping scdelete fl">删除</a><a href="<?php echo $this->build_url(array('app'=>'goods','arg0'=>$this->_var['custom']['item_id'])); ?>" class="scshopping fl">去购买</a></div>
            </li>
         <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
		<!--翻页开始-->
		<?php echo $this->fetch('member.page.bottom.html'); ?>
		<!--翻页结束-->
		<?php else: ?>
		  <div class="empty">
		      <div>您暂未收藏任何商品！赶快来逛逛吧。<br><br><p><a href="./" class="cc_btn s_btn">麦富迪首页</a></p></div>
		  </div>
		<?php endif; ?>

</div>
</div>

<?php echo $this->fetch('footer.html'); ?>

<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/global/jquery.swipe.js"></script> 
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/static/pc/js/usercenter.js"></script>
<script src="/public/global/luck/pc/luck.js"></script>  
<script>
cotteFn.amputate()
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

<script type="text/javascript">
function dropCustom(id){
	if(window.confirm('确定要删除？')){
		$.post("/my_favorite-dropCollect.html",{item_id:id}, function(res){
		    var res = $.parseJSON(res);
		    if(res.done == true){
        		alert(res.msg);
        		location.href='/my_favorite.html';
        		return;
    		}else{
    			alert(res.msg);
        		return;
    		}
		});
	}
}
</script>