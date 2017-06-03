

<!-- 异步加载开始 -->
  <div class="pwd-step pwd-2"><p>1、验证身份</p><p class="on">2、重置登录密码</p><p>3、完成</p></div>
  <div class="myForm">
    <div id="error"></div>
      <div class="item">
        <div class="tit"> 新密码： </div>
        <input type="password" class="txt" data-type="pwd" id="pwd" data-placeholder="6-20个大小写英文字母、符号或数字">
      </div>
      <div class="item">
        <div class="tit"> 确认密码： </div>
        <input type="password" class="txt" data-type="pwd" data-placeholder="请再次输入密码">
      </div>
      <input type="submit" value="下一步" class="next" onClick="next()">
  </div>
<!-- 异步加载结束 -->




<script>
//下一步按钮
function next(){

	if(validate()){
		//提交Ajax验证
		var t =$(".phoneBox").is(":visible") ? "phone" : "email";
		$.ajax({
			url:'member-find_password.html',
			type: "POST",
			dataType: "json",
			data: {account_type:'<?php echo $this->_var['data']['account_type']; ?>',account:<?php echo $this->_var['data']['account']; ?>,opt:3,code:<?php echo $this->_var['data']['authcode']; ?>,ps:$("#pwd").val()},
			success: function(res){
				if(!res.done){
					$('#error').html("<i class='ico fl'></i><span class='fl'>"+res.msg+"</span>");	
				}else{
					$(".main").html(res.retval.content);
				}
			},
			error: function(){}
		})	
	}
}
</script>