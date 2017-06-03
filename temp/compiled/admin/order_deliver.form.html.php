<?php echo $this->fetch('header.html'); ?>
<style>
/*====公共/S====*/
body, div, p, ul, ol, li, nav, footer, dl, dt, dd, h1, h2, h3, h4, h5, h6, form, img {padding: 0; margin: 0;}
body {min-width: 320px; font-size:14px; background:#fff; color:#333; font-family:"微软雅黑", "Arial";}
body a{outline:none;blr:expression(this.onFocus=this.blur());}
ul, ol, li {list-style: none;}
img {vertical-align: middle;border: none; max-width:100%;}
a{text-decoration:none; color:#333;}
input,textarea,select {outline:none; list-style-type:none; border:none; padding:0; margin:0;}
.clearfix:after {content:"."; height:0px; line-height:0px; overflow:hidden; clear:both; display:block; visibility:hidden;}
.clearfix{zoom:1;}
.fl {float: left;}
.fr {float: right;}
/*====公共/E====*/
.container{width:100%;margin:0 auto; overflow:hidden;padding-top:15px;}
.infoy{width:90%;margin:0 5%;overflow:hidden;}
.infoy .house{width:50%;height:25px;line-height:25px;margin-bottom:15px; overflow:hidden;}
.house .orderno{width:20%;height:25px;text-align:right;overflow:hidden;}
.house .amount{width:80%;height:25px;text-align:left;overflow:hidden;}
.house .amount .logistics{width:198px;border:1px solid #333;height:23px;line-height:23px;}
.house .amount .logput{width:193px;height:21px;line-height:25px;border:1px solid #333;padding-left:5px;}
.house .amount .dqzone{width:118px;border:1px solid #333;height:23px;line-height:23px;margin-right:10px;}
.house .amount .logputs{width:367px;height:21px;line-height:25px;border:1px solid #333;padding-left:5px;}
.remark{width:100%; overflow:hidden;}
.remark .fhdrem{width:10%;height:25px;text-align:right;overflow:hidden;}
.remark textarea{border:1px solid #333;width:50%;height:90px;line-height:30px;padding:5px;font-size:14px;}
.container button{clear:both;border:none;width:150px;height:35px;display:block; background:#e66800;color:#fff;margin-top:20px;margin-left:30%; cursor:pointer;}
/*====内容/S====*/

/*====内容/E====*/
</style>



<script type="text/javascript">
$(function(){
    $('#deliver_form').validate({
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
            post_fee : {
                required : true,
            },
            region_id : {
                required : true,
            },
            logi_no : {
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
            post_fee : {
                required : '必填',
            },
            region_id : {
                required : '必填',
            },
            logi_no : {
            	required   : '必填',
            },
            ship_addr : {
            	required   : '必填',
            },
        }
    });
});

$(function(){
	  $("#insured").hide();
	  $(":radio").click(function(){
		  var ck = $(this).val();
		if(ck == 0){
			$("input[name='insured']").attr("value","");
			$("#insured").hide();
		}else{
			$("#insured").show();
		}
	  });
	  
	  $('#deliver_id').change(function(){ 	
		   var sval = $(this).children('option:selected').val();//这就是selected的值 
		   var id = $("input[name='order_id']").val();
		   $.post("./index.php?app=order&act=a_deliver",{did:sval,oid:id}, function(res){
			   var res = eval("("+res+")");
			   if(res.done){
				   $("input[name='post_fee']").attr("value",res.retval.post_fee);
			   }else{
				   alert(res.msg);
			   }
			});
		  });
	 });
</script>

<form method="post" enctype="multipart/form-data" id="deliver_form">
<div class="container">
    	<div class="infoy">
        	<div class="house fl">
            	<p class="orderno fl">订单号：</p>
                <p class="amount fl"><?php echo $this->_var['order_info']['order_sn']; ?>【<?php echo $this->_var['lang']['ORDER_STATUS'][$this->_var['order_info']['status']]; ?>】</p>
            </div>
            <div class="house fr">
            	<p class="orderno fl">下单日期：</p>
                <p class="amount fl"><?php echo local_date("Y-m-d H:i:s",$this->_var['order_info']['add_time']); ?></p>
            </div>
        </div>
        <div class="infoy">
        	<!-- <div class="house fl">
            	<p class="orderno fl">是否保价：</p>
                <p class="amount fl">否</p>
            </div> -->
            <div class="house fr">
            	<p class="orderno fl">配送费用：</p>
                <p class="amount fl"><?php echo price_format($this->_var['order_info']['measure_fee']); ?></p>
            </div>
        </div>
        <div class="infoy">
            <div class="house fl">
            	<p class="orderno fl">物流公司：</p>
            	<p class="amount fl">
                    <select class="logistics" id="deliver_id" name="deliver_id">
                    <?php $_from = $this->_var['ships']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 's');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['s']):
?>
                    <option value="<?php echo $this->_var['k']; ?>" <?php if ($this->_var['k'] == $this->_var['def']['shipping_id']): ?>selected<?php endif; ?>><?php echo $this->_var['s']['shipping_name']; ?></option>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </select>
            	</p>
            </div>
            <div class="house fr">
            	<p class="orderno fl">物流单号：</p>
                <p class="amount fl"><input type="text" class="logput" name="logi_no" /></p>
            </div>
        </div>
        <div class="infoy">
        	<div class="house fl">
            	<p class="orderno fl">物流费用：</p>
                <p class="amount fl"><input type="text" class="logput"  name="post_fee"  value="<?php echo $this->_var['def']['post_fee']; ?>"/></p>
            </div>
         <!--    <div class="house fr">
            	<p class="orderno fl">物流保价：</p>
                <p class="amount fl"><input name="is_insured" type="radio" value="1">&nbsp;&nbsp;是&nbsp;&nbsp;&nbsp;&nbsp;<input name="is_insured" type="radio" checked="checked" value="0">&nbsp;&nbsp;否</p>
            </div> -->
        </div>
<!--         <div class="infoy" id="insured">
        	<div class="house fl">
            	<p class="orderno fl">保价费用：</p>
                <p class="amount fl"><input type="text" name="insured" class="logput" /></p>
            </div>
        </div> -->
        <div class="infoy">
        	<div class="house fl">
            	<p class="orderno fl">收货人姓名：</p>
                <p class="amount fl"><input type="text" class="logput" name="ship_name" value="<?php echo $this->_var['order_info']['ship_name']; ?>" /></p>
            </div>
            <div class="house fr">
            	<p class="orderno fl">电话：</p>
                <p class="amount fl"><input type="text" class="logput" name="ship_tel" value="<?php echo $this->_var['order_info']['ship_tel']; ?>" /></p>
            </div>
        </div>
        <div class="infoy">
        	<div class="house fl">
            	<p class="orderno fl">手机：</p>
                <p class="amount fl"><input type="text" class="logput" name="ship_mobile" value="<?php echo $this->_var['order_info']['ship_mobile']; ?>" /></p>
            </div>
            <div class="house fr">
            	<p class="orderno fl">邮政编码：</p>
                <p class="amount fl"><input type="text" class="logput" name="ship_zip" value="<?php echo $this->_var['order_info']['ship_zip']; ?>" /></p>
            </div>
        </div>
        <div class="infoy">
            <div class="house fl">
            	<p class="orderno fl">地区：</p>
            	<p class="amount fl">
			       <select class="dqzone fl" name="region_id_0" onchange="get_region(this,0)" disabled="disabled">
			            <?php echo $this->html_options(array('options'=>$this->_var['region_0'],'selected'=>$this->_var['def_rid_0'])); ?>
			       </select>
			     
			     	<select class="dqzone fl" name="region_id_1" id="region_id_1" onchange="get_region(this,1)" disabled="disabled">
			            <?php echo $this->html_options(array('options'=>$this->_var['region_1'],'selected'=>$this->_var['def_rid_1'])); ?>
			       </select>
			       
			         <select class="dqzone fl" name="region_id_2" id="region_id_2" disabled="disabled">
			            <?php echo $this->html_options(array('options'=>$this->_var['region_2'],'selected'=>$this->_var['def_rid_2'])); ?>
			       </select>
            	</p>
            </div>
        </div>
        <div class="infoy">
        	<div class="house fl">
            	<p class="orderno fl">地址：</p>
                <p class="amount fl"><input type="text" class="logputs" name="ship_addr" value="<?php echo $this->_var['order_info']['ship_addr']; ?>" /></p>
            </div>
        </div>
        <div class="infoy">
        	<div class="remark fl">
            	<p class="fhdrem fl">发货单备注：</p>
                <textarea name="memo" cols="" rows=""></textarea>
            </div>
        </div>
        <input type="hidden" name="order_id" value="<?php echo $this->_var['order_info']['order_id']; ?>">
        <input type="hidden" name="deliver_name" value="<?php echo $this->_var['def']['shipping_name']; ?>">
        <button>发货</button>
    </div>
</form>
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