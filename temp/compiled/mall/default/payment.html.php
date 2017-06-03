        <div class="mt clearfix">
            <h2>支付方式</h2>
            <ul class="tabBtn clearfix" id="payMethod">
                <?php if ($this->_var['payments']): ?>
                <?php $_from = $this->_var['payments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pmt');$this->_foreach['npmt'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['npmt']['total'] > 0):
    foreach ($_from AS $this->_var['pmt']):
        $this->_foreach['npmt']['iteration']++;
?>
                <li data-id="<?php echo $this->_var['pmt']['payment_id']; ?>" data-code="<?php echo $this->_var['pmt']['payment_code']; ?>" <?php if (($this->_foreach['npmt']['iteration'] <= 1)): ?> class="cur" <?php endif; ?>><?php echo $this->_var['pmt']['payment_name']; ?></li>
                <?php if (($this->_foreach['npmt']['iteration'] <= 1)): ?> 
                <input type="hidden" name="order[payment][id]" id="paymentType" value="<?php echo $this->_var['pmt']['payment_id']; ?>" />
                <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php else: ?>
                <input type="hidden" name="order[payment][id]" id="paymentType" value="" />
                <?php endif; ?>
            </ul>
        </div>