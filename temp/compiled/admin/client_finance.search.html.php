<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">

    <!-- <ul class="subnav">
        <li><a class="btn1" href="index.php?app=zzm">返回管理</a></li>
    </ul> -->
</div>

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
            client_name : {
                required : true,
                remote   : {
                    url :'index.php?app=client_finance&act=ajax_search_name',
                    type:'get',
                    data:{
                        client_name : function()
                        {
                            return $('#client_name').val();
                        },                       
                    },
/*         			success:function(res){
        				if(res.done){
        					return true
        				}else{
        					return false
        				}
        			}*/
                } 
            },
			time_to:{
/* 				time_from_exit:true, */
				compare:true,
			},
        },
        messages : {
            client_name : {
                required : '必填',
                remote   : '用户名不存在！'
            },
           	time_to:{
           		//compare:'截至时间必须大于初始时间',
           	}
            
        }
    });
});
jQuery.validator.addMethod("compare", function(value, element) {   

	var from = $("#time_from").val();
	return value >= from;

	}, "截至时间必须大于初始时间");
/* jQuery.validator.addMethod("time_from_exit", function(value, element) {   

	var from = $("#time_from").val();
	return from;

	}, "请选择初始时间"); */
</script>
    <div class="info">
    <form method="post" enctype="multipart/form-data" id="user_form">
        <table class="infoTable">
            <tbody>
            <tr>
                <th class="paddingT15">用户名：</th>
                <td class="paddingT15 wordSpacing5">
                   <input type="text" id="client_name" value="" name="client_name" class="mcinp" />
                   <!-- <label class="field_notice">请填写用户名</label> -->
                </td>
            </tr>
            
	         <tr>
	        <th class="paddingT15">日期从：</th>
	        <td class="paddingT15 wordSpacing5">
	       <input class="queryInput2 Wdate" type="text" value="" style="width:150px" id="time_from" name="time_from" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
     		至<input class="queryInput2 Wdate" type="text" value="" style="width:150px" id="time_to" name="time_to" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
		         </td>
   			</tr>
            

            <tr>
                <th></th>
                <td class="ptb20">
                  
                    <input type="submit" value="提交" name="Submit" class="tijia">
                    <input type="reset" value="重置" name="Submit2" class="congzi">
                </td>
            </tr>

            </tbody>
        </table>
    </form>
</div>

<?php echo $this->fetch('footer.html'); ?>
