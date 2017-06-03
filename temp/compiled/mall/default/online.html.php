<?php echo $this->fetch('header-new.html'); ?>
<!--头部/E-->
<link href="/public/static/pc/css/gywm.css" rel="stylesheet">
<!-- content -->
<div class="w">
  <h1 class="cjwt">常见问题<span>(<?php echo $this->_var['articlecount']; ?>)</span></h1>
  <ul class="rmzx clearfix">
    <?php $_from = $this->_var['acategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'acategory');$this->_foreach['act'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['act']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['acategory']):
        $this->_foreach['act']['iteration']++;
?>
      <li id="ti_<?php echo $this->_var['k']; ?>" onClick="ceck_pic(<?php echo $this->_var['k']; ?>);" <?php if (! $this->_var['cate_id']): ?>
      <?php if (($this->_foreach['act']['iteration'] <= 1)): ?>class="now_hover"  
      <?php else: ?>class="old_hover" style="border-left:1px solid #d5d5d5;"
      <?php endif; ?>
      <?php else: ?>
      <?php if ($this->_var['cate_id'] == $this->_var['k']): ?>class="now_hover"  style="border-left:1px solid #d5d5d5;"
      <?php else: ?>class="old_hover" style="border-left:1px solid #d5d5d5;"
      <?php endif; ?>
      <?php endif; ?>><?php echo $this->_var['acategory']['name']; ?>&nbsp;&nbsp;<?php echo $this->_var['acategory']['count']; ?></li>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <!-- <li id="ti_1" class="now_hover" onClick="ceck_pic(1);" style="border-right:1px solid #d5d5d5;">内科 17</li>
    <li id="ti_2" class="old_hover" onClick="ceck_pic(2);">外科 8</li> -->
  </ul>
  <?php $_from = $this->_var['acategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'acategory');$this->_foreach['acategory'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['acategory']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['acategory']):
        $this->_foreach['acategory']['iteration']++;
?>
     <div id="tj_<?php echo $this->_var['k']; ?>" class="rwk_box" <?php if (! $this->_var['cate_id']): ?><?php if (! ($this->_foreach['acategory']['iteration'] <= 1)): ?>style='display:none'<?php endif; ?><?php else: ?><?php if ($this->_var['cate_id'] != $this->_var['k']): ?>style='display:none'<?php endif; ?><?php endif; ?>>
      <ul>
        <?php $_from = $this->_var['acategory']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('art_key', 'article');if (count($_from)):
    foreach ($_from AS $this->_var['art_key'] => $this->_var['article']):
?>
        <li>
          <p class="p1"><a href="/professor-view-<?php echo $this->_var['article']['article_id']; ?>.html" ><span>【<?php echo $this->_var['acategory']['name']; ?>】</span><?php echo $this->_var['article']['title']; ?></a></p>
          <p class="p2"><?php echo local_date("Y-m-d",$this->_var['article']['add_time']); ?><span><?php echo local_date("H:i",$this->_var['article']['add_time']); ?></span></p>
        </li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

      </ul> 
    </div>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <h1 class="cjwt">专家在线<span>(<?php echo $this->_var['kefucount']; ?>)</span></h1>
   <ul class="zjzxlist">
   <?php if ($this->_var['adminList']): ?>
   <?php $_from = $this->_var['adminList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'kefu');if (count($_from)):
    foreach ($_from AS $this->_var['kefu']):
?>
      <li>
       <p class="p1"><img src="<?php echo $this->_var['kefu']['face']; ?>" /></p>
       <div>
        <h1><?php echo $this->_var['kefu']['name']; ?><span><?php echo $this->_var['kefu']['nick']; ?></span><img src="/public/static/pc/images/rztb.png" /></h1>
        <p><?php echo $this->_var['kefu']['remark']; ?></p>
       </div>
       <p class="zxzx">
           <span style='float: left'>
               <?php if ($this->_var['kefu']['status'] == 1): ?>
               <span style="float: left;display:inline-block;line-height:45px;padding-left:10px;">在线</span>
               <?php else: ?>
               <span style="float: left;display:inline-block;line-height:45px;padding-left:10px;">忙碌</span>
               <?php endif; ?>
           </span>
           <?php if ($this->_var['user_id'] != ''): ?>
           <a href="javascript:;" data-href="<?php echo $this->_var['kefu']['url']; ?>"  class='sobot_chat' >在线咨询</a>
           <?php else: ?>
           <a href="/member-login.html">在线咨询</a>
           <?php endif; ?>
       </p>
     </li>
   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
   <?php endif; ?>
   </ul>
</div>
<!-- content/E -->
<?php echo $this->fetch('footer-new.html'); ?>
<!--底部/E-->
<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/static/pc/js/public.js"></script>
 <script>
    var $iframe;
     $(".fixed_box").on("click", function() {
        if ($iframe) {
            $iframe.fadeIn();
        }
        $(".fixed_box").hide()
    });
    $(window).on("message", function(evt) {
        var e = evt.originalEvent;
        var data = e.data;
        // alert(data);
        if (data === 'zhichiClose') {
            $iframe.remove();
            $iframe = null;
            $(".fixed_box").hide()
        } else if (data == 'zhichiMin') {
            $iframe.fadeOut();
            $(".fixed_box").show()
        }
    });

    $(document.body).delegate("a", 'click', function(evt) {
        var $elm = $(evt.currentTarget);
        // var height=$(document).height()-300
        var height=400
        if (!$iframe) {
            $iframe = $("<iframe></iframe>");
            $iframe.css({
                'width': 400,
                'height': height,
                "position": "fixed",
                "bottom": 0,
                'right': 20,
                'border-style':'none',
            })
            $iframe.attr("src", $elm.attr("data-href"));
            $(document.body).append($iframe);
        }
        // $iframe.show();
        $iframe.attr("src", $elm.attr("data-href"));
    });

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

<script>
//切换
function ceck_pic(ix)
{
    $('#ti_'+ix).attr('class','now_hover')
    $('#ti_'+ix).siblings().attr('class','old_hover')
    $('#tj_'+ix).show()
    $('#tj_'+ix).siblings('.rwk_box').hide()
}
</script>
</body>
</html>
