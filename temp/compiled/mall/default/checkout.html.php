<?php echo $this->fetch('header-new.html'); ?>
<link href="/public/static/pc/css/orderinfo.css" rel="stylesheet">
<div class="main">
    <div class="orderInfo">
        <h1>填写并核对订单信息</h1>
        <form>
        <!-- 收货信息 -->
        <?php echo $this->fetch('cart/order/shipping.html'); ?>
        
        <!-- 发票信息 -->
        <?php echo $this->fetch('cart/order/invoice.html'); ?>

	   <!-- 支付信息 -->
	    <?php echo $this->fetch('cart/order/payment.html'); ?>
	    
	    <!-- 商品清单 -->
        <?php echo $this->fetch('cart/order/goods.html'); ?>
        <div class="list_box">
			<!-- 抵扣信息 -->
			<?php echo $this->fetch('cart/order/deduction.html'); ?>
			<div class="accordion clearfix">
				<div class="tit" style="padding-left:0; background:none;">订单备注</div>
				<div class="layer orderRemark" style="display:block">
					<textarea name="order[remark]"></textarea>
				</div>
			</div>
			<?php if ($this->_var['member']['pay_password']): ?>
			<div class="accordion clearfix paypassword">
				<p class="payPwd">支付密码：<input type="password" tip="支付密码" id="payPwd" name="dedu[payPwd]" /><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'safe')); ?>" class="blue">忘记支付密码？</a></p>
			</div>
			<?php else: ?>
			<?php endif; ?>
		</div>
	    <!-- 结算信息 -->
	    <?php echo $this->fetch('cart/order/total.info.html'); ?>
        </form>
    </div>
</div>
<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/global/my97date/wdatepicker.js"></script>
<script src="/public/global/luck/pc/luck.js"></script>
<script src="/public/static/pc/js/public.js"></script> 
<script src="/public/static/pc/js/orderinfo.js"></script> 
<script type="text/javascript" src="/public/global/jquery.form.js"></script>
<script>

cotteFn.orderInfo({
    createUrl: "<?php echo $this->build_url(array('app'=>'order','act'=>'create')); ?>",
    serverUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'getServer')); ?>",
    historyUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'getHistory')); ?>",
    addressDel : "<?php echo $this->build_url(array('app'=>'cart','act'=>'addressDrop')); ?>",
    delAddrUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'dropAddr')); ?>",
    getAddrUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'getAddress')); ?>",
    addressUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'address')); ?>",
    editAddrUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'uAddress')); ?>",
    paycenterUrl : "<?php echo $this->build_url(array('app'=>'order','act'=>'paycenter')); ?>",
    debitRuleUrl: "<?php echo $this->build_url(array('app'=>'cart','act'=>'debit_rules')); ?>",
    regionUrl: "<?php echo $this->build_url(array('app'=>'mlselection')); ?>",
    addressSaveUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'addressSave')); ?>",
    hasMoney:<?php echo $this->_var['member']['money']; ?>,
    hasCoin:<?php echo $this->_var['member']['coin']; ?>,
    canCoin:'<?php if ($this->_var['cart']['cloth_bi'] || $this->_var['member']['member_lv_id'] > 1): ?>1<?php else: ?>0<?php endif; ?>',
    userID:'<?php echo $this->_var['member']['user_id']; ?>',
    amount:<?php echo $this->_var['cart']['final_amount']; ?>,
	diy_price:<?php echo $this->_var['cart']['diy_price']; ?>,
	custom_price:<?php echo $this->_var['cart']['custom_price']; ?>,
    hasPayPwd:'<?php if ($this->_var['member']['pay_password']): ?>1<?php else: ?>0<?php endif; ?>',
    userAddr : '<?php echo $this->_var['member']['def_addr']; ?>',
    freeShipping : '<?php echo $this->_var['cart']['free_shipping']; ?>',
    weight : '<?php echo $this->_var['cart']['weight']; ?>'
});



</script>

<?php echo $this->fetch('footer-new.html'); ?>
