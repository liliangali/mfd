<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=index">轮播图列表</a></li>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=add">添加轮播图</a></li>
        <li><span>轮播图分组列表</span></li>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=add_group">添加轮播图分组</a></li>
    </ul>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['groups']): ?>
        <tr class="yx_tatr1">
            <td>分组ID</td>
            <td>分组名称</td>
            <td>所属站点</td>
            <td>标记</td>
            <td>添加时间</td>
            <td class="handler">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['groups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['list']):
?>
        <tr class="tatr2">
            <td width="200"><?php echo $this->_var['list']['id']; ?></td>
            <td><?php echo $this->_var['list']['name']; ?></td>
            <td><?php echo $this->_var['list']['site']; ?></td>
            <td><?php echo $this->_var['list']['code']; ?></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['list']['add_time']); ?></td>
            <td class="handler">
                <a href="index.php?app=shuffling&amp;act=edit_group&amp;id=<?php echo $this->_var['list']['id']; ?>">编辑</a>
<!--                 <a href="javascript:;" onclick="if(confirm('确定删除吗？')) location.href='index.php?app=shuffling&amp;act=drop_group&amp;id=<?php echo $this->_var['list']['id']; ?>'">删除</a> -->
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
<?php echo $this->fetch('footer.html'); ?>