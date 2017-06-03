<?php echo $this->fetch('header-new.html'); ?>
<link href="/public/static/pc/css/ktsp_list.css" type="text/css" rel="stylesheet">
<style>
	.head {
		border: none;
	}
</style>
<div class="mdsp"><img src="/public/static/pc/images/ktsp.jpg"></div>



<div class="gnlb">
	<div class="fllb">
		<ul class="ktsppx">
			<li <?php if (! $_GET['order']): ?> class="on" <?php endif; ?> style="text-align: left; width: 60px; background: none;"><a href="<?php echo $this->build_url(array('app'=>'activegoods')); ?>?sort=p_order&p_id=<?php echo $_GET['p_id']; ?>&son_id=<?php echo $_GET['son_id']; ?>&active_id=<?php echo $this->_var['active_id']; ?> ">综合</a></li>
			
			<li <?php if ($_GET['order'] == 1 && $_GET['sort'] == "buy_count"): ?>class="on"<?php endif; ?> style="background: none;">
			<a href="<?php echo $this->build_url(array('app'=>'activegoods')); ?>?sort=buy_count&order=<?php if ($_GET['order'] && $_GET['sort'] == 'buy_count'): ?>0<?php else: ?>1<?php endif; ?>&p_id=<?php echo $_GET['p_id']; ?>&son_id=<?php echo $_GET['son_id']; ?>&active_id=<?php echo $this->_var['active_id']; ?>">销量</a>
			</li>
			
			<li <?php if ($_GET['order'] == 1 && $_GET['sort'] == "price"): ?>class="on"<?php endif; ?> >
			<a href="<?php echo $this->build_url(array('app'=>'activegoods')); ?>?sort=price&order=<?php if ($_GET['order'] && $_GET['sort'] == 'price'): ?>0<?php else: ?>1<?php endif; ?>&p_id=<?php echo $_GET['p_id']; ?>&son_id=<?php echo $_GET['son_id']; ?>&active_id=<?php echo $this->_var['active_id']; ?>">价格</a>
			</li>
			
			<li <?php if ($_GET['order'] == 1 && $_GET['sort'] == "uptime"): ?>class="on"<?php endif; ?> style="background: no-repeat;" >
			<a href="<?php echo $this->build_url(array('app'=>'activegoods')); ?>?sort=uptime&order=<?php if ($_GET['order'] && $_GET['sort'] == 'uptime'): ?>0<?php endif; ?>&p_id=<?php echo $_GET['p_id']; ?>&son_id=<?php echo $_GET['son_id']; ?>&active_id=<?php echo $this->_var['active_id']; ?>">最新</a>
			</li>
			
        </ul>     
    </div>

<div class="fllb clearfix" style="overflow: initial;">
  <ul class="ktsp_list " id="brick ">
   <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
   <li class="item "> 
	<p class="p1 ">
	   <a href="<?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['item']['goods_id'])); ?> " title='<?php echo $this->_var['item']['name']; ?>'>
	     <img src="<?php echo $this->_var['item']['side_images']; ?> " class="img1 ">
	     <img src="<?php echo $this->_var['item']['thumbnail_pic']; ?> " class="img2 ">
	   </a>
	 </p>
     <p class="p2 "><a href="<?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['item']['goods_id'])); ?> " title='<?php echo $this->_var['item']['item_name']; ?>'><?php echo $this->_var['item']['name']; ?></a></p>
     <p class="p3 ">活动价:¥<?php echo $this->_var['item']['activeprice']; ?></p><p class="p4" style="TEXT-DECORATION: line-through">原价:¥<?php echo $this->_var['item']['price']; ?></p><p><?php if ($this->_var['yhcase'] == 5): ?>免邮<?php endif; ?></p>
     <div class="scxq animated zoomIn like" data-id="<?php echo $this->_var['item']['goods_id']; ?>"><a href="###" class="a_1 fl"><span class="animated zoomIn" >收藏</span></a>
     <a href="<?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['item']['goods_id'])); ?>" class="a_2 fr"><span class="animated zoomIn">查看详情</span></a></div>
   </li>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </ul>  
 </div>
 <div class="pageLinks "><?php echo $this->fetch('page.bottom.html'); ?></div>
</div>

<?php echo $this->fetch('navicate.html'); ?>


<?php echo $this->fetch('footer-new.html'); ?>
<!--底部/E-->
<script src="/public/global/jquery-1.8.3.min.js "></script> 
<script src="/public/static/pc/js/public.js "></script> 
<script src="../../static/js/jquery.masonry.js "></script>
<script src="/public/static/pc/js/category.js "></script>
<script>
</script>
<script>
  //意见反馈弹层
  $('#feedback').click(function(){
	  $.getScript('/public/global/luck/pc/luck.js',function(){
		  luck.open({
			  title:'意见反馈',
			  width:'660px',
			  height:'475px',
			  content:'<iframe width="660 " height="475 " style="display:block " src="view/sc-utf-8/mall/default/feedback/ajax_feedback.html " frameborder="0 "></iframe>',
			  addclass:'mfd-luck'
		  });
	  })
  });
</script>
</body>
</html>