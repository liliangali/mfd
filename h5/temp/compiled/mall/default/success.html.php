<!DOCTYPE html>
<html lang="zh_cn">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="pragma" content="no-cache">
    <title>订单支付成功</title>
    <link rel="stylesheet" type="text/css" href="static/css/style.css" media="screen">
</head>
<body>

<div class="main">  
  <div class="cjcg_box">
    <p class="ddtjcg">订单支付成功</p>
    <ul class="ddbh_ul">
     <li><span>订单编号 : </span><?php echo $this->_var['order']['order_sn']; ?></li>
     <li><span>付款金额 : </span><font><?php echo price_format($this->_var['order']['order_amount']); ?></font></li>
    </ul>
  </div>
  <form action="<?php echo $this->build_url(array('app'=>'order','act'=>'goToPay')); ?>" id="payform" method="post" target="_blank">
    <input type="hidden" name="obj" value="<?php echo $this->_var['obj']; ?>" />
    <input type="hidden" name="os" value="<?php echo $this->_var['order']['order_sn']; ?>" />
  </form>
  
  <p class="ckwdd"><!--<a href="http://www.126.com" class="a_1" onclick="goOrder()">查看我的订单</a>--><a href="/"  class="a_2" onclick="goIndex()">返回首页</a></p>
  <!--<p class="yjdj">如果有任何问题都可以致电给400-989-9899感谢您对衣尚网的信任！</p>-->
</div>
<script>
function goIndex(){
	app.done();
}
function goOrder(){
	 app.toOrder();
}
</script>
</body>
</html>