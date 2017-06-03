<div class="user_nav fl">
<div class="yhcolumn">
<h4>我的账户</h4> 
<ul>
<li <?php if ($this->_var['app'] == 'member' && $this->_var['ac'] == 'index'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member')); ?>">账户预览</a>
<li <?php if ($this->_var['app'] == 'my_order' || $this->_var['app'] == 'fx'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'my_order')); ?>">我的订单</a></li>
<li <?php if ($this->_var['app'] == 'my_comment'): ?> class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'my_comment')); ?>">我的评论</a></li>
<li <?php if ($this->_var['app'] == 'my_favorite'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'my_favorite')); ?>">我的收藏</a></li>
<li <?php if ($this->_var['app'] == 'my_messages'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'my_messages')); ?>">我的消息</a></li>
</ul>
<h4>我的财富</h4>
<ul>
<!-- <li <?php if ($this->_var['ac'] == 'money'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'money','arg0'=>'0')); ?>">账户余额</a></li> -->
<!--<li <?php if ($this->_var['ac'] == 'point'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'point')); ?>">我的积分</a></li>-->
<!-- <li <?php if ($this->_var['ac'] == 'coin'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'coin')); ?>">酷&nbsp;&nbsp;特&nbsp;&nbsp;币</a></li> -->
<li <?php if ($this->_var['ac'] == 'debit'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'debit')); ?>">我的麦券</a></li>
<li <?php if ($this->_var['ac'] == 'activation'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'activation')); ?>">激活优惠券</a></li>
<!-- <li <?php if ($this->_var['app'] == 'kuka'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'kuka','act'=>'customer_kuka')); ?>">我的酷卡</a></li> -->
</ul>
<h4>资料管理</h4>
<ul>
<li <?php if ($this->_var['act'] == 'profile' || $this->_var['act'] == 'detailed' || $this->_var['act'] == 'figure'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'profile')); ?>">个人资料</a></li>

<li <?php if ($this->_var['app'] == 'my_pet'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'my_pet')); ?>">宠物管理</a></li>
<li <?php if ($this->_var['app'] == 'my_address'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'my_address')); ?>">地址管理</a></li>
<li <?php if ($this->_var['act'] == 'safe'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'safe')); ?>">安全设置</a></li>

<!--#先隐藏掉.lirp<li <?php if ($this->_var['act'] == 'special_code'): ?>class="default"<?php endif; ?>><a href="<?php echo $this->build_url(array('app'=>'special_code')); ?>">我的特权码</a></li>-->
</ul>
</div>
</div>