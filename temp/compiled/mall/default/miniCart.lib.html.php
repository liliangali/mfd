<i class="arrow"></i>
<?php if ($this->_var['result']['goods_num'] != 0): ?>
<ul>
<?php $_from = $this->_var['result']['object']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['goods']):
?>
    <li>
        <p class="fl pic"><a href="<?php if ($this->_var['goods']['goods']['goods_id'] > 0): ?><?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['goods']['goods']['goods_id'])); ?><?php else: ?><?php echo $this->_var['goods']['goods']['goods_url']; ?><?php endif; ?>"><img src="<?php echo $this->_var['goods']['goods']['image']; ?>" width="38" height="50"></a></p>
        <div class="fl lhwwbd">
         <p class="name"><?php if ($this->_var['goods']['goods']['goods_id'] > 0): ?><a href="<?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['goods']['goods']['goods_id'])); ?>"><?php echo $this->_var['goods']['goods']['name']; ?></a><?php else: ?><?php echo $this->_var['goods']['goods']['name']; ?><?php endif; ?></p>
         <p class="price"><span><?php echo price_format($this->_var['goods']['goods']['basePrice']); ?></span> x <span><?php echo $this->_var['goods']['goods']['quantity']; ?></span></p>
        </div>
        <input class="close" type="button" value="×" data-type="<?php echo $this->_var['goods']['type']; ?>" data-id="<?php echo $this->_var['key']; ?>">
    </li>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</ul>
<div class="cartInfo">
    <div class="fl left">
        <p class="grey">共计<span id="miniCartNumber"><?php echo $this->_var['result']['goods_num']; ?></span>件商品</p>
        <p>合计：<span class="orange" id="miniCartAmount"><?php echo price_format($this->_var['result']['goods_amount']); ?></span></p>
    </div>
    <div class="fr right"> <a href="<?php echo $this->build_url(array('app'=>'cart')); ?>" class="gopay">去结算</a> </div>
</div>
<?php else: ?>
<p class="kgwc">购物车中还没有商品，赶紧选购吧！</p>
<?php endif; ?>
