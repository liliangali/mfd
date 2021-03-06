<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/static/v1/css/xiniu.css">
<div class="user_box">
    <?php echo $this->fetch('member.menu.html'); ?>
    <div class="user_right user_rights fr">
        <h4>修改登录密码</h4>
        <div class="verification">
            <ul>
                <li class="current">1.验证身份</li>
                <li>2.设置新密码</li>
                <li>3.完成</li>
            </ul>
        </div>
        <div class="binding">
            <p class="bdsj" >已绑定手机：<?php echo $this->_var['phone']; ?></p>
            <form method="post" action="member-login_password_2.html" id="login_password1">
            <div class="card">
                <p class="sjyzm fl">手机校验码：</p>
                <input type="text" class="bdinp fl" name="code">
                <input type="hidden" value="<?php echo $this->_var['user']['phone_mob']; ?>" id="phone" name="phone">
                <input type="button" class="bdbut fl" value="获取验证" id="getCode" onclick="sendcode(this)">
                <p class="tssryz fl">请输入手机校验码</p>
            </div>
            <input type="submit" value="下一步" class="bdxyb">
            </form>
        </div>
        <div class="why">
            <h5>为什么要进行身份验证？</h5>
            <p class="bdsm">1. 为保障您的账户信息安全，在变更账户中的重要信息时需要进行身份验证，感谢您的理解和支持。</p>
            <p class="bdsm">2. 若该手机号无法接收验证短信，请拨打客服电话4008-919-867更改验证手机。</p>
        </div>
    </div>
</div>
<?php echo $this->fetch('footer.html'); ?>
<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/static/pc/js/public.js"></script> 
<script src="/public/global/jquery.form.js"></script>
<script src="/public/static/pc/js/usercenter.js"></script> 
<script type="text/javascript">
    //var countdown=60;
    var phone = $('#phone').val();
    var  type = 'findps';
    function sendcode(val) {
        $.post("./member-sendCode.html",{phone:phone,type:type},function(res){
            var res = eval("("+res+")");
            if(res.done){
                settime(val,60);
                
            }
        })
   }

    function settime(obj,n){
		var _self=$(obj),t=_self.val();
		_self.attr('disabled',true).val('倒计时'+(n--)+'秒');
		(function(){
			if(n>0){
				_self.attr('disabled',true).val('倒计时'+(n--)+'秒');
				setTimeout(arguments.callee,1000);
			}else{
				_self.attr('disabled',false).val(t);	
			}	
		})()
    }
    


</script>
<script>
    $(function(){

        $('#login_password1').ajaxForm({
            beforeSubmit:function(){
                var b=$('#login_password1').validate({
                    submitBtn:{
                        flag:true,
                    },
                    acticle:true,
                    error:function(obj,error){cotteFn.error(obj,error)}
                });
                if(!b){
                    return false
                }
            },
            success:function(res){
                var res = eval("("+res+")");
                if(!res.done){
                    alert(res.msg)
                    $('.bdxyb').val('下一步')

                }else{
                    $('.user_box').replaceWith(res.retval.content);
                }

            },
            error:function(){
                alert('error')
                $('.bdxyb').val('下一步')
            }
        })


    });
</script>