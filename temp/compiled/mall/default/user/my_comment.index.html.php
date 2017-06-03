<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<div class="user_box">
<!--用户中心左菜单部分 START-->
<?php echo $this->fetch('member.menu.html'); ?>
<!--用户中心左菜单部分 END-->
    <div class="user_right user_rights fr">
		<h4>我的评论</h4>
        <ul class="plul">
        <li  class="lccurrent" ><a href="<?php echo $this->build_url(array('app'=>'my_comment','act'=>'index','arg0'=>'1','arg1'=>'all')); ?>" <?php if ($this->_var['status'] == 'all'): ?> class="plcur"<?php endif; ?>>全部</a></li>
        <li><span></span></li>
        <li  class="lccurrent" ><a href="<?php echo $this->build_url(array('app'=>'my_comment','act'=>'index','arg0'=>'1','arg1'=>'uncomment')); ?>" <?php if ($this->_var['status'] == 'uncomment'): ?> class="plcur" <?php endif; ?>>待评价商品</a></li>
        <li><span></span></li>
        <li  class="lccurrent" ><a href="<?php echo $this->build_url(array('app'=>'my_comment','act'=>'index','arg0'=>'1','arg1'=>'commented')); ?>" <?php if ($this->_var['status'] == 'commented'): ?> class="plcur" <?php endif; ?>>已评价商品</a></li>
            
          
        </ul>
        
      	<div class="recently recentlys">
            <table width="100%" frame="void" rules="none" cellspacing="0">
             <?php if ($this->_var['comment_list']): ?>
              <tr class="first firsts">
                <td align="left" style="padding-left:15px;">商品信息</td>
                <td>购买时间</td>
                <td>评论状态</td>
              </tr>
            
           <?php $_from = $this->_var['comment_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'content');$this->_foreach['comment'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['comment']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['content']):
        $this->_foreach['comment']['iteration']++;
?>
             
              <tr id="<?php echo $this->_var['content']['rec_id']; ?>">
              
                <td><img src="<?php echo $this->_var['content']['goods_image']; ?>" class="plcpci"><p style="float:left;line-height:25px;margin:20px 0 0 15px;display:inline;text-align:left;"><?php echo $this->_var['content']['goods_name']; ?> <br/>订单号:&nbsp;&nbsp;<?php echo $this->_var['content']['order_sn']; ?></p></td>
                
                <td><?php echo $this->_var['content']['add_time']; ?></td>
                <td>
                <?php if ($this->_var['content']['comment'] == 0): ?>
                <a href="javascript:;" value="<?php echo $this->_var['content']['rec_id']; ?>" data-type="<?php echo $this->_var['content']['type']; ?>" class="comment">发表评论</a>
                <?php else: ?>
                <a href="javascript:;" class="wdplckpl">查看评论</a>
                <?php endif; ?>
                  
                </td>
                 
              </tr>
              
               <tr class="hide infos">
                
                <td colspan="4" style="padding-left:0px;text-align:left;">
                <?php $_from = $this->_var['content']['pl_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'pinfo');$this->_foreach['pl'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['pl']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['pinfo']):
        $this->_foreach['pl']['iteration']++;
?>
                   <div class="rated">
                   		<div class="ypscore">
                        	<div class="fl">
                                <p class="fl">评分：</p>
                                <p class="branch_<?php echo $this->_var['pinfo']['star']; ?> fl"></p>
                            </div>
                        </div>
                         
                        <p class="ypscore">印象： 
                         <?php $_from = $this->_var['pinfo']['impression']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'im');$this->_foreach['im'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['im']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['im']):
        $this->_foreach['im']['iteration']++;
?>
                        <span><?php echo $this->_var['im']; ?></span>
                         <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </p>
                           
                        <p class="ypscore">评论：<?php echo $this->_var['pinfo']['content']; ?></p>
                   </div> 
                      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </td>
             
              </tr>
              
               
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			<?php else: ?>		
			 <tr>
			 <td>
                <div class="empty">
                    <div>暂无商品信息<br><p><a href="./" class="cc_btn s_btn">麦富迪首页</a></p></div>
                </div>
             </td>
			 </tr>
			<?php endif; ?>		
		 
               
            </table>
             <?php echo $this->fetch('member.page.bottom.html'); ?>
        </div>
     
    </div>
    
</div>

<?php echo $this->fetch('footer.html'); ?>
<form method="post">
<div id="window03" style="display:none;">
    <div class="branch">
    	<!--星级评论打分开始-->
        <div id="star">
            <span class="btxxh">评分：</span>
            <ul data-num="0">
                <li><a href="javascript:;">1</a></li>
                <li><a href="javascript:;">2</a></li>
                <li><a href="javascript:;">3</a></li>
                <li><a href="javascript:;">4</a></li>
                <li><a href="javascript:;">5</a></li>
            </ul>
        </div>
        <div class="impression">
            <span class="btxxh">印象：</span>
            <ul>
               <?php $_from = $this->_var['impress']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'im');$this->_foreach['im'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['im']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['im']):
        $this->_foreach['im']['iteration']++;
?>
                <li ><?php echo $this->_var['im']['impress_name']; ?></li>
               	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
        </div> 
        <!--星级评论打分结束-->
        <p class="xd"><textarea name="content" id="content" cols="" rows="" placeholder="快速写下你的评价，分享给大家吧！"></textarea></p> 
     
        <input type="button" value="评论" class="ltbut" />
    </div>
</div>
</form>




<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/global/jquery.swipe.js"></script> 
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/global/luck/pc/luck.js"></script>
<script src="/public/static/pc/js/usercenter.js"></script> 
<script>
cotteFn.amount10()
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
$(".wdplckpl").click(function(){
	var info=$(this).parents('tr').next('.infos');
	if(info.is(':visible')){
		info.hide();
	}else{
		info.show().siblings('.infos').hide();	
	}
});
</script>








