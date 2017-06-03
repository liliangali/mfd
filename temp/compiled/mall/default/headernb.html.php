
<header class="topBar" id="header">
    <div class="wrap">
        <h1><?php echo $this->_var['title']; ?></h1>
    </div>
</header>
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
