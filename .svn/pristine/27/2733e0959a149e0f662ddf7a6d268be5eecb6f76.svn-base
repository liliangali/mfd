/*
 * 设置点击按钮后验证表单，验证脚本会返回一个布尔值
 * 这种方法适用于ajax提交表单
	$(提交按钮id).click(function(){
		var b=$('#signupForm').validate({acticle:true}) 
		alert(b)
	})
*/
/*--表单验证/S--*/
;(function($){
	$.fn.validate=function(options){
		//提交按钮状态
		var submitBtn={
			flag:false,
			id:$(this).find("input[type='submit']"),
			txt:'提交中...'
		}
		//验证规则
		var validate={
				required:function(e){
					if(e.val()==''){
						_error(e,'required');
						return false
					}
				},
				email:function(e){
					var reg=/^\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$/,val=e.val();
					if(val&&!reg.test(val)){
						_error(e,'email');
						return false
					}
				},
				phone:function(e){
					var reg=/^1[3|5|8|7]\d{9}$/,val=e.val();
					if(val&&!reg.test(val)){
						_error(e,'phone');
						return false
					}
				},
				phone_emall:function(e){
					var reg1=/^\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$/,reg2=/^1[3|5|8|7]\d{9}$/,val=e.val();
					if((val&&!reg1.test(val))&&(val&&!reg2.test(val))){
						_error(e,'email');
						return false
					}	
				},
				number:function(e){
					var val=e.val()
					if(val&&isNaN(e.val())){
						_error(e,'number');
						return false
					}
				},
				url:function(e){
					var reg=/^http:\/\/.+\./,val=e.val();
					if(val&&!reg.test(val)){
						_error(e,'url');
						return false
					}
				},
				maxlength:function(e){
					var len=e.attr('maxlength'),val=e.val();
					if(!len){return true}
					if(val&&!(val.length<=len)){
						_error(e,'maxlength');
						return false
					}
				},
				minlength:function(e){
					var len=e.attr('minlength'),val=e.val();
					if(!len){return true}
					if(val&&!(val.length>=len)){
						_error(e,'minlength');
						return false
					}
				},
				idcard:function(e){
					var reg=/(^\d{15}$)|(^\d{17}([0-9]|X|x)$)/,val=e.val();
					if(val&&!reg.test(val)){
						_error(e,'idcard');
						return false	
					}
				}

		}
		//提示信息
		messages = {
			required: "不能为空",
			email: "格式不正确",
			phone:'格式不正确',
			url: "格式不正确",
			number: "请输入数字",
			maxlength: '超出字数限制',
			minlength: '字数长度不够',
			idcard:'格式不正确'
		}
		//合并对象
		if(options){
			if(options.messages){
				messages=$.extend(messages,options.messages)
			}
			if(options.validate){
				validate=$.extend(validate,options.validate)
			}
			if(options.submitBtn){
				submitBtn=$.extend(submitBtn,options.submitBtn)
			}
		}
		//错误提示
		_error=function(obj,error){
			if(options.error){
				options.error(obj,messages[error]);
				return
			}
			alert(messages[error])
		}
		//校验
		function check(){
			 var rule=$(this).attr('validate').split('|'),
					len=rule.length;
				for(var i=0;i<len;i++){
					if(validate.hasOwnProperty(rule[i])){
						if(validate[rule[i]]($(this))==false){
							return false
							break
						}
					}
				}
		}
		$('[validate]',this).off('focus').on('focus',function(e) {
			$(this).removeClass('error-css').siblings('.error').hide();
		})
		$('[validate]',this).off('blur').on('blur',function(e) {
			check.call(e.currentTarget)
        });
		
		function yanzheng(){
			var success=true;
			$(this).find('[validate]').each(function(i, e) {
			   if(check.call(e)==false){
					success=false
					return false
			   }
            });
			if(success){
				if(submitBtn.flag){
					submitBtn.id.val(submitBtn.txt)
				}
			}
			return success	
		}
		
		if(options.acticle){
			return yanzheng.call(this)
		}else{
			$(this).submit(yanzheng);	
		}
	}
})(jQuery);
/*--表单验证/E--*/


$('#write_time').mobiscroll().date({
	theme: 'android', 
	mode: 'scroller',
	display: 'modal',
	lang: 'zh'
	//minDate: new Date()
});

$('.tijiao').click(function(){
	var b=$('#myform').validate({
			submitBtn:{flag:true},
			validate:{
				bdcode:function(e){
					var reg=/^[a-zA-Z]{1}1[3|5|8|7]\d{9}$/,val=e.val();
					if(val&&!reg.test(val)){
						alert('BD码格式不正确')
						return false
					}	
				},
				wx:function(e){
					reg=/[^\x00-\xff]/,val=e.val();
					if(val&&val.match(reg)){
						alert("微信号不能含有中文字符");
						return false	
					}	
				},
				sex:function(e){
					if(e.val()==0){
						alert('请选择性别');
						return false	
					}
				}
			},
			error:function(obj,error){
				var errorBox=obj.parent().find('.error'),t=obj.attr('tip')?obj.attr('tip'):'';
				alert(t+error)
				obj.addClass('error-css')
				obj.one('focus')
			},
			acticle:true	
		})
		if(b){
			$('#myform')[0].submit();	
		}
})
