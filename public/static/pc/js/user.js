
var cotteFn={
    /*==================
     登录
     ===================*/
    countdown:function(obj,n){
        var t=obj.val();
        obj.attr('disabled',true).val('倒计时'+(n--)+'秒');
        (function(){
            if(n>0){
                obj.attr('disabled',true).val('倒计时'+(n--)+'秒');
                setTimeout(arguments.callee,1000);
            }else{
                obj.attr('disabled',false).val(t);
            }
        })()

    },
    error:function(obj,error){
        var errorBox=obj.parent().find('.error'),t=obj.attr('tip')?obj.attr('tip'):'';
        if(errorBox.length>0){
            errorBox.html(t+error+'<img src="public/static/pc/images/sjbz.png">').show();
        }else{
            obj.parent().append('<p class="error">'+t+error+'<img src="public/static/pc/images/sjbz.png"></p>')
        }
        obj.addClass('error-css')
        obj.one('focus')

    },
    login:function(){
        $('#loginBox').ajaxForm({
            beforeSubmit:function(){
                var b=$('#loginBox').validate({
                    submitBtn:{
                        flag:true,
                    },
                    acticle:true,
                    error:function(obj,error){cotteFn.error(obj,error)}
                });
                if(!b){
                    return false
                }
            },
            success:function(res){
                var res = eval("("+res+")");
                if(!res.done){
                    alert(res.msg)
                    $('#loginIn').val('登录')
                }else{
                    window.location.href=res.retval;
                }
            },
            error:function(){
                alert('error');
                $('#loginIn').val('登录')
            }
        })
    },


    /*==================
     找回密码
     ===================*/
    findPwd:function(){
        //选项卡
        $('.sjyxqh li').click(function(){
            $(this).addClass('now_hover').siblings().removeClass('now_hover');
			$('.sjyxzh>div').eq($(this).index()).siblings('div').hide().end().show();
		})
		//通过手机找回
		$('#phoneFind').ajaxForm({
			beforeSubmit:function(){
				var b=$('#phoneFind').validate({
					submitBtn:{
						flag:true,
					},
					acticle:true,
					error:function(obj,error){cotteFn.error(obj,error)}
				});
				if(!b){
					return false
				}
			},
            success:function(res){
                var res = eval("("+res+")");
                if(!res.done){
                    alert(res.msg)
                    $('#phoneFindNext').val('下一步')

                }else{
                    $('.sjyxzh').replaceWith(res.retval.content);
                }

            },
            error:function(){
                alert('error')
                $('#phoneFindNext').val('下一步')
            }
        })

		//email 验证码
        $('#validateImg').click(function(){
            var src = "ajax-getAuthCode-findps.html?r="+Math.random();
            $("#validateImg").attr('src',src);
        })

        //获取验证码-手机
        $('.getCode').click(function(){
			var reg=/^1[3|5|8|7]\d{9}$/,phone=$('#phone').val();
			if(!reg.test(phone)){
                alert('请输入合法的手机号码')
                return;
			}
            $.ajax({
                "url":"ajax-resetCode.html",
                "data": "phone="+phone+"&category=findps&type=findps&opt=get" ,
                "type": "POST",
                'dataType':'json',
                "success": function(res) {
					if(res.err){
                        alert(res.msg);
                    }else{
                       cotteFn.countdown($('.getCode'),60)
                    }
                }
            });
        })
        //通过邮箱找回
        $('#emallFind').ajaxForm({
            beforeSubmit:function(){
                var b=$('#emallFind').validate({
                    submitBtn:{
                        flag:true,
                    },
                    acticle:true,
                    error:function(obj,error){cotteFn.error(obj,error)}
                });
                if(!b){
                    return false
                }
            },
            success:function(res){
                var res = eval("("+res+")");
				$('#emallFindNext').val('下一步')
                if(!res.done){
                    alert(res.msg)
                }else{
                    $('.sjyxzh').replaceWith(res.retval.content);
                }
            },
            error:function(){
                alert('error');
                $('#emallFindNext').val('下一步')
            }
        })
    },
    /*==================
     注册
     ===================*/
    reg:function(){
        //选项卡
        $('.sjyxqh li').click(function(){
            $(this).addClass('now_hover').siblings().removeClass('now_hover');
            $('.sjyxzh>div').eq($(this).index()).siblings('div').hide().end().show();
        })

	    //注册协议
		$('#provision').click(function(){
			luck.open({
				title:'麦富迪用户注册协议',
				content:'<iframe width="100%" height="400" src="ajax-provision.html" frameborder="0"></iframe>',
				width:'600px',
				height:'440px',
			    addclass:'cotte-luck'
			});
			return false;
		})

        //获取验证码
        $('.getCode').click(function(){
			var _self=this;
			var reg=/^1[3|5|8|7]\d{9}$/,phone=$('#phone').val();
			if(!reg.test(phone)){
                alert('请输入合法的手机号码')
                return;
			}
            $.ajax({
                "url":"/ajax-sendCode.html",
                "data": "phone="+phone+'&type=reg&ty='+_self.getAttribute('data-ty'),
                "type": "POST",
                'dataType':'json',
                "success":function(res) {
                    if(!res.done){
                        alert(res.msg);
                        return;
                    }else{
                        cotteFn.countdown($(_self),60)
                    }
                }
            });

        })
        //通过手机号注册
        $('#phoneReg').ajaxForm({
            beforeSubmit:function(){
                var b=$('#phoneReg').validate({
                    validate:{
                        same:function(e){
                            if(e.val()!=$('.same').val()){
                                cotteFn.error(e,'两次密码输入不一致')
                                return false;
                            }
                        },
                        required2:function(e){
                            if(!e[0].checked){
                                cotteFn.error(e,'请勾选我已阅读并同意《COTTE用户注册协议》')
                                return false
                            }
                        }
                    },
                    submitBtn:{
                        flag:true,
                    },
                    acticle:true,
                    error:function(obj,error){cotteFn.error(obj,error)}
                });
                if(!b){
                    return false
                }
            },
            success:function(res){
                var res = eval("("+res+")");
                if(!res.done){
                    alert(res.msg)
                    $('#phoneRegBtn').val('注册')
                }else{
					if(res.retval.content !=''){
						window.location.href=res.retval.content;
					}else{
						window.location.href='/member-index.html';
					}

                }
            },
            error:function(){
                alert('error');
                $('#phoneRegBtn').val('注册')
            }
        });
//		//通过邮箱注册
//		$('#emailReg').ajaxForm({
//			beforeSubmit:function(){
//				var b=$('#emailReg').validate({
//					validate:{
//						same2:function(e){
//							if(e.val()!=$('.same2').val()){
//								cotteFn.error(e,'两次密码输入不一致')
//								return false;
//							}
//						},
//						required2:function(e){
//							if(!e[0].checked){
//								cotteFn.error(e,'请勾选我已阅读并同意《COTTE用户注册协议》');
//								return false
//							}
//						}
//					},
//					submitBtn:{
//						flag:true,
//					},
//					acticle:true,
//					error:function(obj,error){cotteFn.error(obj,error)}
//				});
//				if(!b){
//					return false
//				}
//			},
//			success:function(){
//				alert('1')
//			},
//			error:function(){
//				alert('error');
//				$('#emailRegBtn').val('注册')
//			}
//		})
    },

    /*==================
     密码重置
     ===================*/

    pwdReset:function(){
        $('#pwdReset').ajaxForm({
            beforeSubmit:function(){
                var b=$('#pwdReset').validate({
                    validate:{
                        same:function(e){
                            if(e.val()!=$('.same').val()){
                                cotteFn.error(e,'两次密码输入不一致')
                                return false;
                            }
                        }
                    },
                    submitBtn:{
                        flag:true,
                    },
                    acticle:true,
                    error:function(obj,error){cotteFn.error(obj,error)}
                });
                if(!b){
                    return false
                }
            },
            success:function(res){
                var res = eval("("+res+")");
                if(!res.done){
                    alert(res.msg)
                    $('#pwdResetBtn').val('完成')
                }else{
                    $('.login').replaceWith(res.retval.content);
                }

            },
            error:function(){
                alert('error');
                $('#pwdResetBtn').val('完成')
            }
        })
    },
	kuka:function(){
        $('.qrbut').click(function(){

			var sn = $('#sn').val();
            var user = $('#inviterlist .fxks')
            var  uid = user.attr('data-value')
			$.post('/kuka-kukaDonation.html',{sn:sn,invitee:uid},function(res){
				var res = eval("("+res+")");
				if(res.done){
					alert('转赠成功')
				    window.location.href="kuka-index.html";
				}else{
					alert(res.msg)
				}
			});
		});
    },
	special:function(){
		$('#special_submit').click(function(){
			var sn = $('#special_code').val();
			$.post('/special_code-special_code.html',{sn:sn},function(res){
				var res = eval("("+res+")");
				if(res.done){
					alert('恭喜您成功使用'+res.retval.name+'，升级为'+res.retval.member_lv_name+'!')
					$(".kksyword").html(res.retval.name+':'+res.retval.description)
				}else{
					alert(res.msg)
				}
			});
		});
    }
}