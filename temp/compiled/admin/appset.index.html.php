<?php echo $this->fetch('header.html'); ?>

<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=appset&act=addess">添加</a></li>
    </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post">
       <div class="yx_qh">
       	<dl>
       	 <dt name="pt_name">平台：</dt>
       	 <dd class="on" data-pt="all">全部</dd>
       	 <dd data-pt="android">Android</dd>
       	 <dd data-pt="ios">IOS</dd>
       	</dl>
       	
       <!--	<dl>
       	 <dt name="yy_name">应用：</dt>
       	 <dd class="on" data-yy="all">全部</dd>
       	 <dd data-yy="cotte">麦富迪APP</dd>
       	 <dd data-yy="figure">量体师APP</dd>
       	</dl>-->
       	
          <input type="hidden" name="pt" value="all" />
          <input type="hidden" name="yy" value="all" />
      </div>
    </form>
  </div>
</div>
<div class="tdare info">
    <table width="100%" cellspacing="0" class="container">
        <tr class="tatr1">
            <td width="15%">版本号</td>
            <td align="left">大小</td>
            <td align="left">发布平台</td>
            <td align="left">发布时间</td>
            <td width="10%">应用</td>
            <td width="10%">平台</td>
            <td width="200">操作</td>
        </tr>
        <?php $_from = $this->_var['versions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'ver');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['ver']):
?>
        <tr class="tatr2">
            <td><?php echo $this->_var['ver']['version']; ?></td>
            <?php if (! empty ( $this->_var['ver']['large'] )): ?>
            <td align="left"><?php echo $this->_var['ver']['large']; ?>MB</td>
            <?php else: ?>
            <td align="left">0MB</td>
             <?php endif; ?>
             <td align="left"><a target="_blank" style="color:blue;text-decoration:underline; font-size:13px;" href="<?php echo $this->_var['ver']['link']; ?>"><?php echo $this->_var['ver']['link']; ?></a></td>
            <td align="left"><?php echo $this->_var['ver']['add_time']; ?></td>
            <td><?php echo $this->_var['ver']['app']; ?></td>
            <td><?php echo $this->_var['ver']['type']; ?></td>
            <td class="handler">
             <a href="index.php?app=appset&amp;act=edit&amp;id=<?php echo $this->_var['ver']['id']; ?>">编辑</a>  
        	|<a name="drop" href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=appset&amp;act=drop&amp;id=<?php echo $this->_var['ver']['id']; ?>');">删除</a> 
            </td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
</div>
<?php echo $this->fetch('footer.html'); ?>
<script>

$(".yx_qh dd").click(function(){
	$(this).addClass('on').siblings().removeClass('on')
    var val_pt=$(".yx_qh dl").eq(0).find('.on').attr("data-pt")
    var val_yy=$(".yx_qh dl").eq(1).find('.on').attr("data-yy")
     $.ajax({
		 type:"post",
		 url:"index.php?app=appset&act=ajax_index",
		 data:{
			   val_pt:val_pt,
			   val_yy:val_yy,
  			 },	 
			 dataType:"json",
              success:function(res){
               //var res=eval("("+res+")");
               var html = '';
               var len=res.retval.length;
                if(res.done == true){
            	   for(var i=0;i<len;i++){
            		  
            	    html +='<tr class="tatr2"><td>'+res.retval[i].version+'</td>';
            	   if(!res.retval[i].large)
            		   {
            		   html+='<td align="left">0MB</td>';
            		   }else{
            			   html+='<td align="left">'+res.retval[i].large+'MB</td>';  
            		   }
            	     html+='<td align="left"><a target="_blank" style="color:blue;text-decoration:underline; font-size:13px;" href="'+res.retval[i].link+'">'+res.retval[i].link+'</a></td><td align="left">'+res.retval[i].add_time+'</td><td>'+res.retval[i].app+'</td><td>'+res.retval[i].type+'</td><td class="handler"><a href=index.php?app=appset&amp;act=edit&amp;id='+res.retval[i].id+'>编辑</a>|<a name="drop" href=javascript:drop_confirm(您确定要删除它吗？, index.php?app=appset&amp;act=drop&amp;id='+res.retval[i].id+');>删除</a></td></tr>'; 
            	   }
            	   $('.container .tatr2').remove();
            	   $('.container').append(html);
               }else{
            	   return;
               }  
            }
		
	});  
}); 





</script>
