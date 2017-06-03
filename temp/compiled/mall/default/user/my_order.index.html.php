<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<div class="user_box">
	<?php echo $this->fetch('member.menu.html'); ?>
    <div class="user_right user_rights fr">
        <div class="lntegral"><p class="mlntegral fl">我的订单</p></div>
        <div class="obtain">
            <dl>
            	<dd><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index')); ?>" <?php if (! $this->_var['status']): ?>class="jfcolor"<?php endif; ?>>全部订单</a></dd>
                <dd><span></span></dd>
                <dd><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'unpay')); ?>" <?php if ($this->_var['status'] == 'unpay'): ?>class="jfcolor"<?php endif; ?>>待付款(<?php echo $this->_var['order_num']['order_daifuk']; ?>)</a></dd>
                <dd><span></span></dd>
                <dd><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'payed')); ?>" <?php if ($this->_var['status'] == 'payed'): ?>class="jfcolor"<?php endif; ?>>待发货(<?php echo $this->_var['order_num']['order_daifah']; ?>)</a></dd>
                <dd><span></span></dd>
                 <dd><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'shipped')); ?>" <?php if ($this->_var['status'] == 'shipped'): ?>class="jfcolor"<?php endif; ?>>待收货(<?php echo $this->_var['order_num']['order_daishouf']; ?>)</a></dd>
                <dd><span></span></dd>
                <dd><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'finished')); ?>" <?php if ($this->_var['status'] == 'finished'): ?>class="jfcolor"<?php endif; ?>>已确认(<?php echo $this->_var['order_num']['order_queren']; ?>)</a></dd>
            </dl>
        </div>
        <div class="content">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['order']):
?>
                <tbody>
                    <tr class="tr_th">
                        <td colspan="4">
                            <span class="tcol1">订单编号: <?php echo $this->_var['order']['order_sn']; ?></span>
                            <span class="tcol2"><?php echo $this->_var['order']['ship_name']; ?></span>
                            <span class="tcol3"><?php echo local_date("Y-m-d H:i:s",$this->_var['order']['add_time']); ?></span>
                         <!--    <span class="tcol4"><a href="javascript:void(0)" class='zhichi'>在线客服</a></span> -->
                        </td>
                    </tr>
                    <tr class="tr_tb">
                        <td>
                            <ul class="tr_list clearfix">
                            <?php $_from = $this->_var['order']['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['list']['iteration']++;
?>
                                <li <?php if (($this->_foreach['list']['iteration'] == $this->_foreach['list']['total'])): ?>style="border:none;"<?php endif; ?>>
                                    <div class="tr_list-img fl">
                                        <a href="<?php if ($this->_var['goods']['type'] != 'custom'): ?><?php echo $this->build_url(array('app'=>'my_order','act'=>'detail','arg'=>$this->_var['order']['order_id'])); ?><?php else: ?><?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['goods']['params']['oGoods']['goods_id'])); ?><?php endif; ?>" <?php if ($this->_var['goods']['type'] == 'custom'): ?>target="_blank"<?php endif; ?>><img src="<?php echo $this->_var['goods']['goods_image']; ?>"></a>
                                    </div>
                                    <div class="tr_list-td fl">
                                        <h3><a href="<?php if ($this->_var['goods']['type'] != 'custom'): ?><?php echo $this->build_url(array('app'=>'my_order','act'=>'detail','arg'=>$this->_var['order']['order_id'])); ?><?php else: ?><?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['goods']['params']['oGoods']['goods_id'])); ?><?php endif; ?>" <?php if ($this->_var['goods']['type'] == 'custom'): ?>target="_blank"<?php endif; ?>><?php echo $this->_var['goods']['goods_name']; ?></a></h3>
                                        <?php if ($this->_var['goods']['type'] == 'fdiy'): ?>
                                        <!--<p style="color:#717171;">定制</p>-->
                                        <?php else: ?>
                                        <?php endif; ?>
                                        <span><?php echo price_format($this->_var['goods']['price']); ?></span>
                                        <?php if ($this->_var['order']['status'] == 40): ?>

                                        <?php if ($this->_var['goods']['comment']): ?>
                                        <!--<a href="<?php echo $this->build_url(array('app'=>'my_comment','act'=>'index','arg0'=>'1','arg1'=>'commented')); ?>#<?php echo $this->_var['goods']['rec_id']; ?>" >查看评论</a>-->
                                        <?php else: ?>
                                        <!--<a href="javascript:;"  value="<?php echo $this->_var['goods']['rec_id']; ?>" data-type="<?php echo ($this->_var['goods']['type'] == '') ? 'custom' : $this->_var['goods']['type']; ?>" class="comment ddxqplcolor">评论</a>-->
                                        <?php endif; ?>

                                        <?php endif; ?>

                                    </div>
                                </li>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            </ul>
                        </td>
                        <td class="jgcolor"><span><?php echo price_format($this->_var['order']['final_amount']); ?></span>
                        <?php if ($this->_var['order']['source_from'] != 'pc'): ?>
                        <p class="sjddbs">手机订单</p>
                        <?php endif; ?>
                        </td>
                        <td class="ddcolor"><span><?php echo $this->_var['order']['status_name']; ?></span></td>
                        <td><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'detail','arg'=>$this->_var['order']['order_id'])); ?>" class="dingdxx">订单详情&nbsp;></a>
                            <?php if ($this->_var['order']['status'] == 40): ?>
                            <a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'detail','arg'=>$this->_var['order']['order_id'])); ?>#pingjia" class="dingdxx">评论&nbsp;></a>
                            <?php endif; ?>
                        <?php if ($this->_var['order']['_opt_pay'] == 1 && $this->_var['order']['_opt_change'] != 1): ?>
                         <form action="<?php echo $this->build_url(array('app'=>'order','act'=>'goToPay')); ?>" method="POST">
                            <input type="hidden" name="os" value="<?php echo $this->_var['order']['order_sn']; ?>">
                            <input type="hidden" name="obj" value="order">
                            <input type="submit" value="去支付" class="qzfput">
                         </form>                         
                        <?php endif; ?>
                        <?php if ($this->_var['order']['status'] == 11): ?>
                             <a href="javascript:;" onclick="if(confirm('确认取消该订单吗？')) location.href='<?php echo $this->build_url(array('app'=>'my_order','act'=>'operation','arg0'=>'cancel','arg1'=>$this->_var['order']['order_id'])); ?>'" class="dingdxx">取消订单 ></a>
                              <a href="<?php echo $this->build_url(array('app'=>'order','act'=>'paycenter')); ?>?id=<?php echo $this->_var['order']['order_sn']; ?>" class=" dingdxx" data-orderid="<?php echo $this->_var['order']['order_id']; ?>">去支付</a>
                        <?php endif; ?>



                        <?php if ($this->_var['order']['status'] == 30): ?>
                             <a href="javascript:;" onclick="if(confirm('确认收货吗？')) location.href='<?php echo $this->build_url(array('app'=>'my_order','act'=>'operation','arg0'=>'finish','arg1'=>$this->_var['order']['order_id'])); ?>'" class="dingdxx">确认认货 ></a>
                        <?php endif; ?>

                        </td>
                    </tr>
                </tbody>
                <?php endforeach; else: ?>
                <tbody>
                    <tr class="tr_th">
                        <td>
                        	<div class="empty">
                                <div>未匹配到订单数据<br><br><p><a href="./" class="cc_btn s_btn">麦富迪首页</a></p></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </table>
        </div>
        <?php echo $this->fetch('page.bottom.html'); ?>
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
<?php echo $this->fetch('../zhichikefu.html'); ?>
<?php echo $this->fetch('footer.html'); ?>
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
<script src="/public/global/jquery.swipe.js"></script> 
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/static/pc/js/orderinfo.js"></script>
<script src="/public/static/pc/js/usercenter.js"></script>
<script src="/public/global/luck/pc/luck.js"></script>

<script>
cotteFn.customer005();
cotteFn.amount10()
cotteFn.orderInfo();
cotteFn.index()
</script>