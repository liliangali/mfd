<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'ajax_tree1.js'; ?>" charset="utf-8"></script>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=goods_type&amp;act=add">新增</a></li>
    </ul>
</div>
<div class="info2">
    <table  class="distinction">
        <?php if ($this->_var['list']): ?>
        <thead>
        <tr class="tatr1">
            <td width="50%">类型名称</td>
            <td class="handler">操作</td>
        </tr>
        </thead>
        <?php endif; ?>

        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'type');if (count($_from)):
    foreach ($_from AS $this->_var['type']):
?>
        <tr class="tatr2">
            <td width="50%">
            <?php echo htmlspecialchars($this->_var['type']['name']); ?>
            </td>
            <td class="handler">
            <span>
            <a href="index.php?app=goodsattribute&amp;type_id=<?php echo $this->_var['type']['type_id']; ?>">扩展属性</a>|
           <!--  <a href="index.php?app=goodsspec&amp;type_id=<?php echo $this->_var['type']['type_id']; ?>">规格</a>| -->
            <a href="index.php?app=goods_type&amp;act=edit&amp;id=<?php echo $this->_var['type']['type_id']; ?>">编辑</a>|
            <a href="javascript:if(confirm('您确定要删除它吗？'))window.location = 'index.php?app=goods_type&amp;act=drop&amp;id=<?php echo $this->_var['type']['type_id']; ?>';">删除</a>
            </span>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="5">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
</div>

<?php echo $this->fetch('footer.html'); ?>