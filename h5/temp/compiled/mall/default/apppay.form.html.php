
<form action="<?php echo $this->build_url(array('app'=>'paycenter','act'=>'goToPay')); ?>" id="payform" method="post">
	<input type="hidden" name="obj" value="order" />
	<input type="hidden" name="os" value="<?php echo $this->_var['orderInfo']['order_sn']; ?>" />
</form>
<script type="text/javascript">
	document.getElementById('payform').submit();
</script>
