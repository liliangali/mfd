<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<div class="user_box">
	<?php echo $this->fetch('member.menu.html'); ?>
    <div class="user_right user_rights fr">
        <div class="lntegral ddxqbor">
        	<p class="mlntegral fl">订单编号：<?php echo $this->_var['order']['order_sn']; ?></p>
            <p class="fr xqtada">时间：<?php echo local_date("Y-m-d H:i:s",$this->_var['order']['add_time']); ?>&nbsp;&nbsp;&nbsp;&nbsp;支付方式：<?php echo $this->_var['order']['payment_name']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
             <?php if ($this->_var['order']['status'] == 11): ?>
				<a href="<?php echo $this->build_url(array('app'=>'order','act'=>'paycenter')); ?>?id=<?php echo $this->_var['order']['order_sn']; ?>" class=" ddxqytc" data-orderid="<?php echo $this->_var['order']['order_id']; ?>">去支付</a>
             <?php endif; ?>
            
              <!-- <?php if ($this->_var['options']['pay'] == 1 && $this->_var['options']['change'] != 1): ?>
                  <form action="<?php echo $this->build_url(array('app'=>'order','act'=>'goToPay')); ?>" method="POST">
                    <input type="hidden" name="os" value="<?php echo $this->_var['order']['order_sn']; ?>">
                    <input type="hidden" name="obj" value="order">
                    <input type="submit" value="去支付" style="background:none; height:42px; padding-left:20px; font-size:16px; color:#e66800; cursor:pointer;">
                  </form>
             <?php endif; ?> -->
            </p>
        </div>
    <!--     <div class="rate">
        <?php if ($this->_var['order']['status'] != 0): ?>
            <dl>
            	<dd class="jdcur">待付款</dd>
                <dt class="jdcursj <?php if ($this->_var['progress']['pay']): ?>jdcursjs<?php endif; ?>">占位</dt>
                <dd<?php if ($this->_var['progress']['pay']): ?> class="jdcur"<?php endif; ?>>已付款</dd>
                <dt<?php if ($this->_var['progress']['pay']): ?> class="jdcursj<?php if ($this->_var['progress']['onstream']): ?> jdcursjs<?php endif; ?>"<?php endif; ?>>占位</dt>
                <dd<?php if ($this->_var['progress']['onstream']): ?> class="jdcur"<?php endif; ?>>生产中</dd>
                <dt<?php if ($this->_var['progress']['onstream']): ?> class="jdcursj<?php if ($this->_var['progress']['ship']): ?> jdcursjs<?php endif; ?>"<?php endif; ?>>占位</dt>
                <dd<?php if ($this->_var['progress']['ship']): ?> class="jdcur"<?php endif; ?>>已发货</dd>
                <dt<?php if ($this->_var['progress']['ship']): ?> class="jdcursj<?php if ($this->_var['progress']['finish']): ?> jdcursjs<?php endif; ?>"<?php endif; ?>>占位</dt>
                <dd<?php if ($this->_var['progress']['finish']): ?> class="jdcur"<?php endif; ?>>已完成</dd>
                <dt<?php if ($this->_var['progress']['finish']): ?> class="jdcur xqspecials"<?php else: ?> class="xqspecial"<?php endif; ?>>占位</dt>
            </dl>
            <ul>
            <?php $_from = $this->_var['progressTime']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'time');if (count($_from)):
    foreach ($_from AS $this->_var['time']):
?>
            	<li><?php echo local_date("Y-m-d H:i:s",$this->_var['time']); ?></li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
       <?php else: ?>
            <dl>
             <dd class="jdcur">已取消</dd>
             <dt class="jdcursj xqspecials">占位</dt>
            </dl>
            <ul>
            	<li><?php echo local_date("Y-m-d H:i:s",$this->_var['status'][$this->_var['order']['status']]); ?></li>
            </ul>
       <?php endif; ?>
        </div> -->
        
        <!--<div class="logistics">
        	<h5>物流信息</h5>
            <div class="state">
            	<p class="fl">订单状态：</p>
                <div class="fl ddcolor">
                	<p><?php echo $this->_var['lang']['ORDER_STATUS'][$this->_var['order']['status']]; ?></p>
                	 <?php if ($this->_var['order']['status'] != 0): ?>
                    <p><?php echo local_date("Y-m-d H:i:s",$this->_var['currentTime']); ?></p>
                    <?php else: ?>
                    <p><?php echo local_date("Y-m-d H:i:s",$this->_var['status'][$this->_var['order']['status']]); ?></p>
                    <?php endif; ?>
                    <div class="wlsee">
                    <?php if ($this->_var['order']['waybillno']): ?>
                    	<a href="#">查看物流</a>
                        <div class="ckwlxx">
                        	<h6>顺丰快递：<?php echo $this->_var['order']['waybillno']; ?></h6>
                        	<ul>
                        	<li><iframe name="kuaidi100" src="<?php echo $this->_var['retUrl']; ?>" width="600" height="380" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no"></iframe></li>
                        	<li><a href=" http://www.kuaidi100.com">数据由快递100提供</a></li>
                        	</ul>                                                  
                        </div>
                        <?php endif; ?>
                   	</div>
                   
                </div>
            </div>
        </div>-->
        <div class="logistics" style="margin-top:14px;">
			<div class="boxs">
              <div class="wltext" style="display:none;">
                  <p>物流公司: <?php echo $this->_var['order']['delivery']['logi_name']; ?>&nbsp;&nbsp;&nbsp;&nbsp;运单号: <?php echo $this->_var['order']['delivery']['logi_no']; ?></p>
                  <div>
                   <div class="state">
                   <?php $_from = $this->_var['order']['delivery']['wuliu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
                <p><?php echo $this->_var['item']['ftime']; ?>：<?php echo $this->_var['item']['context']; ?> 
                </p>
               <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </div>
            
            
                  </div>
              </div>
              <h1>查看物流详情<i class="iconfont"></i></h1>
          </div>
        </div>
        
        <div class="logistics">
        	<h5>收货信息</h5>
            <div class="state">
            	<p>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名：<?php echo $this->_var['order']['ship_name']; ?></p>
                <p>收货地址：<?php echo $this->_var['order']['ship_addr']; ?></p>
                <p>联系电话：<?php echo $this->_var['order']['ship_mobile']; ?></p>

                <!-- <?php if ($this->_var['options']['pay'] == 1): ?>
                 <a href="javascript:;" class="changePay ddxqytc" data-orderid="<?php echo $this->_var['order']['order_id']; ?>">修改支付方式</a>
                <?php endif; ?> -->
                </p>
            </div>
        </div>
        <div class="logistics">
        	<h5>发票信息</h5>
        	
            <div class="state">
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
        </div>
        <div class="logistics">
        	<h5>订单备注</h5>
            <div class="state"><?php echo ($this->_var['order']['memo'] == '') ? '无' : $this->_var['order']['memo']; ?></div>
        </div>
        <div class="logistics" id="pingjia">
        	<h5>商品清单</h5>
            <table width="100%" frame="void" rules="none" cellspacing="0" border="0" class="spqdxx">
              <tr class="ycpxx">
                <td>商品信息</td>
                <td>金额</td>
                <td></td>
              </tr>
              <?php $_from = $this->_var['order']['order_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
              <tr>
                <td class="cpxxsj">
                	<p class="fl cppic"><a href="<?php if ($this->_var['goods']['type'] == 'custom'): ?><?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['goods']['params']['oGoods']['goods_id'])); ?><?php else: ?><?php echo $this->build_url(array('app'=>'fdiy')); ?><?php endif; ?>" <?php if ($this->_var['goods']['type'] == 'custom'): ?>target="_blank"<?php endif; ?>"><img src="<?php echo $this->_var['goods']['goods_image']; ?>"></a></p>
                    <p class="fl cpxxword"><a href="<?php if ($this->_var['goods']['type'] == 'custom'): ?><?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['goods']['params']['oGoods']['goods_id'])); ?><?php else: ?><?php echo $this->build_url(array('app'=>'fdiy')); ?><?php endif; ?>" <?php if ($this->_var['goods']['type'] == 'custom'): ?>target="_blank"<?php endif; ?>">
                        <?php echo $this->_var['goods']['goods_name']; ?>
                        </a></p>
                    <p class="fl cpxxword">
                        <?php if ($this->_var['goods']['type'] == 'fdiy'): ?>
                       <?php echo $this->_var['goods']['spe_name']; ?>
                        <?php else: ?>
                        <?php $_from = $this->_var['goods']['spe_name']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
                        <?php echo $this->_var['item']['p_name']; ?>:<?php echo $this->_var['item']['s_name']; ?>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

                        <?php endif; ?>
                    </p>
                </td>
                <td><?php echo price_format($this->_var['goods']['price']); ?>*<?php echo $this->_var['goods']['quantity']; ?></td>
                <td><?php if ($this->_var['order']['status'] == 40): ?>
                    <?php if ($this->_var['goods']['comment']): ?>
                    <?php if ($this->_var['goods']['type'] == 'custom'): ?>
                    <a href="<?php echo $this->build_url(array('app'=>'my_comment','act'=>'index','arg0'=>'1','arg1'=>'commented')); ?>#<?php echo $this->_var['goods']['rec_id']; ?>" >查看评论</a>
                    <?php else: ?>
                    <?php endif; ?>
                    <?php else: ?>
                    <a href="javascript:;"  value="<?php echo $this->_var['goods']['rec_id']; ?>" data-type="<?php echo ($this->_var['goods']['type'] == '') ? 'custom' : $this->_var['goods']['type']; ?>" class="comment ddxqplcolor">评论</a>
                    <?php endif; ?>
                    <?php endif; ?></td>
              </tr>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </table>
            <div class="paymentxx">
            	<p>订单金额：<?php echo price_format($this->_var['order']['order_amount']); ?></p>
            	<?php if ($this->_var['order']['discount'] > 0): ?>
                <p>抵用券：- <?php echo price_format($this->_var['order']['discount']); ?></p>
                <?php endif; ?>
               <!--  <?php if ($this->_var['order']['coin'] > 0): ?>
                 <p>麦富迪币：- <?php echo price_format($this->_var['order']['coin']); ?></p>
                <?php endif; ?> -->
                <!-- <?php if ($this->_var['order']['money_amount'] > 0): ?>
                <p>余额：- <?php echo price_format($this->_var['order']['money_amount']); ?></p>
                <?php endif; ?> -->
                <p>应付总额：<span><?php echo price_format($this->_var['order']['final_amount']); ?></span></p>
                <p>
             <?php if ($this->_var['order']['status'] == 11): ?>
				<a href="<?php echo $this->build_url(array('app'=>'order','act'=>'paycenter')); ?>?id=<?php echo $this->_var['order']['order_sn']; ?>" class="pay ddxqytc" data-orderid="<?php echo $this->_var['order']['order_id']; ?>">去支付>></a>
             <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>
<div id="window005" style="display:none;">
	<div class="tccontent">
    	<h4>更改支付方式</h4>
        <p class="ddtcword">由于您的订单来自微信，且使用的是微信支付，因WEB版暂时不支持微信支付，如要在WEB版完成支付，请切换相应的支付方式。</p>
        <div class="zfway">
        <?php $_from = $this->_var['payments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pay');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['pay']):
        $this->_foreach['loop']['iteration']++;
?>
        	<a href="javascript:void(0);" <?php if (($this->_foreach['loop']['iteration'] <= 1)): ?>class="zffscur"<?php endif; ?> data-payid="<?php echo $this->_var['pay']['payment_id']; ?>"><?php echo $this->_var['pay']['payment_name']; ?></a>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
        <input type="hidden" name="orderid" value="0">
        <form action="<?php echo $this->build_url(array('app'=>'order','act'=>'goToPay')); ?>" method="POST">
        <input type="hidden" name="os" value="">
        <input type="hidden" name="obj" value="order">
        </form>
        <a href="javascript:void(0);" class="confirmzf">确认支付</a>
    </div>	
</div>

<div id="window006" style="display:none;">
	<div class="tccontent">
    	<h4>更改支付方式</h4>
        <div class="zfway">
        <?php $_from = $this->_var['payments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pay');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['pay']):
        $this->_foreach['loop']['iteration']++;
?>
        	    <a href="javascript:void(0);" <?php if (($this->_foreach['loop']['iteration'] <= 1)): ?>class="zffscur"<?php endif; ?> data-payid="<?php echo $this->_var['pay']['payment_id']; ?>"><?php echo $this->_var['pay']['payment_name']; ?></a>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
        <input type="hidden" name="orderid" value="0">
        <a href="javascript:void(0);" class="confirmzf">确认修改支付方式</a>
    </div>	
</div>

<?php echo $this->fetch('footer.html'); ?>
<script>
var orderid = '<?php echo $this->_var['order']['order_id']; ?>';
</script>
<div id="window09" style="display:none;">	
</div>

<div id="window10" style="display:none;">

</div>

<div id="window03" style="display:none;">
    <div class="branch">
    	<!--星级评论打分开始-->
        <div id="star">
            <span class="btxxh">评分：</span>
            <ul data-num="0">
                <li><a href="javascript:;">1</a></li>
                <li><a href="javascript:;">2</a></li>
                <li><a href="javascript:;">3</a></li>
                <li><a href="javascript:;">4</a></li>
                <li><a href="javascript:;">5</a></li>
            </ul>
        </div>
        <div class="impression">
            <span class="btxxh">印象：</span>
            <ul>
               <?php $_from = $this->_var['impress']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'im');$this->_foreach['im'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['im']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['im']):
        $this->_foreach['im']['iteration']++;
?>
                <li ><?php echo $this->_var['im']['impress_name']; ?></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
        </div> 
        <!--星级评论打分结束-->
        <p class="xd"><textarea name="content" id="content" cols="" rows="" placeholder="快速写下你的评价，分享给大家吧！"></textarea></p> 
     
        <input type="button" value="评论" class="ltbut" />
    </div>
</div>
<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/static/pc/js/usercenter.js"></script>
<script src="/public/global/luck/pc/luck.js"></script>
<script>
cotteFn.customer9()
cotteFn.customer10()
cotteFn.customer11()
cotteFn.amount10()
cotteFn.customer005()
cotteFn.customer006()
</script>
<script type="text/javascript">
	$(function(){
		$(".boxs h1").click(function(){
			$(".wltext").toggle(500);
			if($(".boxs>h1>i").attr("class")=="iconfont"){
				$(".boxs>h1>i").removeClass().addClass("iconfonts");
			}else{
				$(".boxs>h1>i").removeClass().addClass("iconfont");
			}
		})
	})
</script>