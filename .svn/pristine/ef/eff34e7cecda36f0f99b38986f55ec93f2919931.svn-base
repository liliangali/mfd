// 切换手机、邮箱验证
$('.switchMode').click(function(){
	var $this=$(this);
	$('.select1').css({left:$this.offset().left-22,top:$this.offset().top+40}).fadeIn(150);
	return false;
});
$('.select1>span').click(function(){
	$('#error').html('');
	$(this).parent().hide();
	if(this.getAttribute('data-value')==0){
		$('.emallBox').hide();
		$('.phoneBox').show();	
	}else{
		$('.phoneBox').hide();	
		$('.emallBox').show();
	}
	return false;
});
$(document).click(function(){
	$('.select1').hide();	
})

//表单默认提示
$('input[data-placeholder]').each(function(i, e) {
    var t=e.getAttribute('data-placeholder');
	e.value=t;
	if(e.type=='password'){
		e.type='text';
		e.setAttribute('d',1);	
	}
	$(e).focus(function(){
		$(e).removeClass('error-css');
		if(e.value==t){
			e.value='';
			$(e).addClass('cur');
			if(e.getAttribute('d')==1){
				e.type='password';
			}
		}
	});
	$(e).blur(function(){
		if($(e).val()==''){
			e.value=t;
			$(e).removeClass('cur');
			if(e.getAttribute('d')==1){
				e.type='text';	
			}
		}
	});
});

//发送验证码
function sendCode(){
	$('.sendValidate').attr('data-time',10)
	countdown();
	alert('发送成功');
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
				obj.text('发送验证码到手机').removeClass('disabled');
				obj.bind('click',sendCode);
				clearInterval(timer);	
				return
			}
			obj.text('('+n+')秒重新发送');
		},1000)	
	}
}
countdown();

// 表单验证
function validate(){
	$('#error').html('');
	//未通过
	function error(t,obj){
		$('#error').html("<i class='ico fl'></i><span class='fl'>"+t+"</span>");
		$(obj).each(function(i,e){
			if($(e).is(':visible')){
				$(e).addClass('error-css');	
			}	
		});	
	}
	
	//检测是否是默认值
	function isDefault(obj){
		return obj.value==obj.getAttribute('data-placeholder');
	}

	var v={
			phone:function(obj){
				var reg=/^1[3|5|8|7]\d{9}$/;
				if(!reg.test(obj.value)||isDefault(obj)){
					error('手机号码格式不正确',obj);
					return false
				}
				return true
			},
			emall:function(obj){
				var reg=/^\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$/;
				if(!reg.test(obj.value)||isDefault(obj)){
					error('邮箱格式不正确',obj);
					return false
				}
				return true
			},
			phone_emall:function(obj){
				var reg=/^1[3|5|8|7]\d{9}|\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$/;
				if(!reg.test(obj.value)||isDefault(obj)){
					error('您输入的手机或邮箱格式不正确',obj);
					return false
				}
				return true	
			},
			vcode:function(obj){
				if(obj.value==''||isDefault(obj)){
					error('验证码不能为空',obj);	
					return false
				}
				return true
			},
			pwd:function(obj){
				var reg=/^\S{6,20}$/,success=true,oInput=$('input[data-type=pwd]');
				if(!reg.test(obj.value)||isDefault(obj)){
					error('请输入6-20个大小写英文字母、符号或数字',obj);
					success=false;
				}
				if(success&&oInput.length>=2){
					if(oInput.eq(0).val()!=oInput.eq(1).val()){
						error('两次密码输入不一致',oInput.eq(1));
						success=false	
					}
				}
				return success
	
			}
		}

	return function(){
			var success =true;
			$('input[data-type]:visible').each(function(i,e) {
				var p=e.getAttribute('data-type');
				if(v.hasOwnProperty(p)){
                	success=v[p](e);
				}else{
					var p2=p.replace('|','_');
					if(v.hasOwnProperty(p2)){
						success=v[p2](e);		
					}	
				}
				//alert(i+':'+success)
				if(success==false){
					//alert('结束验证')
					return false
				}
            });
			return success
	}()
}

//顶部二级菜单
$('.head .btn-angle').hover(function(){
	var $this=$(this);
	delay=setTimeout(function(){
		var leftNum=($this.offset().left-60+$this.width()/2);
		$('.head .bd-menu').children('.'+$this.attr('data-class')).show().end().css({left:leftNum}).slideDown(200);
		$this.addClass('btn-angle-hover')
	},100)
},function(){
	if(delay){
		clearTimeout(delay);
	}
	var $this=$(this);
	_menu=setTimeout(function(){
		$('.head .bd-menu').children('.'+$this.attr('data-class')).hide().end().hide();
		$this.removeClass('btn-angle-hover');
	},100);
});
$('.head .bd-menu').hover(function(){
	clearTimeout(_menu);
},function(){
	$(this).children('div:visible').hide().end().hide();
	$('.head').find('.btn-angle').removeClass('btn-angle-hover')
	_menu=null;	
})

//注册下一步
function reg(){
	if(validate()){
		//提交Ajax验证
		if(!$('#protocol').attr('checked')){
			$('#error').html("<i class='ico fl'></i><span class='fl'>您还没确认您是否已经阅读协议</span>");	
		}
		$.ajax({
			url:'',
			data:{
				name:$('#userName').val(),
				Pwd:$('#userPwd').val(),
				verify:$('#vCode').val()
			},
			success: function(){
				if(0){
					$('#error').html("<i class='ico fl'></i><span class='fl'>"+错误信息+"</span>");	
				}else{
					//去下一步页面	
				}
			},
			error: function(){}
		})	
	}
}

//刷新验证码
function upCode(){
    var src = "ajax-getAuthCode.html?r="+Math.random();
    $(".validateImg").attr('src',src);
}








