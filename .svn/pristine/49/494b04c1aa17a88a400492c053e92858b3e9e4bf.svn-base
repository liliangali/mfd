/* author yhao.bai 2014.6.10; */
$.fn.extend({
	order: function(options) {
		return this.each(function() {
			new $.order(options);
		});
	}
});

$.order = function(opt){
	opt.s1 = $("#step-1");
	opt.s2 = $("#step-2");
	opt.s3 = $("#step-3");
	opt.s4 = $("#step-4");
	opt.amount = $("#amount");
	opt.alayer = $("#alayer");
	opt.regions;
	opt.update = false;
	$("#submitOrder").click(function(){
		if($(this).parents("p").attr("class") == "p2"){
			var step = $(".ltsj_box").attr("id");
			location.href="#"+step
		}else{
			var lay = $(this).parents(".p3");
			lay.hide();
			lay.next().html('<p class="ddtjz">订单正在提交中...</p>').show();
			var postscript = $("#sortlist textarea[name=postscript]").val();
			$.post(opt.submitUrl, {"ajax":"1",'postscript':postscript}, function(res){
				var res = $.evalJSON(res);
				if(res.done == false){
					$(".submitNotice").html('<p class="ddtscw">'+res.msg+'</p>').show();
					//$(".submitNotice").html(res.msg);
					return false;
				}else{
					location.href=opt.paycenterUrl+"?"+res.retval.order_sn;
				}
			});
		}
	})
	
	$(".useCoupon h3").click(function(){
		var p = $(this).parents("div");
		if(p.hasClass("current") == false) return;
		$.get(opt.couponUrl, {"ajax":"1"},function(res){
			var res = $.evalJSON(res);
			$(".couponlay").html(res.retval);
			
			$(".usecoupon").click(function(){
				var c = [];
				$(".couponlay input[type=checkbox]:checked").each(function(){
					c.push($(this).val());
				})
				
				data = {};
				data.coupon = c.join(",");
				data.ajax = 1;
				$.post(opt.useCoupon,data, function(res){
					var res = $.evalJSON(res);
					if(res.done == false){
						artDialog.alert(res.msg);
					}else{
						opt.amount.html(res.retval.amount);
						opt.alayer.html(res.retval.alayer);
					}
				})
			})
		})
	})
	
	
		$(".useCoin h3").click(function(){
		var p = $(this).parents("div");
		if(p.hasClass("current") == false) return;
		$.get(opt.coinUrl, {"ajax":"1"},function(res){
			var res = $.evalJSON(res);
			$(".coinlay").html(res.retval);
			
			$(".usecoin").click(function(){
				var coin = $(".coinlay input[name=coin]").val();
				data = {};
				data.coin = coin;
				data.ajax = 1;
				$.post(opt.coinUrl,data, function(res){
					var res = $.evalJSON(res);
					if(res.done == false){
						artDialog.alert(res.msg);
					}else{
						opt.amount.html(res.retval.amount);
						opt.alayer.html(res.retval.alayer);
					}
				})
			})
		})
	})
	
	
	  $(".usePoint h3").click(function(){
		var p = $(this).parents("div");
		if(p.hasClass("current") == false) return;
		$.get(opt.pointUrl, {"ajax":"1"},function(res){
			var res = $.evalJSON(res);
			$(".pointlay").html(res.retval);
			
			$(".usepoint").click(function(){
				var point = $(".pointlay input[name=point]").val();
				data = {};
				data.point = point;
				data.ajax = 1;
				$.post(opt.pointUrl,data, function(res){
					var res = $.evalJSON(res);
					if(res.done == false){
						artDialog.alert(res.msg);
					}else{
						opt.amount.html(res.retval.amount);
						opt.alayer.html(res.retval.alayer);
					}
				})
			})
		})
	})
	
	$(".ordernotice a").click(function(){
		var step = $(".ltsj_box").attr("id");
		location.href="#"+step
	})
	
	if(opt.figure == false && opt.needDiy == 1){
		figureForm();
		return;
	}
	
	if(opt.address == false){
		addressForm();
		return;
	}

	if(opt.shipping == false || opt.payment == false){
		shipAndPayForm();
		return;
	}
	
	if(opt.shipping && opt.payment && opt.address && (opt.figure || opt.needDiy == 0)){
		closeLight();
	}

	function figureForm(){
		openLight('如需修改，请先保存量体数据', '量体数据');
		loading(opt.s1);
		$.get(opt.figureUrl, {"ajax":"1"},function(res){
			var res = $.evalJSON(res);
			
			if(res.done == false){
				 artDialog.alert(res.msg);
			}else{
				opt.s1.html(res.retval);
				regionInit("region", true);
				opt.s1.removeClass("m_shxxxg").addClass("ltsj_box");
				
				opt.s1.find(".lihonwei").each(function(){
					$(this).mouseover(function(){
						$(this).addClass('dfbg');
					}).mouseout(function(){
						var input = $(this).find("input");
						if(!input.attr('checked')){
							$(this).removeClass('dfbg');
						}
					})
//					
//					$(this).click(function(){
//						$(this).find("input").attr("checked" , true);
//					})
				})
				
				opt.s1.find("input[name=figure]").click(function(){
					$f = $(this).val();
					if($f > 0){
						$("#reservation").hide();
						$(".biaozm .lihonwei").removeClass('dfbg');
						//$(this).parents(".lihonwei").addClass("dfbg");
					}else{
						$("#reservation").show();
						if($f == -1){
							$("._figurenotice").show();
						}else{
							$("._figurenotice").hide();
						}
					}
				}).each(function(){
					$f = $(this);
					if($f.attr("checked")){
						if($f.val() > 0){
							$("#reservation").hide();
						}else{
							$("#reservation").show();
							var _self = $("#region select:last");
							opt.load = true;
							//loadService(_self);
							var ri = $("#region_id").val();
							var o = ri.split(",");
							opt.regions = o;
							$("#region select:first").find("option").each(function(){
								if($(this).val() == o[0]){
									$(this).attr("selected", "true");
								}
							})
							
							$("#region select:first").change();
							
						}
						if($f.val() == "-1"){
							$("._figurenotice").show();
						}else{
							$("._figurenotice").hide();
						}
					}
				})
				
				$('#retime').datepicker({dateFormat: 'yy-mm-dd',minDate:+1});
				$(".saveFigrue").bind("click", saveFigure);
			}
		})
	}
	
	function addressForm(){
		openLight('如需修改，请先保存收货地址','收货地址');
		loading(opt.s2);
		$.get(opt.addressUrl, {"ajax":"1"},function(res){
			var res = $.evalJSON(res);
			if(res.done == false){
				 artDialog.alert(res.msg);
			}else{
				opt.s2.html(res.retval);
				_actionA();
				var ri = $("#region_id").val();
				var o = ri.split(",");
				opt.regions = o;
				var show = $('.addressbox').css("display");
				if(ri && show == "block"){
					$("#region select:first").find("option").each(function(){
						if($(this).val() == o[0]){
							$(this).attr("selected", "true");
						}
					})
					
					$("#region select:first").change();
				}
			}
		})
	}
	
	function _actionA(){
		$(".addresslist li").each(function(){
			$(this).mouseover(function(){
				$(this).find(".handle").show();
				$(this).addClass("on");
			})

			$(this).mouseout(function(){
				var c = $(this).find("input").attr("checked");
				if(c != "checked"){
					$(this).find(".handle").hide();
					$(this).removeClass("on");
				}
			})
			
			$(this).find("input").click(function(){
					var pu = $(this).parents("ul");
					var pi = $(this).parents("li");
					opt.update = false;
					pu.find("li").removeClass("on");
					pu.find(".handle").hide();
					pi.find(".handle").show();
				    $(".addressbox").hide();
					if($(this).val() == "-1"){
						$(".addressbox").show();
						$(".addressbox input").val('');
						$(".addressbox select:first").find("option:first").attr("selected", 'true');
						$(".addressbox select:first").nextAll("select").empty().append('<option>请选择</option>');
					}
				})

			$(this).find('a:first').click(function(){
				var pu = $(this).parents("ul");
				var pi = $(this).parents("li");
				opt.update = true;
				pu.find(".handle").hide();
				pi.find(".handle").show();
				pu.find("li").removeClass("on");
				pi.find("input").attr('checked', 'true');
				
				var i = pi.find("input").val();
				
				var cn = $("#addr-consignee-"+i).val();
				var pm =$("#addr-phone_mob-"+i).val();
				var rn = $("#addr-region_name-"+i).val();
				var ri = $("#addr-region_id-"+i).val();
				var ad = $("#addr-address-"+i).val();
				var em = $("#addr-email-"+i).val();
				
				$("#consignee input[name=addr_id]").val(i);
				$("#consignee input[name=consignee]").val(cn);
				$("#consignee input[name=phone_mob]").val(pm);
				$("#consignee input[name=region_name]").val(rn);
				$("#consignee input[name=region_id]").val(ri);
				$("#consignee input[name=email]").val(em);
				$("#consignee input[name=address]").val(ad);
				
				var o = ri.split(",");
				opt.regions = o;
				$("#consignee select:first").find("option").each(function(){
					if($(this).val() == o[0]){
						$(this).attr("selected", "true");
					}
				})
				
				$("#consignee select:first").change();
				
				$(".addressbox").show();
			})
			
			$(this).find('a:last').click(function(){
				var pi = $(this).parents("li");
				var i = pi.find("input").val();
				opt.update = false;
				if(i > 0){
					$.post(opt.dropAddr, {"id":i,"ajax":"1"}, function(res){
						var res = $.evalJSON(res);
						if(res.done == false){
							 artDialog.alert(res.msg);
						}else{
							opt.s2.html(res.retval);
							var check = false;
							$(".addresslist input[type=radio]").each(function(){
								if($(this).attr("checked")){
									check = true;
								}
							})
							
							if(check == false){
								$(".addresslist input[type=radio]:first").attr("checked", 'true');
							}
							
							_actionA();
						}
					})
				}
			})
			
		}).find("label").click(function(){
			var pu = $(this).parents("ul");
			var pi = $(this).parents("li");
			pu.find(".handle").hide();
			pu.find("li").removeClass("on");
			opt.update = false;
			pi.find(".handle").show();
			
			pi.find("input").attr('checked', 'true');
			
			var v = pi.find("input").val();
		
			$(".addressbox").hide();
			
			if(v == "-1"){
				$(".addressbox").show();
				$(".addressbox input").val('');
				$(".addressbox select:first").find("option:first").attr("selected", 'true');
				$(".addressbox select:first").nextAll("select").empty().append('<option>请选择</option>');
			}
		})
		
		opt.s2.removeClass("m_shxxxg").addClass("ltsj_box");
		$(".saveAddress").bind("click", saveAddress);
		regionInit("region", false);
	}
	
	
	function shipAndPayForm(){
		openLight('如需修改，请先保存支付及配送方式','支付及配送');
		loading(opt.s3);
		$.get(opt.shipAndPayUrl, {"ajax":"1"},function(res){
			var res = $.evalJSON(res);
			if(res.done == false){
				 artDialog.alert(res.msg);
			}else{
				opt.s3.html(res.retval);
				opt.s3.removeClass("m_shxxxg").addClass("ltsj_box");
				$("#pay li").each(function(){
					$(this).mouseover(function(){
						$(this).addClass("on");
					}).mouseout(function(){
						
						var input = $(this).find("input");
						if(!input.attr("checked")){
							$(this).removeClass("on");
						}
					})
				}).click(function(){
					$(this).siblings().removeClass('on');
					$(this).addClass("on");
					$(this).find("input").attr("checked", "true");
				})
				
			  $("#ship li").each(function(){
					$(this).mouseover(function(){
						$(this).addClass("on");
					}).mouseout(function(){
						
						var input = $(this).find("input");
						if(!input.attr("checked") == true){
							$(this).removeClass("on");
						}
					})
				}).click(function(){
					$(this).siblings().removeClass('on');
					$(this).addClass("on");
					$(this).find("input").attr("checked", "true");
				})
				$(".saveShipAndPay").bind("click", saveShipAndPay);
			}
		})
	}
	
	function invoiceForm(){
		openLight('如需修改，请先保存发票信息','发票信息');
		loading(opt.s4);
		$.get(opt.invoiceUrl, {"ajax":"1"},function(res){
			var res = $.evalJSON(res);
			if(res.done == false){
				 artDialog.alert(res.msg);
			}else{
				opt.s4.html(res.retval);
				$("#needinvoice li").each(function(){
					
					$(this).mouseover(function(){
						//$(this).find(".handle").show();
						$(this).addClass("on");
					}).mouseout(function(){
						var c = $(this).find("input").attr("checked");
						if(c != "checked"){
							//$(this).find(".handle").hide();
							$(this).removeClass("on");
						}
					})
//					
					$(this).click(function(){
						var r = $(this).find("input[type=radio]");
						$(this).siblings().removeClass('on');
						$(this).addClass("on");
						r.attr("checked", "true");
						
						if(r.val() == 1){
							$(".flow-invoice").show();
						}else{
							$(".flow-invoice").hide();
						}
					})
					
				}).find("input[type=radio]").click(function(){
					$(this).parents("ul").children("li").removeClass('on');
					$(this).parents("li").addClass("on");
					if($(this).val() == 1){
						$(".flow-invoice").show();
					}else{
						$(".flow-invoice").hide();
					}
				})
				
				opt.s4.removeClass("m_shxxxg").addClass("ltsj_box");
				$(".saveInvoice").bind("click", saveInvoice);
			}
		})
	}
	
	function openLight(c,n){
		
		$(".lightTitle").html(c);
		$(".ordernotice a").html(n);
		$(".ordernotice").show();
		$(".submitNotice").hide();
		$("#submitOrder").parents("p").removeClass("p3");
		$("#submitOrder").parents("p").addClass("p2").show();
	}
	
	function closeLight(){
		$(".lightTitle").html('<a href="javascript:;">[修改]</a>');
		
		$(".lightTitle").each(function(){
			var s = $(this).parents(".m_shxxxg").attr("id");
			var a = $(this).find("a").unbind();
			switch(s){
				case "step-1":
					a.bind("click", figureForm);
					break;
				
				case "step-2":
					a.bind("click", addressForm);
					break;
				case "step-3":
					a.bind("click", shipAndPayForm)
					break;
				case "step-4":
					a.bind("click", invoiceForm);
					break;
			}
		})
		$("#submitOrder").parents("p").removeClass("p2");
		$("#submitOrder").parents("p").addClass("p3");
		$(".ordernotice").hide();
	}
	
	function saveFigure(){
		/*量体数据保存*/
		var data = {};
		$(".serviceError").hide();
		
		
		//$(".addressForm").validate
		$res = $("#_figureForm").validate({
			errorPlacement: function(error, element){
	            $(element).next('.field_notice').hide();
	            $(element).after(error);
		    },
		    success       : function(label){
		    	label.addClass('right').text('OK!');
		    },
		    rules : {
				realname : {
		            required : true
		        },
		        mobile:{
		            required : true,
		            maxlength: 11,
		            minlength:11,
		            digits:true
		        },
		        region_name:{
		        	required:true
		        },
		        address:{
		        	required : true,
		        	maxlength:150,
		        	minlength:5
		        },
		        retime:{
		        	required : true,
		        	dateISO:true
		        }
		    },
		    messages : {
		    	realname : {
		            required : '真实姓名不能为空'
		        },
		        mobile :{
		            required : '手机号码不能为空',
		            maxlength:"手机号码只能由11位数字组成",
		            minlength:"手机号码只能由11位数字组成",
		            digits   : '手机号码不合法'
		        },
		        region_name:{
		        	 required : '请选择服务地区'
		        },
		        address : {
		            required : '详细地址不能为空',
		            minlength:"详细地址只能在5-150个字符之间",
		            maxlength:"详细地址只能在5-150个字符之间"
		        },
		        retime : {
		            required : '预约时间不能为空',
		            url : '时间格式不合法'
		        }
		    }
		}).form();

		var figure  = opt.s1.find("input[name=figure]:checked").val();
		 
		var service = opt.s1.find("input[name=service]:checked").val();
		
		if(!$res && figure < 0){
			return false;
		}
		
		if(figure < 0 && !service){
			$(".serviceError").show();
			return false;
		}
		data.figure = figure;
		if(figure < 0){
			data.realname = $("#_figureForm input[name=realname]").val();
			data.mobile = $("#_figureForm input[name=mobile]").val();
			data.region_name = $("#_figureForm input[name=region_name]").val();
			data.region_id = $("#_figureForm input[name=region_id]").val();
			data.address = $("#_figureForm input[name=address]").val();
			data.retime = $("#_figureForm input[name=retime]").val();
			data.service = service;
		}
		data.ajax=1;
		saveing();
		$.post(opt.figureUrl, data, function(res){
			var res = $.evalJSON(res);
			if(res.done == false){
				//formStatus(res.msg);
				 closeSave();
				 artDialog.alert(res.msg);
			}else{
				opt.s1.html(res.retval.content);
				opt.amount.html(res.retval.amount);
				opt.alayer.html(res.retval.alayer);
				opt.s1.removeClass("ltsj_box").addClass("m_shxxxg");
				addressForm();
			}
		})
	}
	
	function saveAddress(){
		/*收货地址数据保存*/
		var data = {};
		var a = opt.s2.find("input[name=address]:checked").val();
		
		var res = $("#consignee").validate({
			errorPlacement: function(error, element){
	            $(element).next('.field_notice').hide();
	            $(element).after(error);
		    },
		    success       : function(label){
		    	label.addClass('right').text('OK!');
		    },
		    rules : {
				consignee : {
		            required : true
		        },
		        phone_mob:{
		            required : true,
		            maxlength: 11,
		            minlength:11,
		            digits:true
		        },
		        region_name:{
		        	required:true
		        },
		        address:{
		        	required : true,
		        	maxlength:150,
		        	minlength:5
		        },
		        email:{
		        	required : true,
		        	email:true
		        }
		    },
		    messages : {
		    	consignee : {
		            required : '收货人不能为空'
		        },
		        phone_mob :{
		            required : '手机号码不能为空',
		            maxlength:"手机号码只能由11位数字组成",
		            minlength:"手机号码只能由11位数字组成",
		            digits   : '手机号码不合法'
		        },
		        region_name:{
		        	 required : '请选择所在地区'
		        },
		        address : {
		            required : '详细地址不能为空',
		            minlength:"详细地址只能在5-150个字符之间",
		            maxlength:"详细地址只能在5-150个字符之间"
		        },
		        email : {
		            required : '邮箱地址不能为空',
		            email : '不是一个合法的邮箱地址'
		        }
		    }
		}).form();

		if((a < 0 || !a) && !res){
			return false;
		}

		if(a<0 || !a || opt.update){
			data.addr_id = $("#consignee input[name=addr_id]").val();
			data.consignee = $("#consignee input[name=consignee]").val();
			data.phone_mob = $("#consignee input[name=phone_mob]").val();
			data.region_name = $("#consignee input[name=region_name]").val();
			data.region_id = $("#consignee input[name=region_id]").val();
			data.email = $("#consignee input[name=email]").val();
			data.address = $("#consignee input[name=address]").val();
			data.update = 1;
		}else{
			data.addr_id = a;
			data.consignee = $("#addr-consignee-"+a).val();
			data.phone_mob = $("#addr-phone_mob-"+a).val();
			data.region_name = $("#addr-region_name-"+a).val();
			data.region_id = $("#addr-region_id-"+a).val();
			data.email = $("#addr-email-"+a).val();
			data.address = $("#addr-address-"+a).val();
			data.update = 0;
		}
		
		data.ajax=1;
		opt.update = false;
		saveing();
		$.post(opt.addressUrl, data ,function(res){
			var res = $.evalJSON(res);
			if(res.done == false){
				 closeSave();
				 artDialog.alert(res.msg);
			}else{
				opt.s2.html(res.retval.content);
				opt.amount.html(res.retval.amount);
				opt.alayer.html(res.retval.alayer);
				opt.s2.removeClass("ltsj_box").addClass("m_shxxxg");
				shipAndPayForm();
			}
		})
	}
	
	function saveShipAndPay(){
		/*配送&支付保存*/
		
		var pay = $("#pay input[name=pay]:checked").val();
		var ship = $("#ship input[name=ship]:checked").val();
		saveing();
		$.post(opt.shipAndPayUrl, {"pay":pay,"ship":ship,"ajax":"1"} ,function(res){
			var res = $.evalJSON(res);
			if(res.done == false){
				 closeSave();
				 artDialog.alert(res.msg);
			}else{
				opt.s3.html(res.retval.content);
				opt.amount.html(res.retval.amount);
				opt.alayer.html(res.retval.alayer);
				opt.s3.removeClass("ltsj_box").addClass("m_shxxxg");
				closeLight();
			}
		})
	}
	
	function saveInvoice(){
		/*发票数据保存*/
		var n = $('#needinvoice li').find("input[type=radio]:checked").val();
		var data = {};
		data.ajax = 1;
		data.need = n;
		
		if(n == 1){
			var res = $("#invoiceForm").validate({
				errorPlacement: function(error, element){
		            $(element).next('.field_notice').hide();
		            $(element).after(error);
			    },
			    success       : function(label){
			    	label.addClass('right').text('OK!');
			    },
			    rules : {
					invoicetitle : {
			            required : true,
			            maxlength:200
			        }
			    },
			    messages : {
			    	invoicetitle : {
			            required : '发票抬头不能为空',
			            maxlength: '发票抬头不能超过200字符'
			        }
			    }
			}).form();
			
			if(!res){
				return false;
			}
			
			data.title = $("#invoiceForm input[name=invoicetitle]").val();
			data.type  = $("#invoiceForm input[name=invoicetype]:checked").val();
			data.content  = $("#invoiceForm .fp_select").val();
		}
		saveing();
		$.post(opt.invoiceUrl, data,function(res){
			var res = $.evalJSON(res);
			if(res.done == false){
				 closeSave();
				 artDialog.alert(res.msg);
			}else{
				opt.s4.html(res.retval);
				opt.s4.removeClass("ltsj_box").addClass("m_shxxxg");
				//shipAndPayForm();
				closeLight();
			}
		})
	}
	
	function regionInit(divId, load)
	{
		opt.load = load;
	    $("#" + divId + " > select").change(regionChange); // select的onchange事件
	}
	
	function regionChange()
	{
	    // 删除后面的select
	    $(this).nextAll("select").empty().append('<option>请选择</option>');

	    // 计算当前选中到id和拼起来的name
	    var selects = $(this).siblings("select").andSelf();
	    var ids =new Array();
	    var names = new Array();
	    for (i = 0; i < selects.length; i++)
	    {
	        sel = selects[i];
	        if (sel.value > 0)
	        {
	            ids.push(sel.value);
	            name = sel.options[sel.selectedIndex].text;
	            names.push(name);
	        }
	    }

   	    $(".mls_id").val('');
	    $(".mls_names").val('');
	    // ajax请求下级地区
	    if (this.value > 0)
	    {
	        var _self = this;
	        //var url = REAL_SITE_URL + '/index.php?app=mlselection&type=region';
	        //var url = opt.regionUrl+"?type=region";
	        $.getJSON(opt.regionUrl, {'pid':this.value,'type':'region'}, function(data){
	            if (data.done)
	            {
	                if (data.retval.length > 0)
	                {
                    	ns = $(_self).next("select");
                    	if(ns.is("select")  == false){
                    		 $("<select><option>" + lang.select_pls + "</option></select>").insertAfter(_self);
                    	}
	                   
	                	$(_self).next("select").unbind().bind("change",regionChange)
	                	var data  = data.retval;
	                    for (i = 0; i < data.length; i++)
	                    {
	                    	var s = "";
	                    	try{
	                    		if(opt.regions){
	                    			for(r=0;r<opt.regions.length;r++){
	                    				if(data[i].region_id == opt.regions[r]){
	                    					s = " selected";
	                    				}
	                    			}
	                    		}
	                    	}catch(e){}
	                        $(_self).next("select").append("<option value='" + data[i].region_id + "'"+s+">" + data[i].region_name + "</option>");
	                    }
	                    if(opt.regions){
	                    	var select = $(_self).next("select");
	                    	if(select.val()>0){
	                    		$(select).change();
	                    	}
	                    	//
	                    }
	                }
	                else
	                {
	                	opt.regions = "";
	                	loadService(_self);
	            	    $(".mls_id").val(ids.join(","));
	            	    $(".mls_names").val(names.join("\t"));
	            	    $(_self).nextAll("select").remove()
	                }
	            }
	            else
	            {
	            	artDialog.alert(data.msg);
	            }
	        });
	    }
	}
	
	
	function loadService(_self){
    	if(opt.load){
    		$.get(opt.serviceUrl,{value:$(_self).val(),ajax:1},function(res){
    			 var res = $.evalJSON(res);
    			 var html='';
    			 if(res.done){
    				 var sid = $("#serID").val();
    				 
    				 for(var i=0;i<res.retval.length;i++){
    					   var checked = sid == res.retval[i].idserve ? " checked" : '';
    			           html += '<div class="m_sgp"><p class="p1"><input type="radio" '+checked+' value="'+res.retval[i].idserve+'" name="service"/></p>';
    			           html += '<p class="p2">'+res.retval[i].serve_name+'</p><p class="p3">'+res.retval[i].serve_address+'</p>';
    			           html += ' <p class="p4">'+res.retval[i].mobile+'</p></div>';
    				 }
    				 html += '<p class="clear"></p>';
    				 $("#servicelist").html(html);
						 $('.his_list').height(125).jScrollPane({
						showArrows: true,
						animateScroll: true
					})
    			 }
    		})
    	}
	}
	
	function loading(obj){
		obj.html('<p class="load_box">正在加载中，请稍后...</p>');
	}
	
	function saveing(){
		$(".but_pos").append('<p>正在努力保存中...</p>');
	}
	
	function closeSave(){
		$(".but_pos input").nextAll().remove();
	}
}