<?php echo $this->fetch('header-new.html'); ?>
<!--头部/E-->
<link href="/public/static/pc/css/gywm.css" rel="stylesheet">
<link href="/public/static/pc/css/ktsp_list.css" type="text/css" rel="stylesheet">
<!-- <div class="hdlb_banner"><a href="http://www.dev.mfd.cn/goods-264.html" target="_blank"></a></div> -->
	<?php if (count ( $this->_var['banner'] ) > 0): ?>
	<div id="banner" class="swipe">
	
		<div class="swipe-wrap">
	
	       <?php $_from = $this->_var['banner']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pictures');if (count($_from)):
    foreach ($_from AS $this->_var['pictures']):
?>
			<div><a href="<?php echo $this->_var['pictures']['link_url']; ?>" style="display:block; width:100%; height:400px; background:url(<?php echo $this->_var['pictures']['img']; ?>) no-repeat center center;" <?php if ($this->_var['pictures']['is_bank'] == 1): ?> target="_blank" <?php endif; ?>></a></div>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</div>
		<?php if (count ( $this->_var['banner'] ) > 1): ?>
			<div class="swipe-btn">
				<?php $_from = $this->_var['banner']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pictures');$this->_foreach['image'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['image']['total'] > 0):
    foreach ($_from AS $this->_var['pictures']):
        $this->_foreach['image']['iteration']++;
?>
					<span <?php if ($this->_foreach['image']['iteration'] == 1): ?>class="cur"<?php endif; ?>></span> 
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			</div>
			
			<span class="swipe-prev"></span> <span class="swipe-next"></span> 
		<?php endif; ?>
	</div>
	<?php endif; ?>
<h1 class="bt1">Sales Promotions</h1>
<h2 class="qthd">特惠活动专区</h2>

<div class="tplb">
     <?php $_from = $this->_var['list']['images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'art');if (count($_from)):
    foreach ($_from AS $this->_var['art']):
?>
    	<p><a href="<?php echo $this->_var['art']['link_url']; ?>" id="<?php echo $this->_var['art']['id']; ?>"  <?php if ($this->_var['art']['is_blank'] == 1): ?> target="_blank"<?php endif; ?>><img src="<?php echo $this->_var['art']['img']; ?>"/></a></p>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<!--底部/S-->
<div id="banner" class="swipe">	
  <span class="swipe-prev"></span> <span class="swipe-next"></span> 
</div>


<?php echo $this->fetch('navicate.html'); ?>
<?php echo $this->fetch('footer-new.html'); ?>
<!--底部/E-->
<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/global/jquery.swipe.js"></script> 
<script src="/public/static/pc/js/public.js"></script> 
<script>
//幻灯
(function(){
	var len=$('#banner img').length,slider = Swipe(document.getElementById('banner'), {
		auto: 4000,
		//continuous: false,
		callback: function(index, element) {
			if(len==2){
				switch(index){
					case 2:
					index=0;
					break;
					case 3:
					index=1;
					break; 	
				}
			}
			$('.swipe-btn span').eq(index).addClass('cur').siblings().removeClass();	
		}
	});
	$('.swipe-prev').click(slider.prev);
	$('.swipe-next').click(slider.next);
})();
$("p").on('click','a',function(){
	var id=$(this).attr('id');
	var url=$(this).attr('url');
	$.get('/activepublic-index.html',{id:id},function(res){
		if(res.done){
			console.log(url);
			window.open(url);
		}else{
			
		}
	},'json')
})
</script>
<script>
  //意见反馈弹层
  $('#feedback').click(function(){
	  $.getScript('/public/global/luck/pc/luck.js',function(){
		  luck.open({
			  title:'意见反馈',
			  width:'660px',
			  height:'475px',
			  content:'<iframe width="660" height="475" style="display:block" src="view/sc-utf-8/mall/default/feedback/ajax_feedback.html" frameborder="0"></iframe>',
			  addclass:'mfd-luck'
		  });
	  })
  });
</script>
</body>
</html>
