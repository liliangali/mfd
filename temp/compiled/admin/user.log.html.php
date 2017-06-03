<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
  <ul class="subnav">
    <li><a class="btn1" href="index.php?app=user">管理</a></li>
<!--     <li><a class="btn1" href="index.php?app=user&amp;act=add">新增</a></li> -->

    <li><span>日志查询</span></li>
    <li><span><a class="btn1" href="index.php?app=user&amp;act=user_log">登录记录</a></span></li>
  </ul>
</div>


<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_var['userlog']): ?>
    <tr class="tatr1">
      <td width="70">操作者名称</td>
      <td width="70">操作者ip</td>
      <td width="70">修改用户id</td>
      <td width="70">操作时间</td>
      <td width="670">操作详情</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_var['userlog']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'log');if (count($_from)):
    foreach ($_from AS $this->_var['log']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_var['log']['modified_user_name']; ?></td>
      <td><?php echo $this->_var['log']['modified_last_ip']; ?></td>
      <td><?php echo $this->_var['log']['user_id']; ?></td>
      <td><?php echo $this->_var['log']['add_time']; ?></td>
      <td><?php echo $this->_var['log']['mark']; ?></td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="10">没有符合条件的记录</td>
    </tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  <?php if ($this->_var['users']): ?>
  <div id="dataFuncs">
    <!--<div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=user&act=drop" presubmit="confirm('你确定要删除它吗？该操作不会删除ucenter及其他整合应用中的用户');" />
    </div>-->
    <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
    <div class="clear"></div>
  </div>
  <?php endif; ?>
</div>

<script type="text/javascript">
//日期
$('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
$('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});
</script>

<?php echo $this->fetch('footer.html'); ?>