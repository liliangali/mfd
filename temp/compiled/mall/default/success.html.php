<?php echo $this->fetch('/header-new.html'); ?>
<link href="/public/static/pc/css/gwc.css" type="text/css" rel="stylesheet">

<div class="ddzfcg ddtjcg">
 <h1>您的订单已支付成功！</h1>
 <p class="p1"> 支付金额：<?php echo $this->_var['order']['final_amount']; ?>元</p>
 <p class="p3">订单编号：<?php echo $this->_var['order']['order_sn']; ?></p>
 <?php if ($this->_var['order']['extension'] == 'fabricbook'): ?>
	<div class="ljzfqt"><a href="./fabricbook-applyRecord.html" class="a3 fl">查看订单</a><a href="./fabricbook.html" class="a4 fl">继续购物</a></div>
 <?php else: ?>
	 <div class="ljzfqt"><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'detail','arg0'=>$this->_var['order']['order_id'])); ?>" class="a3 fl">查看订单</a><a href="/" class="a4 fl">继续购物</a></div>
 <?php endif; ?>
</div>

<?php echo $this->fetch('cart/footer.html'); ?>







