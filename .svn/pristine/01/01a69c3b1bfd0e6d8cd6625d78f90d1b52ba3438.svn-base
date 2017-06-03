var cotteFn={
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
	customer:function(){
		//顾客管理添加
		$('#add').click(function(){

			luck.open({
				width:'660px',
				height:'295px',
				title:'添加顾客信息',
				addclass:'cotte-luck',
				content:$('#window').html()
			})


			$(".gkname input").change(function(){
				var customer_name = $(".luck-con .gkname #names").val();
				if(customer_name==""){
					$('.tzzssy1').text('姓名不能为空').show();
					return
				}else{
					$('.tzzssy1').text('').hide();
				}
             });
			$(".gkphone input").change(function(){
				 var customer_mobile = $(".luck-con .gkphone #mobiles").val();
				var reg=/^1[3|5|8|7]\d{9}$/;
				if(customer_mobile==""){
					$('.tzzssy2').text('手机号不能为空').show();
					return
				}else if(!reg.test(customer_mobile)){
					$('.tzzssy2').text('请输入正确的手机号码').show();
					return
				}else{
					$('.tzzssy2').text('').hide();
				}
             });
			$(".gkinp").click(function(){
				var customer_name = $(".luck-con .gkname #names").val();
				 var customer_mobile = $(".luck-con .gkphone #mobiles").val();

				if(customer_name==""){
					$('.tzzssy1').text('姓名不能为空').show();
					return
				}else{
					$('.tzzssy1').text('').hide();
				}
				var reg=/^1[3|5|8|7]\d{9}$/;
				if(customer_mobile==""){
					$('.tzzssy2').text('手机号不能为空').show();
					return
				}else if(!reg.test(customer_mobile)){
					$('.tzzssy2').text('请输入正确的手机号码').show();
					return
				}else{
					$('.tzzssy2').text('').hide();
				}

					$.ajax({
						type:"POST",
						url:"/customer-add.html",
						data:{customer_name:customer_name,customer_mobile:customer_mobile},
						datatype:'json',
						success: function(e){
							var r = $.parseJSON(e);
							//r=eval("("+e+")");
							if(r.done == true) {
								alert('添加顾客成功',1500);
								luck.close()
								window.location.reload()
							}else{
								$('.tzzssy2').text(r.msg).show();
							}
						}
					});
			})

		})
	},
	bindEmail:function(){
		$('.bdbut').click(function(){
			cotteFn.countdown($(this),60)
		})
		$('.bdinp').focus(function(){
			$('.tssryz').text('').hide();
		})
		$('.bdxyb').click(function(){
			if($('.bdinp').val()==''){
				$('.tssryz').text('请输入手机校验码').show();
			}else{
				alert('提交中')
			}
		})
	},

	amount10:function(){
		//量体师点评
		$('.comment').click(function(){
		    var id=$(this).attr("value");
		    var type = $(this).data("type");
			var obj=luck.open({
				width:'660px',
				height:'475px',
				title:'商品评价',
				//title:'量体师点评',
				content:$('#window03').html(),
				class:"cotte-luck"
			});
			(function(){
				var oStar = $(obj).find("#star");
				oStar.on('click','li',function(){
					var num=$(this).index();
					$(this).parent().attr('data-num',num)
				});
				oStar.find('li').hover(function(){
					var li=$(this).parent().find('li');
					li.removeClass();
					for(var i=0;i<=$(this).index();i++){
						li.eq(i).addClass('on')
					}
				},function(){
					var li=$(this).parent().find('li');
					li.removeClass();
					for(var i=0;i<=$(this).parent().attr('data-num');i++){
						li.eq(i).addClass('on')
					}
				});

			/*	$(obj).find(".impression").on('click','li',function(){
					$(this).addClass('pfcur');
				})

*/
				/*$(obj).find(".impression").toggle(function(){
					$(this).addClass('pfcur');
				},function(){
					$(this).removeClass('pfcur');

				});*/
				$(obj).find(".impression").on('click','li',function(){
					var _self=$(this);
					if(_self.hasClass('pfcur')){
						$(this).removeClass('pfcur');
					}else{
						$(this).addClass('pfcur');
					}
				})

				$(".ltbut").click(function(){
				var star = $(".luck-con #star ul").attr('data-num');
			 	star = parseInt(star)+1;

				if(star==0){
					alert('服务评分不能为0',1500);
					return
				}

                var info =  $(".luck-con .xd  #content").val();


				if(info ==""){
					alert('评论内容不能为空',1500);
					return;
				}

		        var impress =[]
		         $(obj).find(".impression .pfcur").each(function(){
		         		impress.push($(this).text())
		         });

		        if(impress == ""){

		         	alert('评论印象不能为空',1500);
		         	return;
		         }

				$.ajax({
					type:"POST",
					url:"/my_comment-addComment.html",
					data:{star:star,info:info,id:id,type:type,impress:impress.join(';')},
					datatype:'json',
					success: function(e){
						var r = $.parseJSON(e);
						//r=eval("("+e+")");
						if(r.done == true) {
							alert('发表评论成功',1500);
							luck.close()
							window.location.reload()
						}else{
							alert(r.msg);
						}
					}
				});
			})





			})();
		})
	},



	 persInfo:function(){
		$('.grbut').click(function(){
			var b=$('.grtab').validate({
				acticle:true,
				error:function(e,error){
					$(e).siblings('.error').remove();
					$(e).parents('td').append('<span class="error" style="color:#f00">'+$(e).attr('tip')+error+'</span>').end();
				}
			});
			if(b){
				alert('保存成功')
			}
		})
	 },
	 morePersInfo:function(){
		$('.grbut').click(function(){
			var b=$('.grtab').validate({
				validate:{
					weixin:function(e){
						reg=/[^\x00-\xff]/,val=e.val();
						if(val&&val.match(reg)){
							_error(e,"weixin");
							return false
						}
					}
				},
				messages:{
					weixin:'格式不正确'
				},
				acticle:true,
				error:function(e,error){
					$(e).siblings('.error').remove();
					$(e).parents('td').append('<span class="error" style="color:#f00">'+$(e).attr('tip')+error+'</span>').end();
				}
			});
			if(b){
				alert('保存成功')
			}
		})
	 },
	 openPwd:function(){
		$('.bdbut').click(function(){
			cotteFn.countdown($(this),60)
		})
		$('.bdinp').focus(function(){
			$('.tssryz').text('').hide();
		})
		$('.bdxyb').click(function(){
			if($('.bdinp').val()==''){
				$('.tssryz').text('请输入手机校验码').show();
			}else{
				alert('提交中')
			}
		})
	 },
	 address:function(){
		   //选择
			$('#address>ul>li').not('#addAddress').click(function(){
				$(this).addClass('cur').siblings().removeClass('cur')
			});
			//删除
			$('#address .caozuoBtn .del').click(function(){
				if(window.confirm('确定要删除？')){
					$(this).parents('li').fadeOut('fast',function(){
						alert('删除成功')
					})
				}
			});
			//地址管理
			function address(obj,id){
				var popLayer=$('<div>'),
					popShadow=$('<div>'),
					offset=(obj=='add')?$(this).offset():$(this).parents('li').offset(),
					left=offset.left,
					top=offset.top;
				popShadow.css({
					width:'100%',
					height:'100%',
					position:'fixed',
					background:'#000',
					opacity:'0.2',
					left:0,
					top:0,
					zIndex:100,
					display:'none'
				}).attr('id','popShadow').click(function(){
					$(this).fadeOut('fast',function(){
						$(this).remove();
					});
					popLayer.fadeOut('fast',function(){
						$(this).remove();
					})
				})
				$('body').append(
					popLayer.css({
						width:335,
						height:445,
						position:'absolute',
						left:left,
						top:top,
						background:'#fff',
						boxShadow:'0 18px 30px #999',
						zIndex:101,
						display:'none'
					}).attr('id','popLayer').html('<iframe src="address.html?'+obj+'&id='+id+'" width="335" height="445" frameborder="0"></iframe>')).append(popShadow);
					popLayer.fadeIn('fast');
					popShadow.fadeIn('fast');
			}

			//添加
			$('#addAddress').click(function(){
				address.call(this,'add',0)
			})
			//修改
			$('#address .caozuoBtn .edit').click(function(){
				address.call(this,'edit',123)
			});
	},

	customer1:function(){

		//顾客信息修改
		$('.gkmodify').click(function(){
			var figure_sn=$(this).attr("value")
			$.post("/customer-getlist.html",{figure_sn:figure_sn}, function(res){
			var res = $.parseJSON(res);
			var info=luck.open({
				width:'660px',
				height:'295px',
				title:'修改顾客信息',
				content:$('#window01').html(),
				btn:['修改',''],
				yes:function(){
					var customer_name = $(info).find("#moname").val()
					var customer_mobile = $(info).find("#mophone").val()
					if(customer_name==""){
						alert('姓名不能为空',1500);
						return
					}
					if(customer_mobile==""){
						alert('手机号不能为空',1500);
						return
					}
					var reg=/^1[3|5|8|7]\d{9}$/;
					if(!reg.test(customer_mobile)){
						alert('请输入正确的手机号码',1500);
						return
					}
					$.ajax({
						type:"POST",
						url:"/customer-edit.html",
						data:{customer_name:customer_name,customer_mobile:customer_mobile,figure_sn:figure_sn},
						datatype:'json',
						success: function(e){
							var r = $.parseJSON(e);
							if(r.done == true) {
								alert('修改顾客成功',1500);
								luck.close()
								window.location.reload()
							}else{
								alert(r.msg);
							}
						}
					});
				}
			})
		    $(info).find("#moname").val(res.retval.customer_name)
		    $(info).find("#mophone").val(res.retval.customer_mobile)
		})

})
},
customer2:function(){
	//顾客信息查看
	$('.gksee').click(function(){
		var figure_sn=$(this).attr("value")
		$.post("/customer-check.html",{figure_sn:figure_sn}, function(res){
			var res = $.parseJSON(res)
		    var info1=luck.open({
				width:'660px',
				height:'630px',
				title:'基本信息',
				content:$('#window02').html()
			})
			$(info1).find("#names").text(res.retval.figure.customer_name)
			$(info1).find("#phones").text(res.retval.figure.customer_mobile)
			$(info1).find("#numbers").text(res.retval.figure.order_num)
			$(info1).find("#times").text(res.retval.figure.lasttime)
	        var str="";
	        $.each(res.retval.figure_info,function(i,d){
	        	str+='<li>'+d.name+'：<span>'+d.val+'</span></li>'
	        });
	        $(info1).find('.ltsj').html(str)

	          var feat="【特殊体型】";
	        $.each(res.retval.feature,function(i,d){
	        	feat+='<span>'+d.name+'：'+d.val+'</span>'
	        });
	        $(info1).find('.special1').html(feat)
	        var sty="【着装风格】";
	        $.each(res.retval.style,function(i,d){
	        	sty+='<span>'+d.name+'：'+d.val+'</span>'
	        });
	        $(info1).find('.special2').html(sty)
		})
	})
}	,
customer3:function(){
	//我的顾客_删除
	$('.dropc').click(function(){
		var figure_sn=$(this).attr("value")
		luck.open({
			width:'395px',
			height:'200px',
			title:'确认删除',
			content:$('#window03').html(),
			btn:['确认','取消'],
			no:function(){

			},
			yes:function(){
				$.ajax({
					type:"POST",
					url:"/customer-drop.html",
					data:{figure_sn:figure_sn},
					datatype:'json',
					success: function(e){
						var r = $.parseJSON(e);
						if(r.done == true) {
							alert('删除顾客成功',1500);
							luck.close()
							window.location.reload()
						}else{
							alert(r.msg);
						}
					}
				});
			}
		})


	})
 },
     amount:function(){
		//购物车_设置签名
		$('#szqm').click(function(){
			luck.open({
				width:'660px',
				height:'426px',
				title:'设置签名',
				content:$('#window04').html()
			})
		})
	 },

	 amputate:function(){
		//我的收藏_删除
		$('#sccut').click(function(){
			luck.open({
				width:'395px',
				height:'200px',
				title:'确认删除',
				content:$('#window05').html()
			})
		})
	 },

		customer6:function(){
		//申请更换
		$('.replacement').click(function(){
			luck.open({
				width:'660px',
				height:'480px',
				title:'面料册申请更换',
				content:$('#window06').html()
			})
		})
	},
		customer7:function(){
		//账户收益
		$('.balance').click(function(){
			luck.open({
				width:'660px',
				height:'475px',
				title:'转入余额',
				addclass:'cotte-luck',
				content:$('#window07').html()
			})
		})
	},

	customer8:function(){
		//结算说明
		$('.settlement').click(function(){
			luck.open({
				width:'660px',
				height:'475px',
				title:'转入余额',
				content:$('#window08').html()
			})
		})
	},

	customer9:function(){
		//订单详情页量体数据
		$('.layer').click(function(){
			var sn = $(this).data("sn");
			$.get("my_order-getFigure.html",{orderid:orderid,sn:sn}, function(res){
				var res = eval("("+res+")");
				if(res.done == true){
					luck.open({
						width:'815px',
						height:'auot',
						title:'量体信息',
						addclass:'cotte-luck',
						content:res.retval
					})
				}else{
					document.location.reload();
				}
			})
		})
	},
	customer10:function(){
		//订单详情页工艺信息
		$('.gyxxsee').click(function(){
			var sn = $(this).data("sn");
			var type = $(this).data("type");
			$.get("my_order-getDict.html",{orderid:orderid,sn:sn,type:type}, function(res){
				var res = eval("("+res+")");
				if(res.done == true){
					luck.open({
						width:'660px',
						height:'470px',
						title:'工艺信息',
						addclass:'cotte-luck',
						content:res.retval
					})
				}else{
					document.location.reload();
				}
			})

		})
	},
	customer11:function(){
		//订单详情页评论
		$('#plsee').click(function(){
			luck.open({
				width:'660px',
				height:'490px',
				addclass:'cotte-luck',
				content:$('#window11').html()
			})
		})
	},
	customer001:function(){
		//麦富迪币-什么是麦富迪币
		$('#money').click(function(){
			luck.open({
				width:'660px',
				height:'490px',
				addclass:'cotte-luck',
				content:$('#window001').html()
			})
		})
	},
	customer002:function(){
		//麦富迪币-如何获取
		$('#brhhq').click(function(){
			luck.open({
				width:'660px',
				height:'490px',
				addclass:'cotte-luck',
				content:$('#window002').html()
			})
		})
	},
	customer003:function(){
		//麦富迪币-如何使用
		$('#brhsy').click(function(){
			luck.open({
				width:'660px',
				height:'490px',
				addclass:'cotte-luck',
				content:$('#window003').html()
			})
		})
	},
	customer004:function(){
		//我的优惠券-如何使用
		$('#kqrhsy').click(function(){
			luck.open({
				width:'660px',
				height:'490px',
				addclass:'cotte-luck',
				content:$('#window004').html()
			})
		})
	},
	customer005:function(){
		//我的w订单-去支付
		$('.pay').click(function(){
			var layer = luck.open({
				width:'650px',
				height:'380px',
				addclass:'cotte-luck',
				content:$('#window005').html()
			});
			$(layer).find("input[name=orderid]").val($(this).data("orderid"));
			$(layer).find(".zfway a").each(function(){
	    		$(this).click(function(){
	        		$(this).siblings().removeClass("zffscur");
	        		$(this).addClass("zffscur");
	    		})
	    	})

	    	$(layer).find(".confirmzf").click(function(){
	    		var payid    = $(layer).find(".zffscur").data("payid");
	    		var orderid  = $(layer).find("input[name=orderid]").val();
	    		$.post("my_order-changePay.html",{payid:payid,orderid:orderid}, function(res){
	    			var res = eval("("+res+")");
	    			if(res.done == false){
	    				alert(res.msg)
	    				return;
	    			}
	    			$(layer).find("input[name=os]").val(res.retval.sn);
	    			$(layer).find("form").submit();
	    		})
	    	})
	    });
	},
	customer006:function(){
		//我的w订单-修改支付方式
		$('.changePay').click(function(){
			var layer = luck.open({
				width:'650px',
				height:'380px',
				addclass:'cotte-luck',
				content:$('#window006').html()
			});
			$(layer).find("input[name=orderid]").val($(this).data("orderid"));
			$(layer).find(".zfway a").each(function(){
	    		$(this).click(function(){
	        		$(this).siblings().removeClass("zffscur");
	        		$(this).addClass("zffscur");
	    		})
	    	})

	    	$(layer).find(".confirmzf").click(function(){
	    		var payid    = $(layer).find(".zffscur").data("payid");
	    		var orderid  = $(layer).find("input[name=orderid]").val();
	    		$.post("my_order-changePay.html",{payid:payid,orderid:orderid}, function(res){
	    			var res = eval("("+res+")");
	    			if(res.done == false){
	    				alert(res.msg)
	    				return;
	    			}
	    			window.location.reload();
	    		})
	    	})
	    });
	},
    customer12:function(){
        $('.grzlltsjpl').click(function(){
            var figure_sn = $(this).next().val();
            var pinglun = $(this);
            $.post("./member-getFigure.html",{figure_sn:figure_sn},function(res){
                var res = $.parseJSON(res);
                $("#lt_id").val(res.retval.liangti_id)
                var lt_id = $("#lt_id").val()
                $("#idserve").val(res.retval.id_serve)
                var idserve = $("#idserve").val()
                $("#names").html(res.retval.liangti_name)
                $("#number").html(res.retval.card_number)
                $("#times").html(res.retval.lasttime)
                var obj=luck.open({
                    width:'660px',
                    height:'475px',
                    title:'量体师评价',
                    content:$('#window12').html(),
                    class:"cotte-luck"
                });
                var oStar = $(obj).find("#star");
                oStar.on('click','li',function(){
                    var num=$(this).index();
                    $(this).parent().attr('data-num',num)
                });
                oStar.find('li').hover(function(){
                    var li=$(this).parent().find('li');
                    li.removeClass();
                    for(var i=0;i<=$(this).index();i++){
                        li.eq(i).addClass('on')
                    }
                },function(){
                    var li=$(this).parent().find('li');
                    li.removeClass();
                    for(var i=0;i<=$(this).parent().attr('data-num');i++){
                        li.eq(i).addClass('on')
                    }
                });

            /*    $(obj).find(".impression").on('click','li',function(){
                    $(this).addClass('pfcur').siblings().removeClass();
                })*/

				$(obj).find(".impression").on('click','li',function(){
					var _self=$(this);
					if(_self.hasClass('pfcur')){
						$(this).removeClass('pfcur');
					}else{
						$(this).addClass('pfcur');
					}
				})

                var con = $(obj).find("#content").change(function(){
                    var con = $(obj).find("#content").val()
                    $(obj).find("#hcon").val(con)
                })
                $(obj).find('#ltbut').click(function(){
                    var content = $(obj).find("#hcon").val()
                    var star = $(".luck-con #star ul").attr('data-num');
                    star = parseInt(star) +1;
                    if(!content){
                        alert('请您输入你对该量体师的评价')
                    }else{
                        $.post("./member-addComment.html",{content:content,lt_id:lt_id,idserve:idserve,star:star,figure_sn:figure_sn},function(data){
                            var data = $.parseJSON(data);
                            if(data.done){
                                alert('评论成功，谢谢')
                                $(pinglun).html('已评论')
                                $(pinglun).unbind("click");
                            }else{
                                alert(data.msg)
                            }
                            luck.close();
                        })
                    }
                })

            })
        })
    },
	coin:function(){
    	//会员中心-什么是麦富迪币弹窗
		$('#provision').click(function(){
			luck.open({
				title:'关于麦富迪币',
				content:'<iframe width="100%" height="400" src="ajax-coin.html" frameborder="0"></iframe>',
				width:'600px',
				height:'auto',
			    addclass:'cotte-luck'
			});
			return false;
		});
    },

	customer15:function(){
		//顾客管理添加
		$('.featck').click(function(){

			luck.open({
				width:'800px',
				height:'auto',
				title:'业绩明细',
				addclass:'cotte-luck',
				content:$('#window007').html()
			});
		});
	},





   receive:function(order_id){
		$.post('/fx-fx_receive.html',{order_id:order_id},function(res){
			var res = eval("("+res+")");

			if(res.done){

				alert(res.retval)
				window.location.href=window.location.href
				return false;
			}else{
				alert(res.msg)
				return false;
			}
		});

	 },
   show:function(order_id){
		var _html = '';
		$.post('/fx-fx_info.html',{order_id:order_id},function(res){
			var res = eval("("+res+")");

			if(res.done){
				var $item  = res.retval;
				if( $item.sign==1){

						   _html ='<table width="0" border="0" cellpadding="0" cellspacing="0" class="fxtab">'+

							'<tr>'+
							'<th>寄回地址：</td>'+
							'<td colspan="3" >'+$item.address+'</td>'+
							'</tr>'+
							'<tr>'+
							'<th>返修原因：</td>'+
							'<td colspan="3">'+$item.reason+'</td>'+
							'</tr>'+
							'</table>';


					if ($item.free==0){

						_html += '<div class="fxzongj">'+
								'<h4>费用共计￥'+$item.order_amount+'</h4>'+
								'<div class="fxzhif">麦富迪币支付: ￥'+$item.coin+'<span>余额支付: ￥'+$item.money_amount+'</span>'+'<span>支付宝支付: ￥'+$item.final_amount+'</span></div>'+
								'</div>';

					}

					_html += '<div class="fxzongj fxfabot">'+
							'<h4>返修方案</h4>';


					if($item.fx_str){
						_html +=   '<ul>'+
								'<li>'+$item.fx_str+'</li>'+
								'</ul>';
					}else{

						if ($item.free==0){
							_html +=   '<ul>'+
									'<li>'+$item.gy_method+'</li>'+
									'</ul>';
						}



						if ($item.free==1){
						   if($item.fx_method){
							   for(var i=0;i<$item.fx_method.length;i++){
								   _html +="<ul><b>"+$item.fx_method[i].cloth+"</b><br/>";
//                                        console.log($item.fx_method[i])
								   if($item.fx_method[i].cx){
									   _html +=   '刺绣<br/>';
									   var cx=$item.fx_method[i].cx
									   if(cx['字体'])_html +=   '<li>1、'+cx['字体']+'</li>';
									   if(cx['大小'])_html +=   '<li>2、'+cx['大小']+'</li>';
									   if(cx['颜色'])_html +=   '<li>3、'+cx['颜色']+'</li>';
									   if(cx['内容'])_html +=   '<li>3、'+cx['内容']+'</li>';
								   }
								   if($item.fx_method[i].lt){
									   _html +=   '量体<br/>';
									   var re =$item.fx_method[i].lt
									   for(j in re){
										   _html +=   '<li>'+re[j]['part_name']+':'+re[j]['size']+'</li>';

									   }
								   }
								   if($item.fx_method[i]['special']){
									   _html +=   '特殊处理<br/>';
									   var spl=$item.fx_method[i].special
									   for(s in spl){
										   if(spl[s]) _html +=   '<li>'+spl[s]+'</li>';
									   }
								   }
								   _html +='</ul>';
							   }
						   }
						}
					}
					_html +=   '</div>';

				}
				if ($item.sign==2){


							_html = '<table width="0" border="0" cellpadding="0" cellspacing="0" class="fxtab">'+
							'<tr>'+
					'<th width="120">返修方式：</td>'+
							'<td>到店返修</td>'+
							'<th>受理门店：</td>'+
							'<td>'+$item.store_name+'</td>'+
							'</tr>'+
							'<tr>'+
							'<th>预约到店时间：</td>'+
							'<td>'+$item.to_time+'</td>'+
							'<th>受理人：</td>'+
							'<td>'+$item.liangti_name+'</td>'+
							'</tr>'+
							'<tr>'+
							'<th>联系方式：</td>'+
							'<td>'+$item.liangti_mob+'</td>'+
							'<th>返修原因：</td>'+
							'<td>'+$item.reason+'</td>'+
							'</tr>'+
							'<tr>'+
							'<th>门店地址：</td>'+
							'<td colspan="3">'+$item.address+'</td>'+

							'</tr>'+
							'</table>';
					if ($item.free==0){
							_html += '<div class="fxzongj">'+
							'<h4>费用共计￥'+$item.order_amount+'</h4>'+
							'<div class="fxzhif">麦富迪币支付: ￥'+$item.coin+'<span>余额支付: ￥'+$item.money_amount+'</span>'+'<span>支付宝支付: ￥'+$item.final_amount+'</span></div>'+
							'</div>';

					}





					_html += '<div class="fxzongj fxfabot">'+
							'<h4>返修方案</h4>';

					if($item.fx_str){
						  _html +=   '<ul>'+
								'<li>'+$item.fx_str+'</li>'+
								'</ul>';
					}else{
						if ($item.free==0){
							_html +=   '<ul>'+
									'<li>'+$item.gy_method+'</li>'+
									'</ul>';
						}

						if ($item.free==1){
							if($item.fx_method){
								for(var i=0;i<$item.fx_method.length;i++){
								_html +="<ul><b>"+$item.fx_method[i].cloth+"</b><br/>";
//                                        console.log($item.fx_method[i])
									if($item.fx_method[i].cx){
										_html +=   '刺绣<br/>';
										var cx=$item.fx_method[i].cx
										if(cx['字体'])_html +=   '<li>1、'+cx['字体']+'</li>';
										if(cx['大小'])_html +=   '<li>2、'+cx['大小']+'</li>';
										if(cx['颜色'])_html +=   '<li>3、'+cx['颜色']+'</li>';
										if(cx['内容'])_html +=   '<li>3、'+cx['内容']+'</li>';
									}
									if($item.fx_method[i].lt){
										_html +=   '量体<br/>';
										var re =$item.fx_method[i].lt
										for(j in re){
											_html +=   '<li>'+re[j]['part_name']+':'+re[j]['size']+'</li>';

										}
									}
									if($item.fx_method[i]['special']){
										_html +=   '特殊处理<br/>';
										var spl=$item.fx_method[i].special
										for(s in spl){
											if(spl[s]) _html +=   '<li>'+spl[s]+'</li>';
										}
									}
									_html +='</ul>';
								}
							}
						}
					}
					'</div>';
				}

				luck.open({
					width:'660px',
					height:'auto',
					title:'返修信息详情',
					addclass:'cotte-luck',
					content:_html
				});
			}else{
				alert(res.msg)
				return false;
			}
		});
	},
    fx:function(){
        var wl_sn = $("#wl_sn").val();
        var to_time = $("#to_time").val()
        var order_id = $("#order_id").val()
        var fx_reason = $("#fx_reason").val()
        $.post('/fx-add.html',{wl_sn:wl_sn,to_time:to_time,order_id:order_id,fx_reason:fx_reason},function(res){
            var res = eval("("+res+")");
            if(res.done){
                alert(res.retval.msg)
                window.location.href="/fxfee.html?id="+res.retval.id;
            }else{
                alert(res.retval.msg)
            }
        });
    },
	customer16:function(){
		//我的订单返修，线上查看弹窗
		$('.fxddtcxqs').click(function(){

			luck.open({
				width:'660px',
				height:'auto',
				title:'返修信息详情',
				addclass:'cotte-luck',
				content:$('#window008').html()
			});
		});
	},

	customer17:function(){
		//我的订单返修，线下查看弹窗
		$('.fxddtcxqx').click(function(){

			luck.open({
				width:'660px',
				height:'auto',
				title:'返修信息详情',
				addclass:'cotte-luck',
				content:$('#window009').html()
			});
		});
	},
}
