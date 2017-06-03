<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
  <ul class="subnav">
    <li><span>管理</span></li>
      <li><a class="btn1" href="index.php?app=feedback&amp;act=export">导出</a></li>
  </ul>
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_var['feedback']): ?>
    <tr class="tatr1">
        <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td><span ectype="order_by">反馈角色</span></td>
      <td><span ectype="order_by">来源地址</span></td>
      <td><span ectype="order_by">联系方式</span></td>
      <td><span ectype="order_by">文字描述</span></td>
      <td><span ectype="order_by">反馈时间</span></td>
      <td><span ectype="order_by">图片详细</span></td>
      <td class="handler">操作</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_var['feedback']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['val']):
?>
    <tr class="tatr2">
        <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['val']['id']; ?>"/></td>
      <td><?php echo $this->_var['val']['user_name']; ?> | <?php echo $this->_var['val']['nickname']; ?></td>
      <td><?php echo $this->_var['val']['url']; ?></td>
      <td><?php echo $this->_var['val']['mobile']; ?></td>
      <td title="<?php echo $this->_var['val']['description']; ?>"><?php echo sub_str($this->_var['val']['description'],50); ?></td>
      <td title="<?php echo $this->_var['val']['description']; ?>"><?php echo local_date("Y-m-d H:i:s",$this->_var['val']['add_time']); ?></td>
      <td>
      <?php $_from = $this->_var['val']['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
      <a target="_blank" href="<?php echo $this->_var['list']['img_url']; ?>"><img height="60" src="<?php echo $this->_var['list']['img_url']; ?>"></a>&nbsp;
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      </td>
      <td class="handler">
      	<a href="index.php?app=feedback&amp;act=info&amp;id=<?php echo $this->_var['val']['id']; ?>">详情</a>
        <a href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=feedback&amp;act=drop&amp;id=<?php echo $this->_var['val']['id']; ?>');">删除</a>
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="12">没有符合条件的记录</td>
    </tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  <?php if ($this->_var['feedback']): ?>
  <div id="dataFuncs">
    <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
      <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
          <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=feedback&act=drop" presubmit="confirm('您确定要删除它吗？');" />
      </div>
  </div>
  <div class="clear"></div>
  <?php endif; ?>

</div>
<?php echo $this->fetch('footer.html'); ?>