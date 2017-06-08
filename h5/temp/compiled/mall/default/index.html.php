<!DOCTYPE html>
<html lang="zh_cn">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<title>订单支付</title>
<link rel="stylesheet" type="text/css" href="static/css/style.css" media="screen">
<link rel="stylesheet" type="text/css" href="static/css/layer.css" media="screen">
</head>

<body>
<div class="main">  
  <div class="cjcg_box">
    <p class="ddtjcg">订单支付</p>
    <ul class="ddbh_ul">
     <li><span>订单编号 : </span><?php echo $this->_var['order']['order_sn']; ?></li>
     <li><span>支付方式 : </span><?php echo $this->_var['order']['payment_name']; ?></li>
     <li><span>订单金额 : </span><font><?php echo price_format($this->_var['order']['order_amount']); ?></font></li>
     <?php if ($this->_var['order']['coin'] > 0): ?>
     <li><span>麦富迪币 : </span><font><?php echo $this->_var['order']['coin']; ?></font></li>
     <?php endif; ?>
     
     <?php if ($this->_var['dmoney'] && $this->_var['dmoney'] > 0): ?>
     <li><span>抵扣券 : </span><font><?php echo price_format($this->_var['dmoney']); ?></font></li>
     <?php endif; ?>
     <?php if ($this->_var['order']['money_amount'] > 0): ?>
     <li><span>已付金额 : </span><font><?php echo price_format($this->_var['order']['money_amount']); ?></font></li>
     <?php endif; ?>
     <li><span>还需支付 : </span><font><?php echo price_format($this->_var['order']['final_amount']); ?></font></li>
     
    </ul>
    <div class="fhsy"><a href='/'>返回首页</a></div>
    <div >
      <div class="fdczf">
       <div class="pdz">
        <div class="qzd">如果有任何问题都可以致电给:<span>400-169-8836</span><br/>感谢您对麦富迪的信任与支持！</div>
        <input type="button" value="立即支付" class="ljzf lijijiesan">
        </div>
      </div>
    </div>
  </div>
  <form action="<?php echo $this->build_url(array('app'=>'order','act'=>'goToPay')); ?>?token=<?php echo $this->_var['apptk']; ?>" id="payform" method="post">
    <input type="hidden" name="obj" value="<?php echo $this->_var['obj']; ?>" />
    <input type="hidden" name="os" value="<?php echo $this->_var['order']['order_sn']; ?>" />
  </form>
</div>
<script type=text/javascript src="static/js/jquery-1.8.3.min.js"></script>
<script type=text/javascript src="static/js/layer.m.js"></script>

<script>

    
$(document).ready(function(){
    $(".checkPaymen").click(function(){
        $("#changePayment").show();
    });
    $(".subPay").click(function(){
        var _id = $("input[name=pay]:checked").val();
        var _sn = $("input[name=os]").val();
        $.post("<?php echo $this->build_url(array('app'=>'order','act'=>'change_payment')); ?>",{id:_id,sn:_sn},function(res){
            var res = $.parseJSON(res);
            if(res != null && res.done == true){
                $('.payment_name').html(res.retval.nm)
                $("#changePayment").hide();
            }else{
                $("#changePayment").hide();
            }
        });
    });
    
    var payment = '<?php echo $this->_var['order']['payment_code']; ?>';
    if(payment != 'wxpay')
    {
        $(".fhsy").click(function () {
            app.done();
        });
    }

    $(".lijijiesan").click(function(){
    	if(payment == 'wxpay')
    	{
    		callpay();
    	}
    	else
    	{

    		 dopay();
    	}
       
    })
})

function dopay(){

    /* var msg=layer.open({
        content: '请在新打开的网银页面完成支付！支付完成前请不要关闭此窗口。',
        btn: ['完成支付', '支付失败'],
        shadeClose: false,
        yes: function(){
        	//alert('yes');
            layer.close(msg);
        },
        no : function(){
        	//alert('sad')
        }
    }); */   
    $(".lijijiesan").unbind('click');
    $("#payform").submit();
}


</script>

<script>
//微信支付相关
function jsApiCall()
{
	WeixinJSBridge.invoke(
		'getBrandWCPayRequest',
		<?php echo $this->_var['payData']; ?>,
		function(res){
			//WeixinJSBridge.log(res.err_msg);
			//alert(res.err_code+res.err_desc+res.err_msg);
			if(res.err_msg == 'get_brand_wcpay_request:ok'){
			    location.href='<?php echo $this->build_url(array('app'=>'my_order','act'=>'detail','arg'=>$this->_var['order']['order_id'])); ?>';
		    }
		}
	);
}

function callpay()
{
	if (typeof WeixinJSBridge == "undefined"){
	    if( document.addEventListener ){
	        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
	    }else if (document.attachEvent){
	        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
	        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
	    }
	}else{
	    jsApiCall();
	}
}

</script>




</body>
</html>



