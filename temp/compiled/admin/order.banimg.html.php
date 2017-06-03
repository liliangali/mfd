<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">

  <ul class="subnav">
    <li><span>个性化标签功能</span></li>
  </ul>
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_var['ordergoodslist']): ?>
    <tr class="tatr1">
      <td width="15%"><span ectype="order_by" fieldname="goods_name">狗狗名称</span></td>
      <td><span ectype="order_by" fieldname="is_sale">寄语</span></td>
	  <td><span ectype="order_by" fieldname="is_sale">生日</span></td>
	  <td><span ectype="order_by" fieldname="is_sale">标签图</span></td>
      <td>审核</td>
	  <td>编辑</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_var['ordergoodslist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
    <tr class="tatr2">
      <td><?php echo htmlspecialchars($this->_var['goods']['dog_name']); ?></td>
      <td><?php echo htmlspecialchars($this->_var['goods']['dog_desc']); ?></td>
      <td><?php echo htmlspecialchars($this->_var['goods']['dog_date']); ?></td>
	  <td><img src="<?php echo $this->_var['goods']['style']; ?>" /></td>
	  <td><?php if ($this->_var['goods']['status'] == '0'): ?>
	  未审核
	  <?php elseif ($this->_var['goods']['status'] == '1'): ?>
	  审核成功
	  <?php else: ?>
	  审核失败
	  <?php endif; ?>
	  </td>
     <td class="handler">
	 <a href="index.php?app=bannerimg&amp;act=view_img&amp;rec_id=<?php echo $this->_var['goods']['rec_id']; ?>">查看</a>
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data info">
      <td colspan="7">没有符合条件的记录</td>
    </tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  <?php if ($this->_var['ordergoodslist']): ?>
  <div id="dataFuncs">
    <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
   <?php endif; ?>
  </div>
  <div class="clear"></div>
</div>
<script>
     
</script>
<?php echo $this->fetch('footer.html'); ?>