<?php echo $this->fetch('header-new.html'); ?>
<script language="javascript" type="text/javascript" src="<?php echo $this->lib_base . "/" . 'Ueditor/ueditor.parse.js'; ?>"></script> 
<!-- 支持html5 video -->
<!-- <link type="text/css" rel="stylesheet" href="<?php echo $this->lib_base . "/" . 'Ueditor/third-party/video-js/video-js.min.css'; ?>"/>

<script language="javascript" type="text/javascript" src="<?php echo $this->lib_base . "/" . 'Ueditor/third-party/video-js/video.js'; ?>"></script>  -->
<!-- <script src="http://html5media.googlecode.com/svn/trunk/src/html5media.min.js"></script> -->
<style>
/*帮助中心开始*/
body {background:#f5f5f5 !important;}
.bzzx_body_box {width:1200px; margin:0 auto; overflow:hidden; padding-bottom:100px;}
.bzzx_left {width:180px; float:left; overflow:hidden; background:#fff; padding:20px;}

.bzzx_right {width:900px; float:right; overflow:hidden; background:#fff; padding:20px 30px;}
.wzbt {font-size:28px; font-weight: normal; height: 36px; line-height: 36px;  margin-bottom: 50px; text-align: center;}

.mbx {color: #616161; font-size: 12px; line-height:50px; height:50px; overflow:hidden;}
.mbx a {color:#757575;}
.jgf {color: #b0b0b0; margin: 0 0.5em;}

.menu_left {}
.zclb {width:180px;  line-height:28px; padding-bottom:20px;}
.zclb h4 {height:50px; line-height:50px; padding-left:15px; font-weight:normal;font-size:16px; cursor:pointer; color:#111;}
.zclb .div_1 .on {color:#e66800;}
.zclb .div_1 a {display:block; height:28px; line-height:28px; padding-left:15px; font-size:13px; color:#717171;}
.zclb .div_1 a:hover {color:#000;}

@media screen and (max-width:1024px) {
.bzzx_body_box {width:950px;}
.bzzx_right {width:650px;}
}

@media screen and (max-width:768px) {
.bzzx_body_box {width:700px;}
.bzzx_right {width:400px;}
}
/*帮助中心结束*/
</style>
<div class="bzzx_body_box">

<div class="mbx">
  <a href="/">首页</a>
  <span class="jgf">></span>
  <a href="javascript:void(0)">帮助中心</a> 
  <span class="jgf">></span>
  <span id="secondtitle">新手指南</span>
  <span class="jgf">></span>
  <span id="thirdtitle">注册新用户</span>
</div>

  <div class="bzzx_left">
    <div class="menu_left menu_wrap_1">
    <?php if (! $this->_var['article_id']): ?>
	    <?php $_from = $this->_var['newcate']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'cate');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['cate']):
        $this->_foreach['foo']['iteration']++;
?>
	      <div class="zclb">
		      	<h4><?php echo $this->_var['cate']['data']['cate_name']; ?></h4>
		        <div class="div_1" <?php if ($this->_var['cate']['data']['cate_id'] == $this->_var['cate_id']): ?>style="display:block;"<?php endif; ?>>
		        <?php $_from = $this->_var['cate']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'articles');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['articles']):
        $this->_foreach['loop']['iteration']++;
?>
		         	<a style="cursor:pointer" value="<?php echo $this->_var['articles']['article_id']; ?>" <?php if ($this->_var['articles']['article_id'] == $this->_var['article']['article_id']): ?>class="on"<?php endif; ?>><?php echo $this->_var['articles']['title']; ?></a>
		        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		        </div>
	    	
	      </div>
	     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
     <?php else: ?>
          <?php $_from = $this->_var['newcate']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'cate');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['cate']):
        $this->_foreach['foo']['iteration']++;
?>
	     	 <div class="zclb">
		      	<h4><?php echo $this->_var['cate']['data']['cate_name']; ?></h4>
		        <div class="div_1" <?php if ($this->_var['cate']['data']['cate_id'] == $this->_var['cate_id']): ?>style="display:block;"<?php endif; ?>>
		        <?php $_from = $this->_var['cate']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'articles');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['articles']):
        $this->_foreach['loop']['iteration']++;
?>
		         	<a style="cursor:pointer" value="<?php echo $this->_var['articles']['article_id']; ?>" <?php if ($this->_var['articles']['article_id'] == $this->_var['article_id']): ?>class="on"<?php endif; ?>><?php echo $this->_var['articles']['title']; ?></a>
		        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		        </div>
	    	
	      </div>
	     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php endif; ?>  
    </div>
    
    
    

    
  </div>
  
  <div class="bzzx_right">
    <h1 class="wzbt"><?php echo ($this->_var['article']['title'] == '') ? '' : $this->_var['article']['title']; ?></h1>
  		<div class="article_content"><?php echo ($this->_var['article']['content'] == '') ? '' : $this->_var['article']['content']; ?></div>
  </div>
</div>

<!--底部开始-->
<link href="/public/static/pc/css/slx_dlzc.css" type="text/css" rel="stylesheet">
<?php echo $this->fetch('footer-new-1.html'); ?>
<!--底部结束-->

<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/static/pc/js/public.js"></script> 

<SCRIPT>
$(function(){
	var secondtitle=$('.on').parent().prev().html();
	if(secondtitle){
		$("#secondtitle").html(secondtitle);
	}else{
		$("#secondtitle").html();
	}
	var thirdtitle=$('.on').html();
	if(thirdtitle){
		$("#thirdtitle").html(thirdtitle);
	}else{
		$("#thirdtitle").html();
	}
})

	/* 左侧标题点击 */
	$(".menu_wrap_1").delegate("a","click",function(){
		//var _thisMeneDetail = $(this).parents(".menu_detail"),	
			//,_menuList = $(this).parents("menu_detail").parents(".menu_list");
		$(".menu_wrap_1 .on").removeClass("on");
		$(this).addClass("on");
		
		var id=$(this).attr('value');
		var title=$(this).parent().prev().html();
		$("#secondtitle").html(title);
		$('#thirdtitle').html($(this).html());
		$.post('help-ajax_article.html',{id:id,title:title},function(res){
			var res=eval("("+res+")");
			$(document).attr("title",res.retval.title);//修改title值
	        if(res.retval.article){	        	
	       		$(".wzbt").html(res.retval.article.title);
	 	       $(".article_content").html(res.retval.article.content);
	       }else{
	       	 $(".wzbt").html("");
		  	       $(".article_content").html("");
	       }
	
		});
	/* 	if(_thisMeneDetail.is( ":visible")){
			_menuList.removeClass("cur");
			_thisMeneDetail.slideUp();
			return false;
		}else{
			$(".menu_detail",".menu_list").slideUp();
			_thisMeneDetail.slideDown();
			return false;
		} */
	});
	$("h4").click(function(){
		$(document).attr("title",'帮助中心 - '+$(this).html()+' - mfd.麦富迪：酷享本色，特立独行！');//修改title值
	})

</SCRIPT>


</body>
</html>
