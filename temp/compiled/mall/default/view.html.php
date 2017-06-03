<?php echo $this->fetch('header-new.html'); ?>
<!--头部/E-->
<link href="/public/static/pc/css/gywm.css" rel="stylesheet">
<link href="/public/static/pc/css/jtxqstyle.css" type="text/css" rel="stylesheet">
<!-- content -->

	<div class="jtxqmain clearfix">
    
    	<div class="jtxqleft">
    		<div class="mbx">
			  <a href="/">首页</a>
			  <span class="jgf">></span>
			  <a href="/professor-online.html">专家在线</a> 
			  <span class="jgf">></span>
			  <a href="/professor-online-<?php echo $this->_var['article']['cate_id']; ?>.html"><?php echo $this->_var['article']['cate_name']; ?></a>
			  <span class="jgf">></span>
			  <span id="thirdtitle"><?php echo $this->_var['article']['title']; ?></span>
			</div>
        	<div class="jtxqcontent">
            	<h1><?php echo $this->_var['article']['title']; ?></h1>
                <p class="jtxqdata"><?php echo local_date("Y年m月d",$this->_var['article']['add_time']); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo local_date("H:i",$this->_var['article']['add_time']); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;浏览<?php echo ($this->_var['article']['click_num'] == '') ? '0' : $this->_var['article']['click_num']; ?></p>
                <!-- <img src="images/2.png" alt=""/> -->
                <p><p class="jtxqword"><?php echo $this->_var['article']['content']; ?></p></p>
            </div>
           <!--  <div class="jtxqzjjy">
            	<h2>专家建议<span>2016 年5月 24日&nbsp;&nbsp;&nbsp;&nbsp;19:36</span></h2>
                <p class="zjjyword">怕猫，小时候带狗儿子去买肉的时候，肉摊主养的猫刚生了孩子，见了我狗儿子，大吼一声，上来刷刷刷3下，可怜我的儿子还没有意识到发生了什么身上已经出了3道划痕，从那以后就烙下病根了。以后我们每次过去买肉，走到肉摊，狗儿子总是在距离肉摊前5米的地方停下，四处打量，死也不靠前，这还不算，小区里野猫比较多，他每次老远见了野猫，就兴冲冲的跑上去，猫跑得越快，他追得越快，可是如果猫停下不跑了，他也就蔫了，在距离猫一米的地方停下，伸着脖子假装嗅来嗅去，就是不靠前，如果这个时候我喊一声，马上有了台阶下了就跑开了，边跑边回头看，意思是如果不是我妈妈喊我，我跟你没完！</p>
                <p class="zjjyword">最有意思的是，有一次老远听着狗儿子很生气地叫 — 跟家里来人了一样， 以为出什么事情了，跑过去一看，狗儿子前面30公分的地方就是一只猫，话说这只猫很无聊的看着我狗儿子，既不害怕也不惊慌，可能狗儿子觉得不够意思，就蹶着屁股以最大分贝的声音叫着，我喊了一声狗儿子的名字，马上屁颠屁颠的跑过来了，还挺骄傲的！</p>
            </div> -->
        </div>
        <div class="jtxqright">
        	<h4>其他</h4>
            <ul>
            	<?php $_from = $this->_var['rel_articles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'rel');if (count($_from)):
    foreach ($_from AS $this->_var['rel']):
?>
            		<li>
	                	<p class="jtxqlist"><a href="/professor-view-<?php echo $this->_var['rel']['article_id']; ?>.html"><span>【<?php echo $this->_var['rel']['cate_name']; ?>】</span><?php echo $this->_var['rel']['title']; ?></a></p>
	                    <p class="jtxqlbtada"><?php echo local_date("Y-m-d H:i",$this->_var['rel']['add_time']); ?></p>
	                </li>
            	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            	<!-- <li>
                	<p class="jtxqlist"><a href="javascript:;"><span>【内科】</span>10个月的小母狗已经来例假两月了！</a></p>
                    <p class="jtxqlbtada">2016-12-24&nbsp;&nbsp;&nbsp;&nbsp;15:36</p>
                </li>
                <li>
                	<p class="jtxqlist"><a href="javascript:;"><span>【内科】</span>金毛脚掌肿了！</a></p>
                    <p class="jtxqlbtada">2016-12-24&nbsp;&nbsp;&nbsp;&nbsp;15:36</p>
                </li>
                <li>
                	<p class="jtxqlist"><a href="javascript:;"><span>【综合科】</span>狗狗突然喘不过气就像人哮喘病... </a></p>
                    <p class="jtxqlbtada">2016-12-24&nbsp;&nbsp;&nbsp;&nbsp;15:36</p>
                </li>
                <li>
                	<p class="jtxqlist"><a href="javascript:;"><span>【内科】</span>发情期交配后流血会怀孕吗？</a></p>
                    <p class="jtxqlbtada">2016-12-24&nbsp;&nbsp;&nbsp;&nbsp;15:36</p>
                </li>
                <li>
                	<p class="jtxqlist"><a href="javascript:;"><span>【内科】</span>狗狗抽搐吐白沫乱走！</a></p>
                    <p class="jtxqlbtada">2016-12-24&nbsp;&nbsp;&nbsp;&nbsp;15:36</p>
                </li>
                <li>
                	<p class="jtxqlist"><a href="javascript:;"><span>【综合科】</span>我家狗狗怀孕50天了，可以驱虫吗... </a></p>
                    <p class="jtxqlbtada">2016-12-24&nbsp;&nbsp;&nbsp;&nbsp;15:36</p>
                </li> -->
            </ul>
            <a href="/professor-online-<?php echo $this->_var['article']['cate_id']; ?>.html" class="jtxqmore">查看更多热门问题...</a>
        </div>
</div>


<!-- content/E -->
<?php echo $this->fetch('footer-new.html'); ?>
<!--底部/E-->
<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/static/pc/js/public.js"></script> 
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
