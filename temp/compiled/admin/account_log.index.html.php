<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
     <li><a class="btn1" href="index.php?app=user">返回用户列表</a></li>
   </ul>
</div>

<div class="info">

    <div class="order_form">
        <h1>基本信息</h1>
        <ul>
        	<li style="width:30%">用户名：<?php echo $this->_var['user']['user_name']; ?></li>
        	<li>当前等级：<?php echo $this->_var['user']['lv_name_info']; ?></li>
        	<li>积分：<?php echo $this->_var['user']['point']; ?></li>
        </ul>
         <div class="clear"></div>
   </div> 
		<div class="order_form" style="border:0px;">
		<h1 style="float:left;">Ta的变动详细</h1>
		<div align="center" style="float:right;margin-right:50px;border:1px solid #ddd;width:100px;height:30px;line-height:30px;"><a href="index.php?app=account_log&amp;act=add&amp;id=<?php echo $this->_var['user']['user_id']; ?>">+调解账户</a></div>
		<?php if ($this->_var['accountlog']): ?>
		<div class="tdare" style="padding-left:0px;margin-top:30px;">
 	    <table width="100%" cellspacing="0" class="dataTable">
 	    <tr class="tatr1">
	 	    <td>账户变更时间</td>
	 	    <td>账户变动原因</td>
		  	<td>积分变动</td>
		  	<td>等级变化</td>
		  	<td>附件图</td>
		  	<td>审核状态</td>
 	    </tr>
 	    
 	    <?php $_from = $this->_var['accountlog']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
 	    <tr class="tatr2">
 	    	<td><?php echo local_date("Y-m-d H:i:s",$this->_var['list']['add_time']); ?></td>
			<td><span title="<?php echo $this->_var['list']['brief']; ?>"><?php echo sub_str($this->_var['list']['brief'],60); ?></span></td>
			<td><?php if ($this->_var['list']['point_type'] == 1): ?><span style="color:green">+<?php echo $this->_var['list']['point']; ?></span><?php elseif ($this->_var['list']['point_type'] == 2): ?><span style="color:red;">-<?php echo $this->_var['list']['point']; ?></span><?php endif; ?></td>
			<td><?php echo $this->_var['list']['lv_id']; ?></td>
			<td>
			<?php if ($this->_var['list']['imgs']): ?>
			<?php $_from = $this->_var['list']['imgs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['img']):
?>
			<a target="_blank" href="<?php echo $this->_var['img']['source_img']; ?>"><img width="60" height="60" src="<?php echo $this->_var['img']['source_img']; ?>" /></a>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			<?php endif; ?>
			</td>
			<td><?php if ($this->_var['list']['status'] == 0): ?>未审核<?php elseif ($this->_var['list']['status'] == 1): ?>审核通过<?php elseif ($this->_var['list']['status'] == 2): ?>已驳回<?php endif; ?></td>
 	    </tr>
 	    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </table>
          <div id="dataFuncs">
    		<div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
    		<div class="clear"></div>
  		  </div>
  		
        </div>
        <div class="clear"></div>
        <?php else: ?>
			<div class="tdare" style="padding-left:0px;margin-top:30px;">当前用户目前没变更记录</div>
        <?php endif; ?>  
		</div>
</div>

<?php echo $this->fetch('footer.html'); ?>