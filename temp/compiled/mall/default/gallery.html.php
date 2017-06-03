<?php echo $this->fetch('header-new.html'); ?>
<link href="/public/static/pc/css/ktsp_list.css" type="text/css" rel="stylesheet">

<style>.head {border: none;}</style>
<?php if ($this->_var['shufflingnumber'] == 1): ?>
<div class="mdsp" style="background: url(<?php echo $this->_var['shuffling']['img']; ?>) center top no-repeat;" title="<?php echo $this->_var['shuffling']['title']; ?>"><a href="<?php echo $this->_var['shuffling']['link_url']; ?>" <?php if ($this->_var['shuffling']['is_blank'] == 1): ?>target="_blank"<?php endif; ?>></a></div>
<?php else: ?>
<div class="device">
	<a class="arrow-left" href="#"><i></i></a>
	<a class="arrow-right" href="#"><i></i></a>
	<div class="swiper-container">
		<div class="swiper-wrapper">
			<?php $_from = $this->_var['shuffling']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ad');if (count($_from)):
    foreach ($_from AS $this->_var['ad']):
?>
                            <div class="swiper-slide" style="background:url(<?php echo $this->_var['ad']['img']; ?>) no-repeat center center;" title="<?php echo $this->_var['ad']['title']; ?>"><a href="<?php echo $this->_var['ad']['link_url']; ?>" <?php if ($this->_var['ad']['is_blank'] == 1): ?>target="_blank"<?php endif; ?>></a></div>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</div>
	</div>
	<div class="pagination"></div>
</div>
<?php endif; ?>

<div class="sppl_list clearfix" style="margin-top: 30px;">
	<h1>品类：</h1>
	<ul>
		<li <?php if (! $_GET['p_id']): ?>class="on" <?php endif; ?>>
			<a href="<?php echo $this->build_url(array('app'=>'gallery')); ?>?p_id=0&son_id=<?php echo $_GET['son_id']; ?>&order=<?php echo $_GET['order']; ?>&sort=<?php echo $_GET['sort']; ?>"> 全部</a>
		</li>
		<?php $_from = $this->_var['pList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'data');if (count($_from)):
    foreach ($_from AS $this->_var['data']):
?>
		<li <?php if ($_GET['p_id'] == $this->_var['data']['cate_id']): ?>class="on" <?php endif; ?>>
			<a href="<?php echo $this->build_url(array('app'=>'gallery')); ?>?p_id=<?php echo $this->_var['data']['cate_id']; ?>&son_id=<?php echo $_GET['son_id']; ?>&order=<?php echo $_GET['order']; ?>&sort=<?php echo $_GET['sort']; ?>"><?php echo $this->_var['data']['cate_name']; ?></a>
		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</ul>
</div>
<div class="sppl_list clearfix" style="margin-bottom: 30px; border:0;">
	<h1>选择：</h1>
	<ul>
		<li <?php if (! $_GET['son_id']): ?>class="on" <?php endif; ?>>
			<a href="<?php echo $this->build_url(array('app'=>'gallery')); ?>?son_id=0&p_id=<?php echo $_GET['p_id']; ?>&order=<?php echo $_GET['order']; ?>&sort=<?php echo $_GET['sort']; ?>"> 全部</a>
		</li>
		<?php $_from = $this->_var['sonList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'data');if (count($_from)):
    foreach ($_from AS $this->_var['data']):
?>
		<li <?php if ($_GET['son_id'] == $this->_var['data']['cate_id']): ?>class="on" <?php endif; ?>>
			<a href="<?php echo $this->build_url(array('app'=>'gallery')); ?>?son_id=<?php echo $this->_var['data']['cate_id']; ?>&p_id=<?php echo $_GET['p_id']; ?>&order=<?php echo $_GET['order']; ?>&sort=<?php echo $_GET['sort']; ?>"><?php echo $this->_var['data']['cate_name']; ?></a>
		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</ul>

</div>

<div class="gnlb">
	<div class="fllb">
		<ul class="ktsppx">
			<li <?php if (! $_GET['order']): ?> class="on" <?php endif; ?> style="text-align: left; width: 60px; background: none;"><a href="<?php echo $this->build_url(array('app'=>'gallery')); ?>?sort=p_order&p_id=<?php echo $_GET['p_id']; ?>&son_id=<?php echo $_GET['son_id']; ?> ">综合</a></li>
			
			<li <?php if ($_GET['order'] == 1 && $_GET['sort'] == "buy_count"): ?>class="on"<?php endif; ?> style="background: none;">
			<a href="<?php echo $this->build_url(array('app'=>'gallery')); ?>?sort=buy_count&order=0&p_id=<?php echo $_GET['p_id']; ?>&son_id=<?php echo $_GET['son_id']; ?>">销量</a>
			</li>
			
			<li <?php if ($_GET['order'] == 1 && $_GET['sort'] == "price"): ?>class="on"<?php endif; ?> >
			<a href="<?php echo $this->build_url(array('app'=>'gallery')); ?>?sort=price&order=<?php if ($_GET['order'] && $_GET['sort'] == 'price'): ?>0<?php else: ?>1<?php endif; ?>&p_id=<?php echo $_GET['p_id']; ?>&son_id=<?php echo $_GET['son_id']; ?>">价格</a>
			</li>
			
			<li <?php if ($_GET['order'] == 1 && $_GET['sort'] == "uptime"): ?>class="on"<?php endif; ?> style="background: none; border:none;">
			<a href="<?php echo $this->build_url(array('app'=>'gallery')); ?>?sort=uptime&order=0&p_id=<?php echo $_GET['p_id']; ?>&son_id=<?php echo $_GET['son_id']; ?>">最新</a>
			</li>
        </ul>

		<form action="" method="get" name="SearchForm" onsubmit="return validate();" class="search_form">
			<input type="text" name="keywords" size="30" placeholder="输入产品关键字" value="<?php echo $_GET['keywords']; ?>"><input type="submit" name="" value="搜索" />
		</form>

    </div>

<div class="fllb clearfix" style="overflow: initial;">
  <ul class="ktsp_list " id="brick ">
   <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
   <li class="item "> 
	<p class="p1 ">
	   <a href="<?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['item']['goods_id'])); ?>" title='<?php echo $this->_var['item']['name']; ?>'>
	     <img src="<?php echo $this->_var['item']['thumbnail_pic']; ?> " class="img1 ">
	   </a>
	 </p>
     <p class="p2 "><a href="<?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['item']['goods_id'])); ?> " title='<?php echo $this->_var['item']['item_name']; ?>'><?php echo $this->_var['item']['name']; ?></a></p>
     <p class="p3 ">¥<?php echo $this->_var['item']['price']; ?></p>
     <p class="p4 ">已售<?php echo $this->_var['item']['buy_count']; ?>件</p>
     <div class="scxq animated zoomIn " data-id="<?php echo $this->_var['item']['goods_id']; ?>"><a href="###" class="a_1 fl like" data-id="<?php echo $this->_var['item']['goods_id']; ?>"><span class="animated zoomIn" >收藏</span></a>
     <a href="<?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['item']['goods_id'])); ?>" class="a_2 fr"><span class="animated zoomIn">查看详情</span></a></div>
   </li>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </ul>  
 </div>
    <div class="pageLinks ">
        <?php if ($this->_var['page_info']['page_count']): ?>
        <div class="page mtr10">
         <!--    <a class="stat"><?php echo sprintf('共 %s 个项目', $this->_var['page_info']['item_count']); ?></a> -->
            <?php if ($this->_var['page_info']['prev_link']): ?>
            <a class="former" href="<?php echo $this->_var['page_info']['prev_link']; ?>"><</a>
            <?php else: ?>
            <span class="formerNull"><</span>
            <?php endif; ?>
            <?php if ($this->_var['page_info']['first_link']): ?>
            <a class="page_link" href="<?php echo $this->_var['page_info']['first_link']; ?>">1&nbsp;<?php echo $this->_var['page_info']['first_suspen']; ?></a>
            <?php endif; ?>
            <?php $_from = $this->_var['page_info']['page_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('page', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['page'] => $this->_var['link']):
?>
            <?php if ($this->_var['page_info']['curr_page'] == $this->_var['page']): ?>
            <a class="page_hover" href="<?php echo $this->_var['link']; ?>"><?php echo $this->_var['page']; ?></a>
            <?php else: ?>
            <a class="page_link" href="<?php echo $this->_var['link']; ?>"><?php echo $this->_var['page']; ?></a>
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php if ($this->_var['page_info']['last_link']): ?>
            <a class="page_link" href="<?php echo $this->_var['page_info']['last_link']; ?>"><?php echo $this->_var['page_info']['last_suspen']; ?>&nbsp;<?php echo $this->_var['page_info']['page_count']; ?></a>
            <?php endif; ?>
            <!-- <a class="nonce"><?php echo $this->_var['page_info']['curr_page']; ?> / <?php echo $this->_var['page_info']['page_count']; ?></a> -->
            <?php if ($this->_var['page_info']['next_link']): ?>
            <a class="down" href="<?php echo $this->_var['page_info']['next_link']; ?>">></a>
            <?php else: ?>
            <span class="downNull">></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php echo $this->fetch('navicate.html'); ?>


<?php echo $this->fetch('footer-new.html'); ?>
<!--底部/E-->
<script src="/public/global/jquery-1.8.3.min.js "></script> 
<script src="/public/static/pc/js/public.js "></script> 
<script src="/public/global/luck/pc/luck.js"></script> 
<script src="../../static/js/jquery.masonry.js "></script>
<script src="/public/static/pc/js/category.js "></script>
<script src="/public/global/idangerous.swiper.js"></script>
<script>
	var mySwiper = new Swiper('.swiper-container',{
		pagination: '.pagination',
		loop:true,
		autoplay: 2500,
		autoplayDisableOnInteraction: false,
		grabCursor: true,
		paginationClickable: true
	})
	$('.arrow-left').on('click', function(e){
		e.preventDefault()
		mySwiper.swipePrev()
	})
	$('.arrow-right').on('click', function(e){
		e.preventDefault()
		mySwiper.swipeNext()
	})
</script>


<script>
$(".like").click(function(){
	 goods_id = $(this).attr("data-id");
	var state = getCookie('hasLogin');
	if(state != 1){
		loginIn.show(function(res){
    		if(res.status != "-1"){
			  favorite(goods_id);
    		}
			})
    }else{
    	favorite(goods_id);
    }
})

function favorite(goods_id){
	$.post("<?php echo $this->build_url(array('app'=>'my_favorite','act'=>'add')); ?>",{id:goods_id, type:"goods"}, function(res){
        var res = eval("("+res+")");
		alert(res.msg);
        return false;
    })
}

function validate()
{
	var keywords = document.forms['SearchForm'].elements['keywords'].value;
	if(keywords == 0)
	{
//		alert('亲~ 请输入商品名称或关键词进行搜索~');
//		return;
	}
}

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