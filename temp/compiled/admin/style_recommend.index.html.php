<?php echo $this->fetch('header.html'); ?>
<script src="templates/js/jquery.imagePreview.1.0.js"></script>
<div id="rightTop">
    <ul class="subnav">
        <li><span>轮播图列表</span></li>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=add">添加轮播图</a></li>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=group_list">轮播图分组</a></li>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=add_group">添加轮播图分组</a></li>
    </ul>
</div>
<div class="mrightTop">
  <div class="fontl">

            <p>发往平台:  </p> <span <?php if (! $_GET['site']): ?>class='on'<?php endif; ?>" attr_val="0" style="cursor:pointer">全部</span>
             <?php $_from = $this->_var['sites']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'site');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['site']):
?>
                              <span <?php if ($_GET['site'] == $this->_var['k']): ?>class='on'<?php endif; ?> attr_val="<?php echo $this->_var['k']; ?>" style="cursor:pointer"><?php echo $this->_var['site']; ?></span>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            
  </div>
  <div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>
</div>
<div class="tdare">
	<table width="100%" cellspacing="0" class="dataTable">
		<?php if ($this->_var['items']): ?>
		<tr class="tatr1">
		    <td>ID</td>
			<td>轮播图名称</td>
			<td>图片</td>
			<td>轮播图tiltle</td>
            <td>链接</td>
            <td>平台</td>
            <td>分组</td>
            <td>排序</td>
            <td>显示</td>
            <td>修改时间</td>
			
			<td class="handler">操作</td>
		</tr>
		<?php endif; ?>
		<?php $_from = $this->_var['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
		<tr class="tatr2">
			<td><?php echo $this->_var['list']['id']; ?></td>
            <td><?php echo $this->_var['list']['name']; ?></td>
            <td><a class='preview' href="javascript:void(0)" attr_src="<?php echo $this->_var['list']['img']; ?>" attr_hight="400" attr_width="600" title="<?php echo $this->_var['list']['name']; ?>"><img src="<?php echo $this->_var['list']['img']; ?>" height="60" /></a></td>
            <td><?php echo $this->_var['list']['title']; ?></td>
            <td><?php echo $this->_var['list']['link_url']; ?></td>
            <td><?php echo $this->_var['list']['site']; ?></td>
            <td><?php echo $this->_var['list']['groups']; ?></td>
            <td><?php echo $this->_var['list']['sort_order']; ?></td>
            <?php if ($this->_var['list']['status'] == 0): ?><td>否</td><?php else: ?><td>是</td><?php endif; ?>
            <td><?php if ($this->_var['list']['edit_time']): ?><?php echo local_date("Y-m-d H:i:s",$this->_var['list']['edit_time']); ?><?php else: ?><?php echo local_date("Y-m-d H:i:s",$this->_var['list']['add_time']); ?><?php endif; ?></td>
			<td class="handler" >
				<a href="index.php?app=shuffling&amp;act=edit&amp;id=<?php echo $this->_var['list']['id']; ?>">编辑</a>|
				<a href="javascript:;" onclick="if(confirm('确定删除吗？')) location.href='index.php?app=shuffling&amp;act=drop&amp;id=<?php echo $this->_var['list']['id']; ?>'">删除</a>
			</td>
		</tr>
		<?php endforeach; else: ?>
		<tr class="no_data">
			<td colspan="6">没有符合条件的记录</td>
		</tr>
		<?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</table>
	<?php if ($this->_var['items']): ?>
	<div id="dataFuncs">
		<div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
		<div class="clear"></div>
	</div>
  <?php endif; ?>
</div>
<script type="text/javascript">
$(function(){
	$("a.preview").preview()   	
	$('.fontl span').click(function(){
	    var site=$(this).attr('attr_val');
	    var url="index.php?app=shuffling&act=index&site="+site;
	    window.location=url;
	})
})

</script>
<?php echo $this->fetch('footer.html'); ?>