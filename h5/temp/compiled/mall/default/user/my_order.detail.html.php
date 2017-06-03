<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1, user-scalable=no, minimal-ui" >
<title>订单详情</title>
<link rel="stylesheet" type="text/css" href="/static/css/mobile_style.css" media="screen">
<link type="text/css" href="public/static/wap/css/productlist.css" rel="stylesheet" />
</head>

<body>
	<div class="container">
    <header class="hdtop topBar">
        <div class="edit fl">
                <p class="p1"><a href="javascript:;" onClick="history.go(-1)"><img src="public/static/wap/images/tw_03.png"></a></p>
                <p class="p2">订单详情</p>
            </div>
    </header>
        <div class="topnoe">
        	<p class="fl">订单金额：¥<?php echo ($this->_var['order']['order_amount'] == '') ? '0.00' : $this->_var['order']['order_amount']; ?></p>
            <p class="fr"><?php echo $this->_var['order']['status_name']; ?></p>
        </div>
        <div class="xqztdata">
        <ul class="xqzttstx">
        	    <li>单号：<?php echo $this->_var['order']['order_sn']; ?></li>
            	<li>时间：<?php echo local_date("Y-m-d H:i:s",$this->_var['order']['add_time']); ?></li>
                <li>支付：<?php echo $this->_var['order']['payment_name']; ?></li>
            </ul>
        </div>
        <p class="jgt"></p>
        <div class="xqztdata">
        	<h4>收货信息</h4>
            <p>姓名：<?php echo $this->_var['order']['ship_name']; ?></p>
            <p>手机：<?php echo $this->_var['order']['ship_mobile']; ?></p>
            <p>地址：<?php echo $this->_var['order']['ship_area']; ?> <?php echo $this->_var['order']['ship_addr']; ?></p>
        </div>
        <p class="jgt"></p>
		<div class="xqztdata">
          <h4>物流信息</h4>
          <p>物流公司：<?php if ($this->_var['order']['delivery']['logi_no']): ?><?php echo $this->_var['order']['delivery']['logi_name']; ?><?php else: ?>暂未发货<?php endif; ?></p>
          <p>运单编号：<?php echo $this->_var['order']['delivery']['logi_no']; ?></p>
          <!-- <p onclick="checkCompanyCode(this)"><input type='button' />查看</p> -->
          <?php if ($this->_var['order']['waybillno']): ?>
          <p ><a href="/my_order-checkExpressInfo-<?php echo $this->_var['order']['order_id']; ?>.html">查看物流</a></p>
          <?php endif; ?>
		</div>
        <p class="jgt"></p>
        <div class="xqztdata">
        	<h4>订单备注</h4>
            <p><?php echo ($this->_var['order']['memo'] == '') ? '无' : $this->_var['order']['memo']; ?></p>
        </div>
        <p class="jgt"></p>

        <div class="xqztdata">
        	<h4>发票信息</h4>
					<?php if ($this->_var['order']['invoice_need'] == 1): ?>
								<h3><?php echo $this->_var['invoice']['title']; ?></h3>
						<?php if ($this->_var['order']['invoice_com'] == 3): ?>
								<?php $_from = $this->_var['order']['invoice']['content']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'voice');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['voice']):
?>
								<p><?php echo $this->_var['invoice_lang'][$this->_var['key']]; ?>：<?php echo $this->_var['voice']; ?></p>
								<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						<?php else: ?>
							 <p><?php echo $this->_var['invoice']['content']; ?></p>
						<?php endif; ?>
				 <?php else: ?>
								 <p>不需要开发票</p>
				 <?php endif; ?>
		</div>
        <p class="jgt"></p>

        <div class="ztspqd">
        	<h3>商品清单</h3>
           <?php $_from = $this->_var['order']['order_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'orders');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['orders']):
?>

            <div class="ztimgtex">
                <div class="ztspxxpic fl"><img src="<?php echo $this->_var['orders']['goods_image']; ?>"></div>
                <div class="zttitword fl">
                    <h4><?php echo $this->_var['orders']['goods_name']; ?></h4>
                    <div class="ztyuan ztytop">¥<?php echo $this->_var['orders']['price']; ?>×<?php echo $this->_var['orders']['quantity']; ?></div>
                    <?php if ($this->_var['orders']['type'] == 'fdiy'): ?>
                    <div class="ztyuan"><?php echo $this->_var['orders']['spe_name']; ?></div>
                    <?php else: ?>
                    <div class="ztyuan ztytop"><?php echo $this->_var['orders']['params']['oProducts']['spec_info']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="jgpl">
                <?php if ($this->_var['orders']['comment'] && $this->_var['order']['status'] == 40): ?>
                  <a href="/comment.html">查看评论</a>
                <?php endif; ?>
                <?php if (! $this->_var['orders']['comment'] && $this->_var['order']['status'] == 40): ?>
                  <a href="/my_comment-publish-<?php echo $this->_var['orders']['rec_id']; ?>-<?php echo $this->_var['orders']['type']; ?>.html">发表评论</a>
                <?php endif; ?>
                </div>
            </div>

           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
        <p class="jgt"></p>
        <div class="balance">
        		<div class="bottnoe">
                <p class="fl">订单金额</p>
                <p class="fr colorj"><?php echo price_format($this->_var['order']['final_amount']); ?></p>
            </div>
							<?php if ($this->_var['order']['discount'] > 0): ?>
							<div class="bottnoe">
							<p class="fl">券</p>
							<p class="fr colorj">- <?php echo price_format($this->_var['order']['discount']); ?></p>
							</div>
							<?php endif; ?>
							<!-- <?php if ($this->_var['order']['coin'] > 0): ?>
							<div class="bottnoe">
								<p class="fl">麦富迪币</p>
							  <p class="fr colorj">- <?php echo price_format($this->_var['order']['coin']); ?></p>
							</div>
							<?php endif; ?> -->
							<!-- <?php if ($this->_var['order']['money_amount'] > 0): ?>
							<div class="bottnoe">
								<p class="fl">余额</p>
								<p class="fr colorj">- <?php echo price_format($this->_var['order']['money_amount']); ?></p>
							</div>
							<?php endif; ?> -->
        </div>
        <div class="actual">应付总额：<span><?php echo price_format($this->_var['order']['final_amount']); ?></span></div>
        <div class="qfkbot fr">
        <?php if ($this->_var['order']['status'] == 11): ?>           
            <a href="order-paycenter.html?id=<?php echo $this->_var['order']['order_sn']; ?>" class="payable fr">去付款</a>
            <a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'operation','arg0'=>'cancel','arg1'=>$this->_var['order']['order_id'])); ?>" class="payable fr">取消订单</a>
        <?php endif; ?>
        
         <?php if ($this->_var['order']['status'] == 30): ?>
            <a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'operation','arg0'=>'finish','arg1'=>$this->_var['order']['order_id'])); ?>" class="payable fr">确认收货</a>
        <?php endif; ?>
        </div>
    </div>

    <script src="http://r.cotte.cn/global/jquery-1.8.3.min.js"></script>
     <script src="http://r.cotte.cn/global/luck/mobile/luck.js"></script>
    <script>
        
        function checkCompanyCode(){
            var corp_code="<?php echo $this->_var['order']['delivery']['corp_code']; ?>"
            var logi_no="<?php echo $this->_var['order']['delivery']['logi_no']; ?>"
            console.log(logi_no)
            if(corp_code.length==0){
                console.log
                $.getJSON('http://www.kuaidi100.com/autonumber/auto?num='+logi_no+'&key=fYgHSpmg2046&callback=?',function(res){
                    // var re=eval('('+res+')')
                        console.log(res[0]['comCode'])
                })
                /*var sign=md5()
                 $.post('http://poll.kuaidi100.com/poll/query.do',{customer:BE3A5D9D03C941D8C2A8B5B7B6D88D8A,},function(res){
                        console.log(res)
                },'json')*/
            }
        }

    	function loseer(obj){
    		var id=$('.payable').attr('data-id');
    		luck.open({
				title:['提示'],
				content: '确定取消该订单吗？',
				btn:['确定','取消'],
				yes:function(){
				$.ajax({
        		url:"/order-loseer.html",
                type: "POST",
        		data:{
        			id:id
        		},
        		success: function(res){
        			window.location.href='/order-orderindex.html?status=all';
        		}

        	})

				},
				no: function(){
					luck.close()
				}

		});

    	}



    </script>


</body>
</html>
