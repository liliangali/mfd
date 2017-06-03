<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
     <li><a class="btn1" href="index.php?app=user">返回用户列表</a></li>
   </ul>
</div>

<div class="info">
    <div class="order_form">
        <h1>基本信息</h1>
        <ul>
        	<li style="width:30%">用户名：<?php echo $this->_var['user']['user_name']; ?></li>
        	<li>当前等级：<?php echo $this->_var['user']['lv_name_info']; ?></li>
        	<li style="width:30%">真实姓名：<?php if ($this->_var['user']['real_name']): ?><?php echo $this->_var['user']['real_name']; ?><?php else: ?>未知<?php endif; ?></li>
        	<!--<li>当前积分：<?php echo $this->_var['user']['point']; ?></li>-->
        	<li style="width:30%">注册时间：<?php echo local_date("Y-m-d",$this->_var['user']['reg_time']); ?></li>
        	<li>最后登录时间 / IP：<?php echo local_date("Y-m-d",$this->_var['user']['last_login']); ?> / <?php echo $this->_var['user']['last_ip']; ?></li>
        	<li style="width:30%">登陆次数：<?php echo $this->_var['user']['logins']; ?></li>
        	<li>购买次数：<?php echo $this->_var['user']['count_num']; ?></li>
        	<li style="width:30%">消费金额：<?php echo $this->_var['user']['amount_num']; ?></li>
			<li>麦劵金额 (数量)：<?php echo $this->_var['user']['debit_sum']; ?> (<?php echo $this->_var['user']['debit_num']; ?>)</li>
        </ul>
         <div class="clear"></div>
   </div> 



	
</div>
<script type="text/javascript">
//日期
$('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
$('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});
</script>
<script>
function searchs(obj)
{
	$("#fsearch").submit();
}		
</script>
<?php echo $this->fetch('footer.html'); ?>