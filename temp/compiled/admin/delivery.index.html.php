	<?php echo $this->fetch('header.html'); ?>
<link href="templates/jquery-tipso/css/tipso.min.css" type="text/css" rel="stylesheet"/>
<script src="templates/jquery-tipso/js/jquery-1.8.3.min.js"></script>
<script src="templates/jquery-tipso/js/tipso.min.js"></script>

<div class="mrightTop">
    <div class="fontl">
        <form method="get">
             <div class="left">
                <input type="hidden" name="app" value="delivery" />
                <input type="hidden" name="act" value="index" />
                <select class="querySelect" name="field"><?php echo $this->html_options(array('options'=>$this->_var['search_options'],'selected'=>$_GET['field'])); ?>
                </select><input class="queryInput" type="text" name="search_name" value="<?php echo htmlspecialchars($this->_var['query']['search_name']); ?>" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <?php if ($this->_var['filtered']): ?>
            <a class="left formbtn1" href="index.php?app=delivery">撤销检索</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="fontr">
        <?php if ($this->_var['deliverys']): ?><?php echo $this->fetch('page.top.html'); ?><?php endif; ?>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['deliverys']): ?>
        <tr class="tatr1">
                    <td ><input type="checkbox" class="checkall" /></td>
            <td width="15%"><span ectype="delivery_by" fieldname="t_begin">单据创建时间</span></td>
            <td width="8%"><span ectype="delivery_by" fieldname="logi_name">物流公司</span></td>
            <td width="10%"><span ectype="delivery_by" fieldname="op_name">操作员</span></td>
            <td width="7%"><span ectype="delivery_by" fieldname="post_fee">物流费用</span></td>
            <td width="15">发货单号</td>
            <td width="10%"><span ectype="delivery_by" fieldname="receiver_name">收货人</span></td>
            <td width="10%">订单号</td>
            <td width="20%">收货地址</td>
           <!--  <td width="7%">物流状态</td> -->
            <td width="10%">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['deliverys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'delivery');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['delivery']):
?>
        <tr class="tatr2">
            <td ><input type="checkbox" class="checkitem" value="<?php echo $this->_var['delivery']['delivery_id']; ?>" /></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['delivery']['t_begin']); ?></td>
            <td><?php echo htmlspecialchars($this->_var['delivery']['logi_name']); ?></td>
            <td><?php echo htmlspecialchars($this->_var['delivery']['op_name']); ?></td>
            <td><?php echo price_format($this->_var['delivery']['post_fee']); ?></td>
            <td><?php echo $this->_var['delivery']['tid']; ?></td>
            <td><?php echo htmlspecialchars($this->_var['delivery']['receiver_name']); ?></td>
            <td><?php echo $this->_var['delivery']['logi_no']; ?></td>
            <td><?php echo $this->_var['delivery']['area_names']; ?><?php echo $this->_var['delivery']['receiver_address']; ?></td>
           <!--   <td><?php echo $this->_var['delivery']['status']; ?></td> -->
            <td>
                <a href="index.php?app=delivery&amp;act=view&amp;id=<?php echo $this->_var['delivery']['delivery_id']; ?>">查看</a> 
            </td>
        </tr>
        <!-- ns add 添加发货单rcmtm的发货单显示 -->
        <tr style="display:none">
        	<td colspan="10" style="padding:0">
            	<div style="background: #eee;bdelivery-bottom: 2px solid #ccc;color: #000; line-height: 42px;padding: 0 10px;">rcmtm发货单号：<?php echo $this->_var['delivery']['r_delivery_id']; ?></div>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?> 
    </table>
    <div id="dataFuncs">
          <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
          <input class="formbtn" type="button" value="发货单打印" onclick="print()" />
    <!--       <input class="export_btn formbtn" data-type="all" type="button" value="导出检索发货单" /> -->
          <input class="export_btn formbtn" data-type="sel" type="button" value="导出所选发货单" />
          <input type="hidden" id="conditions" value="<?php echo $this->_var['conditions']; ?>">
      </div>
        <div class="pageLinks">
            <?php if ($this->_var['deliverys']): ?><?php echo $this->fetch('page.bottom.html'); ?><?php endif; ?>
        </div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">

/**
 * 鼠标悬停显示TITLE
 * @params     obj        当前悬停的标签
 *
 */
function titleMouseOver(obj) {
	$(obj).tipso({
		useTitle: false
	});
}





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
	}else if(_type == 'sel'){
		var _condi = '&sinexporttype=sel';
		var ids='';
		 $(".checkitem:checked").each(function(){
		        //if($(this).attr("checked") == true){
		           ids += ids ? ","+$(this).val() : $(this).val();
		        //}
		    });

	    if(ids.length == 0){
	        return false;
	    }
	     _condi += '&id='+ids;
	}
	if(_type == 'all' || _type == 'sel')
		{
		window.open("index.php?app=delivery&act=export"+_condi);
		}else{
		window.open("index.php?app=delivery&act=figure_export"+_condi);	
		}
	
	
	return;
})

function print(){
    var ids='';
    $(":checkbox").each(function(){
        if($(this).attr("checked")){
        	alert($(this).val());
            ids += ids ? ","+$(this).val() : $(this).val();
        }
    });

    if(ids.length == 0){
        return false;
    }
    window.open("index.php?app=delivery&act=deliveryprint&id="+ids);
}
</script>
<?php echo $this->fetch('footer.html'); ?>
