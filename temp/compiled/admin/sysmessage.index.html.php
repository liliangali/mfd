<?php echo $this->fetch('header.html'); ?>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['message']): ?>
        <tr class="tatr1">
            <td align="left">节点</td>
            <td>内容</td>
            <td>时间</td>
            <td>已读状态</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['message']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'msg');if (count($_from)):
    foreach ($_from AS $this->_var['msg']):
?>
        <tr class="tatr2">
            <td><?php echo htmlspecialchars($this->_var['msg']['node']); ?></td>
            <td><?php echo $this->_var['msg']['content']; ?></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['msg']['dateline']); ?></td>
            <td><?php if ($this->_var['msg']['is_read'] == 0): ?>否<?php else: ?>是<?php endif; ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="4">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['message']): ?>
    <div id="dataFuncs">
        <div class="pageLinks">
            <?php echo $this->fetch('page.bottom.html'); ?>
        </div>
    </div>
    <div class="clear"></div>
    <?php endif; ?>
</div>
<?php echo $this->fetch('footer.html'); ?>
