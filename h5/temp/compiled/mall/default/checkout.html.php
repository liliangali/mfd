<!DOCTYPE html>
<html lang="zh_cn">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<title>订单信息确认</title>
<link rel="stylesheet" type="text/css" href="static/css/style.css" media="screen">
<link rel="stylesheet" type="text/css" href="static/css/layer.css" media="screen">
<link type="text/css" href="public/static/wap/css/productlist.css" rel="stylesheet" />

    <style>
        .wdgwc_box {padding-bottom:50px !important;}
        .jrgwc_box {bottom:0 !important;}
        .fixd_cart {bottom:85px !important;}
    </style>

</head>

<body>
<script>window.onunload=function(){};</script>
<div class="main">
    <?php echo $this->fetch('headerb.html'); ?>
<!--     <header class="hdtop">
        <div class="edit fl">
            <p class="p1"><a href="javascript:history.go(-1)"><img src="public/static/wap/images/tw_03.png" /></a></p>
            <p class="p2">订单确认</p>
        </div>
    </header> -->
<div class="wdgwc_box">
<form id="OrderForm">
  
  

  <?php $this->assign('address',$this->_var['order']['shipping'][$this->_var['order']['shipping']['type']]); ?>
  <a href="<?php echo $this->build_url(array('app'=>'cart','act'=>'address')); ?>" style="display:block">
  <div class="qrddyx">
   <div class="lhwdz">
    <p class="p1">收货地址</p>
    <?php if (! $this->_var['address']['consignee']): ?> <p class="p2"><span>亲~您还没有填写收货地址</span></p>    
    <?php else: ?>
    <p class="p2"><span><?php echo $this->_var['address']['consignee']; ?></span><?php if ($this->_var['address']['phone_mob']): ?><?php echo $this->_var['address']['phone_mob']; ?><?php else: ?><?php echo $this->_var['address']['phone_tel']; ?><?php endif; ?></p>
    <p class="p2"><span><?php echo $this->_var['address']['region_name']; ?></span><?php echo $this->_var['address']['address']; ?> </p>
    <?php endif; ?>
   </div>
  </div>
  </a>
 
 <p class="gwchst"></p>
  
  
   
 <!--  <a href="<?php echo $this->build_url(array('app'=>'cart','act'=>'ships','arg0'=>$this->_var['address']['addr_id'])); ?>?token=<?php echo $this->_var['apptk']; ?>" style="display:block">
  <div class="qrddyx">
   <div class="lhwdz">
    <p class="p1">物流公司</p>
      <?php if ($this->_var['cart']['defship']['shipping_name']): ?>
      <p class="p2">已选择：<?php echo $this->_var['cart']['defship']['shipping_name']; ?> 运费：<?php echo $this->_var['cart']['defship']['post_fee']; ?></p>
      <?php else: ?>
      <p class="p2">请选择物流公司></p>
      <?php endif; ?>
   </div>
  </div>
  </a> 
  <p class="gwchst"></p>-->
  
  
  
  
  <div class="qrddyx clearfix" style="padding:0 3.5%; overflow:hidden;">
	  <h1 class="spqd_bt">商品信息</h1>
	  <div class="spqdgao">
	  <ul class="spqd_ul">
	  <?php $_from = $this->_var['cart']['object']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['foo']['iteration']++;
?>
	  <?php if ($this->_var['item']['check'] == 'yes'): ?>
	   <li class="jj_box">
		<p class="p1"><img src="<?php echo $this->_var['item']['goods']['image']; ?>" /></p>
        <p class="p2 jj_1">
            <?php if ($this->_var['item']['type'] == "fdiy"): ?>
         <?php $_from = $this->_var['item']['goods']['dname_arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'item1');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['item1']):
?>
        <span><?php echo $this->_var['key1']; ?>: <?php echo $this->_var['item1']; ?></span>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php else: ?>
            <?php $_from = $this->_var['item']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'item1');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['item1']):
?>
            <span><?php echo $this->_var['item1']['params']['oProducts']['spec_info']; ?></span>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php endif; ?>
        </p>
	   </li>
	   <?php endif; ?>
	   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
	  </ul>
	  </div>
  </div>
  <p class="gwchst"></p>
  
  

<!--   <a href="<?php echo $this->build_url(array('app'=>'cart','act'=>'invoice')); ?>" style="display:block" >
  <div class="qrddyx">
    <div class="lhwdz">
    <p class="p1">发票信息</p>
    <?php if ($this->_var['order']['invoice']): ?>
    <?php $this->assign('intp',$this->_var['order']['invoice'][$this->_var['order']['invoice']['type']]); ?>
    <p class="p2" style="color:#717171; margin:0;"><?php echo $this->_var['intp']['type']; ?></p>
    <?php else: ?>
    <p class="p2" style="color:#717171; margin:0;">默认不需要发票，如有需要请选择。</p>
    <?php endif; ?>
    </div>
  </div>
  </a>
  <p class="gwchst"></p> -->
  
  
   
   <!--  <div class="qrddyx">
    <p class="p1" id="remark" style="font-size:16px;">订单备注</p>
    <textarea name="remark" placeholder="如果您有关款式以及其它特殊要求，请告诉我们" style="border:1px solid #dfdfdf; margin-top:10px; outline:none;resize:none; color:#717171; width:100%; height:60px; padding:5px; line-height:20px;"></textarea>
    </div> 
  
    <p class="gwchst"></p> -->
    
   <div class="qrddyx" >

    <div class="zfjpsfs">
     <p class="p1">支付方式</p>
     <div class="lzzffs">
      <input type="hidden" name="payment" id="payment" value="" />
      <?php $_from = $this->_var['payments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pmt');$this->_foreach['lpmt'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['lpmt']['total'] > 0):
    foreach ($_from AS $this->_var['pmt']):
        $this->_foreach['lpmt']['iteration']++;
?>
      <p data-id="<?php echo $this->_var['pmt']['payment_code']; ?>" class="payment_li <?php if ($this->_var['member']['money'] <= $this->_var['cart']['order_amount']): ?> <?php if (($this->_foreach['lpmt']['iteration'] <= 1)): ?> on <?php endif; ?> <?php endif; ?>" <?php if (($this->_foreach['lpmt']['iteration'] == $this->_foreach['lpmt']['total'])): ?> style="margin-right:0;" <?php endif; ?>><a href="javascript:void(0)" ><?php echo $this->_var['pmt']['payment_name']; ?></a></p>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
     </div>
    </div>
  </div>
  
<!--   <div class="qrddyx" style="padding-top:2px; padding-bottom:0;">
   <div class="lhwdz">
        <?php if ($this->_var['kuka']): ?>
        <a href="<?php echo $this->build_url(array('app'=>'cart','act'=>'kuka')); ?>" style="display:block">
        <p class="p3 dkj">酷卡<span style="color:#717171;"><?php echo $this->_var['kuka']; ?>张可用</span><font><?php if ($this->_var['cart']['kuka_fee']): ?>已使用<?php else: ?>未使用<?php endif; ?></font></p>
        </a>
        <?php endif; ?>
   </div>
  </div> -->
  

  
  <div class="qrddyx" style="padding-top:2px; padding-bottom:0;">
   <div class="lhwdz">
   <?php if ($this->_var['debits']): ?>
	    <a class="debitA" href="<?php if ($_GET['mfd_cart_is_check']): ?><?php echo $this->build_url(array('app'=>'cart','act'=>'debit')); ?>?mfd_cart_is_check=1<?php else: ?><?php echo $this->build_url(array('app'=>'cart','act'=>'debit')); ?><?php endif; ?>" style="display:block">
	    <p class="p3 dkj">抵扣券<span style="color:#717171;"><?php echo $this->_var['debits']; ?>张可用</span><font><?php if ($this->_var['cart']['debit_fee']): ?>已使用<?php else: ?>未使用<?php endif; ?></font></p>
	    </a>
    <?php endif; ?>
       <input type="hidden" name="did"  value="<?php echo $_GET['did']; ?>">
       <?php if ($_GET['mfd_cart_is_check']): ?>
       <input type="hidden" name="mfd_cart_is_check"  value="1">
       <?php else: ?>
       <?php endif; ?>
   </div>
  </div>
    
  <div class="coupon">
      
    <?php if ($this->_var['member']['coin'] > 0): ?>
    <input type="hidden" id="canCoin" value="<?php if ($this->_var['cart']['has_activity'] != 'yes' || $this->_var['member']['member_lv_id'] > 1): ?><?php if ($this->_var['cart']['has_special'] != 'yes'): ?>yes<?php endif; ?><?php endif; ?>" />
    <?php if ($this->_var['cart']['has_activity'] != 'yes' || $this->_var['member']['member_lv_id'] > 1): ?>
    <?php if ($this->_var['cart']['has_special'] != 'yes'): ?>
    <dl data-type="coin"  data-value="<?php echo $this->_var['member']['coin']; ?>">
        <dt><span class="name fl">麦富迪币</span><span class="num fl"><?php echo $this->_var['member']['coin']; ?>个可用，抵<?php echo $this->_var['member']['coin']; ?>元</span><span class="switch fr" <?php if ($this->_var['cart']['debit_fee']): ?> data-dis="yes" <?php endif; ?>><i class="point"></i></span></dt>
        <dd>使用<input type="number" max="20" min="0" value="">麦富迪币，抵<span class="orange">-0.00元</span><font></font></dd>
    </dl>
    <?php endif; ?>
    <?php endif; ?>
    <?php endif; ?>
    
    <?php if ($this->_var['member']['money'] > 0): ?>
    <dl data-type="money"  data-value="<?php echo $this->_var['member']['money']; ?>">
        <dt><span class="name fl">余额</span><span class="num fl"><?php echo price_format($this->_var['member']['money']); ?>可用</span><span class="switch fr"><i class="point"></i></span></dt>
        <dd>使用<input type="number" max="20" min="0" value="">余额，<span class="orange">-0.00元</span><font></font></dd>
    </dl>
    <?php endif; ?>
    
  </div>
  
    <input type="hidden" id="has_one" value="-1" />
  
 

  <div class="qrddyx">
   <div class="lhwdz clearfix">
    <p class="spje">商品金额</p>
    <p class="jesl"><?php echo price_format($this->_var['cart']['order_amount']); ?></p>
   </div>
   
   <?php if ($this->_var['cart']['defship']['post_fee']): ?>
   <div class="lhwdz clearfix kuka_fee">
    <p class="spje">运费</p>
    <p class="jesl">+<?php echo price_format($this->_var['cart']['defship']['post_fee']); ?></p>
   </div>
   <?php endif; ?>
     <?php if ($this->_var['favorables_yh']): ?>
	 <li><span class="fl">优惠券选择</span>
	   <span class="fr">
	     <select style=" height:28px; border: 1px solid #dfdfdf; width:100px; font-size:12x;" name='favorable_zk' class="favorable_zk"  >
				<option value='0'>选择优惠券</option>
				<?php $_from = $this->_var['favorables_yh']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'fyh');if (count($_from)):
    foreach ($_from AS $this->_var['fyh']):
?>
				<option value='<?php echo $this->_var['fyh']['id']; ?>'<?php if ($this->_var['fyh']['id'] == $this->_var['list']['goods']['youh_id']): ?>selected<?php endif; ?> data-money="<?php echo $this->_var['fyh']['yhcase_value2']; ?>" data-amount="<?php echo $this->_var['fyh']['yhcase_value']; ?>" ><?php echo $this->_var['fyh']['name']; ?></option>
				   
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</select>
	   </span></li>

   <?php endif; ?>
   <?php if ($this->_var['cart']['debit_fee']): ?>
   <div class="lhwdz clearfix debit_fee">
    <p class="spje">抵用券</p>
    <p class="jesl">-<?php echo price_format($this->_var['cart']['debit_fee']); ?></p>
   </div>
   <?php endif; ?>
<!--    <div class="lhwdz clearfix money_fee" data-value="" style="display:none">
    <p class="spje">余额</p>
    <p class="jesl">-<?php echo price_format($this->_var['cart']['money_amount']); ?></p>
   </div>
   <div class="lhwdz clearfix coin_fee" data-value="" style="display:none">
    <p class="spje">麦富迪币</p>
    <p class="jesl">-<?php echo price_format($this->_var['cart']['money_amount']); ?></p>
   </div> -->
  </div>
  
</form>
</div>
</div>
<div class="jrgwc_box">
<div class="jrgwc main">
   <h2 class="houji">合计：<span id="final_amount" data-amount="<?php echo $this->_var['cart']['final_amount']; ?>"><?php echo price_format($this->_var['cart']['final_amount']); ?></span></h2>
   <p class="jiesua"><a href="javascript:void(0)">提交订单</a></p>
</div>
</div>
<script type="text/javascript" src="static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="static/js/jquery.form.js"></script>
<script type=text/javascript src="static/js/layer.m.js"></script>
<script type="text/javascript">
$('.favorable_zk').change(function(){
	var quan_id=$(this).find("option:selected").val();
	var amount=$(this).find("option:selected").attr('data-amount');
    var money=$(this).find("option:selected").attr('data-money');
	var finalmoney=$('#final_amount').attr('data-amount');
	 $.ajax({
         url:'/quan-update.html',
         type:'POST',
         data:{
        	    finalmoney:finalmoney,
        	    quan_id:quan_id,
        	    amount:amount,
        	    money:money
			  },
        	 
         dataType: "json",
      
     success:function(res){
    
     if(res.done)
     {
    	//更改券后金额
    	var quanprice=res.retval.quanprice;
    	$('#final_amount').html(quanprice)

    	
      }	
     		},
     })
})




$('#remark').click(function(){
	var _this = $(this);
	if(_this.next('div').hasClass('show')){
		_this.next('div').hide();
		_this.next('div').removeClass('show')
	}else{
		_this.next('div').show();
		_this.next('div').addClass('show')
	}
});

//公用切换
function _switch(fn){
	var $this=$(this),flag=false;
	if($this.hasClass('cur')){
		$this.removeClass('cur');
		flag=false;	
	}else{
		$this.addClass('cur');
		flag=true;		
	}
	if(typeof fn =='function'){
		fn(flag)	
	}
	return flag	
}
//礼品标记
$('.lipin .switch').click(function(){
	_switch.call(this,function(obj){
		if(obj){
			sessionStorage.setItem('is_gift','yes');
		}else{
			sessionStorage.removeItem('is_gift');
		}
		//alert(obj)	
	})
});
if(sessionStorage.getItem('is_gift')){
    $('.lipin .switch').addClass('cur');
}

function _alert(msg){
    layer.open({
        content: '<p style="text-align:center">'+msg+'</p>',
        shadeClose: false,
        btn: ['确定'],
    }); 
}

function _submitOrder(){

	var _thisBtn = $(this);
	_thisBtn.unbind();
    $('.load_div').show();
	var _one = $('#has_one').val();

    if(_one == '0'){
        var msga=layer.open({
            content: '本员工号不能享受本次活动，将以零售价进行结算。',
            btn: ['确定', '取消'],
            shadeClose: false,
            yes: function(){
                layer.close(msga);
                $('#OrderForm').ajaxSubmit({
                    type:"post",
                    url:"<?php echo $this->build_url(array('app'=>'order','act'=>'newCreate')); ?>?ajax&mfd_reload_mid=<?php echo $this->_var['user_id']; ?>time="+ (new Date()).getTime(),
                    success:function (res,statusText) {
                        var res = $.parseJSON(res); 
                        if(res.done == false){
                               _alert(res.msg);
                               _thisBtn.unbind().bind('click',_submitOrder)
                        }else{
                            location.href="<?php echo $this->build_url(array('app'=>'order','act'=>'paycenter')); ?>?id="+res.retval.order_sn;
                        }
                    }
                });
            },
            no : function(){
            	$(this).bind('click',_submitOrder());

            }
        });
    }else{
        var _data =  [];
        var _dt = "";
      
       $('.coupon dl').each(function(){
            var _this = $(this);
            var _tp = _this.data('type');
            if(sessionStorage.getItem(_tp) && sessionStorage.getItem(_tp+'val') >0 ){
                _dt += "" + _tp + ":" + sessionStorage.getItem(_tp+'val') + ","
            }           
        });
      
       if(sessionStorage.getItem('is_gift') && sessionStorage.getItem('is_gift') == 'yes'){
            var _gift = 'yes';
        }
        
        $('#OrderForm').ajaxSubmit({
            type:"post",
            data:{pmt:_dt,gift:_gift},
            url:"<?php echo $this->build_url(array('app'=>'order','act'=>'newCreate')); ?>?ajax&mfd_reload_mid=<?php echo $this->_var['user_id']; ?>",
            success:function (res,statusText) {
                var res = $.parseJSON(res); 
                if(res.done == false){
                    $('.load_div').hide();
                	_alert(res.msg);
                	_thisBtn.unbind().bind('click',_submitOrder)
                }else{
                    sessionStorage.removeItem('is_gift');
                    sessionStorage.removeItem('money');
                    sessionStorage.removeItem('moneyval');
                    sessionStorage.removeItem('coin');
                    sessionStorage.removeItem('coinval');
                    sessionStorage.removeItem('also');
                    location.href="<?php echo $this->build_url(array('app'=>'order','act'=>'paycenter')); ?>?id="+res.retval.order_sn;
                }
            }
        });
    }
}
$('.jiesua').unbind().bind('click',_submitOrder);

//发票
$('.tax_list').unbind().bind('click',function(){
	var _id = $(this).data('id');
	$('.tax_list').removeClass('now_hover');
	$(this).addClass('now_hover');
	$('.invoice_li').hide();
	$('#tj_'+_id).show();
	$('#invoice_id').val(_id);
});

//支付方式
$('.payment_li').each(function(){
	if($(this).hasClass('on')){
		$('#payment').val($(this).data('id'));
		return false;
	}
});
$('.payment_li').unbind().bind('click',function(){
	if($('#final_amount').data('amount')<=0) return;
	$('.payment_li').removeClass('on');
	$(this).addClass('on');
	$('#payment').val($(this).data('id'))
})


</script>


<?php if ($this->_var['member']['money'] > 0 || $this->_var['member']['coin'] > 0): ?>
<script type="text/javascript">
//sessionStorage.clear();
if($('#canCoin').val() != 'yes'){
	sessionStorage.removeItem('coin');
	sessionStorage.removeItem('coinval');
}
sessionStorage.removeItem('also');

//麦富迪币
$('.coupon .switch').click(function(){
	
    var _self=this;
    if($(_self).data('dis') == 'yes') return;
    var _prt  = $(_self).parents('dl');
    var _type = _prt.data('type');
    _switch.call(_self,function(obj){
        if(obj){
            _prt.find('dd').slideDown('fast');
            $('.'+_type+'_fee').show();
            sessionStorage.setItem(_type,'use');
            
            if(_type == 'coin'){
                $('.debitA').attr('href',"javascript:void(0)")
            }
            
        }else{
            _prt.find('dd').slideUp('fast');
            $('.'+_type+'_fee').hide();
            _prt.find('dd').find('input').val('');
            _change_use(_prt.find('dd').find('input'));
            sessionStorage.removeItem(_type);
            sessionStorage.removeItem(_type+'val');
            
            if(_type == 'coin'){
                $('.debitA').attr('href',"<?php echo $this->build_url(array('app'=>'cart','act'=>'debit')); ?>")
            }
            
        }
    })
});

$('.coupon dl').each(function(){

    var _this = $(this);
    var _tp   = _this.data('type');
    var _val  = sessionStorage.getItem(_tp);
    if(_val == 'use'){
        
        _this.find('.switch').addClass('cur');
        $('.'+_tp+'_fee').show();
        _this.find('dd').show();
        
        if(sessionStorage.getItem(_tp+'val')){
        	_this.find('dd').find('.orange').text(parseFloat(sessionStorage.getItem(_tp+'val')).toFixed(2)+'元');
        	$('.'+_tp+'_fee').find('.jesl').text('-￥'+parseFloat(sessionStorage.getItem(_tp+'val')).toFixed(2));
        }
        
        if(sessionStorage.getItem(_tp+'val')){
            _this.find('dd').find('input').val(sessionStorage.getItem(_tp+'val'));
        }
        
        if(_tp == 'coin'){
            $('.debitA').attr('href',"javascript:void(0)")
        }
        
    }else{
    	_this.find('.switch').removeClass('cur');
    	$('.'+_tp+'_fee').hide();
        _this.find('dd').hide();
    }
    
});
if(sessionStorage.getItem('also')){
       $('#final_amount').data('amount',sessionStorage.getItem('also'));
       $('#final_amount').html('￥'+sessionStorage.getItem('also'));
}else{
    var _also = parseFloat($('#final_amount').data('amount')) - (parseFloat(sessionStorage.getItem('coinval') != null ? sessionStorage.getItem('coinval') : 0 )) - parseFloat(sessionStorage.getItem('moneyval') != null ? sessionStorage.getItem('moneyval') : 0 );
    $('#final_amount').data('amount',_also);
    $('#final_amount').html('￥'+_also);
    sessionStorage.setItem('also',_also);
}


$('.coupon dl').find('input').unbind().bind('keyup',function(){
    _change_use($(this))
});


function _change_use(obj){
    var _this = obj;
    var _val = _this.val() ? _this.val()  : 0;
    
    var _money = _this.parents('dl').data('value');
    
    var _amount =  parseFloat($('#final_amount').data('amount'));
    
    var _type = _this.parents('dl').data('type');
        
    _this.nextAll('font').text('');
    _this.nextAll('font').removeAttr("style");
    
    var _valed = parseFloat(sessionStorage.getItem(_type+'val') ? sessionStorage.getItem(_type+'val') : 0);
    
    if(!/^\d+$/.test(_val) || _val > _money){ //!/^\d+(\.\d+)?$/.test(_val)   !/^[1-9]+(\d+)?$/
        _this.nextAll('font').text('请输入正确的金额');
        _this.nextAll('font').css('color','red');
        return;
    }
    
    if(_val > (_amount + _valed)){
        var _val = _amount + _valed ;
        _this.val(_val)
    }
    
    var _alsoInt = (_amount + _valed - _val);
    
    var _also = parseFloat(_alsoInt).toFixed(2);
    
    sessionStorage.setItem(_type+'val',_val);
    sessionStorage.setItem('also',_alsoInt);

    $('.'+_type+'_fee').find('.jesl').text('-￥'+parseFloat(_val).toFixed(2));
    
    _this.next('.orange').text(parseFloat(_val).toFixed(2)+'元');
    
    $('#final_amount').html('￥'+_also);
    $('#final_amount').data('amount',_alsoInt);
    
}

</script>
<?php else: ?>
<script type="text/javascript">
//sessionStorage.clear();
sessionStorage.removeItem('money');
sessionStorage.removeItem('moneyval');
sessionStorage.removeItem('coin');
sessionStorage.removeItem('coinval');
sessionStorage.removeItem('also');

</script>
<?php endif; ?>
<?php echo $this->fetch('fudong1.html'); ?>
<style>
    .load_div {width:80px; height:100px; background:rgba(0,0,0,0.8); border-radius:10px; color:#fff; position:absolute; left:50%; top:50%; margin:-50px 0 0 -40px; z-index:999999; text-align:center;}
    .load_div img {width:32px; height:32px; display:block; margin:20px auto 8px auto;}
</style>
<div class="load_div" style="display:none;"><img src="/diy/images/load.gif">加载中...</div>
</body>
</html>
