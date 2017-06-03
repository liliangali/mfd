//page脚本
var cotteFn={
	index:function(){
		//幻灯
		(function(){
			var len=$('#banner .swipe-wrap>div').length,slider = Swipe(document.getElementById('banner'), {
				auto: 4000,
				//continuous: false,
				callback: function(index, element) {
					if(len==2){
						switch(index){
							case 2:
							index=0;
							break;
							case 3:
							index=1;
							break; 	
						}
					}
					$('.swipe-btn span').eq(index).addClass('cur').siblings().removeClass();	
				}
			});
			$('.swipe-prev').click(slider.prev);
			$('.swipe-next').click(slider.next);
		})();
		//svg动画
		function hasSVG(){
			SVG_NS = 'http://www.w3.org/2000/svg';
			return !!document.createElementNS &&!!document.createElementNS(SVG_NS, 'svg').createSVGRect;
		} 
		if(hasSVG()){
			var svg1 = new Vivus('mysvg1', {type: 'scenario-sync', duration: 10, dashGap: 20, forceRender: false});
			$('#mysvg1').mouseover(function(){
				svg1.reset().play();	
			});
			var svg2 = new Vivus('mysvg2', {type: 'scenario-sync', duration: 10, dashGap: 20, forceRender: false});
			$('#mysvg2').mouseover(function(){
				svg2.reset().play();	
			});
			var svg3 = new Vivus('mysvg3', {type: 'scenario-sync', duration: 10, dashGap: 20, forceRender: false});
			$('#mysvg3').mouseover(function(){
				svg3.reset().play();	
			});
			var svg4 = new Vivus('mysvg4', {type: 'scenario-sync', duration: 10, dashGap: 20, forceRender: false});
			$('#mysvg4').mouseover(function(){
				svg4.reset().play();	
			});
		}
		//底部app动画
		var t=$('.appShow').offset().top-$(window).height()+200,tim=null;
		$(window).bind('load scroll',function(){
			if(tim){clearTimeout(tim)}
			tim=setTimeout(function(){
				if($(window).scrollTop()>=t){
					$('.appShow').addClass('active');
					$(window).unbind('scroll');	
				}	
			},120)
		});
		//意见反馈弹层
		$('#feedback').click(function(){
			$.getScript('http://r.cotte.cn/global/luck/pc/luck.js',function(){
				luck.open({
					title:'意见反馈',
					width:'660px',
					height:'475px',
					content:'<iframe width="660" height="475" style="display:block" src="view/sc-utf-8/mall/default/feedback/ajax_feedback.html" frameborder="0"></iframe>',
					addclass:'cotte-luck'
				});
			})
		});
		//悬浮导航
		$('.toolbar').on('click','li',function(){
			switch(this.className){
				case 'toolbar-top':
				$('html,body').animate({scrollTop:0},'fast');	
			}	
		})
	},
	chuangye:function(){
		var $container = $('#brick'),num=0,delay=null,flag=null;
		var story = {};
		story.url = "cystory-loadData.html";


		//获取数据
		function getData(){
			var winh=$(window).height(),
				bodyh=$container.height();
			if(($(window).scrollTop()+winh)>=bodyh-1150){
				$(window).off('scroll resize',getData);
				$('.zzjz_box .jzz').text('加载中...').show();
				delay=setTimeout(function(){
				if(!story.url){
					return false;
				}
					num+=50;
					$.get(story.url,function(res){
						var res = eval("("+res+")");
						var $newElems = $(res.retval.content);
						story.url  = res.retval.link;
						if(flag)
						{
							$container.append($newElems).masonry("appended",$newElems);
						}else
						{
							$container.append(res.retval.content);
					        $container.imagesLoaded( function(){
					        $container.masonry({
					        columnWidth:0,
					        itemSelector: '.item'
					            });
					          });
					        flag=true;
					      
							if(num==100){
								$('.zzjz_box .jzz').text('加载完毕').show();
								return	
							}

						}

					});
					$(window).on('scroll resize',getData);
				},1000)
			}	
		}
		$(window).on('scroll resize',getData);
	},
	detail:function(){
		//二、左右切换：最后一个显示在最右侧;
		$(".productshow:eq(0)").Xslider({
			unitdisplayed:5,
			numtoMove:1,
			autoscroll:3000
		});
		//幻灯
		$('.popup-gallery').magnificPopup({
			delegate: 'a',
			type: 'image',
			tLoading: 'Loading image #%curr%...',
			mainClass: 'mfp-img-mobile',
			gallery: {
				enabled: true,
				navigateByImgClick: true,
				preload: [0,1] // Will preload 0 - before current, and 1 after the current image
			},
			image: {
				tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
				titleSrc: function(item) {
					return item.el.attr('title');
				}
			}
		});
		//数量
		var numObj=$('.jajia_box input')
		$('.jajia_box .a_1').click(function(){
			numObj.val(Number(numObj.val())+1)
			return false;	
		});
		
		$('.jajia_box .a_2').click(function(){
			if(Number(numObj.val())>1){
				numObj.val(Number(numObj.val())-1)
			}
			return false;	
		});
		//选项卡悬浮
		var tim=null,offset=$('#JdeTailTab').offset(),_top=offset.top+30;
		$(window).on('load scroll resize',function(){
			//if(tim)clearTimeout(tim);
			//tim=setTimeout(function(){
				if($(window).scrollTop()>=_top){
					$('#JdeTailTab').addClass('fixed');	
				}else{
					$('#JdeTailTab').removeClass('fixed');	
				}
			//},200)
		});
		$('#JdeTailTab .gmhgk').onePageNav({
			currentClass: 'now_hover'
		});
		//加入购物车弹层
		;(function(){
			var node=[
				[$('#Jstep1'),'选择量体方式'],
				[$('#Jstep2'),'量体定制 '],
				[$('#JbaseSize'),'标准码定制 '],
				[$('#Jfujin'),'去附近门店量体 '],
				[$('#Jappoint1'),'指定量体师 '],
				[$('#Jappoint2'),'指定量体师 '],
				[$('#JexistingData'),'现有量体数据 '],
				[$('#Jrecord'),'选择预约记录 '],
			],
			cartStep=[],//记录当前步骤
			JcartTit=$('#JcartTit'),//标题
			JcartPop=$('#JcartPop'),
			JbottomBar=$('#JbottomBar'),
			JaddCar=$('#JbottomBar .addCart'),
			Jerror=$('.errorInfo');
			//错误提示
			function error(e){
				Jerror.text(e).addClass('show');
				setTimeout(function(){
					Jerror.removeClass('show');	
				},1500)	
			}
			//步骤、标题
			function step(obj){
				cartStep.push(obj);
				JcartTit.text(obj[1]);
				$('#Jback').css('visibility','visible');
			}
			function addCart(){
				var productId = $("input[name=product_id]").val();
				var quantity = Number(numObj.val());
				$.post("cart-add.html",{pid:productId,gid:goodsId,num:quantity},function(res){
					var res = eval("("+res+")");
					if(res.done == false){
						alert(res.msg);
						return false;
					}else{
						location.href="cart.html";
					}
				});
			}
			//加入购物车
			$('#JaddCar').click(function(){
		    	var state = getCookie('hasLogin');
		    	if(state != 1){
		    		loginIn.show(function(res){
		    			if(res.status != "-1"){
		    				addCart();
		    			}
		 			})
		        }else{
		        	addCart();
		        }
				//console.log(cartStep)
			});
			$('#JdeTailTab .addCart').click(function(){
				$('html,body').animate({scrollTop:0});
				$('#JaddCar').click();
			});
			//关闭
			$('#Jclose').click(function(){
				JcartPop.removeClass('show');
				$('#cartShadow').remove();
				$.each(cartStep,function(i,e){
					if(i>0){
						e[0].hide();
					}
				});
				node[0][0].show();
				JcartTit.text(node[0][1]);
				cartStep=[node[0]];
				JbottomBar.hide();
				//console.log(cartStep)
			});
			//返回
			$('#Jback').click(function(){
				if(cartStep.length>1){
					$(cartStep.pop()[0]).hide();
					$(cartStep[cartStep.length-1][0]).show();
					JcartTit.text(cartStep[cartStep.length-1][1]);
				}
				if(cartStep.length==1){
					$('#Jback').css('visibility','hidden');
					JbottomBar.hide();	
				}
				if(cartStep.join().indexOf('量体定制')>=0){
					JbottomBar.hide();
				}
			});
			//标准码		
			$('#Jbase-btn').click(function(){
				$('#Jstep1').hide();
				node[2][0].show();
				step(node[2]);
				JbottomBar.show();
				JaddCar.unbind('click').click(function(){
					var flag=true;
					var size = '';
					node[2][0].children('.tabLayer').children('div').each(function(i,e){
						var ul = $(e).find('.cur');
						if(ul.length<=0){
							error('请选择'+node[2][0].children('.tabTit').find('li').eq(i).text()+'尺码')
							flag=false
							return false;	
						}else{
							size += !size ? ul.parent("ul").data("customid")+"|"+ul.data("size") : ","+ul.parent("ul").data("customid")+"|"+ul.data("size");
						}
					});
					if(flag){
						var data = {};
						data.quantity = Number(numObj.val());
						data.goods_id = id;
						data.size = size;
						data.fabric_code = $("#fabric_code").val();
						data.button_code = $("#button_code").val();;
						$.post("/cart-add.html",data,function(res){
							var res = eval("("+res+")");
							if(res.done == false){
								alert(res.msg);
								return false;
							}else{
								location.href="cart.html";
							}
						});
					}
				})
			});
			//标准码->选项卡
			node[2][0].children('.tabTit').on('click','li',function(){
				$(this).addClass('cur').siblings('li').removeClass('cur');
				$("#_size").attr("href","size.htm#"+$(this).data("type"));
				node[2][0].children('.tabLayer').children('div').eq($(this).index()).siblings('div').hide().end().show();	
			});
			//标准码->选项
			node[2][0].children('.tabLayer').on('click','li',function(){
				$(this).addClass('cur').siblings().removeClass('cur');
			});
			//标准码->尺码助手
			$('#sizeHelp-btn').toggle(function(){
				$(this).text('关闭尺码助手');
				$(this).parent().next('.tabLayer').children('div:visible').children('.sizeReferTable').show();
			},function(){
				$(this).text('展开尺码助手');
				$(this).parent().next('.tabLayer').children('div:visible').children('.sizeReferTable').hide();
			})
			//量体定制
			$('#Jamount-btn').click(function(){
				$('#Jstep1').hide();
				node[1][0].show();
				step(node[1]);
			});
			//现有量体数据
			$('#Jdata-btn').click(function(){
				node[1][0].hide();
				node[6][0].show();
				step(node[6]);
				JbottomBar.show();
				JaddCar.unbind('click').click(function(){
					var f = node[6][0].children('.scrollBox').find('.cur');
					if(f.length<=0){
						error('请选择量体数据')	
					}else{
						var data = {};
						data.figureid   = f.data("id");
						data.goods_id = id;
						data.figuretype = 5;
						data.type="suit";
						data.quantity = Number(numObj.val());
						$.post("/cart-add.html",data, function(res){
							var res = eval("("+res+")");
							if(res.done == false){
								alert(res.msg);
								return false;
							}else{
								location.href="cart.html";
							}
						})
					}
				})
			});
			
			//现有量体数据->选项
			node[6][0].children('.scrollBox').on('click','li',function(){
				$(this).addClass('cur').siblings().removeClass('cur');
			})
			//去附近门店量体
			$('#Jfujin-btn').click(function(){
				node[1][0].hide();
				node[3][0].show();
				step(node[3]);
				JbottomBar.show();
				JaddCar.unbind('click').click(function(){
					var b=node[3][0].validate({
						error:function(e,t){
							error(e.prev('.name').text().replace('：','')+t)	
						},
						acticle:true
					});
					if(b){
						var f = node[3][0].children('.mendian').find('.cur');
						if(f.length<=0){
							error('请选择量体数据')
						}else{
							var region    = node[3][0].find("select[name=region]").val();
							var realname  = node[3][0].find("input[name=realname]").val();
							var phone     = node[3][0].find("input[name=phone]").val();
							var date      = node[3][0].find("input[name=dateline]").val();
							var timepart  = node[3][0].find("select[name=timepart]").val();
							var dateline  = node[3][0].find("input[name=dateline]").val();
							var data = {};
							data.serveid   = f.data("id");
							data.type="suit";
							data.goods_id = id;
							data.figuretype = 2;
							data.quantity = Number(numObj.val());
							data.region = region;
							data.realname = realname;
							data.phone = phone;
							data.timepart = timepart;
							data.dateline = dateline;
							$.post("/cart-add.html",data, function(res){
								var res = eval("("+res+")");
								if(res.done == false){
									alert(res.msg);
									return false;
								}else{
									location.href="cart.html";
								}
							})
						}
					}
				})
			});
			//去附近门店量体->选项
			node[3][0].children('.mendian').on('click','li',function(){
				$(this).addClass('cur').siblings().removeClass('cur');
			});
			//指定量体师->筛选
			$('#Jappoint-btn').click(function(){
				node[1][0].hide();
				node[4][0].show();
				step(node[4]);
				JbottomBar.show();
			});
			//指定量体师->下一步
			node[4][0].find('.next').click(function(){
				var b=node[4][0].validate({
					error:function(e,t){
						error(e.parents('.txtBox').find('.name').text().replace('：','')+t)	
					},
					acticle:true
				});
				if(b){
					node[4][0].hide();
					node[5][0].show();
					step(node[5]);
					data = {};
					data.region   = node[4][0].find("select[name=region]").val();
					data.address  = node[4][0].find("input[name=address]").val();
					data.realname = node[4][0].find("input[name=realname]").val();
					data.phone    = node[4][0].find("input[name=phone]").val();
					data.dateline = node[4][0].find("input[name=dateline]").val();
					data.timepart = node[4][0].find("select[name=timepart]").val();
					data.type="suit";
					$.post("/goods-loadFigurer.html", {data:data}, function(res){
						var res  = eval("("+res+")");
						var tit  = "已选择："+res.retval.title
						var content = res.retval.content;
			        	var html='';
			      		 for(var i=0;i<content.length;i++){
			      			if(content[i].is_free)
			       			{
			       				 var _str = "<i>收费 ¥100</i>";
			       			}
			       			else
			       			{
			       				var _str = "<span>免费</span>";
			       			}
			       			html += '<li data-id='+content[i].user_id+'>'+_str+'<p class="p1">姓名：'+content[i].real_name+'</p><p class="p2">电话：'+content[i].phone_mob+'</p><p class="p3">地区：'+content[i].address+'</p></li>';
			      	     }
			      		 
			      		$("#assignlist").html(html);
			      		 
			      		$(".assign_title").html(tit);
						
			      		//指定量体师选择
						node[5][0].children('.scrollBox').off('click').on('click','li',function(){
							$(this).addClass('cur').siblings().removeClass('cur');
						});
					})
					
					JaddCar.unbind('click').click(function(){
						var f = node[5][0].find('.scrollBox').find('.cur');
						if(f.length<=0){
							error('请选择指定量体师')	
						}else{
							data.figurerid = f.data("id");
							data.goods_id = id;
							data.figuretype = 6;
							data.quantity = Number(numObj.val());
							$.post("/cart-add.html",data, function(res){
								var res = eval("("+res+")");
								if(res.done == false){
									alert(res.msg);
									return false;
								}else{
									location.href="cart.html";
								}
							})
						}
						
					})
				}
			});
			
			//操作记录
			$('#Jrecord-btn').click(function(){
				node[1][0].hide();
				node[7][0].show();
				step(node[7]);
				JbottomBar.show();
				JaddCar.unbind('click').click(function(){
					var f = node[7][0].children('.scrollBox').find('.cur');
					if(f.length<=0){
						error('请选择量体数据')	
					}else{
						data = {};
						data.figuretype = f.data("type");
						data.type="suit";
						data.history = 1;
						data.goods_id = id;
						data.quantity = Number(numObj.val());
						
						if(data.figuretype == 5){
							data.figureid = f.find("input[name=figureid]").val();
						}else{
							data.phone = f.find("input[name=phone]").val();
							data.realname = f.find("input[name=realname]").val();
							data.region = f.find("input[name=region]").val();
							data.timepart = f.find("input[name=timepart]").val();
							data.dateline = f.find("input[name=dateline]").val();
						}
						
						if(data.figuretype == 2){
							data.serveid = f.find("input[name=serveid]").val();
						}
						
						if(data.figuretype == 6){
							data.address = f.find("input[name=address]").val();
							data.figurerid = f.find("input[name=figurerid]").val();
						}
						
						$.post("/cart-add.html",data, function(res){
							var res = eval("("+res+")");
							if(res.done == false){
								alert(res.msg);
								return false;
							}else{
								location.href="cart.html";
							}
						})
					}
				})
			});
			//操作记录->选项
			node[7][0].children('.scrollBox').on('click','li',function(){
				$(this).addClass('cur').siblings().removeClass('cur');
			});
		})()
	}
}