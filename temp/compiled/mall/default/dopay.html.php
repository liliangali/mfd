<h3>正在连接支付网关, 请稍等...</h3>
<form action="<?php echo $this->_var['payform']['gateway']; ?>" id="payform" method="<?php echo $this->_var['payform']['method']; ?>" style="display:none">
<?php $_from = $this->_var['payform']['params']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('_k', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['_k'] => $this->_var['value']):
?>
  <input type="hidden" name="<?php echo $this->_var['_k']; ?>" value="<?php echo $this->_var['value']; ?>" />
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</form>
<script type="text/javascript">
    document.getElementById('payform').submit();
</script>