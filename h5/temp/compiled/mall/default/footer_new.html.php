<div class="basenav">
        <div class="wrap clearfix">
        <ul>
            <li <?php if ($this->_var['currentApp'] == 'default'): ?>class="on"<?php endif; ?>>
                <a href="/" class="a1"><p>首页</p></a>
            </li>
            <li <?php if ($this->_var['currentApp'] == 'product'): ?>class="on"<?php endif; ?>>
                <a href="<?php echo $this->build_url(array('app'=>'product','act'=>'index')); ?>" class="a2"><p>分类</p></a>
            </li>
            <li >
                <a href="/fdiy-1-3.html" class="a3"><p>定制</p></a>
            </li>
            <li>
                <a href="/cart-index.html" class="a4"><p>购物车</p></a>
            </li>
            <li <?php if ($this->_var['currentApp'] == 'member'): ?>class="on"<?php endif; ?>>
                <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'index')); ?>" class="a5"><p>我的</p></a>
            </li>
        </ul>
    </div>
    <div class="sta"></div>
</div>
<script>
    //检测是否是微信浏览器
    function is_weixin() {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == "micromessenger") {
            return true;
        } else {
            return false;
        }
    }
    if (is_weixin()) {
        document.getElementById("header").style.display = 'none';
    }
</script>
