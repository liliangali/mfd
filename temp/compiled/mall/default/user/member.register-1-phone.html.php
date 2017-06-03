    <div class="reg-step reg-2-1"><p>1、填写账号信息</p><p class="on">2、验证手机/邮箱</p><p>3、注册成功</p></div>
    <div class="verifyOk">
        <div class="txt"><i class="ico fl"></i><span class="fl">验证码短信已发送，请查看</span><p>注册手机：<?php echo $this->_var['hphone']; ?></p></div>
        <div class="btn">
            <input type="text" data-placeholder="填写短信验证码" class="vCode fl" id="auth_code">
            <div data-time="<?php echo $this->_var['t']; ?>" class="sendValidate fl">(<?php echo $this->_var['t']; ?>)秒重新发送</div>
            <div class="nextBox">
                <a href="#" class="btn-red" id="nextBtn"><span>下一步</span></a>
            </div>
        </div>
    </div>
<script type="text/javascript">

//下一步按钮
$('#nextBtn').bind('click', function() {
var user_name = "<?php echo $this->_var['data']['user_name']; ?>";
         $.ajax({
             type: "POST",
             url: "/member-register.html",
             data: {step2:1,uname:$("#auth_code").val(),phone:user_name},
             dataType: "json",
             success: function(res){
            	 if(!res.done){
            		 msg(res.msg);
 				}else{
                     msg("注册成功");
 					window.location.href="/member.html";
 				}
             }
         });
});


//发送验证码
function sendCode(){

       $.ajax({
           "url":"/ajax-resetSMSCode.html",
           "data": "phone=<?php echo $this->_var['data']['user_name']; ?>&category=reg&type=reg&opt=reset" ,
           "type": "POST",
           'dataType':'json',
           "success": function(res) {
               if(res.err){
                   msg('出错:'+res.msg);
               }else{
                   $('.sendValidate').attr('data-time',res.msg)
                    countdown();
               }
           }
       });
  
}
//倒计时按钮
function countdown(){
    var obj=$('.sendValidate'),
        time=obj.attr('data-time');
    if(time>0){
        obj.addClass('disabled').unbind('click');
        var n=time;
        var timer=setInterval(function(){
            n--
            if(n<0){
                obj.text('重新发送').removeClass('disabled');
                obj.bind('click',sendCode);
                clearInterval(timer);   
                return
            }
            obj.text('('+n+')秒重新发送');
        },1000) 
    }
}
countdown();


</script>

