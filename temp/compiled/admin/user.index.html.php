<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
  <ul class="subnav">
    <li><span>管理</span></li>
<!--     <li><a class="btn1" href="index.php?app=user&amp;act=add">新增</a></li> -->
    <li><a class="btn1" href="index.php?app=user&amp;act=admin_userlog">日志查询</a></li>
    <?php if ($_GET['serve_type'] == 4): ?><li><a class="btn1" href="index.php?app=serve&wait_verify=1&t=4">待审核</a></li><?php endif; ?>
   <!--  <li><a class="btn1" href="index.php?app=user&amp;act=export">导出</a></li>  -->
      <li><span><a class="btn1" href="index.php?app=user&amp;act=user_log">登录记录</a></span></li>

  </ul>
</div>   

<div class="mrightTop">
  <div class="fontl">
    <form method="get">
       <div class="left">
          <input type="hidden" name="app" value="user" />
          <input type="hidden" name="act" value="index" />
           <!--<select class="querySelect" name="field_serve_type"><?php echo $this->html_options(array('options'=>$this->_var['tname'],'selected'=>$_GET['field_serve_type'])); ?>
          </select>-->

          <!--<select class="querySelect" name="field_member_lv"><?php echo $this->html_options(array('options'=>$this->_var['member_lv'],'selected'=>$_GET['field_member_lv'])); ?>-->
          <!--</select>-->
          
          <select class="querySelect" name="field_name"><?php echo $this->html_options(array('options'=>$this->_var['query_fields'],'selected'=>$_GET['field_name'])); ?>
          </select>
          <input class="queryInput" type="text" name="field_value" value="<?php echo htmlspecialchars($_GET['field_value']); ?>" />
        注册时间从：<input class="queryInput2" type="text" value="<?php echo $_GET['add_time_from']; ?>" style="width:80px" id="add_time_from" name="add_time_from" class="pick_date" />
        至：<input class="queryInput2" type="text" value="<?php echo $_GET['add_time_to']; ?>" style="width:80px" id="add_time_to" name="add_time_to" class="pick_date" />

         <?php echo $_GET['lv_time_from']; ?>
           <?php echo $_GET['lv_time_to']; ?>


        排序:
        <select class="querySelect" name="sort"><?php echo $this->html_options(array('options'=>$this->_var['sort_options'],'selected'=>$_GET['sort'])); ?>
        </select>

          <input type="submit" class="formbtn" value="查询" />
      </div>
      <?php if ($this->_var['filtered']): ?>
      <a class="left formbtn1" href="index.php?app=user&serve_type=<?php echo $_GET['field_serve_type']; ?>">撤销检索</a>
      <?php endif; ?>
    </form>
  </div>
  <div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_var['users']): ?>
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>会员名 </td>
      <!--<td><span ectype="order_by" fieldname="final_amount_num">当前积分</span></td>-->
      <td>手机号码</td>
      <td><span ectype="order_by" fieldname="reg_time">注册时间</span></td>
      <td><span ectype="order_by" fieldname="last_login">最后登录</span></td>
      <td><span ectype="order_by" fieldname="logins">登录次数</span></td>
      <td>注册来源</td>
      <td>来源渠道</td>
      <td class="handler">操作</td>
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_var['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'user');if (count($_from)):
    foreach ($_from AS $this->_var['user']):
?>
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['user']['user_id']; ?>" /></td>
      <td><?php echo htmlspecialchars($this->_var['user']['user_name']); ?></td>
      <!--<td><?php echo $this->_var['user']['point']; ?></td>-->
      <td><?php echo htmlspecialchars($this->_var['user']['phone_mob']); ?></td>
      <td><?php echo local_date("Y-m-d",$this->_var['user']['reg_time']); ?></td>
      <td><?php if ($this->_var['user']['last_login']): ?><?php echo local_date("Y-m-d",$this->_var['user']['last_login']); ?><?php endif; ?><br />
        <?php echo $this->_var['user']['last_ip']; ?></td>
      <td><?php echo $this->_var['user']['logins']; ?></td>
      <td>
      <?php echo $this->_var['user']['come']; ?>
      </td>
        <td>
            <?php if ($this->_var['user']['channel_name']): ?>
            <?php echo $this->_var['user']['channel_name']; ?>
            <?php else: ?>
            无
            <?php endif; ?>
        </td>
      <td class="handler">
      <span style="width: 100px">

      <a href="index.php?app=user&amp;act=edit&amp;id=<?php echo $this->_var['user']['user_id']; ?>">编辑</a>
      | <a href="index.php?app=user&amp;act=member_info&amp;id=<?php echo $this->_var['user']['user_id']; ?>">详情</a>
      |<a href="index.php?app=user&amp;act=channel&amp;id=<?php echo $this->_var['user']['user_id']; ?>" target="_blank">生成二维码</a>
      |<a href="index.php?app=user&amp;act=index&amp;channel_pid=<?php echo $this->_var['user']['user_id']; ?>" >下线会员</a>
      <?php if ($this->_var['user_info']['user_id'] == 1 || $this->_var['user_info']['user_name'] == '18765911731'): ?>|
          <!--<a href="index.php?app=account_log&amp;id=<?php echo $this->_var['user']['user_id']; ?>">账户</a>-->
          <?php endif; ?>
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


$('#lv_time_from').datepicker({dateFormat: 'yy-mm-dd'});
$('#lv_time_to').datepicker({dateFormat: 'yy-mm-dd'});
</script>

<?php echo $this->fetch('footer.html'); ?>