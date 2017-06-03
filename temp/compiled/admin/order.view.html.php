<?php echo $this->fetch('header.html'); ?>
<link href="templates/jquery-tipso/css/tipso.min.css" type="text/css" rel="stylesheet"/>
<script src="templates/jquery-tipso/js/jquery-1.8.3.min.js"></script>
<script src="templates/jquery-tipso/js/tipso.min.js"></script>
<div id="rightTop">
    <p><b>订单详情</b></p>
    <p><b>&nbsp;&nbsp;<a href="<?php if ($this->_var['reUrl']): ?><?php echo $this->_var['reUrl']; ?><?php else: ?>index.php?app=order&amp;act=export<?php endif; ?>">返回</a></b></p>
</div>
<div class="info">
    <div class="demand">
    </div>
    <div class="order_form">
        <h1>订单日志</h1>
        <ul style="width: 500px;">
            <?php $_from = $this->_var['order_log_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('lovk', 'log');if (count($_from)):
    foreach ($_from AS $this->_var['lovk'] => $this->_var['log']):
?>
                <li style="float: left;width: 500px;" data-tipso="<?php echo $this->_var['log']['remark']; ?>"  <?php if ($this->_var['log']['remark']): ?>onmouseover="titleMouseOver(this);"<?php endif; ?> ">(<?php echo local_date("Y/m/d H:i",$this->_var['log']['alttime']); ?>)&nbsp;&nbsp;<span class="red_common"><?php echo $this->_var['log']['log_text']; ?></span>&nbsp;&nbsp;操作人:&nbsp;<span class="red_common"><?php echo $this->_var['log']['op_name']; ?></span></li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
        <div class="clear"></div>
    </div>
        <div class="order_form">
        <h1>订单状态</h1>
        <ul>
            <li>订单号 ：<?php echo $this->_var['order']['order_sn']; ?></li>
            <li>订单状态 ：
            <?php if ($this->_var['order']['extension'] == 'fabricbook'): ?>
            <?php echo $this->_var['lang']['fabricBookOrderStatus'][$this->_var['order']['status']]; ?>
            <?php else: ?>
            <?php echo $this->_var['lang']['ORDER_STATUS'][$this->_var['order']['status']]; ?>
            <?php endif; ?>
            <?php if ($this->_var['order']['express']): ?> - 快递单号：<?php echo $this->_var['order']['express']; ?><?php endif; ?>
			<?php if ($this->_var['order']['orderrefund_id']): ?> <a href="index.php?app=orderrefund&amp;act=view&amp;id=<?php echo $this->_var['order']['orderrefund_id']; ?>">查看退款详情></a><?php endif; ?>
			</li>



            <?php if ($this->_var['order']['express']): ?> - 快递单号：<?php echo $this->_var['order']['express']; ?>  - 发货时间：<?php echo local_date("Y/m/d H:i",$this->_var['order']['ship_time']); ?>  - 更新帐号：<?php echo $this->_var['order']['deliver_name']; ?> <?php endif; ?></li>
            <li>订单总价 ：<span class="red_common"><?php echo price_format($this->_var['order']['order_amount']); ?></span></li>
            <li>零售总价 ：<span class="red_common"><?php echo price_format($this->_var['order']['goods_amount']); ?></span></li>
           <!--  <li>余额支付 ：<span class="red_common"><?php echo price_format($this->_var['order']['money_amount']); ?></span></li> -->
            <!-- <li>麦富迪币：<span class="red_common"><?php echo price_format($this->_var['order']['coin']); ?></span></li> -->
            <li>优惠券  ：<span class="red_common"><?php echo price_format($this->_var['order']['debit_amount']); ?></span>
            <?php if ($this->_var['dlist']): ?>
             (
              <?php $_from = $this->_var['dlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('dKey', 'dItem');if (count($_from)):
    foreach ($_from AS $this->_var['dKey'] => $this->_var['dItem']):
?>
              <?php echo $this->_var['dItem']['d_name']; ?>:<?php echo price_format($this->_var['dItem']['d_money']); ?>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              )
              <?php endif; ?>
            </li>
           <!--  <li>酷卡  ：<span class="red_common"><?php echo price_format($this->_var['order']['kuka_amount']); ?><br/></span>
            <?php if ($this->_var['kuka']): ?>
              <?php $_from = $this->_var['kuka']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('dKey', 'dItem');if (count($_from)):
    foreach ($_from AS $this->_var['dKey'] => $this->_var['dItem']):
?>
             编号：(<?php echo $this->_var['dItem']['k_sn']; ?>) 金额:<?php echo price_format($this->_var['dItem']['k_money']); ?> (<?php echo $this->_var['dItem']['is_line']; ?>)<br/>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              <?php endif; ?>
            </li> -->
            <li>[<?php echo $this->_var['order']['payment_name']; ?>]支付 ：<span class="red_common"><?php if ($this->_var['real']): ?><?php echo price_format($this->_var['order']['final_amount']); ?><?php else: ?><?php echo price_format($this->_var['real_amount']); ?><?php endif; ?></span></li>
			<?php if ($this->_var['has_delay']): ?>
			<li>收货延期 ：<span class="red_common">
				用户已申请延期收货，操作时间:<?php echo local_date("Y-m-d H:i:s",$this->_var['has_delay']['delay_time']); ?>
			</span></li>
			<?php endif; ?>

      
        </ul>
        <div class="clear"></div>
    </div>
    <?php if ($this->_var['order']['invoice_need']): ?>
    <div class="order_form">
        <h1>发票信息:</h1>
        <ul>
          <li>发票类型：<?php if ($this->_var['order']['invoice_com'] == '1'): ?>个人<?php else: ?>公司<?php endif; ?></li>
           <li>发票抬头：<?php echo $this->_var['order']['invoice_type']; ?></li>
           <?php if ($this->_var['order']['invoice_com'] == '3'): ?>
           <?php $_from = $this->_var['order']['invoice_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('taxKey', 'tax');if (count($_from)):
    foreach ($_from AS $this->_var['taxKey'] => $this->_var['tax']):
?>
           <li><?php echo $this->_var['invoice'][$this->_var['taxKey']]; ?> ：<?php echo $this->_var['tax']; ?></li>
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
           <?php else: ?><li>发票内容 ：<?php echo $this->_var['order']['invoice_title']; ?></li><?php endif; ?>
         </ul>
          <div class="clear"></div>
      </div>
    <?php endif; ?>
    <div class="order_form">
        <h1>收货信息:</h1>
        <ul>
        
          <li>收货人：<?php echo $this->_var['order']['ship_name']; ?></li>
           <li>收货人电话：<?php echo $this->_var['order_info']['ship_mobile']; ?></li>
           <li>收货地址 ：<?php echo $this->_var['order']['ship_area']; ?> <?php echo $this->_var['order']['ship_addr']; ?></li>

            <?php if ($this->_var['order']['extension'] != 'fabricbook'): ?>
            <?php if ($this->_var['order']['status'] == ORDER_ACCEPTED): ?><?php if ($this->_var['has_check'] == 'yes'): ?><li>审核订单 ：<a href="index.php?app=order&act=check&tp=go&id=<?php echo $this->_var['order']['order_id']; ?>"><span>通过</span></a> | <a href="index.php?app=order&act=check&tp=bc&id=<?php echo $this->_var['order']['order_id']; ?>"><span>不通过</span></a> | <a href="index.php?app=order&act=check&tp=no&id=<?php echo $this->_var['order']['order_id']; ?>">取消</a></li><?php endif; ?><?php endif; ?>
            <?php if ($this->_var['order']['status'] == ORDER_SHIPPED): ?><li>订单操作 ：<a href="index.php?app=order&act=check&tp=fini&id=<?php echo $this->_var['order']['order_id']; ?>"><span>完成</span></a> | <a href="index.php?app=order&act=check&tp=no&id=<?php echo $this->_var['order']['order_id']; ?>"><span>作废</span></a> <?php endif; ?>
            <?php if ($this->_var['order']['status'] == ORDER_PRODUCTION || $this->_var['order']['status'] == ORDER_STOCKING || $this->_var['order']['status'] == ORDER_ACCEPTED || $this->_var['order']['status'] == ORDER_SHIPPED): ?>
           <?php if ($this->_var['order']['waybillno']): ?>
		     <li style="color:red;font-weight:bold;">
            <!--快递：<span> <?php echo $this->_var['order']['shipping_name']; ?></span>  发货单号：<span><?php echo $this->_var['order']['waybillno']; ?> </span>-->

            <form action="index.php?app=order&act=opts" method="POST">
            <li style="color:red;font-weight:bold;">
                快递： <select name="kuaidi">
                <?php $_from = $this->_var['shiplist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'ship');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['ship']):
?>
                <option value="<?php echo $this->_var['key']; ?>" <?php if ($this->_var['key'] == $this->_var['order']['shipping_id']): ?> selected <?php endif; ?> ><?php echo $this->_var['ship']; ?></option>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </select>

                发货单号：<input type="text" name="waybillno" value="<?php echo $this->_var['order']['waybillno']; ?>" >
                <input type="submit" name="ship"  value="去发货">(该功能为人工发货使用,不懂慎用)
            </li>
            <input type="hidden" name="order_id" value="<?php echo $this->_var['order']['order_id']; ?>">
            </form>


			</li>
			<?php else: ?>
			  <form action="index.php?app=order&act=opts" method="POST">
            <li style="color:red;font-weight:bold;">
           快递： <select name="kuaidi">
           <?php $_from = $this->_var['shiplist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'ship');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['ship']):
?>
           <option value="<?php echo $this->_var['key']; ?>"><?php echo $this->_var['ship']; ?></option>
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </select>
     
                 发货单号：<input type="text" name="waybillno" value="<?php echo $this->_var['order']['waybillno']; ?>" >
                <input type="submit" name="ship"  value="去发货">(该功能为人工发货使用,不懂慎用)
            </li>
            <input type="hidden" name="order_id" value="<?php echo $this->_var['order']['order_id']; ?>">
            </form>
			<?php endif; ?>
 		
            <?php if (! $this->_var['order']['waybillno']): ?>
            <form action="index.php?app=order&act=edians" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $this->_var['order']['order_id']; ?>">
             <input type="submit" name="edian"  value="推送e店宝">（该功能为手动推送e店宝，不懂慎用）
             </form>
     
            <?php endif; ?>
            <?php endif; ?>
            <?php else: ?>
            <form action="index.php?app=order&act=opt" method="POST">
                <?php if ($this->_var['operation']): ?>
                <li style="color:red;font-weight:bold;">客服操作：
                    <?php if ($this->_var['operation']['cancel']): ?>
                    <input type="submit" name="cancel" value="取消订单">
                    <?php endif; ?>
                    <?php if ($this->_var['operation']['returned']): ?>
                    <input type="submit" name="returned" value="已退货">
                    <?php endif; ?>
                    <?php if ($this->_var['operation']['ship']): ?>
                    发货单号：<input type="text" name="express" value="">
                    <input type="submit" name="ship" value="去发货">
                    <?php endif; ?>
                </li>
                <input type="hidden" name="order_id" value="<?php echo $this->_var['order']['order_id']; ?>">
            </form>
            <?php endif; ?>
            <?php endif; ?>
        </ul>
        <div class="clear"></div>
    </div>
    <div class="order_form">
        <h1>订单详情</h1>
        <ul>
            <li>用户名 ：<?php echo htmlspecialchars($this->_var['order']['user_name']); ?></li>
            <?php if ($this->_var['order']['payment_code']): ?>
            <li>支付方式 ：<?php echo htmlspecialchars($this->_var['order']['payment_name']); ?></li>
            <?php endif; ?>
            <?php if ($this->_var['order']['pay_message']): ?>
            <li>支付留言  ：<?php echo htmlspecialchars($this->_var['order']['pay_message']); ?></li>
            <?php endif; ?>
            <li>下单时间 ：<?php echo local_date("Y-m-d H:i:s",$this->_var['order']['add_time']); ?></li>
            <?php if ($this->_var['order']['pay_time']): ?>
            <li>支付时间 ：<?php echo local_date("Y-m-d H:i:s",$this->_var['order']['pay_time']); ?></li>
            <?php endif; ?>
            <?php if ($this->_var['order']['shipping_id']): ?>
            <li>配送方式 ：<?php echo $this->_var['order']['shipping']; ?></li>
            <?php endif; ?>
            <?php if ($this->_var['order']['one_num']): ?>
            <li>员工号：<?php echo $this->_var['order']['one_num']; ?></li>
            <?php endif; ?>
            <li>类型：
            <?php if ($this->_var['order']['is_gift'] == 1): ?>
            礼品
            <?php else: ?>
            普通
            <?php endif; ?>
            </li>
            <?php if ($this->_var['order']['memo']): ?>
            <li>订单备注 ：<?php echo $this->_var['order']['memo']; ?></li>
            <?php endif; ?>
        </ul>
        <div class="clear"></div>
    </div>
  <div class="order_form">
        <h1>商品信息</h1>
<?php $_from = $this->_var['order_info']['order_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
   <!-- <li><?php echo $this->_var['lang'][$this->_var['emb']['e_tname']]; ?> ：<?php echo $this->_var['emb']['e_name']; ?></li> -->
		<img src="<?php echo $this->_var['list']['goods_image']; ?>" width="60" />
		<ul>
		<li>名称 ： <?php echo $this->_var['list']['goods_name']; ?></li>
            <li>

                上传标签图:<input type="file" name="mesimg" data-orderid="<?php echo $this->_var['list']['rec_id']; ?>" onchange="readFile(this)">
          标签图 ：
                <a href="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['list']['style']; ?>" target="_blank" class="file_a file_ass<?php echo $this->_var['list']['rec_id']; ?>" <?php if ($this->_var['list']['style']): ?> <?php else: ?>style="display:none"<?php endif; ?>>
                    <img src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['list']['style']; ?>" width="100" height="100" class="file_img file_imgss<?php echo $this->_var['list']['rec_id']; ?>">
                </a>
            </li>
        <li>类型： <?php if ($this->_var['list']['type'] == 'fdiy'): ?>diy商品<?php else: ?>普通<?php endif; ?></li>
            <li>物料编码 ： <?php echo $this->_var['list']['code']; ?></li>
		<li>属性 ： <?php if ($this->_var['list']['type'] == 'fdiy'): ?><?php echo $this->_var['list']['spe_name']; ?><?php else: ?>
            <?php $_from = $this->_var['list']['spe_name']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
            <?php echo $this->_var['item']['p_name']; ?>:<?php echo $this->_var['item']['s_name']; ?>&nbsp;
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php endif; ?></li>
        <li>单价 ： <?php echo $this->_var['list']['price']; ?></li>
		<li>数量 ： <?php echo $this->_var['list']['quantity']; ?></li>
		<li>小计 ： <?php echo $this->_var['list']['subtotal']; ?></li>
		<?php if ($this->_var['list']['fav_price']): ?>
		<li>优惠方案 ： <?php echo $this->_var['list']['fav_name']; ?></li>
		<li>优惠金额 ： <?php echo $this->_var['list']['fav_price']; ?></li>
		<?php endif; ?>
            <?php if ($this->_var['list']['type'] == 'fdiy'): ?>
            <li>宠物名称 ： <?php echo $this->_var['list']['dog_name']; ?></li>
            <li>宠物生日 ： <?php echo $this->_var['list']['dog_date']; ?></li>
            <li>主人寄语 ： <?php echo $this->_var['list']['dog_desc']; ?></li>
            <li>时间 ： <?php echo $this->_var['list']['time_name']; ?></li>
            <li>运动量 ： <?php echo $this->_var['list']['run_name']; ?></li>
            <li>体况 ： <?php echo $this->_var['list']['body_name']; ?></li>
            <li>体重 ： <?php echo $this->_var['list']['weight']; ?></li>
            <li>哺乳小狗数目 ： <?php echo $this->_var['list']['dog_nums']; ?></li>
            <?php if ($this->_var['list']['feed_list']): ?>
            <li>饲喂量 ： <?php echo $this->_var['list']['feed_list']['feed_w']; ?>g/天</li>
            <li>次数 ： <?php echo $this->_var['list']['feed_list']['nums']; ?></li>
            <?php endif; ?>
            <?php endif; ?>
		</ul>
        <div class="clear"></div>
         <hr>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<h1>赠品信息-常规商品</h1>
<?php $_from = $this->_var['order']['c_giveaway_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'c_giveaway');if (count($_from)):
    foreach ($_from AS $this->_var['c_giveaway']):
?>
<ul>
<li>名称 ： <?php echo $this->_var['c_giveaway']['name']; ?></li>
<li>货号 ： <?php echo $this->_var['c_giveaway']['bn']; ?></li>
<li>成本价格 ： <?php echo $this->_var['c_giveaway']['mktprice']; ?></li>
<li>最低消费 ： <?php echo $this->_var['c_giveaway']['l_money']; ?></li>
<li>最高消费 ： <?php echo $this->_var['c_giveaway']['h_money']; ?></li>
</ul>
<div class="clear"></div>
<hr>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<h1>赠品信息-赠品商品</h1>
<?php $_from = $this->_var['order']['f_giveaway_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'f_giveaway');if (count($_from)):
    foreach ($_from AS $this->_var['f_giveaway']):
?>
<ul>
<li>名称 ： <?php echo $this->_var['f_giveaway']['name']; ?></li>
<li>货号 ： <?php echo $this->_var['f_giveaway']['bn']; ?></li>
<li>成本价格 ： <?php echo $this->_var['f_giveaway']['mktprice']; ?></li>
<li>最低消费 ： <?php echo $this->_var['f_giveaway']['l_money']; ?></li>
<li>最高消费 ： <?php echo $this->_var['f_giveaway']['h_money']; ?></li>
</ul>
<div class="clear"></div>
<hr>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<h1>赠品信息-礼包</h1>
<?php $_from = $this->_var['order']['g_giveaway_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'g_giveaway');if (count($_from)):
    foreach ($_from AS $this->_var['g_giveaway']):
?>
<ul>
<li>名称 ： <?php echo $this->_var['g_giveaway']['name']; ?>&nbsp;<b style="cursor: pointer;color:#ff5400" title='<?php echo $this->_var['g_giveaway']['title_list']['title']; ?>'>[详]</b></li>
<li>成本价格 ： <?php echo $this->_var['g_giveaway']['price']; ?></li>
<li>最低消费 ： <?php echo $this->_var['g_giveaway']['l_money']; ?></li>
<li>最高消费 ： <?php echo $this->_var['g_giveaway']['h_money']; ?></li>
</ul>
<div class="clear"></div>
<hr>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>


<?php if ($this->_var['order']['status'] == '20'): ?>
     <form method="post" enctype="multipart/form-data" action="index.php?app=order&act=view_img" id="dis_form">
           <div>
                <li >
                    标签图审核:
               
                    <select name="status">
					<option value="0" <?php if ($this->_var['order']['img_status'] == '0'): ?>selected<?php endif; ?>>未审核</option>
					<option value="1" <?php if ($this->_var['order']['img_status'] == '1'): ?>selected<?php endif; ?>>审核成功</option>
					<option value="2" <?php if ($this->_var['order']['img_status'] == '2'): ?>selected<?php endif; ?>>审核失败</option>
					</select>
                </li>
            </div> 
			 <input class="tijia" type="submit" name="Submit" value="提交" />
                <input type="hidden" name="order_id" value="<?php echo $this->_var['order']['order_id']; ?>"/>
    </form>
<?php endif; ?>
    </div>
</div>
<script>
    function readFile(obj)
    {

        var file = obj.files[0];
        var order_goods_id = $(obj).attr("data-orderid");
        //判断类型是不是图片
        if(!/image\/\w+/.test(file.type)){
            alert("请确保文件为图像类型");
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(e){


            $.post("index.php?app=order&act=uploadimg", {id: order_goods_id, upimg:this.result},
                function(data)
                {
                    $('.file_ass'+order_goods_id).css('display',"");
                    $('.file_imgss'+order_goods_id).attr('src',data);
                    $('.file_ass'+order_goods_id).attr('href',data);
                }
            );

            $(obj).next().val(this.result)
        };





    }

</script>



<?php echo $this->fetch('footer.html'); ?>
<script>
    /**
     * 鼠标悬停显示TITLE
     * @params     obj        当前悬停的标签
     *
     */
    function titleMouseOver(obj) {
        $(obj).tipso({
            useTitle: false,
            width:500,
            background:'#55b555'
        });
    }
</script>
