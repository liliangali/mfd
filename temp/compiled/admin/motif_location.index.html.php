<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=motif&amp;act=index">栏目列表</a></li>
        <li><a class="btn1" href="index.php?app=motif&amp;act=add">添加栏目</a></li>
        <li><span>栏目位置列表</span></li>
        <li><a class="btn1" href="index.php?app=motif&amp;act=add_group">添加栏目位置</a></li>
    </ul>
</div>
<div class="mrightTop">
  <div class="fontl">

             <div class="left">
            发布平台:<select class="querySelect mySelect" name="sites"><option value="">全部</option><?php echo $this->html_options(array('options'=>$this->_var['sites'],'selected'=>$_GET['site'])); ?></select>
            </div>

  </div>
  <div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['locations']): ?>
        <tr class="tatr1">
            <td>位置ID</td>
            <td>位置名称</td>
            <td>位置标记</td>
            <td>所属站点</td>
            <td>添加时间</td>
            <td class="handler">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['locations']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['list']):
?>
        <tr class="tatr2">
            <td width="200"><?php echo $this->_var['list']['id']; ?></td>
            <td><?php echo $this->_var['list']['name']; ?></td>
            <td><?php echo $this->_var['list']['code']; ?></td>
            <td><?php echo $this->_var['list']['site']; ?></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['list']['add_time']); ?></td>
            <td class="handler">
                <a href="index.php?app=motif&amp;act=edit_group&amp;id=<?php echo $this->_var['list']['id']; ?>">编辑</a>
<!--                 <a href="javascript:;" onclick="if(confirm('确定删除吗？')) location.href='index.php?app=motif&amp;act=drop_group&amp;id=<?php echo $this->_var['list']['id']; ?>'">删除</a> -->
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
        window.location.href="index.php?app=motif&act=group_list&site="+site;
    }) 

</script>
<?php echo $this->fetch('footer.html'); ?>