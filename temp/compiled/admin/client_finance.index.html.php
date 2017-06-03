<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
	$(function() {
		$('#user_form').validate({
			errorPlacement : function(error, element) {
				$(element).next('.field_notice').hide();
				$(element).after(error);
			},
			success : function(label) {
				label.addClass('right').text('OK!');
			},
			onkeyup : false,
			rules : {
			/* client_name : {
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
				/* 	success:function(res){
				    		if(res.done){
				        		return true
				       		 }else{
				        		return false
				       		}
				     	 }
				      } 
				   },*/
				time_to : {
					/* 				time_from_exit:true, */
					compare : true,
				},
			},
			messages : {
				/*             client_name : {
				 required : '必填',
				 remote   : '用户名不存在！'
				 }, */
				time_to : {
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
		<!-- <li><a class="btn1" href="<?php echo $this->_var['url']; ?>">返回</a></li>
        <li><a class="btn1" href="index.php?app=client_finance&amp;act=export">导出</a></li> -->
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get" id="user_form">
             <div class="left">
                <input type="hidden" name="app" value="client_finance" />
                <input type="hidden" name="act" value="index" />
                用户：<input type="text" name="client_name" value="<?php echo $_GET['client_name']; ?>"/>
                
                <?php echo $this->_var['user_id']; ?>
                时间从:<input class="queryInput2 Wdate" type="text" value="<?php echo $_GET['time_from']; ?>" style="width:150px" id="time_from" name="time_from" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
                至：<input class="queryInput2 Wdate" type="text" value="<?php echo $_GET['time_to']; ?>" style="width:150px" id="time_to" name="time_to" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" />
                <input type="submit" class="formbtn" name='submit' value="查询" />
            </div>
            <?php if ($this->_var['filtered']): ?>
            <a class="left formbtn1" href="index.php?app=client_finance&act=index">撤销检索</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="fontr">
        <?php if ($this->_var['finance_list']): ?><?php echo $this->fetch('page.top.html'); ?><?php endif; ?>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">

        <tr class="tatr1">
        	<td width="5%" class="firstCell"><input type="checkbox" class="checkall" />全选</td>
            <td width="4%"><span ectype="order_by" fieldname="fyear">用户</span></td>
            <td width="4%"><span ectype="order_by" fieldname="fmonth">起初余额</span></td>

            <td width="4%"><span ectype="order_by" fieldname="fday">借方（发货）</span></td>
            
            <td width="15%"><span ectype="order_by" fieldname="order_sn">贷方（收款）</span></td>

            <td width="30%"><span ectype="order_by" fieldname="abstract">期末余额</span></td>
            <td width="10%">操作</td>

        </tr>

        <?php $_from = $this->_var['finance_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'finance');if (count($_from)):
    foreach ($_from AS $this->_var['finance']):
?>
        <tr class="tatr2">
        	<td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['finance']['user_id']; ?>" /></td>
        	<td><?php echo $this->_var['finance']['user_name']; ?></td>
        	<td><?php echo $this->_var['finance']['start_balance']; ?></td>
        	<td><?php echo price_format($this->_var['finance']['expend']); ?></td>
        	<td style="color:green"><?php echo price_format($this->_var['finance']['earning']); ?></td>
        	<td><?php echo $this->_var['finance']['end_balance']; ?></td>
        	<td><a href="index.php?app=client_finance&amp;act=detail&amp;client_name=<?php echo $this->_var['finance']['user_name']; ?>&amp;user_id=<?php echo $this->_var['finance']['user_id']; ?>&amp;time_from=<?php echo $_GET['time_from']; ?>&amp;time_to=<?php echo $_GET['time_to']; ?>">查看明细账</a></td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?> 
    </table>
    <?php if ($this->_var['finance_list']): ?>
    <div id="dataFuncs">
          <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
          	  <input type="checkbox" class="checkall" />全选
	          <!-- <input class="formbtn" type="button" value="订单打印" onclick="print()" /> -->
	          <input class="export_btn formbtn" data-type="all" type="button" value="导出检索订单" />
	          <input class="export_btn formbtn" data-type="sel" type="button" value="导出所选订单" />
	          <input type="hidden" id="conditions" value="<?php echo $this->_var['conditions']; ?>">
      	  </div>
      	
        <div class="pageLinks">       		 
            <?php echo $this->fetch('page.bottom.html'); ?>
            <span class="page mtr10">共<?php echo $this->_var['count']; ?>笔交易，累计发生：发货<?php echo price_format($this->_var['expend_money']); ?>元，收款<?php echo price_format($this->_var['earning_money']); ?>元</span>
        </div>
    </div>
    <?php endif; ?>
    <div class="clear"></div>
</div>
<script type="text/javascript">
	//二级层
	$('.dataTable .showInfo').click(function() {
		var $this = $(this), obj = $this.parents('tr').next('tr');
		if ($this.hasClass('show')) {
			obj.hide();
			$this.removeClass('show')
		} else {
			obj.show();
			$this.addClass('show')
		}
	})

	$('.export_btn').unbind().bind(
			'click',
			function() {
				var _type = $(this).attr('data-type');
				
				if (_type == 'all') {
					var _condi = '&type=client_finance_all';
					$('.mrightTop').find('.fontl').find('input').each(
							function() {
								if ($(this).val() && $(this).attr('name')) {
									if ($(this).attr('name') != 'app'
											&& $(this).attr('name') != 'act' && $(this).attr('name')!='submit') {
										_condi += '&' + $(this).attr('name')
												+ '=' + $(this).val();
									}
								}
							})
				} else {
					var ids = '';
					$(".checkitem").each(function() {
						if ($(this).attr("checked") == true) {
							ids += ids ? "," + $(this).val() : $(this).val();
						}
					});

					if (ids.length == 0) {
						return false;
					}
					var _condi='&type=client_finance_all';
					$('.mrightTop').find('.fontl').find('input').each(
							function() {
								if ($(this).val() && $(this).attr('name')) {
									if ($(this).attr('name') != 'app'
											&& $(this).attr('name') != 'act' && $(this).attr('name')!='submit') {
										_condi += '&' + $(this).attr('name')
												+ '=' + $(this).val();
									}
								}
							})            		 _condi += '&user_ids=' + ids;
				}
			 
				window.open("index.php?app=client_finance&act=export" + _condi);
				return;
			})

	function print() {
		var ids = '';
		$(".checkitem").each(function() {
			if ($(this).attr("checked") == true) {
				ids += ids ? "," + $(this).val() : $(this).val();
			}
		});

		if (ids.length == 0) {
			return false;
		}
		window.open("index.php?app=order&act=orderprint&id=" + ids);
	}
</script>
<?php echo $this->fetch('footer.html'); ?>
