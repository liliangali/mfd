<?php echo $this->fetch('header.html'); ?>
<script src="templates/js/jquery.imagePreview.1.0.js"></script>
<div id="rightTop">
    <ul class="subnav">
        <li><span>栏目列表</span></li>
        <li><a class="btn1" href="index.php?app=motif&amp;act=add">添加栏目</a></li>
        <li><a class="btn1" href="index.php?app=motif&amp;act=group_list">栏目位置列表</a></li>
        <li><a class="btn1" href="index.php?app=motif&amp;act=add_group">添加栏目位置</a></li>
    </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
          <form method="get">
             <div class="left">
            发布平台:<select class="querySelect mySelect" name="sites"><option value="">全部</option><?php echo $this->html_options(array('options'=>$this->_var['sites'],'selected'=>$_GET['site'])); ?></select>
            发布位置:<select class="querySelect mySelect" name="locations"><option value="">全部</option><?php echo $this->html_options(array('options'=>$this->_var['locations'],'selected'=>$_GET['location'])); ?></select>
            </div>
            <?php if ($this->_var['filtered']): ?>
            <a class="left formbtn1" href="index.php?app=motif">撤销检索</a>
            <?php endif; ?>
        </form>
  </div>
  <div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>
</div>
<div class="tdare">
	<table width="100%" cellspacing="0" class="dataTable">
		<?php if ($this->_var['items']): ?>
		<tr class="tatr1">
		    <td>栏目ID</td>
			<td>栏目标题</td>
            <td>发布平台</td>
            <td>发布位置</td>
            <td>栏目排序</td>
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
            <td><?php echo $this->_var['list']['title']; ?></td>
            <td><?php echo $this->_var['list']['site']; ?></td>
            <td><?php echo $this->_var['list']['location']; ?></td>
            <td><?php echo $this->_var['list']['sort_order']; ?></td>
            <?php if ($this->_var['list']['is_show'] == 0): ?><td>否</td><?php else: ?><td>是</td><?php endif; ?>
            <td><?php if ($this->_var['list']['edit_time']): ?><?php echo local_date("Y-m-d H:i:s",$this->_var['list']['edit_time']); ?><?php else: ?><?php echo local_date("Y-m-d H:i:s",$this->_var['list']['add_time']); ?><?php endif; ?></td>
			<td class="handler" >
				<a href="index.php?app=motif&amp;act=edit&amp;id=<?php echo $this->_var['list']['id']; ?>">编辑</a>|
				<a href="javascript:;" onclick="if(confirm('确定删除吗？')) location.href='index.php?app=motif&amp;act=drop&amp;id=<?php echo $this->_var['list']['id']; ?>'">删除</a>
<!-- 				|<?php if ($this->_var['list']['top']): ?><a style="color:red">已置顶</a><?php else: ?><a  href="javascript:;" onclick="if(confirm('确定置顶吗？')) location.href='index.php?app=motif&amp;act=local_top&amp;id=<?php echo $this->_var['list']['id']; ?>'">置顶</a><?php endif; ?> -->
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

    
	$('.mySelect').change(function() {
        var site=$("select[name='sites']").val();
        var location=$("select[name='locations']").val();
        window.location.href="index.php?app=motif&act=index&site="+site+"&location="+location;
    }) 

</script>
<?php echo $this->fetch('footer.html'); ?>