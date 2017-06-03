<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'ajax_tree1.js'; ?>" charset="utf-8"></script>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=goodsspec&amp;act=add">新增</a></li>
    </ul>
</div>
<div class="info2">
    <table  class="distinction">
        <?php if ($this->_var['list']): ?>
        <thead>
        <tr class="tatr1">
            <td width="25%">规格名称</td>
           <!--  <td width="20%">规格备注</td> -->
            <td width="15%">类型</td>
          <!--   <td width="10%">显示方式</td> -->
            <td width="30%">操作</td>
        </tr>
        </thead>
        <?php endif; ?>

        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'type');if (count($_from)):
    foreach ($_from AS $this->_var['type']):
?>
        <tr class="tatr2">
            <td width="25%"><?php echo htmlspecialchars($this->_var['type']['spec_name']); ?></td>
            <?php if ($this->_var['type']['spec_type'] == 'image'): ?>
            <td width="15%">图片</td>
            <?php else: ?>
             <td width="15%">文字</td>
            <?php endif; ?>
            
            <?php if ($this->_var['type']['spec_show_type'] == 'flat'): ?>
           <!--  <td width="15%">平铺</td> -->
            <?php else: ?>
            <!--  <td width="15%">下拉框</td> -->
            <?php endif; ?>
            <td  width="20%">
            <span>
            <!-- <a href="index.php?app=addspec&amp;spec_id=<?php echo $this->_var['type']['spec_id']; ?>&type=<?php echo $this->_var['type']['spec_type']; ?>">规格值管理</a>| -->
            <a href="index.php?app=goodsspec&amp;act=edit&amp;id=<?php echo $this->_var['type']['spec_id']; ?>">编辑</a>|
            <a href="javascript:if(confirm('您确定要删除它吗？'))window.location = 'index.php?app=goodsspec&amp;act=drop&amp;id=<?php echo $this->_var['type']['spec_id']; ?>';">删除</a>
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