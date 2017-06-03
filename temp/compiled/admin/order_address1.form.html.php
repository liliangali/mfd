<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#user_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : {
        	ship_name : {
                required : true,
            },
            p_region_id : {
                required : true,
            },
            region_id : {
                required : true,
            },
            ship_tel : {
            	required   : true,
            },
            ship_addr : {
            	required   : true,
            },
        },
        messages : {
        	ship_name : {
                required : '必填',
            },
            p_region_id : {
                required : '必填',
            },
            region_id : {
                required : '必填',
            },
            ship_tel : {
            	required   : '必填',
            },
            ship_addr : {
            	required   : '必填',
            },
        }
    });
});
</script>

<div id="rightTop">

<!--   <ul class="subnav">
    <li><font style="color:red;size:42px">请选择配送方式:</font>
      <select  name="shipping_id" id="shipping_id" onchange="ship()">
            <?php echo $this->html_options(array('options'=>$this->_var['ship_type'],'selected'=>$this->_var['shipping_id'])); ?>
     </select>
     </li>
  </ul> -->
</div>
<script>
function ship()
{
	var ship_id = $("#shipping_id").val();
	$("#ship_id").val = ship_id;
	window.location.href = "index.php?app=order&act=editAddress&id=<?php echo $this->_var['order_info']['order_id']; ?>&ship_id="+ship_id;
}

</script>

<div class="info">
<!-- <p>订单ID:<?php echo $this->_var['order_info']['order_id']; ?></p> -->
<!-- <p>订单号:<?php echo $this->_var['order_info']['order_sn']; ?></p> -->
  <form method="post" enctype="multipart/form-data" id="user_form">
  
  
  
    <table class="infoTable">
    <tr>
        <th class="paddingT15"> 地区:</th>
        <td class="paddingT15 wordSpacing5">
        <select  name="region_id_0" onchange="get_region(this,0)">
            <?php echo $this->html_options(array('options'=>$this->_var['region_0'],'selected'=>$this->_var['def_rid_0'])); ?>
       </select>
     
     	<select  name="region_id_1" id="region_id_1" onchange="get_region(this,1)">
            <?php echo $this->html_options(array('options'=>$this->_var['region_1'],'selected'=>$this->_var['def_rid_1'])); ?>
       </select>
       
         <select  name="region_id_2" id="region_id_2">
            <?php echo $this->html_options(array('options'=>$this->_var['region_2'],'selected'=>$this->_var['def_rid_2'])); ?>
       </select>
         </td>
   </tr>

      <tr>
        <th class="paddingT15"> 收货人姓名:</th>
        <td class="paddingT15 wordSpacing5">
        <input class="infoTableInput2" id="ship_name" type="text" name="ship_name" value="<?php echo $this->_var['order_info']['ship_name']; ?>" />
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 收货人电话:</th>
        <td class="paddingT15 wordSpacing5">
        <input class="infoTableInput2" id="ship_mobile" type="text" name="ship_mobile" value="<?php echo $this->_var['order_info']['ship_mobile']; ?>" />
         </td>
      </tr>


   <tr>
        <th class="paddingT15"> 详细地址:</th>
        <td class="paddingT15 wordSpacing5">
        <input class="infoTableInput2" id="ship_addr" type="text" name="ship_addr" value="<?php echo $this->_var['order_info']['ship_addr']; ?>" />
        </td>
      </tr>
      
         <tr>
        <th class="paddingT15"> 订单总金额:</th>
        <td class="paddingT15 wordSpacing5">
        <input class="infoTableInput2" id="order_amount" type="text" name="order_amount" value="<?php echo $this->_var['order_info']['order_amount']; ?>" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"  onafterpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
        只能输入数字、小数</td>
      </tr>
      
      <tr>
        <th></th>
        <input class="infoTableInput2" name="id" type="hidden" value="<?php echo htmlspecialchars($this->_var['order_info']['order_id']); ?>" />      
        <td class="ptb20"><input class="tijia" type="submit" name="Submit" value="提交" />
              </td>
      </tr>
      
      <tr>
  <th style="padding:20px 20px 5px;">修改记录：</th>
  <td>
  <?php $_from = $this->_var['address_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'edit');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['edit']):
        $this->_foreach['loop']['iteration']++;
?>
  <p style='border:1px;'><font style = "font-weight:bold"><?php echo $this->_foreach['loop']['iteration']; ?>、<?php echo $this->_var['edit']['add_time']; ?> <?php echo $this->_var['edit']['admin_name']; ?>修改了</font><br>
  <?php echo $this->_var['edit']['old_new_str']; ?>
  <hr/>
  </p>
  
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?></td>
  </tr>
      
      
    </table>
  </form>
</div>

<script>
function get_region(obj,i)
{
	var p_id = $(obj).val();
	$.post("./index.php?app=order&act=get_region",{pid:p_id}, function(res){
		   var res = eval("("+res+")");
		   var num =1; 
		   var id = i+num;
		   var mid = id+num;
		   $('#region_id_'+id).empty();
		   $('#region_id_'+mid).html('<option value="">请选择...</option>');
		   $('#region_id_'+id).append('<option value="">请选择...</option>')
	       $('#region_id_'+id).append(res.retval)
	});
}
</script>


<?php echo $this->fetch('footer.html'); ?>