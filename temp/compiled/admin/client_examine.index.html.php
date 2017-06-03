<?php echo $this->fetch('header.html'); ?>
<style>
.xztab{border-top:1px solid #d7d7d7; border-left:1px solid #d7d7d7;overflow:hidden; text-align:center;}
.xztab tr th{border-bottom:1px solid #d7d7d7;border-right:1px solid #d7d7d7; background:#e5e5e5;line-height:24px; font-weight:normal;}
.xztab tr td{border-bottom:1px solid #d7d7d7;border-right:1px solid #d7d7d7;line-height:50px;}
.xztab tr td span{color:#fe0102;}
.xztab tr td a img{width:60px;height:60px;display:block;float:left;margin:10px 5px;}
.luck-title{text-align:center}
.mfd-luck span{font-weight:bold;}
.luck-con{padding: 40px;}
.y_l_atr{float:left;width:50%;height:20px;line-height: 20px;margin-bottom: 10px;}
.y_l_b{float:left;width: 100%;margin-top: 10px;}
.y_l_b textarea{width: 330px;height:100px;}
.y_l_a{float:left;width: 100%;height:40px;margin-top: 10px;}
.y_l_a input{float:left;margin-left: 40px;}
#content{padding-left:0px;}
</style>
<div id="rightTop">
    <ul class="subnav">
        <li><span>账户调解审核</span></li>
    </ul>

</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="post">
            <div class="left">
                <input type="hidden" name="app" value="client_examine" />
                <input type="hidden" name="act" value="index" />
              	用户：<input type="text" name="name"  value="<?php echo htmlspecialchars($this->_var['name']); ?>" />
              	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" class="formbtn" value="查询" />
            </div>
        </form>
    </div>
    <!--<div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>-->
</div>
<div class="tdare">

	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="xztab">
      <tr>
        <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
        <th rowspan="2">用户</th>
        <th colspan="2">积分</th>
        <th colspan="2">用户等级</th>
        <th rowspan="2">调解原因</th>
        <th rowspan="2" width="350">凭证</th>
        <th rowspan="2">操作</th>
      </tr>
      <tr>
        <th></th>
        <th>当前</th>
        <th>调解</th>
        <th>当前</th>
        <th>调解</th>
      </tr>

     <?php $_from = $this->_var['accountlog']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
      <tr>
        <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['list']['id']; ?>"/></td>
        <td><?php echo $this->_var['list']['user_name']; ?></td>
        <td><?php echo $this->_var['list']['oldpoint']; ?></td>
        <td><?php if ($this->_var['list']['point_type'] == 1): ?><span style="color:green">+<?php echo $this->_var['list']['point']; ?></span><?php elseif ($this->_var['list']['point_type'] == 2): ?><span style="color:red;">-<?php echo $this->_var['list']['point']; ?></span><?php endif; ?></td>
        <td><?php echo $this->_var['list']['oldlv']; ?></td>
        <td><span><?php echo $this->_var['list']['newlv']; ?></span></td>
        <td><span title="<?php echo $this->_var['list']['brief']; ?>"><?php echo sub_str($this->_var['list']['brief'],10); ?></span></td>
        <td>
         <?php if ($this->_var['list']['images']): ?>
           <?php $_from = $this->_var['list']['images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['img']):
?>
          <a target="_blank" href="<?php echo $this->_var['img']['source_img']; ?>"><img width="60" height="60" src="<?php echo $this->_var['img']['source_img']; ?>"></a>
        	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        	  <?php endif; ?>
        </td>


        <td>
        <?php if ($this->_var['list']['status'] == 1): ?>
         <a href="javascript:;" value="<?php echo $this->_var['list']['id']; ?>" data-sn="<?php echo $this->_var['list']['id']; ?>" class="tg">通过审核</a>
            <?php elseif ($this->_var['list']['status'] == 0): ?>
        <a href="javascript:;" value="<?php echo $this->_var['list']['id']; ?>" data-sn="<?php echo $this->_var['list']['id']; ?>" class="comment">审核</a>
           <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      
    </table>
    <?php if ($this->_var['accountlog']): ?>
    <div id="dataFuncs">
    		<div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
    		<div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
          	<input class="formbtn batchButton" type="button" value="通过" name="id" uri="index.php?app=client_examine&act=through" presubmit="confirm('你确定要通过吗?');" />
      	    </div>
    		
    </div>
    <div class="clear"></div>
     <?php else: ?>
			<div class="tdare" style="padding-left:0px;margin-top:30px;">当前用户目前没变更记录</div>
    <?php endif; ?>
</div>
<script type="text/javascript">

$('.comment').click(function(){


    var id=$(this).attr("value");

	$.get("/admin/index.php?app=client_examine&act=getaccount",{id:id}, function(res){
		var res = eval("("+res+")");
		if(res.done == true){
			luck.open({
				width:'815px',
				height:'auot',
				title:'调解审核',
				addclass:'mfd-luck',
				content:res.retval
			})

			$(".tijia").click(function(){
				var id = $('#ac_id').val();
				if(id ==''){
					alert('要审核的账户调整不存在',1500);
					return;
				}
				var status = 1;
	    	  	$.post("/admin/index.php?app=client_examine&act=edit",{id:id,status:status},function(res){
	     		 var ress = eval('('+res+')');
	     		  if(ress.done == true)
	    		  {
     				alert('审核成功',1500);
					luck.close()
					window.location.reload()
	    	      }
	     		  else
	    		  {
	     			 alert(ress.msg);
	    		  }
	     	  	})
			})

			$(".congzi").click(function(){
				var id = $('#ac_id').val();
				var fail_reason = $('#content').val();

				var status =2; //驳回的审核 状态

				if(id ==''){
					alert('要审核的账户调整不存在',1500);
					return;
				}
				if(fail_reason ==''){
					alert('驳回原因不能为空',1500);
					return;
				}

			  	$.post("/admin/index.php?app=client_examine&act=reject",{id:id,status:status,fail_reason:fail_reason},function(res){
		     		 var ress = eval('('+res+')');
		     		  if(ress.done == true)
		    		  {
	     				alert('驳回成功',1500);
						luck.close()
						window.location.reload()
		    	      }
		     		  else
		    		  {
		     			 alert(ress.msg);
		    		  }
		     	})

			})

		}else{
			document.location.reload();
		}
	})

})

$('.tg').click(function(){
	var id=$(this).attr("value");

	$.get("/admin/index.php?app=client_examine&act=gettginfo",{id:id}, function(res){
		var res = eval("("+res+")");
		if(res.done == true){
			luck.open({
				width:'815px',
				height:'auot',
				title:'调解审核',
				addclass:'mfd-luck',
				content:res.retval
			})
		}else{
			document.location.reload();
		}
	})



})

</script>
<?php echo $this->fetch('footer.html'); ?>
