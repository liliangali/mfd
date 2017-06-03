<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <p style="display:none;"><strong>area_manage</strong></p>
    <ul class="subnav">
     <li><span>推送记录</span></li>
   <li><a class="btn1" href="index.php?app=dosysmsg&act=addSend">发送系统推送</a></li>
  </ul>
</div>
<div class="tdare info">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['list']): ?>
        <tr class="tatr1">
            <td width="10%">标题</td>
            <td width="10%">内容</td>
            <td width="10%">发送人数</td>
			<td width="10%">发放时间</td>
            <td width="10%">发送者</td>
			<td width="10%">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
        <tr class="tatr2">
            <td><?php echo $this->_var['item']['title']; ?></td>
            <td><?php echo $this->_var['item']['content']; ?></td>
            <td><?php echo $this->_var['item']['send_num']; ?></td>
            <td><?php echo $this->_var['item']['send_time']; ?></td>
            <td><?php echo $this->_var['item']['admin_name']; ?></td>
			<td><a href="index.php?app=dosysmsg&amp;act=sendInfo&amp;id=<?php echo $this->_var['item']['id']; ?>" > 详情</a></td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="5">无数据</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
</div>
  <?php if ($this->_var['list']): ?>
  <div id="dataFuncs">
    <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
    <div class="clear"></div>
  </div>
  <?php endif; ?>
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
