<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
  <ul class="subnav">
    <li><span>管理</span></li>
  </ul>
</div>


<div class="mrightTop">
  <div class="fontl">
    <form method="get">
       <div class="left">
          <input type="hidden" name="app" value="smsRegTmp" />
          <input type="hidden" name="act" value="index" />
          <select class="querySelect" name="field_name"><?php echo $this->html_options(array('options'=>$this->_var['query_fields'],'selected'=>$_GET['field_name'])); ?>
          </select>
          <input class="queryInput" type="text" name="field_value" value="<?php echo htmlspecialchars($_GET['field_value']); ?>" />
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <?php if ($this->_var['filtered']): ?>
      <a class="left formbtn1" href="index.php?app=smsRegTmp}">撤销检索</a>
      <?php endif; ?>
    </form>
  </div>
</div>


<div class="tdare">
  <form method=get>
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_var['code_list']): ?>
    <tr class="tatr1">
      <td width="140">手机号</td>
      <td width="100"><span ectype="order_by">code码</span></td>
      <td width="120"><span ectype="order_by" fieldname="category">大分类</span></td>
      <td width="120"><span ectype="order_by" fieldname="type">小分类</span></td>
      <td width="170"><span ectype="order_by" fieldname="add_time">添加时间</span></td>
      <td width="170"><span ectype="order_by" fieldname="fail_time">过期时间</span></td>
      <td width="210">备注</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_var['code_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'code');if (count($_from)):
    foreach ($_from AS $this->_var['code']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_var['code']['phone']; ?></td>
      <td><?php echo $this->_var['code']['code']; ?></td>
      <td><?php echo $this->_var['code']['category']; ?></td>
      <td><?php echo $this->_var['code']['type']; ?></td>
      <td><?php echo local_date("Y-m-d H:i:s",$this->_var['code']['add_time']); ?></td>
      <td><?php echo local_date("Y-m-d H:i:s",$this->_var['code']['fail_time']); ?></td>
      <td><?php echo sub_str($this->_var['code']['ps'],60); ?></td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="10">没有符合条件的记录</td>
    </tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  </form>
  <?php if ($this->_var['code_list']): ?>
  <div id="dataFuncs">
    <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
    <div class="clear"></div>
  </div>
  <?php endif; ?>
</div>



<?php echo $this->fetch('footer.html'); ?>