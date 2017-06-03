
<style type="text/css">
body,td {font-size:13px; height:28px;}
.spxj td {text-align:center; height:32px;  padding:0 10px;}
</style>

<?php $_from = $this->_var['orderArr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['order']):
?>
<table width="100%" cellpadding="1">
    <tr>
        <td colspan="2" style="text-align:center; font-size:24px; padding:10px 0;">Mfd.麦富迪商城</td>
    </tr>
    <tr>
        <td>订单编号：</td><td><?php echo $this->_var['order']['order_sn']; ?><!-- 订单号 --></td><td></td>
    </tr>
    <tr>
        <td width="10%">下单时间：</td><td><?php echo local_date("Y-m-d H:i:s",$this->_var['order']['add_time']); ?></td>
        <td width="10%">发货时间：</td><td><?php echo local_date("Y-m-d H:i:s",$this->_var['order']['ship_time']); ?></td>
    </tr>
</table>
<table width="100%" border="1" style="border-collapse:collapse;border-color:#000; margin:10px 0;" class="spxj">
    <tr align="center">
        <td bgcolor="#cccccc">商品名称  <!-- 商品名称 --></td>
        <td bgcolor="#cccccc">购买数量<!-- 商品数量 --></td>
        <td bgcolor="#cccccc">RCMTM凭单<!-- 商品数量 --></td>
    </tr>
    <?php $_from = $this->_var['order']['goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['goods']):
?>
    <tr>
        <td>&nbsp;<?php echo $this->_var['goods']['goods_name']; ?><!-- 商品名称 --></td>
        <td align="right"><?php echo $this->_var['goods']['quantity']; ?>&nbsp;<!-- 商品数量 --></td>
        <td align="right"><?php echo $this->_var['goods']['rcmtm_id']; ?>&nbsp;<!-- 商品数量 --></td>
    </tr>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>
<table width="100%" cellpadding="1">
    <tr>
        <td width="10%">用户名：</td><td><?php echo $this->_var['order']['user_name']; ?></td><td></td>
    </tr>
    <tr>
        <td width="10%">收货人：</td><td><?php echo $this->_var['order']['ship_name']; ?></td><td></td>
    </tr>
     <?php if ($this->_var['order']['shipping_id'] == 2): ?>
    <tr>
        <td>门店电话：</td><td><?php echo $this->_var['order']['self']['mobile']; ?></td>
        <td></td>
    </tr>
     <tr>
        <td width="10%">门店地址：</td><td><?php if ($this->_var['order']['self']['region_name']): ?>[<?php echo $this->_var['order']['self']['region_name']; ?>]<?php endif; ?> <?php echo $this->_var['order']['self']['serve_address']; ?></td><td></td>
    </tr>
    <?php else: ?>
     <tr>
        <td width="10%">联系电话：</td><td><?php echo $this->_var['order']['ship_mobile']; ?></td><td></td>
    </tr>
     <tr>
        <td>收货地址：</td><td>[<?php echo $this->_var['order']['ship_area']; ?>]&nbsp;<?php echo $this->_var['order']['ship_addr']; ?>&nbsp;<!-- 收货人地址 --></td>
     </tr>
    <?php endif; ?>
    <tr>
        <td></td><td align="right">谢谢惠顾，欢迎再次光临</td>
     </tr>
</table>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>