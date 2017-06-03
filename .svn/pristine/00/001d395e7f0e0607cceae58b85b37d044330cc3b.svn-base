

/*--添加收藏/S--*/
$(document).on('click','.AddFavorite',function(){
	var e=window.location.href,t=document.title;
	try {
		window.external.addFavorite(e, t)
	}
	catch (n) {
		try {
			window.sidebar.addPanel(t, e, "")
		} catch (n) {
			msg("加入收藏失败，请使用Ctrl+D进行添加！")
		}
	}
	return false
})
/*--添加收藏/E--*/

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

/*--弹窗登陆/E--*/
function login(obj){
	use('../../static/expand/layer/layer.min.js',function(){
		var _reload=true;
		window.loginPop=$.layer({
			type:2,
			title:'会员登陆',
			shade: [0.3, '#000'],
			area: ['550px','360px'],
			moveType: 1,
			iframe: {src: 'view/sc-utf-8/mall/default/user/ajax_login.html'},
			close: function(index){
				_reload=false
			},
			end: function(){
				if(_reload){
					if(typeof obj=='function'){
						obj()
					}else if(typeof obj=='string'){
						if(/^http:\/\//.test(obj)){
							window.location.href=obj;
						}
					}
					setTimeout(function(){top.location.reload()},1500)
				}
			}
		})
	})
}
/*--弹窗登陆/E--*/

/*--分享/S--*/
$(document).on('click','.shareList>a,.share>span',function(){
	var cla=this.className,url=window.location.href,page='',title=document.title,img=$('.article>.con img:eq(0)').attr('src');
	if(typeof(img) == 'undefined' && $('.custom_box .diy_left .diy_bigimg img').attr('src')) {
		img = $('.custom_box .diy_left .diy_bigimg img').attr('src');
		title += '(阿里裁缝个性化定制)';
	}

	 if (cla == "weixin") {
        var html = '<div style="text-align:center"><img src="http://qr.liantu.com/api.php?text=' + url + '" width="260" height="260"></div><p style="text-align:center;padding:10px 20px;line-height:17px">使用微信“扫一扫”即可将网页分享到我的朋友圈</p>';

		use('../../static/expand/layer/layer.min.js',function(){
			$.layer({
				type: 1,
				title:'分享到微信朋友圈',
				shade: [0.3, '#000'],
				area: ['310px','340px'],
				moveType: 1,
				page: {html:html}
			})
		})
        return
    }
	if(cla=='qqwb'){cla='qblog'}
	switch(cla){
		 case 'qblog'://腾讯微博
            page = "http://share.v.t.qq.com/index.php?c=share&a=index&url=" + url + "&title=" + title + " @阿里裁缝&appkey=801cf76d3cfc44ada52ec13114e84a96&pic=" + img;
            break;
        case 'qzone'://QQ空间
            page = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?title=" + title + " @阿里裁缝&url=" + url + "&summary=来自阿里裁缝&pics=" + img;
            break;
        case 'sina'://新浪微博
            page = "http://v.t.sina.com.cn/share/share.php?appkey=691463583&title=" + title + " @阿里裁缝&url=" + url + "&pic=" + img;
			break;
		case 'douban'://豆瓣
            page = "http://www.douban.com/share/service?href="+url+"&name=@阿里裁缝&text=来自阿里裁缝&image="+img;
			break
		case 'renren'://人人
            page = "http://widget.renren.com/dialog/share?resourceUrl="+url+"&srcUrl="+url+"&title="+title+"&description=@阿里裁缝&pic="+img;
	}
	  window.open(page, "阿里裁缝分享", "height=600, width=700");

})
/*--分享/E--*/

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

//过滤空格
$(document).on('keyup',"input[type='text']",function(e){
	e=e||window.event
	if(e.keyCode==32){
		this.value=this.value.replace(/\s/g,'')
	}
})


/*--回车事件/S--*/
function enter(event,fn) {
	e = event ? event : (window.event ? window.event : null);
	if (e.keyCode == 13) {
			return fn()
	}
}
/*--回车事件/E--*/


/*--表单验证/S--*/
;(function($){
	$.fn.verify=function(options){
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
			email: "请输入合法的邮件地址",
			phone:'请输入正确的手机号码',
			url: "请输入合法的 URL地址",
			number: "请输入数字",
			maxlength: '超出字数限制',
			minlength: '字数长度不够',
			idcard:'请输入正确的身份证号码'
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
		window._error=function(obj,error){
			var txt="";
			if(error=='required'&&obj.attr('placeholder')!=undefined){
				txt=obj.attr('placeholder')+messages[error]
			}else{
				txt=messages[error]	
			}
			if(obj.next('.error').length){
				obj.addClass('error-css').next('.error').text(txt).show();

			}else{
				obj.addClass('error-css').after('<label class="error">'+txt+'</label>')
			}
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
		$('[validate]',this).on('focus',function(e) {
			$(this).removeClass('error-css').next('.error').hide();
		})
		$('[validate]',this).on('blur',function(e) {
			check.call(e.currentTarget)
        });
		$(this).submit(function(){
			var success=true;
			$(this).find('[validate]').each(function(i, e) {
			   if(check.call(e)==false){
					success=false
					//return false
			   }
            });
			if(success){
				if(submitBtn.flag){
					submitBtn.id.val(submitBtn.txt)
				}
			}
			return success
		})
	}
})(jQuery);
/*--表单验证/E--*/

function hasLogin(){
	var status = 0;
	$.ajax({
		url :"static/js/jquery.cookie.js",
		type: "GET",
		dataType:"script",
		async:false,
		success:function(){
			status = $.cookie("hasLogin");
		}
	})

	return status;
}
