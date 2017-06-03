//头部菜单
$('#topMenu').click(function(){
	$('#menuLayer').addClass('show');
	$('#shadowLayer').unbind('click').click(function(){
		$('#menuLayer').removeClass('show');
		$(this).css('visibility','hidden')
	}).css('visibility','visible')
});

/*
 * 说明：cotte-wap 所有页面脚本
 * 规则：每个页面对应一个类，保存在cottewap对象下
 * 调用：cottewap.类()
*/
var cottewap={
	popTip:function (txt,time){//公共提示
		luck.open({
			content:txt,
			time:time
		})
	},

	pageIndex:function(){//首页
		var myswipe=Swipe(document.getElementById('slider'), {
			//auto:1000,
			continuous:false,
			callback: function(index, element) {
				var len=myswipe.getNumSlides();
				$('#slider .prev,#slider .next').removeClass('null')
				if(len>1){
					if((index+1)==myswipe.getNumSlides()){
						$('#slider .next').addClass('null');
					}
					if(index==0){
						$('#slider .prev').addClass('null');
					}
				}else{
					$('#slider .prev,#slider .next').hide();
				}
			}
		});
		$('#slider .prev').click(myswipe.prev).addClass('null');
		$('#slider .next').click(myswipe.next);
		//顶部下载
		$('.topdownload .close').click(function(){
			$('.topdownload').addClass('hidden');
		})
	},
	pageCat:function(e){
		$('.catpage').on('click','li',function(){
			var index=$(this).index();
			$('.catpage').addClass('showcat');
			$('.catLayer').find('.cat').eq(index).siblings('.cat').hide().end().show();
			$('.catLayer').addClass('transform:translate3d(0,0,0);-webkit-transform:translate3d(0,0,0)');
		})
	},
	pageList:function(){//商品列表页面
	    var flag=true;
		$(window).scroll(function(){
			if($(window).scrollTop()>=$(document).height()-$(window).height()){
				if($('.tip').html()==''){
					return false;
				}
				if(flag){
					flag=false
					var page = parseInt($("#page").val())+1;
					var topic = $('.lblist .current').attr('value');
					$.get("/custom-getList.html"+"?&topic="+topic+"&page="+page, function(res){
						var res = eval("("+res+")");
						if(page<=res.retval.count){
							$('#listBox').append('<div class="wordpic"> <a href="/custom-goodsInfo.html?id='+res.retval.content.c_id+'"> <img width="320" height="365" src="'+res.retval.content.image+'">\
				<p class="title">'+res.retval.content.suit_name+'</p>\
				<p class="money">￥'+res.retval.content.price+'</p>\
			  </a></div>');
							if(page==res.retval.count){
								$('#listBox').append('<div class="tip"></div>')
							}else{
								$("#page").val(page)
							}
							flag=true
						}
					})
				}
			}
		})

	},
	pageLogin:function(){//登录页面
		$('#loginBtn').click(function(){
			if($('.username').val()==''){
				cottewap.popTip('用户名不能为空',1500);
				return
			}
			if($('.userpwd').val()==''){
				cottewap.popTip('密码不能为空',1500);
				return
			}
//		    $("#password").keydown(function(e){
//		        var curKey = e.which;
//		        if(curKey == 13){
		        	var username = $(".username").val();
		    		var password = $(".userpwd").val();
		    		var ret_url=$(".ret_urll").val();
		    		//alert(username);alert(password);alert(captcha);
		    		$.ajax({
		    			type:"POST",
		    			url:"/member-login.html",
		    			data:{user_name:username,password:password},
		    			datatype:'json',
		    			success: function(e){
		    				r=eval("("+e+")");
		    				if(r.done == 1) {
		    					cottewap.popTip(r.msg,2000);
								setInterval(function(){
									window.location.href=ret_url;
								},2000)	;
		    					return
		    				}else if(r.done == 0) {
		    					cottewap.popTip(r.msg,2000);
		    					return
		    				}
		    			}
		    		});
		})
	},
	pageReg:function(){//注册页面
		//获取验证码
		$('#getCode').click(function(){
			var $this=$(this),n=60,delay=null,phone=$('.phone').val(),reg=/^1[3|5|8|7]\d{9}$/;
			if(!reg.test(phone)){
				cottewap.popTip('请输入正确的手机号码',2000);
				return
			}
			$this.attr('disabled',true).addClass('disabled');
			$.ajax({
					type:'GET',
					url:"/ajax-sendCode.html",
					data:"phone="+phone+"&type=reg",
					datatype:'json',
					success:function(e){
						//alert(phone);
						r=eval("("+e+")");
						if(r.done){
							delay
					}else{
							cottewap.popTip(r.msg,2000);
							clearInterval(delay);
							$this.attr('disabled',false).val('获取验证码').removeClass('disabled');
						}
					}
				});
			delay=setInterval(function(){
							n--
							if(n>0){
								$this.val('正在获取（'+n+')')
							}else{
								clearInterval(delay);
								$this.attr('disabled',false).val('获取验证码').removeClass('disabled');
							}
			},1000)
		});
		//同意条款
		$('.zcxy_off').click(function(){
			$(this).toggleClass('zcxy_on')
		});
		//提交注册
		$('#regBtn').click(function(){
			var reg=/^1[3|5|8|7]\d{9}$/,phone=$('.phone').val(),invite=$('#invite').val(),validateCode=$('.validateCode').val(),pwd1=$('.userpwd1').val(),pwd2=$('.userpwd2').val();
			var ret_url=$(".ret_url").val();
			if(!reg.test(phone)){
				cottewap.popTip('请输入正确的手机号码',2000);
				return
			}
			if(validateCode==''){
				cottewap.popTip('验证码不能为空',2000);
				return
			}
			if(!(pwd1.length>=6) || !(pwd1.length<=32)){
				cottewap.popTip('请输入正确的密码',2000);
				return
			}
			if(pwd2==''){
				cottewap.popTip('确认密码不能为空',2000);
				return
			}
			if(pwd1!=pwd2){
				cottewap.popTip('两次密码输入不一致',2000);
				return
			}
			if(!$('.zcxy_off').hasClass('zcxy_on')){
				cottewap.popTip('必须勾选同意麦富迪',2000);
				return
			}
			$.ajax({
				url:'member-register.html',
				type: "POST",
				dataType: "json",
				data:{
					phone:phone,
					password:pwd1,
					uname:validateCode,
					step2:'step2',
					invite:invite,
					type:$('.type').val(),
					code:$('.code').val(),
				    s_id:$('#s_id').val()

					//step1:$('#step1').val(),
					//agree:'agree'
				},
				success: function(res){
					//r=eval("("+res+")");
					if(!res.done){
						cottewap.popTip(res.msg,2000);
						return
					}else{


							//bd
							if(res.retval.content ==2){

								luck.open({
										title:['提示'],
										content: '注册成功,只差一步就能拿走礼包了,<br/>下载麦富迪APP',
										btn:['以后再说','前往下载'],
										yes:function(){
											luck.close()
											window.location.href='/';
										},
										no: function(){
											luck.close()
											window.location.href = ret_url
										}

								});
							}else{
								window.location.href=ret_url;
							}
							//作品分享
							if(res.retval.g_url !=''){
								window.location.href=res.retval.g_url;
							}

						return
						//去下一步页面
					}
				},
				error: function(){}
			})
			//alert('在这里写ajax提交，以及手机验证码验证')
		});
	},
//	pageTel:function(date){
//		luck.open({
//			  content: date,
//			  btn: [
//			    '取消',
//			    '呼叫'
//			  ],
//			  yes: function () {
//				  luck.close()
//			  },
//			  no: function () {
//				  window.location.href='tel://'+date;
//			  }
//			});
//
//	},
	pageFindPwd:function(){//找回密码
		//获取验证码
		$('#getCode').click(function(){
			var $this=$(this),n=60,delay=null,phone=$('.phone').val(),reg=/^1[3|5|8|7]\d{9}$/;
			if(!reg.test(phone)){
				cottewap.popTip('请输入正确的手机号码',1500);
				return
			}
			$this.attr('disabled',true).addClass('disabled');
			$.ajax({
					type:'POST',
					url:"/ajax-sendCode.html",
					data:{phone:phone,type:'findps'},
					//data: {account_type:'phone',account:phone,
					datatype:'json',
					success:function(e){
						//alert(phone);
						r=eval("("+e+")");
						if(r.done){
							delay
					}else{
							cottewap.popTip(r.msg,1500);
							clearInterval(delay);
							$this.attr('disabled',false).val('获取验证码').removeClass('disabled');
						}
					}
				});
			delay=setInterval(function(){
				n--
				if(n>0){
					$this.val('正在获取（'+n+')')
				}else{
					clearInterval(delay);
					$this.attr('disabled',false).val('获取验证码').removeClass('disabled')
				}
			},1000);
		});
		//提交注册
		$('#okBtn').click(function(){
			var reg=/^1[3|5|8|7]\d{9}$/,phone=$('.phone').val(),validateCode=$('.validateCode').val(),pwd1=$('.userpwd1').val(),pwd2=$('.userpwd2').val();
			if(!reg.test(phone)){
				cottewap.popTip('请输入正确的手机号码',2000);
				return
			}
			if(validateCode==''){
				cottewap.popTip('验证码不能为空',2000);
				return
			}
			if(!(pwd1.length>=6) || !(pwd1.length<=32)){
				cottewap.popTip('请输入6到32位的新密码',2000);
				return
			}
			if(pwd2==''){
				cottewap.popTip('确认新密码不能为空',2000);
				return
			}
			if(pwd1!=pwd2){
				cottewap.popTip('两次密码输入不一致',2000);
				return
			}
			$.ajax({
				url:'member-find_password.html',
				type: "POST",
				dataType: "json",
				data: {account_type:'phone',account:phone,opt:2,authcode:validateCode,ps:pwd1},
				success: function(res){
					if(!res.done){
						cottewap.popTip(res.msg,2000);
						return
					}else{
						cottewap.popTip(res.msg,2000);
						setTimeout(function(){window.location.href="/member-login.html"},2000);
						return
					}
				},
				error: function(){}
			})
			//alert('在这里写ajax提交，以及手机验证码验证')
		});
	},
	pageDetail:function(){//详细页面
		if($('#shadowLayer').length<=0){
			$('.main').append('<div id="shadowLayer"></div>')
		}

		//添加到购物车
		function addCart(dis,sid){
			$.post('/custom-addDisCart.html', {dis:dis,sid:sid}, function(res){
				var res = eval("("+res+")");

				if(res.statusCode == 1){
					if(res.result.size == 'diy')
					{
						luck.open({
							content: '加入购物车成功',
							btn:['继续购物','去预约'],
							yes:function(){
								luck.close()
								$('.sizeTable').removeClass('show');
								$('#shadowLayer').css('visibility','hidden');
							},
							no: function(){
								window.location.href='/cart-index.html';
							}
						});
					}else{
						luck.open({
							content: '加入购物车成功',
							btn:['继续购物','去结算'],
							yes:function(){
								luck.close()
								$('.sizeTable').removeClass('show');
								$('#shadowLayer').css('visibility','hidden');
							},
							no: function(){
								window.location.href='/cart-index.html';
							}
						});
					}

				}else{
					if(res.error.errorCode=200){
//						cottewap.popTip(res.error.msg,1500);
//						setTimeout(function(){window.location.href="/member-login.html"},1600);
						window.location.href="/member-login.html";
						return false;
					}
					cottewap.popTip(res.error.msg,1500);
					return false;
				}
			})
		}
		//滚动图
		var myswipe=Swipe(document.getElementById('slider'), {
			//auto:1000,
			continuous:false,
			callback: function(index, element) {
				$('#slider .little').text((myswipe.getPos()+1)+'/'+myswipe.getNumSlides())
			}
		});
		$('#slider .little').text(1+'/'+myswipe.getNumSlides())
		//查看商品信息
		$('#infoShow').click(function(){
			$('#shadowLayer').unbind('click').click(function(){
				$(this).css('visibility','hidden')
				$('.productInfo').removeClass('show');
			}).css('visibility','visible');
			$('.productInfo').addClass('show');
		});



//		//加入购物车
//		$('#addCart').click(function(){
//			$('#sizeTable .step1').show();
//			$('#sizeTable .step2').hide();
//			$('#shadowLayer').unbind('click').click(function(){
//				$(this).css('visibility','hidden')
//				$('.sizeTable').removeClass('show');
//			}).css('visibility','visible');
//			$('#sizeTable').addClass('show');
//		});
//		$('#sizeTable .base').click(function(){
//			$('#sizeTable .step1').hide();
//			$('#sizeTable .step2').show();
//		});
//		$('#sizeTable .amount').click(function(){
//			var li_length = parseInt($('.scrollBox div').length);
//			var key;
//			var dis_arr = {};
//			for(var i=0;i<li_length;i++){
//				key = parseInt($('#tab_'+i).attr('data-id'));
//				dis_arr[key] = 'diy';
//			}
//			if(dis_arr.length <li_length){
//				cottewap.popTip('尺码选择不完整',1500);
//				//alert('尺码选择不完整')
//				return false;
//			}
//
//			var dis =JSON.stringify(dis_arr) ;
//			addCart(dis,sid)
//		});
//		$('#sizeTable .tab').children('li').click(function(){
//			var n=$(this).index();
//			$(this).siblings().removeClass('cur').end().addClass('cur');
//			$('#sizeTable .itemList').eq(n).siblings().hide().end().show();
//		}).eq(0).trigger('click');
//		$('#sizeTable .scrollBox').on('click','li',function(){
//			$(this).addClass('cur').siblings('.cur').removeClass('cur');
//		})

		//下单按钮
		$('#addCart').click(function(){
			$.post("/custom-is_login.html",function(res){
				var res = eval("("+res+")");
				if(res.statusCode == 0)
				{
					cottewap.popTip(res.error.msg,1500);
//					setTimeout(function(){window.location.href="/member-login.html"},1600);
					window.location.href="/member-login.html";
					return false;
				}
				else
				{
					window.location.href = "/custom-preCart-"+sid+".html";
				}
			});
//			alert(sid);
			
			/*$('#bottomSize').addClass('show');
			$('#shadowLayer,#bottomSize .close').unbind('click').click(function(){
				$('#bottomSize').removeClass('show');
				$('#shadowLayer').css('visibility','hidden');
			});
			$('#shadowLayer').css('visibility','visible');*/
		});

		//选择标准码
		$('#bottomSize .base').click(function(){
			$('#sizeTable').addClass('show');
			$('#bottomSize').removeClass('show');
			$('#shadowLayer,#sizeTable .back').unbind('click').click(function(){
				$('#sizeTable').removeClass('show');
				$('#shadowLayer').css('visibility','hidden');
			});
		});
		//tab
		$('#sizeTable .tabBar').children('li').click(function(){
			var n=$(this).index();
			$(this).siblings().removeClass('cur').end().addClass('cur');
			$('#sizeTable .itemList').eq(n).siblings().hide().end().show();
		}).eq(0).trigger('click');

		//layer
		$('#sizeTable .scrollBox').on('click','li',function(){
			$(this).addClass('cur').siblings('.cur').removeClass('cur');
		})
		//保存尺码
		$('#sizeOk').click(function(){
			var flag=true,n=0,dis_arr={};
			$('#sizeTable .itemList').each(function(index, element) {
				if($(this).find('.cur').length<=0){
					flag=false;
					n=index;
					return flag;
				}
				dis_arr[$('#sizeTable .tabBar li').eq(index).attr('data-id')]=$(this).find('.cur').html();
			});
			if(flag){
				var dis =JSON.stringify(dis_arr) ;
				addCart(dis,sid);
			}else{
				luck.open({content:'请选择'+$('#sizeTable .tabBar li').eq(n).text()+'尺码',time:1500})
			}
		});

		//量体定制
		$('#bottomSize .amount').click(function(){
			var dis_arr={}
			$('#sizeTable .tabBar li').each(function(i, e) {
				dis_arr[$(e).attr('data-id')]='diy';
			});
			var dis =JSON.stringify(dis_arr) ;
			addCart(dis,sid);
		});














	},

    pageAddInviter:function(){
		var invite = $('#invite').val()
		if(invite==''){
		    cottewap.popTip('邀请码不能为空',1500);
			return
		}

		if(invite.length !=8 && invite.length !=12){
		    cottewap.popTip('错误的邀请码',1500);
			return
		}

		$.ajax({
			url:'member-addinviter.html',
			type:'POST',
			data:{invite:invite},
			dataType:'json',
			success:function(res){
				if(res.statusCode==1){
					cottewap.popTip(res.result.success,1500);
					setTimeout(function(){
						window.location.href='member-index.html'	},1000);
					return
				}else{
					cottewap.popTip(res.error.msg,1500);
					return
				}
			},
			error:function(res){}
		});

	},


	 pageAddbd:function(){
		var bd = $('#bd').val()
		if(bd==''){
		    cottewap.popTip('BD码不能为空',1500);
			return
		}

		if(bd.length !=8 && bd.length !=12){
		    cottewap.popTip('错误的BD码',1500);
			return
		}

		$.ajax({
			url:'erweima-geterweima.html',
			type:'POST',
			data:{bd:bd},
			dataType:'json',
			success:function(res){
				if(res.done == true){
					alert(res.retval.content); return;
					$("#erweima img").attr("src",res.retval.content);
					cottewap.popTip(res.msg,1500);
					return
				}else{
					cottewap.popTip(res.error.msg,1500);
					return
				}

			},
			error:function(res){}
		});

	},


	pageEditAddres:function(){

		$('#switch').click(function(){
			var $this=$(this);
			if($this.hasClass('cur')){
				$this.removeClass('cur').attr('value',0);
			}else{
				$this.addClass('cur').attr('value',1)
			}
		});
		$('#preservation').click(function(){
			var name=$('.name').val(),phone=$('.phone').val(),region_id=$('.mls_id').val(),region_name=$('.mls_names').val(),address2=$('.address2').val();
			var is_def=$('#switch').attr("value");
			if(name==""){
				cottewap.popTip('姓名不能为空',1500);
				return
			}
			var reg=/^1[3|5|8|7]\d{9}$/;
			if(!reg.test(phone)){
				cottewap.popTip('请输入正确的手机号码',1500);
				return
			}
			if(region_name==''||address2==''){
				cottewap.popTip('地址信息不完整',1500);
				return
			}
			//alert('在这里写ajax提交')
			 var r = '';

           $("#region select").each(function(){
        		r += r ? ","+ $(this).val() : $(this).val();
             })
             $("#region_list").val(r);

				address(name,phone,region_id,region_name,r,address2,is_def);
		})
	},
	pageEditPwd:function(){
		$('#save').click(function(){
			var oldPwd=$('.oldPwd').val(),newPwd1=$('.newPwd1').val(),newPwd2=$('.newPwd2').val();
			if($('.oldPwd').val()==""){
				cottewap.popTip('旧密码不能为空',1500);
				return
			}
			if($('.newPwd1').val()==""){
				cottewap.popTip('新密码不能为空',1500);
				return
			}
			if($('.newPwd2').val()==""){
				cottewap.popTip('确认新密码不能为空',1500);
				return
			}
			if($('.newPwd2').val()!=$('.newPwd1').val()){
				cottewap.popTip('两次密码不一致',1500);
				return
			}
			$.ajax({
				url:'member-password.html',
				type:'POST',
				data:{orig_password:oldPwd,new_password:newPwd1,confirm_password:newPwd2},
				dataType:'json',
				success:function(res){
					if(res.done){
						cottewap.popTip(res.msg,1500);
						setTimeout(function(){
							window.location.href='member-index.html'	},1000);
						return
					}else{
						cottewap.popTip(res.msg,1500);
						return
					}
				},
				error:function(res){}
			});

			//alert('后端该你了')
		});
	},
	pageEditPayPwd:function(){
		$('#save').click(function(){
			var op=$('.oldPwd');
			if(op.length>0){
				var oldPwd=$('.oldPwd').val();
			}else{
				var oldPwd='';
			}
			var newPwd1=$('.newPwd1').val(),newPwd2=$('.newPwd2').val();
			if(op.length>0 && $('.oldPwd').val()==""){
				cottewap.popTip('旧支付密码不能为空',1500);
				return
			}
			if($('.newPwd1').val()==""){
				cottewap.popTip('新支付密码不能为空',1500);
				return
			}
			if($('.newPwd2').val()==""){
				cottewap.popTip('确认新密码不能为空',1500);
				return
			}
			if(newPwd1==oldPwd){
				cottewap.popTip('请填写与旧支付密码不一样的新支付密码',2000);
				return
			}
			if(!(newPwd1.length>=6) || !(newPwd1.length<=32)){
				cottewap.popTip('请输入6到32位的新支付密码',2000);
				return
			}
			if($('.newPwd2').val()!=$('.newPwd1').val()){
				cottewap.popTip('两次输入的支付密码不一致',1500);
				return
			}
			$.ajax({
				url:'member-pay_password.html',
				type:'POST',
				data:{orig_password:oldPwd,new_password:newPwd1,confirm_password:newPwd2},
				dataType:'json',
				success:function(res){
					if(res.done){
						cottewap.popTip(res.msg,1500);
						setTimeout(function(){
							window.location.href='member-index.html'	},1000);
						return
					}else{
						cottewap.popTip(res.msg,1500);
						return
					}
				},
				error:function(res){}
			});

			//alert('后端该你了')
		});
	},
	pageMessageList:function(){//我的消息页面
	    var flag=true;
		$(window).scroll(function(){
			if($(window).scrollTop()>=$(document).height()-$(window).height()){
				if($('.tip').html()==''){
					return false;
				}
				if(flag){
					flag=false
					var page = parseInt($("#page").val())+1;
					//var topic = $('.lblist .current').attr('value');
					$.get("/my_messages-ajax_messages_lists-"+page+".html", function(res){
						var res = eval("("+res+")");
						if(page<=res.retval.count){
							$.each(res.retval.content,function(n,i){
								var tmp=i.is_read
								if(tmp==0){

									$('#listBox').append('<li id=""><h1><a href="my_messages-info-'+i.id+'.html"><b></b>'+i.title+'</a><span class="yxz"></span></h1><a href="my_messages-info-'+	i.id+'.html"><p>'+i.add_time+'</p></a></li>');
								}else{
									$('#listBox').append('<li><h1><a href="my_messages-info-'+i.id+'.html">'+i.title+'</a><span class="yxz"></span></h1><a href="my_messages-info-'+i.id+'.html"><p>'+i.add_time+'</p></a></li>');
								}
							})
							if(page==res.retval.count){
								$('#listBox').append('<div class="tip"></div>')
							}else{
								$("#page").val(page)
							}
							flag=true
						}
					})
				}
			}
		})

	},
}

//检测是否是微信浏览器
function is_weixin(){
	var ua = navigator.userAgent.toLowerCase();
	if(ua.match(/MicroMessenger/i)=="micromessenger") {
		return true;
 	} else {
		return false;
	}
}
if(is_weixin()){
	$('.topBar').hide();
}