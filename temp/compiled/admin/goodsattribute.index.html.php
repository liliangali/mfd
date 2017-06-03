<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>属性列表</span></li>
        <li><a class="btn1" href="index.php?app=goodsattribute&amp;act=add&type_id=<?php echo $this->_var['type_id']; ?>">新增</a></li>
    </ul>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['list']): ?>
        <tr class="tatr1">
            <td align="left">属性名称</td>
            <!-- <td align="left">别名</td> -->
            <td align="left">属性类型</span></td>
            <td class="table-center">排序</td>
            <td class="table-center">属性值</td>
            <td class="handler">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'attr');if (count($_from)):
    foreach ($_from AS $this->_var['attr']):
?>
        <tr class="tatr2">
            <td align="left"><?php echo htmlspecialchars($this->_var['attr']['attr_name']); ?></td>
            <!-- <td align="left"><?php echo htmlspecialchars($this->_var['attr']['alias']); ?></td> -->
            <td align="left"><?php echo htmlspecialchars($this->_var['typelists'][$this->_var['attr']['type_id']]); ?></td>
            <td align="left" class="table-center"><?php echo $this->_var['attr']['sort_order']; ?></td>
            <td class="table-center"><?php echo $this->_var['attr']['attr_values']; ?></td>
            <td class="handler">
            <a href="index.php?app=goodsattribute&amp;act=edit&amp;attr_id=<?php echo $this->_var['attr']['attr_id']; ?>">编辑</a>  |  
             <a name="drop" href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=goodsattribute&amp;act=drop&amp;attr_id=<?php echo $this->_var['attr']['attr_id']; ?>');">删除</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['attr_lists']): ?>
    <div id="dataFuncs">
    <!-- 
        <div id="batchAction" class="left paddingT15">
            <input class="formbtn batchButton" type="button" value="删除" name="attr_id" uri="index.php?app=parttype&act=drop&type_id=<?php echo $this->_var['type_id']; ?>" presubmit="confirm('您确定要删除它吗？');" />
        </div>
    -->
        <div class="pageLinks">
            <?php echo $this->fetch('page.bottom.html'); ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="clear"></div>
</div>
<?php echo $this->fetch('footer.html'); ?>
