<?php echo $this->fetch('header-new.html'); ?>
<link href="/public/static/pc/css/gwc.css" rel="stylesheet">
<div class="wdgwc_box w">
<!---->
	<h1 class="wdgwc">我的购物车</h1>
	<div class="spxx">
		<p class="p1">商品信息</p>
		<p class="p2">单价(元)</p>
		<p class="p3">优惠方案</p>
		<p class="p4">数量</p>
		<p class="p5">金额(元)</p>
		<p class="p6">操作</p>
	</div>
	<form role="form" id="checkBo-demo-from" class="cartMain">
		<ul class="gwclb" id="JcartList">

			<?php $_from = $this->_var['cart']['object']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
			
			<li data-id="<?php echo $this->_var['list']['ident']; ?>" data-type="<?php echo $this->_var['list']['type']; ?>" data-cate="<?php echo $this->_var['list']['cate']; ?>" data-price="<?php echo $this->_var['list']['goods']['markprice']; ?>" data-sub="<?php echo $this->_var['list']['subtotal']; ?>" <?php if ($this->_var['list']['goods']['favorable_id'] || $this->_var['list']['goods']['zhek_id']): ?> data-favor="<?php echo $this->_var['list']['subtotal']; ?>"<?php endif; ?>  data-ft="<?php echo $this->_var['list']['first']; ?>"  class="cartList">
				<div class="fxk fl">
				<?php if ($this->_var['list']['goods']['marketable']): ?>
				    <label class="cb-checkbox checkbox-row <?php if ($this->_var['list']['check']): ?> checked ch11 <?php endif; ?>">
						<input type="checkbox">
						<!--<span class="cb-inner"><i><input type="checkbox"></i></span>-->
                    </label>
                 <?php else: ?>
                      已下架
                  <?php endif; ?>
					
				</div>
				<p class="gwclbtu fl"><a href="<?php if ($this->_var['list']['goods']['goods_id'] > 0): ?><?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['list']['goods']['goods_id'])); ?><?php else: ?><?php echo $this->_var['list']['goods']['goods_url']; ?><?php endif; ?>"><img src="<?php echo $this->_var['list']['goods']['image']; ?>"></a></p>
				<div class="gwclbjs fl">
					<h1>
						<?php if ($this->_var['list']['type'] == 'fdiy'): ?>
						<?php if ($this->_var['list']['dog_name']): ?><?php echo $this->_var['list']['dog_name']; ?>的专属狗粮<?php else: ?>订制化主粮<?php endif; ?>
						<?php else: ?>
						<?php echo $this->_var['list']['goods']['name']; ?>
						<?php endif; ?>
					</h1>
					<p class="p1">
						<?php if ($this->_var['list']['type'] == 'fdiy'): ?>
						定制商品：<span style="padding-left: 5px;"><?php echo $this->_var['list']['goods']['dname']; ?></span>
						<?php else: ?>
						<?php $_from = $this->_var['list']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ls');if (count($_from)):
    foreach ($_from AS $this->_var['ls']):
?> 
						<span style="padding-left: 5px;"><?php echo $this->_var['ls']['params']['oProducts']['spec_info']; ?></span><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?><?php endif; ?>
						
					</p>
					<?php if ($this->_var['list']['goods']['pdata']['yhcase']): ?>
					<span><img src="/public/static/pc/images/sales_<?php echo $this->_var['list']['goods']['pdata']['yhcase']; ?>.png" class="cuxiao"><?php echo $this->_var['list']['goods']['pdata']['name']; ?></span>
					<?php endif; ?>
				</div>
				<p class="jiag fl"><?php echo price_format($this->_var['list']['goods']['basePrice']); ?></p>
				<p class="youhui fl">
				
				<?php if ($this->_var['list']['favorable_mj']): ?>
				<div class="youhui_box fl" data-ss="11">
				<input style="display:none;" value="<?php echo $this->_var['list']['goods']['goods_id']; ?> " data-num="<?php echo $this->_var['list']['goods']['quantity']; ?>" data-ident="<?php echo $this->_var['list']['ident']; ?>" class="goodsid">
				<select style=" height:28px; border: 1px solid #dfdfdf; width:100px; font-size:12x;" name='favorable' class="selecss" data-id="<?php echo $this->_var['list']['ident']; ?>" >
				<option value='0'>选择优惠</option>
				<?php $_from = $this->_var['list']['favorable_mj']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'fav');if (count($_from)):
    foreach ($_from AS $this->_var['fav']):
?>
				<option value='<?php echo $this->_var['fav']['id']; ?>'<?php if ($this->_var['fav']['id'] == $this->_var['list']['goods']['favorable_id']): ?>selected<?php endif; ?>><?php echo $this->_var['fav']['name']; ?></option>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</select>
				</div>
				<?php endif; ?>
				<?php if ($this->_var['list']['favorable_zk']): ?>
				<div class="zhek_box fl" data-ss="11">
				<input style="display:none;" value="<?php echo $this->_var['list']['goods']['goods_id']; ?> " data-num="<?php echo $this->_var['list']['goods']['quantity']; ?>" data-ident="<?php echo $this->_var['list']['ident']; ?>" class="goodsid">
				<select style=" height:28px; border: 1px solid #dfdfdf; width:100px; font-size:12x;" name='favorable_zk' class="selecss" data-id="<?php echo $this->_var['list']['ident']; ?>" >
				<option value='0'>选择折扣优惠</option>
				<?php $_from = $this->_var['list']['favorable_zk']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'fav');if (count($_from)):
    foreach ($_from AS $this->_var['fav']):
?>
				<option value='<?php echo $this->_var['fav']['id']; ?>'<?php if ($this->_var['fav']['id'] == $this->_var['list']['goods']['zhek_id']): ?>selected<?php endif; ?>><?php echo $this->_var['fav']['name']; ?></option>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</select>
				</div>
				<?php endif; ?>
				</p>
				<div class="jajia_box fl">
					<a href="javascript:;" class="a_1"></a>
					<input type="text" value="<?php echo $this->_var['list']['goods']['quantity']; ?>" disabled data-num="<?php echo $this->_var['list']['goods']['quantity']; ?>">
					<a href="javascript:;" class="a_2"></a>
				</div>
				<p class="jine fl"><?php echo price_format($this->_var['list']['goods']['price']); ?></p> 
				<a href="javascript:;" class="cazua fl"></a>
			</li> 

			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

		</ul>
		<div class="gwcjs_je">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="48" style="padding-left: 20px;">
					<label class="cb-checkbox" id="check-all" style="position: relative; top: 3px;">
					<input type="checkbox">
					</label>
					</td>
					<td width="70">全选</td>
					<td width="500" class="sc"><a href="javascript:;" class="clearCart">删除</a></td>
					<td width="480" class="dzsp">您定制了<span id="cart_num"><?php echo ($this->_var['cart']['check_num'] == '') ? '0' : $this->_var['cart']['check_num']; ?></span>件商品，总价：<span><?php echo price_format(($this->_var['cart']['check_amount_m'] == '') ? '0' : $this->_var['cart']['check_amount_m']); ?></span></td>
					<td width="150"><input type="button" value="去结算" class="mr"></td>
				</tr>
			</table>
		</div>
	</form>
</div>
<div id="window04" class="hide"></div>

<script src="/public/global/jquery-1.8.3.min.js"></script>
<script src="/public/global/luck/pc/luck.js"></script>
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/global/checkbox-master/src/js/checkBo.js"></script>
<script src="/public/global/jquery.form.js"></script>
<script src="/public/static/pc/js/Xslider.js"></script>
<script src="/public/static/pc/js/cart.js"></script>
<script>
	$(document).ready(function() {
		$.cart({
			dropUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'drop')); ?>",
			cartUrl : "<?php echo $this->build_url(array('app'=>'cart')); ?>",
			clearUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'clear')); ?>",
			updateUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'update')); ?>",
			checkoutUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'checkout')); ?>",
			checkUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'check')); ?>",
			embUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'embs')); ?>",
			embSaveUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'embsSave')); ?>",
			choiceUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'choice')); ?>",
			choiceAllUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'choiceAll')); ?>",
			favoreUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'updata_favore')); ?>",
			zhekUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'updata_zhek')); ?>",
		});
	});
</script>
<script>
 /*function youhui(obj){
 var goodsid=$('#goodsid').val();
 var num=$('#goodsid').attr('data-num');
 var fav_id=$(obj).val();
 var ident=$('.cartList').attr('data-id');

		  $.post("<?php echo $this->build_url(array('app'=>'cart','act'=>'updata_favore')); ?>",{ident:ident,fav_id:fav_id,goods_id:goodsid,num:num},function(res){
                  if(res.done == true){
			
                	  _loadData();
					  
                  }else{
                	  alert(res.msg)               	  
                  }
        },"json");
	}*/
</script>
<?php echo $this->fetch('footer-new.html'); ?>
