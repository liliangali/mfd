<footer class="footer" id="footerweixin">
    <div class="footer_box">
        <ul>
            <li class="<?php if ($this->_var['currentApp'] == 'default'): ?>act<?php endif; ?>">
                <a href="<?php echo $this->build_url(array('app'=>'default')); ?>">
                    <i class="nav1"></i>
                    <p>首页</p>
                </a>
            </li>
            <li class="<?php if ($this->_var['currentApp'] == 'product'): ?>act<?php endif; ?>">
                <a href="javascript:;">
                    <i class="nav2"></i>
                    <p>尚品</p>
                </a>
                <div class="fl_select">
                    <div class=""><a href="product.html">全部</a></div>
                    <div class=""><a href="product.html?p_id=128516&son_id=0">主粮</a></div>
                    <div class=""><a href="product.html?p_id=128519&son_id=0">零食</a></div>
                    <div class=""><a href="product.html?p_id=128522&son_id=0">湿粮</a></div>
                    <div class=""><a href="product.html?p_id=128526&son_id=0">咬胶</a></div>
                </div>
            </li>
            <li class="<?php if ($this->_var['currentApp'] == 'fdiy'): ?>act<?php endif; ?>">
                <a href="<?php echo $this->build_url(array('app'=>'fdiy','arg0'=>'1','arg1'=>'3')); ?>">
                    <i class="nav3"></i>
                    <p>定制</p>
                </a>
            </li>
            <li class="<?php if ($this->_var['currentApp'] == 'cart'): ?>act<?php endif; ?>">
                <a href="<?php echo $this->build_url(array('app'=>'cart')); ?>">
                    <i class="nav4">
                    	<span><?php echo $this->_var['cart_goods_num']; ?></span>
                    </i>
                    <p>购物车</p>
                </a>
            </li>
            <li class="<?php if ($this->_var['currentApp'] == 'member'): ?>act<?php endif; ?>">
                <a href="<?php echo $this->build_url(array('app'=>'member')); ?>">
                    <i class="nav5"></i>
                    <p>我的</p>
                </a>
            </li>
        </ul>
    </div>
</footer>

<script type="text/javascript">


        var key = true;
        $(".footer li").eq(1).click(function() {

            if(key) {
                $(".fl_select").fadeIn("slow", function() {
                    key = false;
                });

            } else {
                $(".fl_select").fadeOut("fast", function() {
                    key = true;
                });

            }

        })

</script>
<script type="text/javascript">
    function is_weixin(){
        var ua = navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i)=="micromessenger") {
            return true;
        } else {
            return false;
        }
    }
    if(is_weixin()){

    }
    else
    {
        document.getElementById('footerweixin').style.display='none';
    }
</script>