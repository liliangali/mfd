<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<script src="../static/v1/js/jquery.min.js"></script>
<script src="/public/static/pc/js/public.js"></script>
<div class="user_box">
    <?php echo $this->fetch('member.menu.html'); ?>
    <div class="user_right user_rights fr">
        <h4>安全中心</h4>
        <div class="set">
            <div class="hpbfdssz fl">
                <p class="hpjw fl">账户安全级别</p>
                <div  class="hptia fl"><p style="width:<?php echo $this->_var['percent']; ?>%;background-color:<?php echo $this->_var['color']; ?>"></p></div>
                <p class="jiaog fl"><?php echo $this->_var['safe_level']; ?></p>
            </div>
            <p class="baoh fr">互联网账号存在被盗风险，建议您定期更改密码以保护账户安全。</p>
        </div>
        <div class="condition contop">
            <p class="szpassd fl">登录密码</p>
            <p class="szword fl">互联网账号存在被盗风险，建议您定期更改密码以保护账户安全。</p>
             <a href="member-login_password_1.html"><input type="button" value="修改" class="xginp lscolor fr"></a>
        </div>
       <!--  <div class="condition">
            <p <?php if ($this->_var['profit']['pay_password']): ?> class="szpassd fl"  <?php else: ?>class="szpassd szpassdbj fl" <?php endif; ?>>支付密码</p>
            <p class="szword fl">启用支付密码后，在使用账户中麦富迪币可优惠券等资产时，需要输入支付密码。</p>
            <a href="member-pay_password_1.html"><input type="button" class="xginp lscolor fr" id="pay_button" ><input type="hidden" value="<?php echo $this->_var['button']; ?>" id="button"></a>
        </div> -->
         <div class="condition">
            <p <?php if ($this->_var['very']): ?> class="szpassd fl" <?php else: ?>class="szpassd szpassdbj fl" <?php endif; ?>>手机验证</p>
            <p class="szword fl">您验证的手机： <?php echo $this->_var['user']['phone_mob']; ?>   若已丢失或停用，请立即更换，避免账户被盗</p>
              <a href="member-phone_bind_1.html"><input type="button" value="修改"class="xginp lscolor fr"></a>
        </div> 
        <div class="condition">
            <p <?php if ($this->_var['profit']['email']): ?> class="szpassd fl"  <?php else: ?>class="szpassd szpassdbj fl" <?php endif; ?>>邮箱验证</p>
            <p class="szword fl">
            <?php if ($this->_var['user']['email']): ?>
            已绑定邮箱:<?php echo $this->_var['user']['email']; ?>
            <?php else: ?>
            验证后，可用于快速找回登录密码，接收账户余额变动提醒。
            <?php endif; ?>
            </p>
             <a href="member-email_bind_1.html"><input type="button" <?php if ($this->_var['user']['email']): ?> value="已绑定" <?php else: ?> value="立即绑定"<?php endif; ?>class="xginp lscolor fr" ></a>
        </div>
     <!--    <div class="condition">
            <p <?php if ($this->_var['auth_state']['status'] == '2' || $this->_var['auth_state']['status'] == '0'): ?>class="szpassd szpassdbj fl" <?php else: ?> class="szpassd fl" <?php endif; ?>>实名认证</p>
            <p class="szword fl">验证后，可用于快速找回登录密码，接收账户余额变动提醒。</p>
             <a href="member-cyz_auth_1.html"><input type="button" <?php if ($this->_var['auth_state']['status'] == '1'): ?> value="认证成功" <?php elseif ($this->_var['auth_state']['status'] == '2'): ?> value="认证失败"<?php elseif ($this->_var['auth_state']['status'] == '0'): ?> value="审核中" <?php else: ?>value="立即认证"<?php endif; ?>class="xginp lscolor fr"></a>
        </div> -->
    </div>
</div>
<script>
    var button = $('#button').val()
    $(document).ready(function(){
        if(button == 0){
            $('#pay_button').val('立即启用')
        }else{
            $('#pay_button').val('修改')
            $('#pay_button').parent().attr('href','member-edit_pay_password_1.html');
        }
    })
</script>
<?php echo $this->fetch('footer.html'); ?>