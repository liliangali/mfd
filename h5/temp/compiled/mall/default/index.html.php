<!DOCTYPE html>
<html lang="zh_cn">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<meta content="telephone=no" name="format-detection" />
<title>购物车</title>
<link rel="stylesheet" type="text/css" href="static/css/style.css" media="screen">
<link rel="stylesheet" type="text/css" href="static/css/layer.css" media="screen">

	<link rel="stylesheet" type="text/css" href="public/static/wap/css/footer.css" />

    <style>
      .fixd_cart {bottom:100px !important;}
    </style>

</head>

<body style="background:#f6f6f6;">
<script>window.onunload=function(){};</script>
<form action="" id="CartListForm">
<div class="main">
	<?php echo $this->fetch('headernb.html'); ?>
  <div class="wdgwc_box">
    <?php $_from = $this->_var['cart']['object']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['clist'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['clist']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['clist']['iteration']++;
?>

	    <div class="gwclb clearfix" data-id="<?php echo $this->_var['item']['ident']; ?>" data-tp="<?php echo $this->_var['item']['type']; ?>" <?php if (($this->_foreach['clist']['iteration'] <= 1)): ?>style="padding-top:25px;"<?php endif; ?>>
	      <a href="javascript:void(0)" class="scal" data-id="<?php echo $this->_var['item']['ident']; ?>" data-tp="<?php echo $this->_var['item']['type']; ?>"></a>
	      <p class="m_gwctu"><?php if ($this->_var['item']['check'] == 'yes'): ?><span class="on"></span><?php else: ?><span class="off"></span><?php endif; ?>
	      <?php if ($this->_var['item']['list']): ?>
	         <input type="checkbox" name="check[list][<?php echo $this->_var['item']['ident']; ?>]" value="<?php echo $this->_var['item']['ident']; ?>" <?php if ($this->_var['item']['check'] == 'yes'): ?> checked="checked" <?php endif; ?> style="display: none"  >
	      <?php else: ?>
	          <input type="checkbox" name="check[data][<?php echo $this->_var['item']['ident']; ?>]" value="<?php echo $this->_var['item']['ident']; ?>"   <?php if ($this->_var['item']['check'] == 'yes'): ?> checked="checked" <?php endif; ?> style="display: none"  >
	      <?php endif; ?>
	      <a href="javascript:void(0)">
			  <img src="<?php echo $this->_var['item']['goods']['image']; ?>" />
		  </a>
	      </p>
	
	      <div class="xinxixz">
	        <h3>
				<?php if ($this->_var['item']['type'] == "custom"): ?>
				<?php echo $this->_var['item']['goods']['name']; ?>
				<?php else: ?>
				<?php if ($this->_var['item']['dog_name']): ?><?php echo $this->_var['item']['dog_name']; ?>的专属狗粮<?php else: ?>订制化主粮<?php endif; ?>
				<?php endif; ?>
			</h3>
	        <p class="ltdz"><span><?php echo price_format($this->_var['item']['goods']['markprice']); ?></span></p>
	        <p class="ltdz"><?php if ($this->_var['item']['type'] == 'fdiy'): ?>
						定制商品<span style="padding-left: 5px;"><?php echo $this->_var['item']['goods']['dname']; ?></span>
						<?php else: ?>
						<?php $_from = $this->_var['item']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ls');if (count($_from)):
    foreach ($_from AS $this->_var['ls']):
?> 
						<span style="padding-left: 5px;"><?php echo $this->_var['ls']['params']['oProducts']['spec_info']; ?></span><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?><?php endif; ?></p>
						
			<p class="youhui fl">
				
				<?php if ($this->_var['item']['favorable_mj']): ?>
				<div class="youhui_box fl" data-ss="11">
				<select style=" height:28px; border: 1px solid #dfdfdf; width:100px; font-size:12x;" name='favorable' class="selecss" data-ident="<?php echo $this->_var['item']['ident']; ?>" >
				<option value='0'>选择优惠</option>
				<?php $_from = $this->_var['item']['favorable_mj']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'fav');if (count($_from)):
    foreach ($_from AS $this->_var['fav']):
?>
				<option value='<?php echo $this->_var['fav']['id']; ?>'<?php if ($this->_var['fav']['id'] == $this->_var['item']['goods']['favorable_id']): ?>selected<?php endif; ?>><?php echo $this->_var['fav']['name']; ?></option>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</select>
				</div>
				<?php endif; ?>
				<?php if ($this->_var['item']['favorable_zk']): ?>
				<div class="zhek_box fl" data-ss="11">
				<input style="display:none;" value="<?php echo $this->_var['item']['goods']['goods_id']; ?> " data-num="<?php echo $this->_var['item']['goods']['quantity']; ?>" data-ident="<?php echo $this->_var['item']['ident']; ?>" class="goodsid">
				<select style=" height:28px; border: 1px solid #dfdfdf; width:100px; font-size:12x;" name='favorable_zk' class="selectzk" data-id="<?php echo $this->_var['list']['ident']; ?>" >
				<option value='0'>选择折扣优惠</option>
				<?php $_from = $this->_var['item']['favorable_zk']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'fav');if (count($_from)):
    foreach ($_from AS $this->_var['fav']):
?>
				<option value='<?php echo $this->_var['fav']['id']; ?>'<?php if ($this->_var['fav']['id'] == $this->_var['item']['goods']['zhek_id']): ?>selected<?php endif; ?>><?php echo $this->_var['fav']['name']; ?></option>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</select>
				</div>
				<?php endif; ?>
				</p>
	        <div class="jiajian_2" data-id="<?php echo $this->_var['item']['ident']; ?>" data-tp="<?php echo $this->_var['item']['type']; ?>" data-ft="<?php echo $this->_var['item']['first']; ?>">
	          <p class="jian" data-do="jian">-</p>
	          <input type="number" value="<?php echo $this->_var['item']['quantity']; ?>" disabled class="change" name="change" data-num="<?php echo $this->_var['item']['quantity']; ?>" <?php if ($this->_var['item']['first']): ?> readonly <?php endif; ?> />
				<?php $_from = $this->_var['item']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ls');if (count($_from)):
    foreach ($_from AS $this->_var['ls']):
?>
				<?php if ($this->_var['ls']['is_try'] == 0): ?>
	          <p class="jia" data-do="jia">+</p>
				<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	        </div>
	      
	      </div>
	    </div>

    <?php if (! ($this->_foreach['clist']['iteration'] == $this->_foreach['clist']['total'])): ?><p class="gwchst"></p><?php else: ?><p style="height:1px; overflow:hidden; background:#e5e5e5;"></p><?php endif; ?>
    
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    
  </div>
</div>

<div class="jrgwc_box">
 <div class="jrgwc main">
   <p class="one_to_one m_gwctu1 qx" id="checkAll"><?php if ($this->_var['choiceAll']): ?><span class="on"></span><?php else: ?><span class="off"></span><?php endif; ?>全选</p>
   <h2 class="houji">合计：<span><?php echo price_format($this->_var['cart']['check_amount']); ?></span></h2>

   <p class="jiesua"><a href="javascript:void(0)">结算 (<?php echo $this->_var['cart']['check_num']; ?>)</a></p>
 </div>
</div>
</form>

<script type="text/javascript" src="static/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="static/js/cart.js"></script>
<script type="text/javascript" src="static/js/jquery.form.js"></script>
<script type=text/javascript src="static/js/layer.m.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	
    $.cart({
        dropUrl:"/cart-drop.html",
        cartUrl:"/cart.html",
        clearUrl:"/cart-clear.html",
        updateUrl:"/cart-update.html",
        checkoutUrl:"/cart-checkout.html",
        checkUrl:"<?php echo $this->build_url(array('app'=>'cart','act'=>'check')); ?>",
        choiceUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'choice')); ?>",
        choiceAllUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'choiceAll')); ?>",
		favoreUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'updata_favore')); ?>",
		zhekUrl : "<?php echo $this->build_url(array('app'=>'cart','act'=>'updata_zhek')); ?>",
        tk : "<?php echo $this->_var['tk']; ?>"
    });
    
    $('.embs_sel').unbind().bind('change',function(){
    	var _this = $(this);
    	var _text = _this.find("option:selected").text(); //_this.val()
    	var _id = _this.data('id');
    	_this.parents('.xinxixz').find('.wdqmlr').find('.'+_id).html(_text);
    });
    $('.embs_inp').unbind().bind('change',function(){
        var _this = $(this);
        var _text = _this.val(); //_this.val()
        var _id = _this.data('id');
        _this.parents('.xinxixz').find('.wdqmlr').find('.'+_id).html(_text);
    });
    $('.embs_sel').each(function(){
    	var _this = $(this);
        var _text = _this.find("option:selected").text(); //_this.val()
        var _id = _this.data('id');
        _this.parents('.xinxixz').find('.wdqmlr').find('.'+_id).html(_text);
    });
    $('.embs_inp').each(function(){
    	var _this = $(this);
        var _text = _this.val(); //_this.val()
        var _id = _this.data('id');
        _this.parents('.xinxixz').find('.wdqmlr').find('.'+_id).html(_text);
    })
    
    
});

</script>

   <SCRIPT>
        $("#sortlist h3").bind("click",function(){
        var element=$(this).parent();
        if (element.hasClass("current")){
            element.removeClass("current");
        }else{
            element.addClass("current");
        }
    })
   </SCRIPT>
<?php echo $this->fetch('footer_footer.html'); ?>
<?php echo $this->fetch('fudong1.html'); ?>
</body>
</html>
