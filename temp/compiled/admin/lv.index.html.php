<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
  <ul class="subnav">
    <li><span>管理</span></li>
    <li><a class="btn1" href="index.php?app=lv&amp;act=add">新增</a></li>
  </ul>
</div>

<!-- <div class="mrightTop">
  <div class="fontl">
    <form method="get">
       <div class="left">
          <input type="hidden" name="app" value="lv" />
          <input type="hidden" name="act" value="index" />
          <select class="querySelect" name="field_name"><?php echo $this->html_options(array('options'=>$this->_var['query_fields'],'selected'=>$_GET['field_name'])); ?>
          </select>
          <input class="queryInput" type="text" name="field_value" value="<?php echo htmlspecialchars($_GET['field_value']); ?>" />
          排序:
          <select class="querySelect" name="sort"><?php echo $this->html_options(array('options'=>$this->_var['sort_options'],'selected'=>$_GET['sort'])); ?>
          </select>
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <?php if ($this->_var['filtered']): ?>
      <a class="left formbtn1" href="index.php?app=lv">撤销检索</a>
      <?php endif; ?>
    </form>
  </div>
  <div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>
</div> -->
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_var['users']): ?>
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>等级名称</td>
      <td><span ectype="order_by" fieldname="experience">经验值或业绩</span></td>
      <td><span ectype="order_by" fieldname="default_lv">是否默认</span></td>
      <!--<td><span ectype="order_by" fieldname="dis_count">折扣或提成比例</span></td>-->
      <!--<td><span ectype="order_by" fieldname="dis_count">BD提成比例</span></td>-->
      <!--<td><span ectype="order_by" fieldname="dis_count">创业者提成比例</span></td>-->
<!--&lt;!&ndash;       <td><span ectype="order_by" fieldname="experience">经验值或业绩</span></td> &ndash;&gt;-->
      <!--<td><span ectype="order_by" fieldname="lv_type">等级类型</span></td>-->
       <td class="handler">操作</td>

    </tr>
    <?php endif; ?>
    <?php $_from = $this->_var['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'user');if (count($_from)):
    foreach ($_from AS $this->_var['user']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['user']['member_lv_id']; ?>" /></td>
      <td><?php echo htmlspecialchars($this->_var['user']['name']); ?></td>
      <td><?php echo htmlspecialchars($this->_var['user']['experience']); ?></td>
       <td><?php if ($this->_var['user']['default_lv']): ?>是
      <?php else: ?>否<?php endif; ?>
      </td>
      <!--<td><?php echo $this->_var['user']['dis_count']; ?></td>-->
     <!--&lt;!&ndash;   <td><?php echo $this->_var['user']['experience']; ?></td> &ndash;&gt;-->
       <!--<td><?php echo $this->_var['user']['bd_dis_count']; ?>%</td>-->
       <!--<td><?php echo $this->_var['user']['cy_dis_count']; ?>%</td>-->
       <!--<td><?php echo $this->_var['tname']['\$user']['lv_type']; ?></td>-->
     
      <td class="handler">
      <span style="width: 100px">
       &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;<a href="index.php?app=lv&amp;act=edit&amp;id=<?php echo $this->_var['user']['member_lv_id']; ?>">编辑</a>
      </span>
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="10">没有符合条件的记录</td>
    </tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  <?php if ($this->_var['users']): ?>
  <div id="dataFuncs">
    <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
    <div class="clear"></div>
  </div>
  <?php endif; ?>
</div>
<?php echo $this->fetch('footer.html'); ?>