<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <p style="display:none;"><strong>配送方式管理</strong></p>
    <ul class="subnav">
    	<li><span><a href="index.php?app=shipping&act=add">添加配送方式</a></span></li>
  </ul>
</div>
<div class="tdare info">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['shippings']): ?>
        <tr class="tatr1">
            <td class="firstCell" width="15%">配送方式名称</td>
            <!-- <td>配送方式描述</td> -->
            <td width="5%">启用</td>
            <td width="10%">编码</td>
            <td width="150">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['shippings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'shipping');if (count($_from)):
    foreach ($_from AS $this->_var['shipping']):
?>
        <tr class="tatr2">
            <td class="firstCell"><?php echo $this->_var['shipping']['shipping_name']; ?></td>
          <!--   <td><span class="padding1"><?php echo $this->_var['shipping']['shipping_desc']; ?></span></td> -->
            <td><?php if ($this->_var['shipping']['enabled']): ?>是<?php else: ?>否<?php endif; ?></td>
            <td><span class="padding1"><?php echo $this->_var['shipping']['code']; ?></span></td>
            <td>
 
          <a href="index.php?app=shipping&amp;act=edit&shipping_id=<?php echo $this->_var['shipping']['shipping_id']; ?>">编辑</a> 
          <a href="javascript:;" onclick="if(confirm('确认删除该配送方式吗？')) location.href='index.php?app=shipping&amp;act=drop&shipping_id=<?php echo $this->_var['shipping']['shipping_id']; ?>'" class="delete">移除</a>
          <?php if (! $this->_var['shipping']['is_default']): ?><a href="index.php?app=shipping&amp;act=setdefault&amp;shipping_id=<?php echo $this->_var['shipping']['shipping_id']; ?>">设为默认</a><?php else: ?>默认物流<?php endif; ?>
             
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="5">尚未添加任何配送方式</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
</div>

<script>
/*
$(document).ready(function(){
	$('*[ectype="dialog"]').click(function(){
		var id = $(this).attr('dialog_id');
		var title = $(this).attr('dialog_title') ? $(this).attr('dialog_title') : '';
		var url = $(this).attr('uri');
		var width = $(this).attr('dialog_width');
		ajax_form(id, title, url, width);
		return false;
		}); 
})*/
</script>
<?php echo $this->fetch('footer.html'); ?>
