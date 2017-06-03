<link rel="stylesheet" type="text/css" href="static/css/fudong.css" media="screen">

<div class="fixd_cart">
    <a href="<?php echo $this->_var['kefu_url']; ?>" class="zhiCustomBtn"><i class="kefu_icon">0</i></a>
    <a href="<?php echo $this->build_url(array('app'=>'cart')); ?>"><i class="cart_icon">0</i></a>
</div>

<script src="https://www.sobot.com/chat/frame/js/entrance.js?sysNum=2b17cdee375a475e963aeed478c37fbf"
        class="zhiCustomBtn"  id="zhichiScript" data-args="manual=true">
</script>
<script>
    //初始化智齿咨询组件实例
    var zhiManager = (getzhiSDKInstance());
    //设置用户信息
    zhiManager.set('userinfo', {
        partnerId:'<?php echo $this->_var['kefu_uinfo']['user_id']; ?>',   //用户对接ID，必填，用户唯一标示，通常是企业自有用户系统中的userId
        tel: '',   //电话或手机
        email: '',   //邮箱
        uname: '<?php echo $this->_var['kefu_uinfo']['user_name']; ?>',   //昵称
        visitTitle: '',   //对话页标题，若不传入系统将获取用户打开咨询组件时所在页面的标题
        visitUrl: '',   //对话页URL，若不传入系统将获取用户打开咨询组件时所在页面的URL
        face: '<?php echo $this->_var['kefu_uinfo']['avatar']; ?>',   //头像URL
        realname: '',   //真实姓名
        weibo: '',   //微博账号
        weixin: '',   //微信账号
        qq: '',   //QQ号
        sex: '', //0 女 1 男
        birthday: '',   //生日，如“1990-01-01”
        remark: '',   //用户的备注信息
        params: ''   //自定义用户信息字段
    });
    //true 自定义咨询按钮 废弃系统默认按钮
    zhiManager.set('customBtn', 'true');
    zhiManager.set('groupId','84c09075813e45f68eae414cf91e897a');
    zhiManager.set('title','欢迎咨询');
    zhiManager.set('powered',false);
    zhiManager.on("receivemessage", function(ret) {
        //code write here ...
        console.log(111);
        console.log(ret);
    });

    //获取离线客服发的消息数
    zhiManager.on("unread.count",function(data){
        console.log(222);
        console.log(data);
        $(".kefu_icon").text(data);
    });

    //再调用load方法
    zhiManager.on("load", function() {
        zhiManager.initBtnDOM();
    });
</script>

<script>
    document.body.addEventListener('touchstart', function () {});
</script>