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
/*             client_name : {
                required : true,
                remote   : {
                    url :'index.php?app=client_finance&act=ajax_search_name',
                    type:'get',
                    data:{
                        client_name : function()
                        {
                            return $('#client_name').val();
                        },                       
                    }, */
/*         			success:function(res){
        				if(res.done){
        					return true
        				}else{
        					return false
        				}
        			}
                } 
            },*/
			time_to:{
/* 				time_from_exit:true, */
				compare:true,
			},
        },
        messages : {
/*             client_name : {
                required : '必填',
                remote   : '用户名不存在！'
            }, */
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
<div id="rightTop">
    <ul class="subnav">
		<li><a class="btn1" href="<?php echo $this->_var['url']; ?>">返回</a></li>
        <li><a class="btn1" href="index.php?app=client_finance&amp;act=export&amp;type=client_finance_detail&amp;user_id=<?php echo $this->_var['user_id']; ?>&amp;<?php if ($this->_var['time_from']): ?>time_from=<?php echo $this->_var['time_from']; ?><?php else: ?>time_from=<?php echo $_GET['time_from']; ?><?php endif; ?><?php if ($this->_var['time_to']): ?>&amp;time_to=<?php echo $this->_var['time_to']; ?><?php else: ?>&amp;time_to=<?php echo $_GET['time_to']; ?><?php endif; ?>">导出</a></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get" id="user_form">
             <div class="left">
                <input type="hidden" name="app" value="client_finance" />
                <input type="hidden" name="act" value="detail" />
                用户：<?php echo $this->_var['client_name']; ?>
                <input type="hidden" name="client_name" value="<?php echo $this->_var['client_name']; ?>"/>
                <input type="hidden" name="user_id" value="<?php echo $this->_var['user_id']; ?>"/>
                时间从:<input class="queryInput2 Wdate" type="text" value="<?php if ($this->_var['time_from']): ?><?php echo $this->_var['time_from']; ?><?php else: ?><?php echo $_GET['time_from']; ?><?php endif; ?>" style="width:150px" id="time_from" name="time_from" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
                至：<input class="queryInput2 Wdate" type="text" value="<?php if ($this->_var['time_to']): ?><?php echo $this->_var['time_to']; ?><?php else: ?><?php echo $_GET['time_to']; ?><?php endif; ?>" style="width:150px" id="time_to" name="time_to" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <?php if ($this->_var['filtered']): ?>
            <a class="left formbtn1" href="index.php?app=client_finance&act=detail&user_id=<?php echo $this->_var['user_id']; ?>&client_name=<?php echo $this->_var['client_name']; ?>">撤销检索</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="fontr">
        <?php if ($this->_var['finance_info']): ?><?php echo $this->fetch('page.top.html'); ?><?php endif; ?>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">

        <tr class="tatr1">
            <td width="4%"><span ectype="order_by" fieldname="fyear">年</span></td>
            <td width="4%"><span ectype="order_by" fieldname="fmonth">月</span></td>

            <td width="4%"><span ectype="order_by" fieldname="fday">日</span></td>
            
            <td width="15%"><span ectype="order_by" fieldname="order_sn">订单号</span></td>

            <td width="15%"><span ectype="order_by" fieldname="abstract">摘要</span></td>
            <td width="10%">借方（发货）</td>
            <td width="10%">贷方（收款）</td>
             <td width="10%">余额</td>

        </tr>
       <!-- 期初余额 -->
		<tr class="tatr2">
        	<td></td>
        	<td></td>
        	<td></td>
        	<td></td>
        	<td>期初余额</td>
        	<td ></td>
        	<td ></td>
        	<td><?php echo $this->_var['start_balance']; ?></td>
        </tr>
        <?php $_from = $this->_var['finance_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'finance');if (count($_from)):
    foreach ($_from AS $this->_var['finance']):
?>
        <tr class="tatr2">
        	<td><?php echo local_date("Y",$this->_var['finance']['add_time']); ?></td>
        	<td><?php echo local_date("m",$this->_var['finance']['add_time']); ?></td>
        	<td><?php echo local_date("d",$this->_var['finance']['add_time']); ?></td>
        	<td><?php if ($this->_var['finance']['finance_sn']): ?><?php echo $this->_var['finance']['finance_sn']; ?><?php else: ?>无订单号<?php endif; ?></td>
        	<td>
        	<?php $_from = $this->_var['finance']['abstract']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['v']):
?>
        	<?php echo $this->_var['v']; ?><br/>
        	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        	</td>
        	<td ><?php if ($this->_var['finance']['type'] == 1): ?><?php echo $this->_var['finance']['trans_amount']; ?><?php endif; ?></td>
        	<td  style="color:green"><?php if ($this->_var['finance']['type'] == 2): ?><?php echo $this->_var['finance']['trans_amount']; ?><?php endif; ?></td>
        	<td><?php echo $this->_var['finance']['end_balance']; ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?> 
    </table>
    <div id="dataFuncs">

      	
        <div class="pageLinks">       		 
            <?php echo $this->fetch('page.bottom.html'); ?>
            <span class="page mtr10"><?php if ($this->_var['finance_info']): ?>共<?php echo $this->_var['count']; ?>笔交易，累计发生：发货<?php echo $this->_var['expend_money']; ?>元，收款<?php echo $this->_var['earning_money']; ?>元<?php endif; ?></span>
        </div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
//二级层
$('.dataTable .showInfo').click(function(){
	var $this=$(this),obj=$this.parents('tr').next('tr');
	if($this.hasClass('show')){
		obj.hide();
		$this.removeClass('show')
	}else{
		obj.show();
		$this.addClass('show')
	}
})

$('.export_btn').unbind().bind('click',function(){
	var _type = $(this).attr('data-type');
	
	if(_type == 'all'){
		var _condi = '&sinexporttype=all';
		$('.mrightTop').find('.fontl').find('input').each(function(){
	        if($(this).val() && $(this).attr('name')){
	            if($(this).attr('name') != 'app' && $(this).attr('name') != 'act'){
	                _condi += '&'+$(this).attr('name')+'='+$(this).val();
	            }
	        }
	    })
	    $('.mrightTop').find('.fontl').find('select').each(function(){
	        if($(this).val() && $(this).attr('name'))
	        _condi += '&'+$(this).attr('name')+'='+$(this).val();
	    })
	}else{
		var ids='';
	    $(".checkitem").each(function(){
	        if($(this).attr("checked") == true){
	            ids += ids ? ","+$(this).val() : $(this).val();
	        }
	    });

	    if(ids.length == 0){
	        return false;
	    }
	    var _condi = '&ids='+ids;
	}
	
	window.open("index.php?app=order&act=export"+_condi);
	return;
})

function print(){
    var ids='';
    $(".checkitem").each(function(){
        if($(this).attr("checked") == true){
            ids += ids ? ","+$(this).val() : $(this).val();
        }
    });

    if(ids.length == 0){
        return false;
    }
    window.open("index.php?app=order&act=orderprint&id="+ids);
}
</script>
<?php echo $this->fetch('footer.html'); ?>
