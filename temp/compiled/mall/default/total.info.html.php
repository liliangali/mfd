        <div class="accounts mt clearfix">
            <ul class="priceList">
                <li><span class="fl">商品件数：</span><span class="fr"><?php echo $this->_var['cart']['check_num']; ?>件</span></li>
                <li><span class="fl">金额合计：</span><span class="fr"><?php echo price_format($this->_var['cart']['check_amount']); ?>元</span></li>
                <li style="display:none"><span class="fl">酷卡：</span><span class="fr">-300元</span></li>
                <li style="display:none"><span class="fl">优惠券：</span><span class="fr">-300元</span></li>
                <li style="display:none"><span class="fl">麦富迪币：</span><span class="fr">-300元</span></li>
                <li style="display:none"><span class="fl">余额：</span><span class="fr">-5000元</span></li>
                <li><span class="fl"><?php if ($this->_var['cart']['free_shipping']): ?>(免)<?php endif; ?>运费：</span><span class="fr"><?php if ($this->_var['cart']['free_shipping']): ?>0.00<?php else: ?><?php echo $this->_var['defShips']['post_fee']; ?><?php endif; ?>元</span></li>
                <li><p><span class="fl">应付总额：</span>
                    <span class="fr orange priceNum" data-amount="<?php echo $this->_var['cart']['order_amount']; ?>">31000.00<em>元</em></span>
                </p></li>
            </ul>
            <input type="button" value="立即下单" id="orderDown" class="orderDown fr" />
            <a href="<?php echo $this->build_url(array('app'=>'cart')); ?>" class="backCart fr">返回购物车</a>
            <input type="hidden" id="_sizeinfo" value="" title="尺码信息" />
            <input type="hidden" id="_address" value="" title="配送地址" />
            <input type="hidden" id="_invoice" value="" title="发票信息" />
            <input type="hidden" id="_price" value="5900" title="商品总价" />
            <input type="hidden" id="_over" value="2350" title="余额" />
        </div>