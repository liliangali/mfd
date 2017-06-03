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
/*--回车事件/S--*/
function enter(event,fn,num,obj) {
	e = event ? event : (window.event ? window.event : null);
	if (e.keyCode == 13) {
		if(num&&obj&&obj.value.length==num){
			return fn()
		}
	}
}
/*--回车事件/E--*/

/*--屏蔽input[type=text]空格输入/S--*/
$("input[type='text']").keyup(function(e){
	e=e||window.event
	if(e.keyCode==32){
		this.value=this.value.replace(/\s/g,'')
	}
})
/*--屏蔽input[type=text]空格输入/E--*/

//表单默认提示
$('input[data-placeholder]').each(function(i, e) {
    var t=e.getAttribute('data-placeholder');
	e.value=t;
	if(e.type=='password'){
		e.type='text';
		e.setAttribute('d',1);	
	}
	$(e).focus(function(){
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



// 表单验证
$(document).on('focus','.error-css',function(){
	$(this).removeClass('error-css');
	$('#error').html('')
})
function validate(){
	
	$('#error').html('');
	//未通过
	function error(t,obj){
		$('#error').html("<i class='ico fl'></i><span class='fl'>"+t+"</span>");
		$(obj).each(function(i,e){
			if($(e).is(':visible')){
				$(e).addClass('error-css')
			}	
		});	
		if($('.validateImg')){
			upCode('logincode');
		}
		
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
				var reg=/(^1[3|5|8|7]\d{9}$)|(^\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$)/;
				if(!reg.test(obj.value)||isDefault(obj)){
					error('您输入的手机或邮箱格式不正确',obj);
					return false;

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
						error('请输入确认密码',oInput.eq(1));
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
//				alert(i+':'+success)
				if(success==false){
//					alert('结束验证')
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

//注册下一步-add by xiao5
function reg(){
	
	if(validate()){
		//提交Ajax验证
	 
		if(!$('#protocol').is(':checked')){
			$('#error').html("<i class='ico fl'></i><span class='fl'>您还没确认您是否已经阅读协议</span>");	
			return false;
		}
		
		$.ajax({
			url:'member-register.html',
			type: "POST",
			dataType: "json",
			data:{
				user_name:$('#userName').val(),
				password:$('#userPwd').val(),
				captcha:$('#vCode').val(),
				step1:$('#step1').val(),
				agree:$('#protocol').val(),
				invite:$('#invite').val()
			},
			success: function(res){
			
				if(!res.done){
					$('#error').html("<i class='ico fl'></i><span class='fl'>"+res.msg+"</span>");
				}else{
					$(".main").html(res.retval.content);
					//去下一步页面	
				}
			},
			error: function(){}
		})	
	}
}
//登陆-add by xiao5
function login(){
	if(validate()){
		//提交Ajax验证
	
		$.ajax({
			url:'member-login.html',
			type: "POST",
			dataType: "json",
			data:{
				user_name:$('#userName').val(),
				password:$('#userPwd').val(),
				captcha:$('#vCode').val(),
				recall:$('#recall').attr('checked')
			},
			success: function(res){
				if(!res.done){
					$('#error').html("<i class='ico fl'></i><span class='fl'>"+res.msg+"</span>");	
					upCode('logincode');
				}else{
					 window.location.href = $('#ret_url').val();
				}
			},
			error: function(){}
		})	
	}
}

//刷新验证码-add by xiao5
function upCode(t){
    var src = "ajax-getAuthCode-"+t+".html?r="+Math.random();
    $(".validateImg").attr('src',src);
}
/*--模块加载器/S--*/
function use(module, callback, charset){
	var i = 0, head = document.getElementsByTagName('head')[0];
	var module = module.replace(/\s/g, '');
	var iscss = /\.css$/.test(module);
	var node = document.createElement(iscss ? 'link' : 'script');
	var id = module.replace(/\.|\//g, '');
	if(iscss){
		node.type = 'text/css';
		node.rel = 'stylesheet';
	}
	if(charset){
        node.setAttribute("charset",charset);
    }
	node[iscss ? 'href' : 'src'] =module;
	node.id = id;
	if(!document.getElementById(id)){
		head.appendChild(node);
	}else{
		if(callback){
			callback();	
		}	
	}
	if(callback){
		if(document.all){
			$(node).ready(callback);
		} else {
			$(node).load(callback);
		}
	}
}
/*--模块加载器/E--*/

/*--公共提示/S--*/
function msg(t,w,h,fn,btn){//提示文本，宽度，高度，回调函数，按钮数量
	t=t?t:'';
	w=w?w:330;
	h=h?h:150;
	btn=btn?btn:1;
	use('../../static/expand/layer/layer.min.js',function(){
		var msgLayer=$.layer({
			area: [+w+'px',h+'px'],
			moveType: 1,
			shade: [0.3, '#000'],
			dialog: {
				msg: t,
				btns: btn,
				type: 0,
				yes: function(){
					layer.close(msgLayer);
					if(typeof fn=='function'){fn()}
				}
			}
		});
	});
}
/*--公共提示/E--*/
//注册协议
