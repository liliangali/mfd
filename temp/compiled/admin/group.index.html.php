<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
  <ul class="subnav">
    <li><span>管理</span></li>
  </ul>
</div>


<div class="tdare">
  <form method=get>
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_var['group_list']): ?>
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="200">职称</td>
      <td width="140"><span ectype="order_by" fieldname="email">所属小组</span></td>
      <td width="210">操作</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_var['group_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'admin');if (count($_from)):
    foreach ($_from AS $this->_var['admin']):
?>
    <?php $_from = $this->_var['admin']['son']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key2', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key2'] => $this->_var['item']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['admin']['user_id']; ?>" /></td>
      <td><?php echo htmlspecialchars($this->_var['item']); ?></td>
      <td><?php echo htmlspecialchars($this->_var['admin']['name']); ?></td>


      <td>
      <?php if ($this->_var['admin']['privs'] == all): ?>系统管理员
      </td>
      <?php else: ?>
      <span>
       <!--<a href="index.php?app=admin&amp;act=sub&amp;id=<?php echo $this->_var['admin']['user_id']; ?>">信息维护</a>-->
        <a href="index.php?app=admin&amp;act=edit&amp;id=<?php echo $this->_var['key2']; ?>">编辑权限</a> |
         <a href="index.php?app=admin&amp;act=groupindex&amp;id=<?php echo $this->_var['key2']; ?>">管理员列表</a>
        <!--<a href="javascript:drop_confirm('你确定要删除它吗？该操作不会删除ucenter及其他整合应用中的用户', 'index.php?app=admin&amp;act=drop&amp;id=<?php echo $this->_var['admin']['user_id']; ?>');">删除管理</a>-->
      </span>
      </td>
      <?php endif; ?>
    </tr>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="10">没有符合条件的记录</td>
    </tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  </form>
  <?php if ($this->_var['admins']): ?>
  <div id="dataFuncs">
    <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
    <div class="clear"></div>
  </div>
  <?php endif; ?>
</div>
<?php echo $this->fetch('footer.html'); ?> 