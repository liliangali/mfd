<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1, user-scalable=no, minimal-ui">
<meta name="format-detection" content="telphone=no, email=no">
<?php echo $this->_var['page_seo']; ?>
<link rel="shortcut icon" href="favicon.ico" />
<link rel="icon" href="animated_favicon.gif" type="image/gif" />
<link href="/public/static/pc/css/public.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/public/static/pc/css/animate.min.css" />
</head>
<body>
<div class="head">
	<div class="w1" style="">
		<a href="/" class="logo fl"><img alt="<?php echo $this->_var['site_title']; ?>" src="<?php echo $this->_var['site_logo']; ?>"></a>
		<button class="toggleBtn"><i></i><i></i><i></i></button>
		<ul class="cotteNav fr">
		<?php if ($this->_var['navigates']): ?>
		<?php $_from = $this->_var['navigates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'navigate');$this->_foreach['nav'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav']['total'] > 0):
    foreach ($_from AS $this->_var['navigate']):
        $this->_foreach['nav']['iteration']++;
?>
		          <?php if (is_array ( $this->_var['navigate'] )): ?>
		          <?php if ($this->_var['navigate']['children']): ?>
		                  <li class="ktspsl" data-cmd="menu">
			                 <div class="hd"><a href="<?php echo $this->_var['navigate']['link']; ?>" <?php if ($this->_var['navigate']['title']): ?>title="<?php echo $this->_var['navigate']['title']; ?>"<?php endif; ?> <?php if ($this->_var['navigate']['alone']): ?>target='_blank'<?php endif; ?> <?php if ($this->_var['currentApp'] == 'man' || $this->_var['currentApp'] == 'goods' || $this->_var['currentApp'] == 'gallery'): ?>class="cur"<?php else: ?>class="color"<?php endif; ?>><?php echo $this->_var['navigate']['name']; ?></a></div>
			              <p class="xljt"></p>
			              <div class="bd">
			               <dl>
			               <?php $_from = $this->_var['navigate']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
			                <dd><a href="<?php echo $this->_var['child']['link']; ?>"><?php echo $this->_var['child']['name']; ?></a></dd>
			               <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			               </dl>
			              </div>
			            </li>
		          <?php else: ?>
		                     <li><a href="<?php echo $this->_var['navigate']['link']; ?>" <?php if ($this->_var['navigate']['title']): ?>title="<?php echo $this->_var['navigate']['title']; ?>"<?php endif; ?> <?php if ($this->_var['navigate']['alone']): ?>target='_blank'<?php endif; ?> <?php if ($this->_var['navigate']['curr']): ?>class="cur"<?php endif; ?><?php if (($this->_foreach['nav']['iteration'] <= 1) && $this->_var['currentApp'] == 'default' && ! $this->_var['navigates']['curr']): ?>class="cur"<?php endif; ?>><?php echo $this->_var['navigate']['name']; ?></a></li>        
		          <?php endif; ?>
		          <?php endif; ?>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		<?php endif; ?>

			
		</ul>
	</div>
    <?php if (! $this->_var['visitor']['user_id']): ?>
    <div class="user" data-cmd="menu">
        <!--<p class="txmr"><img src="/public/static/pc/images/logo.png"></p>-->
        <div class="hd"><a href="/member-login.html" class="grey">登录</a>/<a href="/member-register.html" class="grey">注册</a></div>
    </div>
    <?php else: ?>
    <div class="user user_tu" data-cmd="menu">   
        <p class="txmr"><img src="<?php echo $this->_var['avatar']; ?>"></p>
        <div class="hd"><a href="/member.html"><?php if ($this->_var['visitor']['nickname'] != ''): ?><?php echo $this->_var['visitor']['nickname']; ?><?php else: ?><?php echo $this->_var['visitor']['user_name']; ?><?php endif; ?></a></div>
        <div class="bd"> <i class="arrow"></i> <a href="/member.html">账户中心</a> <a href="/member-profile.html">个人资料</a><a href="/my_order.html">我的订单</a> <a href="/my_favorite.html">我的收藏</a> <a href="/member-logout.html">安全退出</a> </div>
    </div>
    <?php endif; ?>
     <div class="cart" data-cmd="menu_cart">
      <?php if ($this->_var['cart_goods_num'] != 0): ?>
        <div class="hd orange"><a href="/cart.html"><span><?php echo ($this->_var['cart_goods_num'] == '') ? '0' : $this->_var['cart_goods_num']; ?></span></a></div>
      <?php else: ?>
         <div class="hd orange"><a href="/cart.html"><span>0</span></a></div>
      <?php endif; ?>
        <div class="bd">
            <p class="kgwc">亲，你的购物车还是空的，赶紧去选购吧～</p>
        </div>
    </div>   
</div>
<div class="menuLayer">
	<ul>
		<li>
		<?php if (! $this->_var['visitor']['user_id']): ?>
		<a href="/member-login.html">登录/注册</a>
		<?php else: ?>
		<a href="/member.html"><?php if ($this->_var['visitor']['nickname'] != ''): ?><?php echo $this->_var['visitor']['nickname']; ?><?php else: ?><?php echo $this->_var['visitor']['user_name']; ?><?php endif; ?></a>
		<?php endif; ?>
		</li>
		<li><a href="/cart.html">购物车<em id="minNav-cart"></em></a></li>
		<li><a href="/" <?php if ($this->_var['currentApp'] == 'default'): ?>class="cur"<?php endif; ?>>首页</a></li>			
		<li><a href="<?php echo $this->build_url(array('app'=>'gallery')); ?>">麦富迪尚品</a></li>
		<li><a href="/fdiy-1.html" target="_blank">我要定制</a></li>

		<li><a href="/activepublic.html" <?php if ($this->_var['currentApp'] == 'activepublic'): ?>class="cur"<?php endif; ?>>活动特惠</a></li>
		<li><a href="/help.html" <?php if ($this->_var['currentApp'] == 'help'): ?>class="cur"<?php endif; ?>>帮助中心</a></li>
		<li><a href="/down.html" <?php if ($this->_var['currentApp'] == 'down'): ?>class="cur"<?php endif; ?> target="_blank">APP下载</a></li>
	</ul>
</div>

<?php if (APP != 'professor' && ACT != 'online'): ?>
<a href="https://www.sobot.com/chat/oldpc/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf&groupid=84c09075813e45f68eae414cf91e897a" class="fixed_kefu" target="_blank"><i></i></a>
<?php endif; ?>