<div id="box">
<?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'lists');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['lists']):
?>
    <div class="imagetx">
    	<h4><p class="ddh fl">订单号:<?php echo $this->_var['lists']['order_sn']; ?> </p><p class="dfk fr"><?php echo $this->_var['lists']['status_name']; ?></p></h4>
        <a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'detail','arg'=>$this->_var['lists']['order_id'])); ?>">
        <dl class="c_db">
		<?php $_from = $this->_var['lists']['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'goods');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['goods']):
        $this->_foreach['loop']['iteration']++;
?>
        	<dd><img src="<?php echo $this->_var['goods']['goods_image']; ?>" width="42" height="42"></dd>
          <?php if ($this->_foreach['loop']['total'] == 1): ?>
          <dt class="c_bf1">
            <p><?php echo $this->_var['goods']['goods_name']; ?></p>
            <p style="color:#717171;">
              <?php if ($this->_var['goods']['type'] == 'fdiy'): ?>
              定制
              <?php else: ?>

              <?php endif; ?>
            </p>
            </dt>
            <dt style="margin-top:6px;"><?php echo price_format($this->_var['goods']['price']); ?>×<?php echo $this->_var['goods']['quantity']; ?></dt>
          <?php endif; ?>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </dl>
        </a>
        <dl><div class="sjfk">实付款：  <span style="color:#e66800"><?php echo price_format($this->_var['lists']['final_amount']); ?></span></div>
        <div class="ddbut">
            <?php if ($this->_var['lists']['status'] == 11): ?>
                 <a href="javascript:;" onclick="luck3(<?php echo $this->_var['lists']['order_id']; ?>)" class="dingdxx">取消订单</a>
            <?php endif; ?>
            <?php if ($this->_var['lists']['status'] == 11): ?>
            <a href="<?php echo $this->build_url(array('app'=>'order','act'=>'paycenter')); ?>?id=<?php echo $this->_var['lists']['order_sn']; ?>" class="pay dingdxx" data-orderid="<?php echo $this->_var['lists']['order_id']; ?>">去支付</a>
            <?php endif; ?>
            <?php if ($this->_var['lists']['status'] == 30): ?>
                  <a href="/my_order-checkExpressInfo-<?php echo $this->_var['lists']['order_id']; ?>.html" class="dingdxx">查看物流</a>
                 <a href="javascript:;" onclick="luck4(<?php echo $this->_var['lists']['order_id']; ?>)" class="dingdxx">确认认货</a>
            <?php endif; ?>
        </div>
        </dl>

    </div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<script src="public/global/luck/mobile/luck.js"></script>
<script>
function luck3(id){
	
	luck.open({
		title: ['取消订单','background:#e59501;color:#fff'],//标题
		content: '确认取消该订单吗？',//内容
		btn:['确认','取消'],
		yes:function(){
			location.href="my_order-operation-cancel-"+id+".html"
			luck.close()
		},
		no: function(){
			luck.close()	
		}
	});
	//_hmt.push(['_trackEvent','luck-mobile-询问框','click'])
}
function luck4(id){
	
	luck.open({
		title: ['确认收货','background:#e59501;color:#fff'],//标题
		content: '确认收货吗？',//内容
		btn:['确认','取消'],
		yes:function(){
			location.href="my_order-operation-finish-"+id+".html"
			luck.close()
		},
		no: function(){
			luck.close()	
		}
	});
	//_hmt.push(['_trackEvent','luck-mobile-询问框','click'])
}
</script>
