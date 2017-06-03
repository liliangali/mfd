<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#coupon_form').validate({
		rules: {
			cpn_money: {
			    min: 0
			},
			cpn_prefix:{
			    maxlength :4
			}
	    },
		messages: {
			cpn_money: {
				min: "正确的金额数"
			},
			cpn_prefix:{
			    maxlength :'最多四位'
			}
		 },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
    });
});
</script>

<div class="info">
    <form method="post"  action="index.php?app=order&act=updateOrder" enctype="multipart/form-data" id="coupon_form" onsubmit="return toVaild()">
        <table class="infoTable">
          
           <tr>
                <th class="paddingT15"> 订单编号 :</th>
                <td class="paddingT15 wordSpacing5">
                <?php echo $this->_var['order_info']['order_sn']; ?>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 当前状态 :</th>
                <td class="paddingT15 wordSpacing5">
                <?php echo $this->_var['lang']['ORDER_STATUS'][$this->_var['defst']]; ?>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">修改状态为  :</th>
                <td class="paddingT15 wordSpacing5">
					<select id="status" name="status" class="required " <?php if ($this->_var['order_info']['status'] == ORDER_CANCELED || $this->_var['appoint']): ?>disabled="disabled"<?php endif; ?>>
					<option value="99"<?php if ($this->_var['order_info']['status'] == 99): ?> selected="selected"<?php endif; ?>>订单备注</option>
					<option value="11"<?php if ($this->_var['order_info']['status'] == 11): ?> selected="selected"<?php endif; ?>>待付款</option>
					<option value="20"<?php if ($this->_var['order_info']['status'] == 20): ?> selected="selected"<?php endif; ?>>已付款</option>
					<option value="61"<?php if ($this->_var['order_info']['status'] == 61): ?> selected="selected"<?php endif; ?>>待发货</option>
					<option value="30"<?php if ($this->_var['order_info']['status'] == 30): ?> selected="selected"<?php endif; ?>>已发货</option>
					<!-- <option value="41"<?php if ($this->_var['order_info']['status'] == 41): ?> selected="selected"<?php endif; ?>>返修中</option> -->
					<option value="40"<?php if ($this->_var['order_info']['status'] == 40): ?> selected="selected"<?php endif; ?>>已完成</option>
			        <!-- <option value="72"<?php if ($this->_var['order_info']['status'] == 72): ?> selected="selected"<?php endif; ?>>订单重下(修改)</option> -->
					<!-- <option value="80"<?php if ($this->_var['order_info']['status'] == 80): ?> selected="selected"<?php endif; ?>>已退款</option> -->
					<option value="0"<?php if ($this->_var['order_info']['status'] == 0): ?> selected="selected"<?php endif; ?>>作废</option>
					<!-- <option value="43"<?php if ($this->_var['order_info']['status'] == 43): ?> selected="selected"<?php endif; ?>>订单异常</option>
					<option value="44"<?php if ($this->_var['order_info']['status'] == 44): ?> selected="selected"<?php endif; ?>>物流异常</option> -->
					</select><FONT  style="color: rgb(209, 72, 54);" >提示：如果已付款的订单要改为 已作废，请记得去处理此订单的退款！！！</FONT>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">操作备注  :</th>
                <td class="paddingT15 wordSpacing5">
                <textarea  name="remark" id="remark"></textarea>
                </td>
            </tr>
		   <tr>
		  <th style="padding:20px 20px 5px;">修改记录：</th>
		  <td>
		  <?php $_from = $this->_var['order_log_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
        $this->_foreach['loop']['iteration']++;
?>
		  <p style='border:1px;'><font style = "font-weight:bold"><?php echo $this->_foreach['loop']['iteration']; ?>、<?php echo $this->_var['item']['alttime']; ?> <?php echo $this->_var['item']['op_name']; ?>修改了</font><br>
		  <?php echo $this->_var['item']['log_text']; ?>
		  <p style='border:1px;'><font style = "font-weight:bold">修改原因</font><br>
		  <?php echo $this->_var['item']['remark']; ?>
		  <hr/>
		  </p>
		  
		  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		  </td>
		  </tr>
  
            <!-- 
            <tr>
                <th class="paddingT15"><label for="article">修改状态原因 :</label></th>
                <td class="paddingT15 wordSpacing5">
                    <textarea id="remark" name="remark" style="width:400px;height:110px;"><?php echo htmlspecialchars($this->_var['data']['cpn_content']); ?></textarea>
                </td>
            </tr>
             -->
	        <tr>
	            <th></th>
	            <td class="ptb20">
	                <input class="tijia" type="submit" name="Submit" id="Submit" value="提交" />
	                <input class="congzi" type="reset" name="Submit2" value="重置" />
	            </td>
	        </tr>
        </table>
        <input type="hidden" name="order_id" value="<?php echo $this->_var['order_info']['order_id']; ?>" />
        <input type="hidden" name="modify_id" value="<?php echo $this->_var['appoint']; ?>" />
    </form>
</div>
<script type="text/javascript">
var  order_status = <?php echo $this->_var['defst']; ?>;
function toVaild()
{
	
	var status = $('#status').val();

	/* if(status == 0)
	{
		alert('请选择审核状态')
		return false;		
	} */
	if(!order_status)
	{
		alert('订单已取消 不允许再次修改状态');
		return false;
	}
	
	var fail_reason = $('#remark').val();
	if(!fail_reason)
	{
		alert('请填写备注')
		return false;
	}
	return true;
	
}


$('#start_time').datepicker({dateFormat: 'yy-mm-dd'});
$('#end_time').datepicker({dateFormat: 'yy-mm-dd'});
</script>
<?php echo $this->fetch('footer.html'); ?>
