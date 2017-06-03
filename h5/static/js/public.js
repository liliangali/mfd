/*--顶部二级菜单/S--*/
function twoMenu(){
	$('.topBar .btn-angle').hover(function(){
		var $this=$(this);
		delay=setTimeout(function(){
			var leftNum=($this.offset().left-55+$this.width()/2);
			$('.topBar>.bd-menu').children('.'+$this.attr('data-class')).show().end().css({left:leftNum}).slideDown(200);
			$this.addClass('btn-angle-hover')
		},100)
	},function(){
		if(delay){
			clearTimeout(delay);
		}
		var $this=$(this);
		_menu=setTimeout(function(){
			$('.topBar>.bd-menu').children('.'+$this.attr('data-class')).hide().end().hide();
			$this.removeClass('btn-angle-hover');
		},100);
	});
	$('.topBar>.bd-menu').hover(function(){
		clearTimeout(_menu);
	},function(){
		$(this).children('div:visible').hide().end().hide();
		$('.topBar').find('.btn-angle').removeClass('btn-angle-hover')
		_menu=null;
	})
}
/*--顶部二级菜单/E--*/

/*--地区导航/S--*/
function anav(){
	$('.anav').hover(function(){
		$(this).addClass('anav-hover');
		$(this).children('.anav .bd-menu').show(400);
		$(this).children('.anav .btn-angle').addClass('btn-angle-hover')
	},function(){
		var $this=$(this);
		anav=setTimeout(function(){
			$this.removeClass('anav-hover');
			$this.children('.anav .bd-menu').stop().hide(400);
			$this.children('.anav .btn-angle').removeClass('btn-angle-hover');
		},100);
	});
	$('.anav>.bd-menu').mouseover(function(){
		clearTimeout(anav);
	})
}
/*--地区导航/E--*/

/*--表单默认提示/S--*/
$(function(){
	$('input[data-placeholder]').each(function(i, e) {
		var t=e.getAttribute('data-placeholder');
		e.value=t;
		e.style.color='#666';
		if(e.type=='password'){
			e.type='text';
			e.setAttribute('d',1);
		}
		$(e).focus(function(){
			$(e).parents('.search').addClass('focus')
			if(e.value==t){
				e.value='';
				e.style.color='#000';
				$(e).addClass('cur');
				if(e.getAttribute('d')==1){
					e.type='password';
				}
			}
		});
		$(e).blur(function(){
			$(e).parents('.search').removeClass('focus');
			if(e.value==''){
				e.value=t;
				e.style.color='#666';
				$(e).removeClass('cur');
				if(e.getAttribute('d')==1){
					e.type='text';
				}
			}
		});
	});
})
/*--表单默认提示/E--*/

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

/*-- 下载APP/E--*/
$(document).on('mouseover','.downApp',function(){
	$(this).addClass('downApp-hover')
});
$(document).on('mouseout','.downApp',function(){
	$(this).removeClass('downApp-hover')
});
/*--下载APP/E--*/

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
	use('/static/expand/layer/layer.min.js',function(){
		var _reload=true;
		window.loginPop=$.layer({
			type: 2,
			title:'会员登陆',
			shade: [0.3, '#000'],
			area: ['550px','360px'],
			moveType: 1,
			iframe: {src: '../../view/sc-utf-8/mall/default/user/ajax_login.html'},
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



/*--固定漂浮导航/S--*/
function fixedNav(obj){
	var wintop=0,_top=0,objtop=parseInt($(obj).css('top'));
	window.onscroll=set;
	window.onresize=set;
	function set(){
		wintop=$(window).scrollTop()
		_top=(objtop-wintop>0)?(objtop-wintop):0
		$(obj).css('top',_top);

		if($(window).width()<1360){
			$(obj).css('left',0);
		}else{
			$(obj).css('left','auto');
		}
	}
}
/*--固定漂浮导航/E--*/

/*--顶部导航随屏滚动/S--*/
function FixedTop(obj){
	window.onscroll=set;
	window.onresize=set;
	var objh=$(obj).height();
	function set(){
		wintop=$(window).scrollTop()
		if(wintop>0){
			$(obj).css({position:"fixed",top:0,left:0})
			$('body').css('paddingTop',objh)
		}else{
			$(obj).css('position','static')
			$('body').css('paddingTop',0)
		}
	}
}
$(function(){
	if($('.head2').length){
		FixedTop('.head2')
	}
})
$(document).on('keyup',"input[type='text']",function(e){
	e=e||window.event
	if(e.keyCode==32){
		this.value=this.value.replace(/\s/g,'')
	}
})
/*--顶部导航随屏滚动/E--*/

/*--右下方悬浮导航/S--*/
$(function(){
	var html='<div class="fixed-nav"><a class="like" href="member-index-user_favorite-1-2.html" target="_blank">我的收藏</a><a class="survey">问题反馈</a><a class="backTop">返回顶部</a></div>';
	if(window.location.href.indexOf('ajax_login.html')<=0&&$("link[href$='public.css']").length>0){
		$('body').append(html);
	}else{
		return
	}
	$('.fixed-nav .backTop').click(function(){
		$('html,body').animate({scrollTop:0},'fast')
	});
	$('.fixed-nav .survey').click(function(){
			use('../../static/expand/layer/layer.min.js',function(){
				var html=[];
				html.push("<form  method=\"post\" id=\"myForm\" enctype=\"multipart/form-data\">")
				html.push("<div class=\"fankui\">")
				html.push( "    <h4>1、问题描述<i class=\"red\">*</i></h4>")
				html.push( "    <textarea class=\"editor\" name=\"description\"></textarea>")
				html.push( "    <div class=\"updata\">")
				html.push( "         <ul id=\"ul_li\" class=\"fl\">")
				html.push( "        </ul>")
				html.push( "        <div class=\"btn\">上传图片<input id=\"upimg_file\" name=\"up_file\" type=\"file\" value=\"\"></div>")
				html.push( "    </div>")
				html.push( "    <h4>2、你的联系方式？</h4>")
				html.push( "    <p>联系方式：<input name=\"mobile\" type=\"text\" class=\"txt\" id=\"contact\"><em style='font-size:12px;color:#999'>(qq/手机/邮箱)</em></p>")
				html.push( "    <div class=\"error\"></div>")
				html.push( "    <div class=\"btnBox\">")
				html.push( "        <input type=\"button\" value=\"提交\" class=\"ok\">")
				html.push( "        <input type=\"button\" value=\"关闭\" class=\"no\" onClick=\"layer.closeAll()\">")
				html.push( "    </div>")
				html.push( "</div>")
				html.push( "</form>")
			$.layer({
				type: 1,
				title:'问题反馈',
				shade: [0.3, '#000'],
				area: ['575px','510px'],
				moveType: 1,
				page: {html:html.join('')}
			})
		})
	});
	function fankui(){
		var $this=$(this);
		if($('.fankui .editor').val()==""){
			$('.fankui .error').text('问题描述不能为空')
		}else{
			$.ajax({
				url:"/feedback-add.html",
				type : 'post',
				beforeSend: function(){
					$this.val('提交中...');
					$(document).off('click',fankui);
				},
				data:{
					description:$('.fankui .editor').val(),
					mobile:$('#contact').val()==$('#contact').attr('data-placeholder')?"":$('#contact').val(),
					url:window.location.href,
					img1_url:$('#input_0').val(),
					img2_url:$('#input_1').val(),
					img3_url:$('#input_2').val(),
					img4_url:$('#input_3').val(),
					img5_url:$('#input_4').val()
				},
				success: function(){
					layer.closeAll();
					msg('提交成功，感谢您的反馈！',345)
					$('.fankui .btnBox .ok').val('提交')
					$(document).on('click','.fankui .btnBox .ok',fankui)
				}
			})
		}
	}
	$(document).on('click','.fankui .btnBox .ok',fankui)
})
/*--右下方悬浮导航/E--*/

/*--回车事件/S--*/
function enter(event,fn) {
	e = event ? event : (window.event ? window.event : null);
	if (e.keyCode == 13) {
			return fn()
	}
}
/*--回车事件/E--*/

/*--搜索联想/S--*/
$(document).on('keyup','#searchTxt',function(e){
	if(e.keyCode==13){
		$(this).next('.btn').click();
		return
	}
	if(e.keyCode==37||e.keyCode==38||e.keyCode==39||e.keyCode==40){
		return	
	}
	var str=$.trim(this.value);
	if(str!=''){
		$.ajax({
            type:'get',
            url:'ajax-matchWord.html?w='+str,
            dataType:'jsonp',
            jsonp:'callback',
            jsonpCallback:'displayRes',
        });
	}
})
function displayRes(res){
	var content = '<ul>';
	if(res.his.length >0){
		for(var i=0;i<res.his.length;i++){
			content +="<li style='color:#7a77c8' data-key='"+res.his[i].key+"'>"+res.his[i].name+"</li>";
		}
	}

	if(res.match.length >0){
		for(var i=0;i<res.match.length;i++){
			content +="<li data-key='"+res.match[i].key+"'>"+res.match[i].name+"</li>";
		}
	}

	content += "</ul>";
	//alert(content);
	if(res.his.length >0||res.match.length >0){
		$('.search .recomm').empty().html(content).show();
	}
}

$(document).click(function(){
	$('.search .recomm').hide()
})
$(document).on('click','.search .recomm li',function(){
	$('#searchTxt').val($(this).data('key'));
});
(function(){
	var n=-1;
	$(document).keydown(function(e){
		var obj=$('.search .recomm')
			len=obj.find('li').length;
		if(obj.is(':visible')){
			if(e.keyCode==40){
				n++;
				if(n==len){n=0}
				obj.find('li').eq(n).siblings().removeClass('cur').end().addClass('cur');
				$('#searchTxt').val(obj.find('li').eq(n).text())
			}else if(e.keyCode==38){
				n--;
				if(n<0){n=len-1}
				obj.find('li').eq(n).siblings().removeClass('cur').end().addClass('cur');
				$('#searchTxt').val(obj.find('li').eq(n).text())
			}
		}
	})
})();
/*--搜索联想/E--*/

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

//ns add 添加图片上传
function delUpimg(li_num){
    $("#li_"+li_num).remove();
    if(num){
        num--;
    }
}
var num = 0;
$(document).on('change','#upimg_file',function(){
		if($('#upimg_file').val()==""){
		  return
		}
		var li_html = '';
		if(num == 5){
			msg('最多能上传5张图片');
			return
		}
		if($('#ul_li .loading').length<=0){
			$('#ul_li').append('<li class="loading">加载中..</li>')
		}
		var options = {
				url:'feedback-upload.html?num='+num+'&r='+(10000*Math.random())+'&type=',
				success: function(data) {
					var dd = eval( "(" + data + ")" );
					num++;
					li_html += '<li id="li_'+num+'"><img width="80" height="80" src="'+dd.src+'" />';
					li_html += "<input type='hidden' id='input_"+num+"' name='input_"+num+"' value='"+dd.file+"' >";
					li_html += '<a href="javascript:void(0);" onclick=delUpimg(\''+num+  '\'); >删除</a></li>';
					if(li_html!=''){
						$('#ul_li .loading').remove();
					}
					$('#ul_li').append(li_html);
				}
		};
		use('../../static/expand/jquery.form.js',function(){
			$('#myForm').ajaxSubmit(options);
		})
});




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
