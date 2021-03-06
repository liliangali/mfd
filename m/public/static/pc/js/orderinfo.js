
var cotteFn={
	orderInfo:function(opt){ 
		
		//初始化信息
		var _user = opt.userID ? opt.userID : 0;
		
		//var _orderS = {'figure':{},'ship':{},'tax':{},'pay':{},'dedu':{'debit':{'amount':0,'use':{'0003':['',0],'0005':['',0]}},'coin':0,'money':0},'amount':_amount,'also':_amount};
		var _orderS = {'figure':{},'ship':{},'tax':{},'pay':{},'dedu':{'debit':{'sn':'','mn':0},'coin':0,'money':0,kuka:{'amount':0,'use':{}}},'amount':opt.amount,'also':opt.amount,'_also':opt.amount};
		
		//sessionStorage.clear()
		if(sessionStorage.getItem(_user+'_order')){
			var _orderS = JSON.parse(sessionStorage.getItem(_user+'_order'));
		}else{
			sessionStorage.setItem(_user+'_order', JSON.stringify(_orderS))
		}
		
		var ss = JSON.parse(sessionStorage.getItem(_user+'_order'));
		
		//初始化订单总额
		$('.priceList').find('li').eq(1).find('span').eq(1).text(_fmtPrice(opt.amount));
		
		//初始化 优惠券
		function initDebit(){
			var _debit = _orderS['dedu']['debit'];
			_orderS['dedu']['debit'] = {'sn':'','mn':0};
			$('.kuquanBox').find('.radioBox li').each(function(i){
				if($(this).data('sn') == _debit['sn']){
					_orderS['dedu']['debit'] = {'sn':$(this).data('sn'),'mn':$(this).data('money')};
					return false;
				}
			})
		}
		initDebit();
	   
	   //=== 初始化 酷卡
	   
		var _kukas = new Array();
	   $('.kukaBox').find('li').each(function(i){
		   _kukas[$(this).data('sn')] = $(this).data('sn');
		})
		var _kuka = 0;
	   for( var k in  _orderS['dedu']['kuka']['use']){
		  if(!_kukas[k] || parseFloat(_orderS['dedu']['kuka']['use'][k]) <= 0){
			  _orderS['dedu']['kuka']['use'][k] = 0;
		  }else{
			  _kuka = _kuka + parseFloat(_orderS['dedu']['kuka']['use'][k]);
		  }
		}
	   if(_kuka != parseFloat(_orderS['dedu']['kuka']['amount'])){
		   _orderS['dedu']['kuka']['amount'] = _kuka;
	   }
	   
	   //=== 初始化余额 麦富迪币
	   if(opt.hasPayPwd == '0'){
		   _orderS['dedu']['money'] = 0;
		   _orderS['dedu']['coin'] = 0;
	   }else{
		   if(parseFloat(_orderS['dedu']['money'])>opt.hasMoney){
			   _orderS['dedu']['money'] = opt.hasMoney;
		   }
		   if(parseFloat(_orderS['dedu']['coin'])>opt.hasCoin){
			   _orderS['dedu']['coin'] = opt.hasCoin;
		   }
	   }
	   if(opt.canCoin == '0'){
		   _orderS['dedu']['coin'] = 0;
	   }
	   
		_Load();
		function _Load(){
			setOrderSs();
			_loadDedu();
			console.log(_orderS)
		}
		function setOrderSs(){
			
			_orderS['also'] = _orderS['_also'] = opt.amount - _orderS['dedu']['money'] - _orderS['dedu']['coin'] - _orderS['dedu']['debit']['mn'] - _orderS['dedu']['kuka']['amount'];
			   
			if(_orderS['also'] <= 0) _orderS['also'] = 0;
			if(_orderS['also'] >= opt.amount) _orderS['also'] = opt.amount;
			
			sessionStorage.setItem(_user+'_order', JSON.stringify(_orderS))
			ss = JSON.parse(sessionStorage.getItem(_user+'_order'));
		}
		
		//初始化优惠信息
		function _loadDedu(){
			
			var _dedu = ss['dedu'];
			var _ttLi = $('.accounts ').find('.priceList').find('li');
			
			if(parseFloat(_dedu['kuka']['amount']) > 0){
				$('.kukaBox').find('.tit').addClass('show');
				$('.kukaBox').find('.layer').show()
				for ( var p in  _orderS['dedu']['kuka']['use']){
					if(!_kukas[p] || parseFloat(_orderS['dedu']['kuka']['use'][p]) <= 0){
						  _orderS['dedu']['kuka']['use'][p] = 0;
					}else{
						$('.kuka_'+p).addClass('cur')
					}
				}
				
				_ttLi.eq(2).show();
				_ttLi.eq(2).find("span.fr").text('-'+_fmtPrice(_dedu['kuka']['amount']));
				
			}else{
				_ttLi.eq(2).hide();
				_ttLi.eq(2).find("span.fr").text('-'+_fmtPrice(0));
			}
			
			
			if(parseFloat(_dedu['debit']['mn']) > 0){
				$('.kuquanBox').find('.tit').addClass('show');
				$('.kuquanBox').find('.layer').show()
				$('.debit_'+_dedu['debit']['sn']).addClass('cur')
				
				_ttLi.eq(3).show();
				_ttLi.eq(3).find("span.fr").text('-'+_fmtPrice(_dedu['debit']['mn']));
				
			}else{
				_ttLi.eq(3).hide();
				_ttLi.eq(3).find("span.fr").text('-'+_fmtPrice(0));
			}
			
			if(_dedu['coin']>0){
				$('.kubiBox').find('.tit').addClass('show');
				$('.kubiBox').find('.layer').show()
				$('.kubiBox').find('input:text').val(_dedu['coin'])
				
				$('.kubiBox').find('p').find('b.orange').eq(0).text(_dedu['coin']);	
				$('.kubiBox').find('p').find('b.orange').eq(1).text(_dedu['coin']);	
				
				_ttLi.eq(4).show();
				_ttLi.eq(4).find("span.fr").text('-'+_fmtPrice(_dedu['coin']));
			}else{
				_ttLi.eq(4).hide();
				_ttLi.eq(4).find("span.fr").text('-'+_fmtPrice(0));
			}
			
			if(_dedu['money']>0){
				$('.moneyBox').find('.tit').addClass('show');
				$('.moneyBox').find('.layer').show()
				$('.moneyBox').find('input:text').val(_dedu['money'])
				_ttLi.eq(5).show();
				
				_ttLi.eq(5).find("span.fr").text('-'+_fmtPrice(_dedu['money']));
			}else{
				_ttLi.eq(5).hide();
				_ttLi.eq(5).find("span.fr").text('-'+_fmtPrice(0));
			}
			
			//$('.priceNum').html(_fmtPrice(ss['also'],1)+'<em>元</em>');
			$('.priceNum').html(_fmtPrice(ss['also'],1));
			if(_dedu['money']>0 || _dedu['coin']>0){
				$('.paypassword').show();
			}else{
				$('.paypassword').hide();
			}
		}

		
		function _fmtPrice(num,fmt){
			fmt=fmt?fmt:1;
			if(fmt == 1)
				return parseFloat(num).toFixed(0)+'元';
			else 
				return '￥'+parseFloat(num).toFixed(2);//return parseFloat(num).toFixed(0)
		}
/*======================*
		配送信息
 *======================*/
		
		//初始化 收货地址
		$("#addressLayer li:not(.syxdz)").first().addClass('cur').siblings().removeClass('cur');
		$('#shipAddress').val($("#addressLayer li:not(.syxdz)").first().data('id'))
		$("#addressLayer li:not(.syxdz)").each(function(i){
			if($(this).data('id') == opt.userAddr){
				$(this).addClass('cur').siblings().removeClass('cur');
				$('#shipAddress').val($(this).data('id'))
				return false;
			}
		})
		//配送方式切换
		$('#addressTab li').click(function(){
			$(this).addClass('cur').siblings().removeClass('cur');
			var index = $(this).index();
			$('#addressLayer > div').eq(index).show().siblings().hide();
			if(index>0){
				$('#fixTime').hide();
			}else{
				$('#fixTime').show();	
			}
			$('#shipType').val($(this).data('id'));
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
				$.post('cart-address.html',{id:id,obj:obj},function(res){
			        if(res.done == true){
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
								}).attr('id','popLayer').html('<div style="width:335px;height:445px">'+res.retval.content+'</div>')).append(popShadow);
								popLayer.fadeIn('fast');
								popShadow.fadeIn('fast');
								
								$('#addressForm').find('select').on('change', regionChange)
								
								$('#J_cancel').click(closeLayer)
								$('#J_save').on('click',saveAddress);
								
							    var ri = $("#area_list").val();
							    var regions = ri.split(",");
							    $("#addressForm select:first").find("option").each(function(){
							        if($(this).val() == regions[0]) $(this).attr("selected", "true");
							    })
							    $("#addressForm select:first").change();
			        }else{
			        }
			    },"json");
			}
			bindAddress();
			bindShipsIds();
			//****************************  地址 ↓
			
			//表单样式
			$(document).on('blur','.input-text',function(){
			    if(this.value==''){
			        $(this).parents('.form-section').removeClass('form-section-active').removeClass('form-section-focus');  
			    }else{
			        $(this).parents('.form-section').removeClass('form-section-focus')  
			    }
			})
			$(document).on('focus','.input-text',function(){
			    $(this).parents('.form-section').addClass('form-section-active').addClass('form-section-focus');
			})

			//关闭层
			function closeLayer(){
			    $('#popShadow',window.parent.document).click()
			}
			
			function saveAddress(){
			    var b = false;
			    b=$('.address-edit-box').validate({
			            acticle:true,
			            error:function(obj,error){
			                obj.next('.msg-error').remove()
			                $(obj).parent().append('<p class="msg msg-error">'+error+'</p>')
			                obj.focus();
			                obj.one('blur',function(){
			                    $(this).next('.msg-error').remove();
			                })
			            },
			        });
			    if(b){
			        $('#addressForm').ajaxSubmit({
			            type:"post",
			            url : opt.addressSaveUrl,
			            success:function(res){
			                var res = $.parseJSON(res);
			                if(res.done == true){
		                    	$('#address ul').html(res.retval.content)
								bindAddress();
			                    closeLayer();
			                }
			            }
			        });
			    }
			}
			
			function bindAddress(){
				$('#addAddress').unbind().bind('click',function(){
					address.call(this,'add',0)
				})
				$('#address .caozuoBtn .edit').unbind().bind('click',function(){
					var _id = $(this).parents('li').data('id');
					address.call(this,'edit',_id)	
				});
				
				$('#address>ul>li').not('#addAddress').unbind().bind('click',function(){
					$(this).addClass('cur').siblings().removeClass('cur');
					$('#address').find('#shipAddress').val($(this).data('id'))
					
					//选中地区 触发事件 post请求 遍历默认选中物流公司
				});
				//删除
				$('#address .caozuoBtn .del').unbind().bind('click',function(){
					if(window.confirm('确定要删除？')){
						var _this = $(this)
						var _id = _this.parents('li').data('id');
						$.post(opt.addressDel,{id:_id},function(res){
							if(res.done == true){
								_this.parents('li').fadeOut('fast',function(){
									_this.parents('li').remove();
									$('#address').find("li:not([class='syxdz'])").eq(0).addClass('cur').siblings().removeClass('cur');
									$('#address').find("input[name='order[ship][address]']").val($('#address').find("li:not([class='syxdz'])").eq(0).data('id'))
								})
								bindAddress()
							}else{
								alert(res.msg)
							}
						},"json");
					}
				});
			}

			function bindShipsIds(){
				$('#shipsId>ul>li').not('#shipsId').unbind().bind('click',function(){
					$(this).addClass('cur').siblings().removeClass('cur');
					$('#defShipsId').val($(this).data('id'));
					var oa = $('.priceList').find('li').eq(3).find('span').eq(1).text().replace("元",'');
						oa = oa.replace("-",'');
					if(opt.freeShipping == 0){
						if(!opt.weight){
							var sprice = '9999.99';
						}else if(parseFloat($(this).data('fw')) >= parseFloat(opt.weight)){
							var sprice = $(this).data('fw');
						}else{
							var sprice = (parseFloat(opt.weight) - parseFloat($(this).data('fw')))/$(this).data('sw')*$(this).data('sy')+parseFloat($(this).data('fy'));
						}
						$('.priceList').find('li').eq(6).find('span').eq(1).text(_fmtPrice(sprice));
						$('.priceList').find('li').eq(7).find('span').eq(1).text(_fmtPrice(parseFloat(opt.amount)-parseFloat(oa)+sprice));
					}

				});
	
			}
			
			function regionChange() {
				
			    var ri = $("#area_list").val();
			    var regions = ri.split(",");
			    //var pin = $(this).parents('.xm-select').index();
			    var pin = $(this).parents('.xm-select').data('index');
			    
			    var _nxp = $('.address-edit-box').find('.xm-select').eq(pin + 1);

			    //$(this).nextAll("select").remove();
			    _nxp.find('select').remove();
			    
			    //var selects = $(this).siblings("select").andSelf();
			    var selects = $(this).parents('#addressForm').find("select");

			    var id = 0;
			    var names = new Array();
			    var ids = new Array();
			    for (i = 0; i < selects.length; i++) {
			        sel = selects[i];
			        if (sel.value > 0) {
			            ids.push(sel.value);
			            id = sel.value;
			            name = sel.options[sel.selectedIndex].text;
			            names.push(name);
			        }
			    }
			    
			    if(this.value > 0) {
			        
			        var _self = this;
			        $.getJSON(opt.regionUrl, {
			            'pid': this.value,
			            'type': 'region'
			        }, function(data) {
			            if (data.done) {
			                if (data.retval.length > 0) {
			                    ns = _nxp.find('.dropdown').find("select");
			                    if (ns.is("select") == false) {
			                        _nxp.find('.dropdown').append("<select><option>请选择</option></select>");
			                        //$("<select><option>请选择</option></select>").insertAfter(_self);
			                    }

			                    _nxp.find("select").unbind().bind("change", regionChange)
			                    var data = data.retval;
			                    for (i = 0; i < data.length; i++) {
			                        var s = "";
			                        try {
			                            if (regions) {
			                                for (r = 0; r < regions.length; r++) {
			                                    if (data[i].region_id == regions[r]) {
			                                        s = " selected";
			                                    }
			                                }
			                            }
			                        } catch (e) {}
			                        _nxp.find("select").append("<option value='" + data[i].region_id + "'" + s + ">" + data[i].region_name + "</option>");
			                    }
			                    if (regions) {
			                        var select = _nxp.find("select");
			                        if (select.val() > 0) $(select).change();
			                    }
			                } else {
			                    regions = "";
			                    $("#area_list").val(ids.join(","));
			                    $("#area_id").val(ids.join(","));
			                    $("#area_name").val(names.join("\t"));
			                    _nxp.find('select').remove();
			                }
			            }
			        });
			    }
			}
			
			
			
			//****************************  地址 ↑
			
			$('#J_province').on('change',function(){
				if($(this).val()){
					$('#shipMendian').html('<div class="loading">加载中...</div>')
					$.post(opt.serverUrl,{id:$(this).val()},function(res){
						if(res.done == true){
							$('#shipMendian').html(res.retval.content)
						}
					},"json")
				}
			})
			
			//门店自提选择
			$('#qhnear').on('click','dl',function(){
				$(this).addClass('cur').siblings('dl').removeClass('cur');
				$('#shipServer').val($(this).data('sid'))
			})
/*======================*
		送货时间
 *======================*/
 			$('#fixTime li').click(function(){
				$(this).addClass('cur').siblings().removeClass('cur');
				$('#deliveryTime').val($(this).data('id'))
			})
 
/*======================*
		支付方式
 *======================*/
			$('#payMethod li').click(function(){
				$(this).addClass('cur').siblings().removeClass('cur');
				$('#paymentType').val($(this).data('id'))
			});
			
/*======================*
		抵用券
 *======================*/

			//键入 麦富迪币 余额
			$('.useCoinMoney').keyup(function(){  //#JyueTxt #JcotteMoneyTxt
				
				var _this = $(this),_val = _this.val() ? parseFloat(_this.val()) : 0;
				
				var _dVal = parseFloat(_this.parents('.accordion').data('value'));
				
				var _type = _this.parents('.accordion ').data('type');
				
				var _also   =  parseFloat(_orderS['also']) + parseFloat(_orderS['dedu'][_type]);
				
				if(isNaN(_this.val()) || !/^\d+$/.test(_val)){ //!/^\d+(\.\d+)?$/.test(_val)   !/^[1-9]+(\d+)?$/
					this.value=''
					return false
				}

				if(_val > _dVal) _val = _dVal
				if(_val > opt.amount) _val = opt.amount
				if(_val > _also) _val = _also
				if(_also > opt.amount) val = 0;
				if(_orderS['_also'] < 0) _val = 0
				
				_this.val(_val)
				
				_orderS['dedu'][_type] = _val
				setOrderSs()
				_loadDedu();

			});

/*======================*
		发票信息
 *======================*/
		$('#fapiao .tabBtn li').click(function(){
			var n=$(this).index();
			$(this).addClass('cur').siblings().removeClass('cur');
			switch(n){
				case 0:
				$('#fapiao .tabLayer>div').hide();
				break;
				case 1:
				$('#fapiao .tabLayer>div').eq(0).siblings().hide().end().show();
				break;
				case 2:
				$('#fapiao .tabLayer>div').eq(2).siblings().hide().end().show();
				break;
				case 3:
				$('#fapiao .tabLayer>div').eq(1).siblings().hide().end().show();
			}

			$('#invoiceType').val($(this).data('id'))
			return false	

		});
		
/*======================*
		各种优惠券
 *======================*/
		$('.accordion .tit').click(function(){
			var $this=$(this),$layer=$this.next('.layer');
			if($layer.is(':visible')){
				$layer.slideUp('fast');
				$this.removeClass('show')
			}else{
				$layer.slideDown('fast');
				$this.addClass('show');	
			}
		});
		//单选
		$('.kuquanBox li').click(function(){
			var $this=$(this);
			var _sn = $this.data('sn');
			var _mn = parseFloat($this.data('money'));
			
			if($this.hasClass('cur')){
				$this.removeClass('cur');
				_orderS['dedu']['debit']['sn'] = ''
				_orderS['dedu']['debit']['mn'] = 0
			}else{
				
				if(parseFloat(_orderS['also']) <=0 ) return;
				
				$('.radioBox li').removeClass('cur');
				
				_orderS['dedu']['debit']['sn'] = _sn
				_orderS['dedu']['debit']['mn'] = _mn
				
			}

			setOrderSs()
			_loadDedu();

		});
		
		$('.kukaBox').find('li').click(function(){
			
			var _this=$(this);
			
			var _sn = _this.data('sn');
			var _mn = parseFloat(_this.data('money'));
			
			var _amount = parseFloat(_orderS['dedu']['kuka']['amount']);
			var _oldUse = parseFloat(_orderS['dedu']['kuka']['use'][_sn]) ? parseFloat(_orderS['dedu']['kuka']['use'][_sn]) : 0;
			
			if(_this.hasClass('cur')){
				_this.removeClass('cur');
				_orderS['dedu']['kuka']['amount'] = _amount - _oldUse;
				_orderS['dedu']['kuka']['use'][_sn] = 0;
			}else{
				if(parseFloat(_orderS['also']) <=0 ) return;
				_orderS['dedu']['kuka']['amount'] = _amount - _oldUse + _mn;
				_orderS['dedu']['kuka']['use'][_sn] = _mn;
				
			}

			setOrderSs()
			_loadDedu();
			
		})
		
		
		
		$('#quanInfo').click(function(){
			luck.open({
				title:'优惠券使用规则',
				content:'<iframe width="100%" height="345" src="'+opt.debitRuleUrl+'" frameborder="0"></iframe>',
				addclass:'cotte-luck',
				width:'600px',
				height:'400px'
			})	
		})
		
		
/*======================*
		立即下单
 *======================*/

		$('#orderDown').unbind().bind('click',createOrder)
		function createOrder(){
			$('#orderDown').unbind();
			$(this).parents('form').ajaxSubmit({
	            type:"post",
	            url : opt.createUrl,
	            data : JSON.parse(sessionStorage.getItem(_user+'_order')),
	            success:function(res){
	                var res = $.parseJSON(res);
	                if(res.done == true){
	                	sessionStorage.clear()
	                	location.href=opt.paycenterUrl+"?id="+res.retval.order_sn;
	                	$('#orderDown').unbind().bind('click',createOrder)
	                	//luck.close()
	                }else{
	                	luck.alert('错误提示',res.msg,1);
	                	$('#orderDown').unbind().bind('click',createOrder)
	                }
	             }
	        });
		}

//		$('#orderDown').click(function(){
//			function error(obj,t){
//				$('html,body').animate({scrollTop:obj.offset().top});
//				luck.alert('错误提示',t,1);
//			}
//			//验证配送信息
//			var address,fapiao;
//			if($('#addressTab .cur').index()==0){
//				if($('#address .cur').length<=0){
//					error($('#address'),'请选择一个寄送地址')	;
//					return
//				}else{
//					address=$('#addressTab .cur').attr('data-id')	
//				}
//			}else{
//				if($('#qhnear dl.cur').length<=0){
//					error($('#qhnear'),'请选择一个门店');
//					return
//				}else{
//					address=$('##qhnear dl.cur').attr('data-id');	
//				}
//			}
//			//验证发票信息
//			switch($('#fapiao .tabBtn .cur').index()){
//				case 1:
//					var val=$('#fapiao .tabLayer div:eq(0)').find('input[name=taitou]').val();
//					if(val==''){
//						error($('#fapiao'),'发票抬头不能为空');	
//					}else{
//						fapiao='taitou='+val;	
//					}
//				break;
//				case 2:
//					var flag=true;
//					$('#fapiao .tabLayer .fapiaoInfo li').each(function(i, e) {
//						var obj=$(e).children('input');
//						if(obj.val()==''){
//							error(obj,obj.attr('tip')+'不能为空');
//							flag=false;
//							return false;	
//						}
//					});
//					if(flag){
//						fapiao=$('.fapiaoInfo form').serialize();
//					}else{
//						return	
//					}
//				break;
//				case 3:
//					var val=$('#fapiao .tabLayer div:eq(1)').find('input[name=taitou]').val();
//					if(val==''){
//						error($('#fapiao'),'单位名称不能为空');	
//						return
//					}else{
//						fapiao='taitou='+val;
//					}
//			}
//			//支付密码
//			var payPwd=$('#payPwd');
//			if(payPwd.length>0&&payPwd.val()==''){
//				error(payPwd,'支付密码不能为空');		
//			}
////			//提交订单
////			$.ajax({
////				url:'',
////				data:{
////					address:address,
////					fapiao:fapiao//其他信息开发在这里补全	
////				},success: function(){}
////			})
//		});
	}	
}
