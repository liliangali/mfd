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
					var len=Number(e.attr('maxlength')),val=e.val();
					if(val&&(val.length>len)){
						_error(e,'maxlength');
						return false
					}
				},
				minlength:function(e){
					var len=Number(e.attr('minlength')),val=e.val();
					if(val&&val.length<len){
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
			$('[validate]',this).off('focus').on('focus',function(e) {
				$(this).removeClass('error-css').siblings('.error').hide();
			})
			$('[validate]',this).off('blur').on('blur',function(e) {
				check.call(e.currentTarget)
			});
			$(this).submit(yanzheng);
		}
	}
})(jQuery);
/*--表单验证/E--*/

/*--图片切换特效/S--*/
;(function($){
	var tools = {
		//生成一个不重复的别名
		rand:function(under, over) {
			switch (arguments.length) {
			  case 1:
				return parseInt(Math.random() * under + 1);

			  case 2:
				return parseInt(Math.random() * (over - under + 1) + under);

			  default:
				return 0;
			}
		},
		//产生指定范围的随机数
		genDefID:function(str) {
			var time = new Date().getTime() + "";
			var id = str + "_" + time.substring(time.length - 6) + "_" + Math.round(Math.random() * 1e3);
			return id;
		}
	};
	$.fn.picRoll = function(info) {
		var defaults = {
			width:"100%",
			height:"100%",
			datas:[],
			isOpenNew:true
		};
		var opts = $.extend(defaults, info);
		var jthis = $(this).css({
			position:"relative",
			overflow:"hidden"
		});
		var time = tools.rand(1, 2);
		//var time=2;
		var currentIndex = 0;
		//当前动画播放到哪一幅图片
		var pictures = [];
		for (var i = 0; i < opts.datas.length; i++) {
			var currentData = opts.datas[i];
			var contentId = tools.genDefID("cotte");
			pictures.push(contentId);
			var imgContent = $("<a/>").css({
				display:"block",
				width:opts.width,
				height:opts.height,
				cursor:"pointer",
				position:"absolute",
				left:0,
				top:jthis.height() * i * -1
			}).attr({
				id:contentId,
				href:currentData.href,
				target:opts.isOpenNew === true ? "_blank" :"_self"
			}).appendTo(jthis);
			if (currentData.title && $.trim(currentData.title) != "") {
				imgContent.attr({
					alt:currentData.title,
					title:currentData.title
				});
			}
			var img = $("<img/>").css({
				border:0,
				height:"100%",
				width:"100%"
			}).attr({
				src:currentData.img
			}).appendTo(imgContent);
		}
		var handler = {
			move:function() {
				if (currentIndex < opts.datas.length - 1) currentIndex++; else {
					currentIndex = 0;
				}
				var last = 0;
				if (currentIndex == 0) {
					last = opts.datas.length - 1;
				} else {
					last = currentIndex - 1;
				}
				//console.log("当前："+currentIndex+";前一个："+last)
				$("#" + pictures[last]).css({
					"z-index":99
				});
				$("#" + pictures[currentIndex]).css({
					top:jthis.height() * -1,
					"z-index":999
				}).animate({
					top:0
				}, 500, function() {
					$("#" + pictures[last]).css({
						top:jthis.height() * -1
					});
				});
				window.setTimeout(function() {
					var _guid = myguid;
					var _handler;
					if (window.picRollHasRolle.split(",").length == window.picRoll.length + 1) {
						window.picRollHasRolle = "";
					}
					var isOk = true;
					while (isOk) {
						var nextIndex = tools.rand(1, window.picRoll.length) - 1;
						_handler = window.picRoll[nextIndex];
						_guid = _handler.id;
						if (window.picRollHasRolle.indexOf(_guid + ",") >= 0) {
							isOk = true;
						} else {
							isOk = false;
						}
					}
					window.picRollHasRolle += _guid + ",";
					_handler.handler.move();
				}, time * 1e3);
			}
		};
		if (!window.picRoll) {
			window.picRoll = [];
		}
		var myguid = newGuid();
		//alert(myguid);
		window.picRoll.push({
			handler:handler,
			id:myguid
		});
		if (!window.picRollStart) {
			window.setTimeout(function() {
				var _handler;
				_handler = window.picRoll[tools.rand(1, window.picRoll.length) - 1];
				_guid = _handler.id;
				window.picRollHasRolle = _guid + ",";
				_handler.handler.move();
			}, time * 1e3);
			window.picRollStart = true;
		}
	};
	function newGuid() {
		var guid = "{";
		for (var i = 1; i <= 32; i++) {
			var n = Math.floor(Math.random() * 16).toString(16);
			guid += n;
			if (i == 8 || i == 12 || i == 16 || i == 20) guid += "-";
		}
		return guid + "}";
	}
})(jQuery);
/*--图片切换特效/E--*/

/*--模拟下拉菜单/S--*/
;(function($){
	$.fn.selects=function(options){
		defaults={
			speed:150,
			width:"90px"
		}
		var $set=$.extend(defaults,options),$this=$(this),$html=$this.html(),$defaultTxt=$this.children("a:eq(0)").text();
		var $content="<dl class='s_select' style='width:"+$set.width+"'><dt><span class='tit'>"+$defaultTxt+"</span></dt><dd style='width:"+$set.width+"'>"+$html+"</dd></dl><input type='hidden' value='"+$defaultTxt+"' />";
		$this.html($content);

		//事件
		$("dt",$this).click(function(){
			$(this).next("dd").slideDown($set.speed)
		})
		$("dl",$this).mouseleave(function(){
			$(this).children("dd").slideUp($set.speed)
		})
		$("a",$this).click(function(){
			var $txt=$(this).text();
			$(this).parents("div").find("input").val($txt);
			$(this).parents("dl").find(".tit").text($txt);
			$(this).parents("dl").find("dd").slideUp($set.speed);
		})
	}
})(jQuery);
/*--模拟下拉菜单/E--*/

//二级菜单
$('[data-cmd=menu]').mouseenter(function(){
	$(this).children('.bd').stop().slideDown('fast');
});
$('[data-cmd=menu]').mouseleave(function(){
	$(this).children('.bd').slideUp('fast');
});

//迷你购物车
$('[data-cmd=menu_cart] .hd').mouseenter(function(){
	var $this = $(this),delay=null;
	$this.next('.bd').stop().slideDown('fast');
	if(delay)clearTimeout(delay);
	delay=setTimeout(function(){
		console.log(Math.random())
		$.get("/default-miniCart.html", function(res){
			var res = eval("("+res+")");
			$this.next('.bd').html(res.retval);
		});	
	},120)

});
$('[data-cmd=menu_cart]').mouseleave(function(){
	$(this).children('.bd').slideUp('fast');
});
$('[data-cmd=menu_cart] .bd').on('click','.close',drop);
function drop(){
	var type = $(this).data("type");
	var id   = $(this).data("id");
	$(this).parents("li").remove();
	$.post("/cart-drop.html",{tp:type,id:id}, function(res){
		var res = eval("("+res+")");
		$("#miniCartNumber").html(res.retval.goods_num);
		$("#miniCartAmount").html(res.retval.amount);
	})
}
//表单提示
setTimeout(function(){
	$('.inputBox input').val('');
},120)
$(document).on('focus','.inputBox input',function(){
	$(this).parent().find('.placeholder').addClass('t');
})
$(document).on('blur','.inputBox input',function(){
	var $this=$(this);
	if($this.val()==''){
		$(this).parent().find('.placeholder').removeClass('t');
	}
});

//弹层登录
var loginIn={
	callback:null,
	show:function(fn){
		luck.open({
			title:'用户登录',
			width:'580px',
			height:'480px',
			content:'<iframe width="580" height="500" style="display:block" src="view/sc-utf-8/mall/default/user/ajax_login.html" frameborder="0"></iframe>',
			addclass:'cotte-luck'
		});
		loginIn.callback=fn;
	}
}

//响应式导航
$('.toggleBtn').bind('mouseup touchend',function(e){
	e.preventDefault();
	e.stopPropagation();
	$('body').addClass('showMenuLayer').one('mouseup touchend',function(e){
		setTimeout(function(){
			$('body').removeClass('showMenuLayer');
		},200)
	});
});
//是否登陆
function getCookie(c_name)
{
	if (document.cookie.length>0)
	{
	  c_start=document.cookie.indexOf(c_name + "=")
	  if (c_start!=-1)
	  {
		c_start=c_start + c_name.length+1
		c_end=document.cookie.indexOf(";",c_start)
		if (c_end==-1) c_end=document.cookie.length
		return unescape(document.cookie.substring(c_start,c_end))
	  }
	}
	return ""
}