
	<?php echo $this->fetch('header.html'); ?>
<link href="templates/jquery-tipso/css/tipso.min.css" type="text/css" rel="stylesheet"/>
<script src="templates/jquery-tipso/js/jquery-1.8.3.min.js"></script>
<script src="templates/jquery-tipso/js/tipso.min.js"></script>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=order&amp;act=export">技术导出</a></li>
        <li><a class="btn1" href="index.php?app=order&amp;act=exportt">财务导出</a></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get">
             <div class="left">
                <input type="hidden" name="app" value="order" />
                <input type="hidden" name="act" value="index" />
                <select class="querySelect" name="field"><?php echo $this->html_options(array('options'=>$this->_var['search_options'],'selected'=>$_GET['field'])); ?>
                </select><input class="queryInput" type="text" name="search_name" value="<?php echo htmlspecialchars($this->_var['query']['search_name']); ?>" />
                  <!--  <select class="querySelect" name="has_measure"><?php echo $this->html_options(array('options'=>$this->_var['body_options'],'selected'=>$_GET['has_measure'])); ?></select> -->

                 <?php $_from = $this->_var['lang']['ORDER_STATUS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
                 <input type="checkbox" name="status[]" class="order_status" value="<?php echo $this->_var['key']; ?>" <?php if (in_array ( $this->_var['key'] , $this->_var['query']['status'] )): ?>checked<?php endif; ?>/>:<?php echo $this->_var['item']; ?>
                 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>


                 <select class="querySelect" name="mes_status">
                     <option value="-1">mes推送状态</option>
                     <?php echo $this->html_options(array('options'=>$this->_var['mes_status_list'],'selected'=>$this->_var['query']['mes_status'])); ?>
                 </select>
                下单时间从:<input class="queryInput2 Wdate" type="text" value="<?php echo $this->_var['query']['add_time_from']; ?>" style="width:150px" id="add_time_from" name="add_time_from" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
                至：<input class="queryInput2 Wdate" type="text" value="<?php echo $this->_var['query']['add_time_to']; ?>" style="width:150px" id="add_time_to" name="add_time_to" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" />
            <!--     发货时间从：<input class="queryInput2 Wdate" type="text" value="<?php echo $this->_var['query']['add_time_go']; ?>" style="width:150px" id="add_time_go" name="add_time_go" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
                                                至：<input class="queryInput2 Wdate" type="text" value="<?php echo $this->_var['query']['add_time_do']; ?>" style="width:150px" id="add_time_do" name="add_time_do" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" /> -->
                订单金额从：<input class="queryInput2" type="text" value="<?php echo $this->_var['query']['order_amount_from']; ?>" name="order_amount_from" />
                至:<input class="queryInput2" type="text" style="width:60px;" value="<?php echo $this->_var['query']['order_amount_to']; ?>" name="order_amount_to" class="pick_date" />
                图片审核：
                <select class="querySelect" name="img_status">
                     <option value="0">全部状态</option>
                     <option <?php if ($this->_var['query']['img_status'] === 0): ?>selected<?php endif; ?> value="1">未审核</option>
                     <option <?php if ($this->_var['query']['img_status'] === 1): ?>selected<?php endif; ?> value="2">已审核</option>
                     <option <?php if ($this->_var['query']['img_status'] === 2): ?>selected<?php endif; ?> value="3">审核失败</option>
                </select>
                赠品状态：
                <select class="querySelect" name="is_giveaway">
                     <option value="0">全部状态</option>
                     <option <?php if ($this->_var['query']['is_giveaway'] === 0): ?>selected<?php endif; ?> value="1">未搭配</option>
                     <option <?php if ($this->_var['query']['is_giveaway'] === 1): ?>selected<?php endif; ?> value="2">已搭配</option>
                </select>


                <input type="submit" class="formbtn" value="查询" />
            </div>
            <?php if ($this->_var['filtered']): ?>
            <a class="left formbtn1" href="index.php?app=order">撤销检索</a>
            <?php endif; ?>
			
			<a href="<?php echo $this->_var['siteurl']; ?>"><input type="button" value="手动更新物流信息"/></a>
        </form>
    </div>
    <div class="fontr">
        <?php if ($this->_var['orders']): ?><?php echo $this->fetch('page.top.html'); ?><?php endif; ?>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['orders']): ?>
        <tr class="tatr1">
                    <td ><input type="checkbox" class="checkall" /></td>
            <td width="10%"><span ectype="order_by" fieldname="order_sn">订单号</span></td>
           <!--  <td width="1%"><span ectype="order_by" fieldname="order_sn">rcmtm订单号|rcmtm订单状态</span></td> -->

            <td width="15%"><span ectype="order_by" fieldname="add_time">下单时间</span></td>
            
            <td width="5%"><span ectype="order_by" fieldname="">用户名</span></td>
            <td width="8%">收货人姓名</td>
            <td width="7%"><span ectype="order_by" fieldname="order_amount">订单金额</span></td>
            <!-- <td width="7%">交货日期</td> -->
            <td width="7%">发货时间</td>
            <!--  <td width="7%">运单号|物流单号</td> -->
            <td width="7%"><span ectype="order_by" fieldname="status">订单状态</span></td>
           <!--  <td width="7%">品类</td> -->
            <td width="7%">订单来源</td>
            <td width="7%">订单类型</td>
			<td width="7%">图片审核</td>
            <td width="7%">推送结果</td>
            <td>赠品状态</td>
            <td width="47%">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['order']):
?>
        <tr class="tatr2"  >
            <td ><input type="checkbox" class="checkitem" value="<?php echo $this->_var['order']['order_id']; ?>" /></td>
            <td><?php echo $this->_var['order']['order_sn']; ?> </td>
            <!-- <td><input type="button" class="showInfo" value="查看详情"></td> -->

           <!--  <td><?php if ($this->_var['order']['mtm_ids']): ?><?php $_from = $this->_var['order']['mtm_ids']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'mids');if (count($_from)):
    foreach ($_from AS $this->_var['mids']):
?><?php echo $this->_var['mids']; ?>&nbsp|&nbsp<?php echo $this->_var['status_arr'][$this->_var['mids']]; ?><br><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?><?php endif; ?></td> -->

            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['order']['add_time']); ?></td>
            <td><?php echo htmlspecialchars($this->_var['order']['user_name']); ?></td>
            <td><?php echo htmlspecialchars($this->_var['order']['ship_name']); ?></td>
            <td><?php echo price_format($this->_var['order']['order_amount']); ?></td>
           <!--  <td><?php echo $this->_var['order']['mtm_delivery_date']; ?></td> -->
            <td><?php echo $this->_var['order']['mtm_delivery_date']; ?></td>
           <!--  <td>(<?php echo ($this->_var['order']['waybillno'] == '') ? '-' : $this->_var['order']['waybillno']; ?>) <br> (<?php echo ($this->_var['order']['express'] == '') ? '-' : $this->_var['order']['express']; ?>)</td> -->
            <td data-tipso="<?php echo $this->_var['order']['push_error']; ?>"  <?php if ($this->_var['order']['push_error']): ?>onmouseover="titleMouseOver(this);"<?php endif; ?> ><?php if ($this->_var['order']['extension'] == 'fabricbook'): ?><?php echo $this->_var['lang']['fabricBookOrderStatus'][$this->_var['order']['status']]; ?><?php else: ?><?php echo ($this->_var['lang']['ORDER_STATUS'][$this->_var['order']['status']] == '') ? '-' : $this->_var['lang']['ORDER_STATUS'][$this->_var['order']['status']]; ?><?php endif; ?> <?php if ($this->_var['lang']['ORDER_STATUS'][$this->_var['order']['status']] == '待量体'): ?> <?php if ($this->_var['dqtime'] > $this->_var['order']['y_time'] + 3600 * 24): ?><font color="#FF0000">超时</font><?php endif; ?><?php endif; ?><?php if ($this->_var['order']['if_fx'] == 1): ?><font color="#FF0000">(返修中)</font><?php endif; ?></td>
          <!--   <td><?php if ($this->_var['order']['order_pinlei']): ?><?php $_from = $this->_var['order']['order_pinlei']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?><?php echo $this->_var['item']; ?><br><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?><?php endif; ?></td> -->
            <td><?php echo $this->_var['order']['source_from']; ?></td>
            <td><?php if ($this->_var['order']['tps'] == 'fdiy'): ?>DIY产品<?php elseif ($this->_var['order']['tps'] == 'ftwo'): ?>混合产品<?php elseif ($this->_var['order']['tps'] == 'fpu'): ?>常规产品<?php endif; ?></td>
			<td><?php if ($this->_var['order']['tps'] == 'fdiy' || $this->_var['order']['tps'] == 'ftwo'): ?><?php if ($this->_var['order']['img_status'] == '0'): ?>未审核<?php elseif ($this->_var['order']['img_status'] == '1'): ?>审核成功<?php elseif ($this->_var['order']['img_status'] == '2'): ?>审核失败<?php endif; ?><?php endif; ?></td>
            <td>
                <?php if ($this->_var['order']['mes_status'] == 0): ?>尚未推送<?php elseif ($this->_var['order']['mes_status'] == 1): ?>推送成功<?php elseif ($this->_var['order']['mes_status'] == 2): ?>推送失败<?php echo $this->_var['order']['mes_res']; ?><?php endif; ?>
               
               </td>
            <td>
                <?php if ($this->_var['order']['is_giveaway'] == 0): ?><b style="color:#398bfb">未搭配</b><?php elseif ($this->_var['order']['is_giveaway'] == 1): ?>已搭配<?php endif; ?>
               </td>


            <td>
             
         
                <a href="index.php?app=order&amp;act=view&amp;id=<?php echo $this->_var['order']['order_id']; ?>">查看</a> 
                <?php if ($this->_var['order']['status'] == 11): ?><a href="index.php?app=order&act=editAddress&id=<?php echo $this->_var['order']['order_id']; ?>&cur_page=<?php echo $this->_var['page_info']['curr_page']; ?>">|订单编辑</a><?php endif; ?>
				<?php if ($this->_var['order']['status'] == 40): ?><?php endif; ?> 

                <?php if ($this->_var['order']['status'] == 30 || $this->_var['order']['status'] == 40 || $this->_var['order']['status'] == 0): ?><a href="index.php?app=order&act=viewDelivery&id=<?php echo $this->_var['order']['order_id']; ?>">|物流信息</a><?php endif; ?>
                <?php if ($this->_var['order']['status'] == 11 || $this->_var['order']['status'] == 20 || $this->_var['order']['status'] == 61 || $this->_var['order']['status'] == 30): ?><a href="index.php?app=order&act=updateOrder&id=<?php echo $this->_var['order']['order_id']; ?>&cur_page=<?php echo $this->_var['page_info']['curr_page']; ?>&by=id_40">|完成</a><?php endif; ?>
                <?php if ($this->_var['order']['status'] == 11): ?><a href="index.php?app=order&act=updateOrder&id=<?php echo $this->_var['order']['order_id']; ?>&cur_page=<?php echo $this->_var['page_info']['curr_page']; ?>&by=id_0">|作废</a><?php endif; ?>
                <?php if ($this->_var['order']['status'] == 11 || $this->_var['order']['status'] == 20 || $this->_var['order']['status'] == 61 || $this->_var['order']['status'] == 30): ?><a href="index.php?app=order&act=updateOrder&id=<?php echo $this->_var['order']['order_id']; ?>&cur_page=<?php echo $this->_var['page_info']['curr_page']; ?>&by=id_99">|订单备注</a><?php endif; ?>
                <?php if ($this->_var['order']['type'] == 'fdiy'): ?>
                <?php if ($this->_var['order']['mes_status'] != 1 && $this->_var['order']['status'] == 20): ?>
                |<a href="index.php?app=order&amp;act=mesf&amp;id=<?php echo $this->_var['order']['order_id']; ?>">推送</a>
                <?php endif; ?>
                <?php else: ?>
                <!--非diy订单-->
                <?php endif; ?>
                
                <?php if ($this->_var['order']['mes_status'] != 1): ?>
                |
                <a href="index.php?app=order&amp;act=giveaway&amp;id=<?php echo $this->_var['order']['order_id']; ?>">赠品添加</a>
                |
                <a style="font-weight:bold;color:red" href="index.php?app=order&amp;act=newMesf&amp;id=<?php echo $this->_var['order']['order_id']; ?>">推送</a>
                <?php endif; ?>


            </td>
        </tr>
        <!-- ns add 添加订单rcmtm的订单显示 -->
        <tr style="display:none">
        	<td colspan="10" style="padding:0">
            	<div style="background: #eee;border-bottom: 2px solid #ccc;color: #000; line-height: 42px;padding: 0 10px;">rcmtm订单号：<?php echo $this->_var['order']['r_order_id']; ?></div>
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
          <input class="formbtn" type="button" value="订单打印" onclick="print()" />
          <input class="export_btn formbtn" data-type="all" type="button" value="技术导出检索订单" />
          <input class="export_btn formbtn" data-type="sel" type="button" value="技术导出所选订单" />
              <input class="exportt_btn formbtn" data-type="all" type="button" value="财务导出检索订单" />
              <input class="exportt_btn formbtn" data-type="sel" type="button" value="财务导出所选订单" />
          <input type="hidden" id="conditions" value="<?php echo $this->_var['conditions']; ?>">
      </div>

        <div class="pageLinks">
            <?php if ($this->_var['orders']): ?><?php echo $this->fetch('page.bottom.html'); ?><?php endif; ?>
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
	        if($(this).val() && $(this).attr('name') ){
	            if($(this).attr('name') != 'app' && $(this).attr('name') != 'act' && !$(this).hasClass("order_status")){
	                  _condi += '&'+$(this).attr('name')+'='+$(this).val();
	            }
                if($(this).hasClass("order_status") && $(this).is(':checked')){
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
	     _condi += '&ids='+ids;
	}
	if(_type == 'all' || _type == 'sel')
		{
		window.open("index.php?app=order&act=export"+_condi);
		}else{
		window.open("index.php?app=order&act=figure_export"+_condi);	
		}
	
	
	return;
})


$('.exportt_btn').unbind().bind('click',function(){
    var _type = $(this).attr('data-type');
    if(_type == 'all'){
        var _condi = '&sinexporttype=all';
        $('.mrightTop').find('.fontl').find('input').each(function(){
            if($(this).val() && $(this).attr('name') ){
                if($(this).attr('name') != 'app' && $(this).attr('name') != 'act' && !$(this).hasClass("order_status")){
                    _condi += '&'+$(this).attr('name')+'='+$(this).val();
                }
                if($(this).hasClass("order_status") && $(this).is(':checked')){
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
        _condi += '&ids='+ids;
    }
    if(_type == 'all' || _type == 'sel')
    {
        window.open("index.php?app=order&act=exportt"+_condi);
    }else{
        window.open("index.php?app=order&act=figure_exportt"+_condi);
    }


    return;
})


function print(){
    var ids='';
    $(".checkitem").each(function(){
        if($(this).attr("checked")){
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
