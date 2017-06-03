<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#messagetemplate_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {    
            mt_title : {
                required : true
            },
            mt_content    :{
                required     : true
            },
            mt_type :{
                required : true,
                remote :{
                	url:'index.php?app=messagetemplate&act=ajax_col',
                	type:'get',
                	data:{
                		id:"<?php echo $this->_var['id']; ?>",
                		column:'mt_type',
                		value:function(){
                			return $("#mt_type").val();
                		}
                	}
                }
            },
            parent_id:{
                required   : true
             },

        },
        messages : {
            mt_title : {
                required : "消息标题不能为空",
            },
            mt_type : {
                required : "消息分类不能为空",
                remote:'该使用标记已被使用，请更换其他名称',
            },
            mt_content    : {
                required     : '模版内容不能为空'             
            },   
            parent_id:{
                required   : '分类不能为空',
             },
        }
    });
});

</script>
<script type="text/javascript" src="<?php echo $this->res_base . "/" . 'js/luck/luck.js'; ?>" charset="utf-8"></script>
<?php echo $this->_var['build_editor']; ?>
<div id="rightTop">
	<ul class="subnav">
		<li><a class="btn1" href="index.php?app=messagetemplate">管理</a></li>
		<?php if ($this->_var['messagetemplate']['mt_id']): ?>
		<li><a class="btn1"
			href="index.php?app=messagetemplate&amp;act=add">新增</a></li>
		<?php else: ?>
		<li><span>新增</span></li> <?php endif; ?>
		<li><a class="btn1" href="index.php?app=icategory&amp;act=index">消息分类</a></li>
	</ul>
</div>

<div class="info">
	<form method="post" enctype="multipart/form-data"
		id="messagetemplate_form">
		<table class="infoTable">
			<tr>
				<th class="paddingT15">标题:</th>
				<td class="paddingT15 wordSpacing5"><input
					class="infoTableInput" id="mt_title" type="text" name="mt_title"
					value="<?php echo htmlspecialchars($this->_var['messagetemplate']['mt_title']); ?>" /></td>
			</tr>
			<tr>
				<th class="paddingT15">使用标记:</th>
				<td class="paddingT15 wordSpacing5">
					<input	class="infoTableInput" id="mt_type" type="text" name="mt_type" value="<?php echo htmlspecialchars($this->_var['messagetemplate']['mt_type']); ?>" /> 		
					<label	class="field_notice">使用标记唯一，最好使用英文和'_'组成的短语</label></td>
			</tr>
			<tr>
				<th class="paddingT15"><label for="parent_id">所属分类:</label></th>
				<td class="paddingT15 wordSpacing5">
				<div id="parent_id">

				<?php echo $this->html_radios(array('options'=>$this->_var['parents'],'name'=>'parent_id','checked'=>$this->_var['messagetemplate']['parent_id'])); ?>

				</div>
				<!-- <select id="parent_id"
					name="parent_id"><option value="">请选择...</option> 
					<?php echo $this->html_options(array('options'=>$this->_var['parents'],'selected'=>$this->_var['messagetemplate']['parent_id'])); ?>
				</select></td> -->
			</tr>
			<tr>
				<th class="paddingT15">是否特殊消息:</th>
				<td class="paddingT15 wordSpacing5">
				<div id="parent_id">
				<?php echo $this->html_radios(array('options'=>$this->_var['specials'],'name'=>'is_special','checked'=>$this->_var['messagetemplate']['is_special'])); ?>
				</div>
				<!-- <select id="parent_id"
					name="parent_id"><option value="">请选择...</option> 
					<?php echo $this->html_options(array('options'=>$this->_var['parents'],'selected'=>$this->_var['messagetemplate']['parent_id'])); ?>
				</select></td> -->
			</tr>	
			<tr>
				<th class="paddingT15">通知模块:</th>
				<td class="paddingT15 wordSpacing5"><input
					class="infoTableInput" id="mt_module" type="text" name="mt_module"
					value="<?php echo htmlspecialchars($this->_var['messagetemplate']['mt_module']); ?>" /></td>
			</tr>
			<tr>
				<th class="paddingT15">消息节点:</th>
				<td class="paddingT15 wordSpacing5"><input
					class="infoTableInput" id="mt_code" type="text" name="mt_code"
					value="<?php echo htmlspecialchars($this->_var['messagetemplate']['mt_code']); ?>" /></td>
			</tr>
			<tr  <?php if ($this->_var['messagetemplate']['parent_id'] == 1): ?>style="display:none"<?php endif; ?>>
			<th class="paddingT15" > <label for="site_name">发放设备:</label></th>
				<td class="paddingT15 wordSpacing5">
					<select name='send_type' id='send_type'>
								  <?php echo $this->html_options(array('options'=>$this->_var['send_type'])); ?>
					</select>
				</td>
			</tr>
			<tr id="url_pc"<?php if ($this->_var['messagetemplate']['parent_i'] == 1): ?>  style="display:none"  <?php endif; ?>>
				<th class="paddingT15">pc跳转url:</th>
				<td class="paddingT15 wordSpacing5">
					<input	class="infoTableInput" id="mt_pc_url" type="text" name="mt_pc_url"	value="<?php echo htmlspecialchars($this->_var['messagetemplate']['mt_pc_url']); ?>" />
				</td>
			</tr>
			<tr id="url_app" <?php if ($this->_var['messagetemplate']['parent_id'] == 1): ?> style="display:none" <?php endif; ?>>
				<th class="paddingT15">app跳转url:</th>
				<td class="paddingT15 wordSpacing5">
					<?php echo $this->html_radios(array('options'=>$this->_var['mt_app_url'],'name'=>'mt_app_url','checked'=>$this->_var['messagetemplate']['mt_app_url'])); ?>
				</td>
			</tr>
			<tr id="member_t" <?php if ($this->_var['messagetemplate']['obj_val']): ?>style="display:none"<?php endif; ?>>
				<th class="paddingT15"> <label for="site_name">发放对象:</label></th>
				<td class="paddingT15 wordSpacing5 obj">
					<select name='obj_type' >
								<?php if (! $this->_var['messagetemplate']['obj_type']): ?>
								<?php echo $this->html_options(array('options'=>$this->_var['obj_type'],'selected'=>'-1')); ?>
								<?php else: ?>
								  <?php echo $this->html_options(array('options'=>$this->_var['obj_type'],'selected'=>$this->_var['messagetemplate']['obj_type'])); ?>
								  <?php endif; ?>
					</select>
					<span style="color:blue;cursor:pointer" id="ponintUser">指定会员</span>
				</td>
			</tr>
      
			<tr id="member_p" <?php if (! $this->_var['messagetemplate']['obj_val']): ?>style="display:none"<?php endif; ?>>
				<th class="paddingT15"> <label for="site_name">发放对象:</label></th>
				<td class="paddingT15 wordSpacing5 obj">
					<input type='text' name='member' placeholder='输入用户电话号' id='member'/> <input type='button' value='确定' id='addok'/>
					<span style="color:blue;cursor:pointer" id="ponintUserT">指定会员类型</span>
					<br>
					<div style='border:1px dashed #BBBBBB;width:500px;height:auto;min-height:200px;padding-left:5px' id='member_text'>
						<?php if ($this->_var['messagetemplate']['obj_val']): ?>
							<?php $_from = $this->_var['messagetemplate']['obj_val']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['val']):
?>
								<label onclick= 'del(this)'><input  name='' type='text'  value="<?php echo $this->_var['val']['phone']; ?>" readonly='readonly' style='width:80px;height:16px;border:2px solid #EEEEEE; border-right:20px solid #EEEEEE;margin:5px 5px 5px 5px;' /><input type='hidden' name='obj_val[]' value="<?php echo $this->_var['val']['user_id']; ?>" /></label>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							<input type="hidden" class="text width2" value="0" name="obj_type" id="obj_type"/>
						<?php endif; ?>
					</div>
				</td>
			</tr>

			<?php if ($this->_var['messagetemplate']['add_time']): ?>
			<tr>
				<th class="paddingT15">添加时间:</th>
				<td class="paddingT15 wordSpacing5">

					<?php echo local_date("Y-m-d H:i:s",$this->_var['messagetemplate']['add_time']); ?></td>
			</tr>
			<tr>
				<th class="paddingT15">修改时间:</th>
				<td class="paddingT15 wordSpacing5">
					<?php if ($this->_var['messagetemplate']['alter_time']): ?>
					<?php echo local_date("Y-m-d H:i:s",$this->_var['messagetemplate']['alter_time']); ?> <?php endif; ?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th class="paddingT15"><label for="mt_content">消息内容:</label></th>
				<td class="paddingT15 wordSpacing5">
				<textarea	id="mt_content"  name="mt_content" style="width: 650px; height: 400px;"><?php echo htmlspecialchars($this->_var['messagetemplate']['mt_content']); ?></textarea>
				</td>
			</tr>
			<!-- <tr>
                <th class="paddingT15" valign="top">
                    <label for="parent_id">商品链接:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select name="cat"><option value="0">请选择...</option></select>
                  商品名称:<input type="text" name="goods_name"> <input type="button" name="but" value="search" onclick="searchGoods(this)">
                    <select name="goods_id" id="goodsid">
                        <option value=0>pls_select_goods</option>
                    </select>
                    <input type="button" name="bt" value="新增" onclick=selectGoods(this)>
                    <ul style="clear:both;list-style:none;" id="goods_list">
                    <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
                        <li><input type="checkbox" value="<?php echo $this->_var['goods']['goods_id']; ?>" name="ids[]" checked=true> <?php echo $this->_var['goods']['goods_name']; ?></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </ul>
                    </td>
                    
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="process">上传:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <input type="file" name="cover_pic">
                   <?php if ($this->_var['process']['cover_img']): ?>
                      <img src="../upload/process/<?php echo $this->_var['process']['cover_img']; ?>" width="50" height="50">
                   <?php endif; ?>
                </td>
            </tr>-->

			<tr>
				<th></th>
				<td class="ptb20"><input class="tijia" type="submit"
					name="Submit" value="提交" /> <input class="congzi"
					type="reset" name="Submit2" value="重置" /></td>
			</tr>
		</table>
	</form>
</div>
<script>
/* function change_type(id){
	var value=$("#mt_type").val();
	if(!value){
		$("#mt_type").after("<span id='addword' style='color:red'>使用标记不能为空</span>");
		return;
	}
	$.get("index.php?app=messagetemplate&act=ajax_col",{id:id,column:'mt_type',value:value},function(res){
		var res=eval("("+res+")");
		if(res){
			return;
		}else{
			$("#mt_type").next('.field_notice').hide();
			$("#mt_type").after("<span id='addword' style='color:red'>该使用标记已被使用，请更换其他名称</span>");
			return false;
		}
	})
} */
// $.(function(){
	
// })
$("#parent_id").click(function(){
	/* console.log($('#parent_id input[name="parent_id"]:checked').val()); */
	var check_val=$('#parent_id input[name="parent_id"]:checked').val();

	if(check_val==1){

		$("#url_pc").attr("style","display:none");
		$("#url_app").attr("style","display:none");
		$("#send_type").parent().parent().attr("style","display:none");
	}else{
		$("#url_pc").removeAttr("style");
		$("#url_app").removeAttr("style");
		$("#send_type").parent().parent().removeAttr("style");
	}
	
})

function changeCate(obj)
{
	  var val = $(obj).val();
	  var li_length = parseInt($('#member_t select option').length);
	  for(var i=0;i<li_length;i++)
	  {
        var info = $('#member_t select option:eq('+i+')').val();
        if(val == 1)
     	  {
      	  if(info == 1)
         	  {
      		  $('#member_t select option:eq('+i+')').remove();
         	  }
     	  }
        else
        {
      	 
        }  
      	
        
    }
	  
	  if(val == 0)
		  {
		  	if(li_length < 5)
		    {
		  	  var str = "<option value='1' >消费者</option>";
     	  $('#member_t select').append(str);
		    }
		  }
}

 $("#ponintUser").click(function(){
	  $("#member_t").css("display","none")
	  $("#member_p").css("display","")
	  var str = '<input type="hidden" class="text width2" value="0" name="obj_type" id="obj_type"/>';
	  $("#member_p").append(str);
 })
 
  $("#ponintUserT").click(function(){
	  $("#member_p").css("display","none")
	  $("#member_t").css("display","")
	  $("#obj_type").remove();
 })
 
 /*  $("#money_d_m").click(function(){
	  $("#money_d").css("display","none")
	  $("#money_t").css("display","")
	  var str ='<input type="text" class="text width2" name="money"/>元/币<input type="hidden" class="text width2" value="1" name="money_type"/>';
	  $("#money_type1").empty();
	  $("#money_type2").append(str);
	  
 })
 
  $("#money_t_m").click(function(){
	  $("#money_t").css("display","none")
	  $("#money_d").css("display","")
	  var str ='<input type="text" class="text width2" name="money"/>元/币<input type="hidden" class="text width2" value="0" name="money_type"/>';
	  $("#money_type2").empty();
	  $("#money_type1").append(str);
 }) */
 
 $("#addok").click(function(){
	  
	  var member = $("#member").val();
	  var cate = 0;
	  
	  //检查是否已经在添加文件中存在
	  var input_length = parseInt($("#member_text input").length);
	  if(input_length > 0)
		  {
		  for(var i=0;i<input_length;i++)
   	  {
   		  var info = $('#member_text input:eq('+i+')').val();
   		  if(info == member)
   		  {
   			  luck.alert('系统提示','此会员已添加成功!请勿重复添加',6);
   			  return false;
   		  }	   
   	  }
		  }
	  
	  //alert(input_length);
	  
	  $.post("index.php?app=debit&act=checkm",{phone:member,cate:cate},function(res){
		 var ress = eval('('+res+')');
		  if(ress.done == true)
			  {
			  var sp = "<label onclick= 'del(this)'><input  name='' type='text'  value="+member+" readonly='readonly' style='width:80px;height:16px;border:2px solid #EEEEEE; border-right:20px solid #EEEEEE;margin:5px 5px 5px 5px;' /><input type='hidden' name='obj_val[]' value="+ress.retval+" /></label>";
	    	  $("#member_text").append(sp);
			  }
		  else
			  {
			  luck.alert('系统提示','会员不存在',6);
			  }
	  })
	  
	  
 })
 
 function del(objs)
 {
	  luck.confirm('系统提示','确认删除',function(obj){
					if(obj)
						{
						//luck.alert('系统提示',t);
	    				$(objs).remove();
						}
		},['确定','取消']);
 }


</script>
<?php echo $this->fetch('footer.html'); ?>
