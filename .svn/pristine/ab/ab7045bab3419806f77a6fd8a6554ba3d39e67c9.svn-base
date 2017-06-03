
var cotteFn={
	orderFabric:function(opt){ 

/*======================*
		量体信息
 *======================*/
 
//		$('#selectSize dd span').click(function(){
//			var index=$(this).index()-1;
//			if(index<0){
//				$('#setSize>div').hide();
//				return
//			}
//			$('#setSize>div').eq(index).siblings().hide().end().show();
//			$('#figureType').val($(this).data('n'))
//		});
//		
//		//门店量体
//		$('#selectCity').on('change',function(){
//			if($(this).val()){
//				$.post(opt.serverUrl,{id:$(this).val()},function(res){
//					if(res.done == true){
//						$('#mendian').html(res.retval.content)
//					}
//				},"json")
//			}
//		})
//		//门店选择
//		$('#mendian').on('click','dl',function(){
//			$(this).addClass('cur').siblings().removeClass();
//			$('#figureServerId').val($(this).data('sid'))
//		})
//
//		
//		//现有量体数据
//		$('#getHistory').click(function(){
//			var _input = $(this).parent('.search').find('input:text')
//			var _val  = _input.val(),_data = _input.data('value');
//			if(_data == _val) return;
//			var _box = $(this).parents('.existing').find('dl dd')
//			$.post(opt.historyUrl,{val:_val}, function(res){
//		        if(res.done == true){
//		            _box.html(res.retval.content);
//		            _input.data('value',_val);
//		            $('#setSize .existing li').click(function(){
//		    			$(this).addClass('cur').siblings().removeClass();	
//		    			$('#HistoryId').val($(this).data('id'))
//		    		});
//		        }
//		    },"json")
//		})
		

/*======================*
		配送信息
 *======================*/

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
			//选择
			$('#address>ul>li').not('#addAddress').click(function(){
				$(this).addClass('cur').siblings().removeClass('cur');
			});
			//删除
			$('#address .caozuoBtn .del').click(function(){
				if(window.confirm('确定要删除？')){
					var _this = $(this)
					var _id = _this.parents('li').data('id');
					$.post(opt.addressDel,{id:_id},function(res){
						if(res.done == true){
							_this.parents('li').fadeOut('fast',function(){
								_this.parents('li').remove();
								$('#address').find("li:not([class='syxdz'])").eq(0).addClass('cur').siblings().removeClass('cur');
							})
						}else{
							alert(res.msg)
						}
					},"json");
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
					}).attr('id','popLayer').html('<iframe src="cart-address.html?'+obj+'&id='+id+'" width="335" height="445" frameborder="0"></iframe>')).append(popShadow);
					popLayer.fadeIn('fast');
					popShadow.fadeIn('fast');
			}
			
			//添加
			$('#addAddress').click(function(){
				address.call(this,'add',0)
			})
			//修改
			$('#address .caozuoBtn .edit').click(function(){
				var _id = $(this).parents('li').data('id');
				address.call(this,'edit',_id)	
			});
			
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
				$(this).addClass('cur').siblings().removeClass('cur')
			});
			
/*======================*
		抵用券
 *======================*/

			//键入 麦富迪币 余额
			$('.useCoinMoney').keyup(function(){  //#JyueTxt #JcotteMoneyTxt
				
				var _this = $(this),_val = _this.val() ? _this.val()  : 0;
				
				var _dVal = parseFloat(_this.parents('.accordion').data('value'));
				
				var _type = _this.parents('.accordion ').data('type');
				
				var _amount = parseFloat($('.priceNum').data('amount'));
				var _also   =  parseFloat(_orderS['also']) + parseFloat(_orderS['dedu'][_type]);
				
				if(isNaN(_this.val()) || !/^\d+$/.test(_val)){ //!/^\d+(\.\d+)?$/.test(_val)   !/^[1-9]+(\d+)?$/
					this.value=''
					return false
				}

				if(_val > _dVal) _val = _dVal
				if(_val > _amount) _val = _amount
				if(_val > _also) _val = _also
				
				_this.val(_val)
				
				/*if(_type == 'coin'){
					$(this).parent('div').find('p').find('b.orange').eq(0).text(_val);
					$(this).parent('div').find('p').find('b.orange').eq(1).text(_val);
				}*/
				
				_orderS['dedu'][_type] = _val
				_orderS['also'] = _also - _val
				setOrderSs()
				_loadDedu();

			});
			

			//使用 麦富迪币 余额	
			$('.JcotteMoneyBtn').click(function(){  //JYueBtn  JcotteMoneyBtn
				var _this = $(this);
				var _val = _this.prev('input').val()
				var _dVal = parseFloat(_this.parents('.accordion').data('value'));
				var _amount = parseFloat($('.priceNum').data('amount'));
				var _type = $(this).parents('.accordion ').data('type');
				var _also = parseFloat(_orderS['also']) + parseFloat(_orderS['dedu'][_type]);
				
				if(isNaN(_val) || !/^\d+$/.test(_val)) return
				
				if(_val < 0 || _val > _dVal || _val > _amount || _val > _also) return
				
				_orderS['dedu'][_type] = _val
				_orderS['also'] = _also - _val
				setOrderSs()
				_loadDedu();
			})
			
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
		$('.radioBox li').click(function(){
			var $this=$(this);
			var _id = $this.parent('.radioBox').data('id');
			var _sn = $this.data('sn');
			var _mn = parseFloat($this.data('money'));
			
			var _amount = parseFloat(_orderS['dedu']['debit']['amount']);
			var _oldUse = parseFloat(_orderS['dedu']['debit']['use'][_id][1]);
			var _also   = parseFloat(_orderS['also']);
			
			if($this.hasClass('cur')){
				$this.removeClass('cur');
				_orderS['dedu']['debit']['amount'] = _amount - _oldUse;
				_orderS['also']   = _also + _oldUse;
				_orderS['dedu']['debit']['use'][_id] = ['',0]
			}else{
				if(parseFloat(_orderS['also']) <=0 && !$this.siblings('li').hasClass('cur')) return;
				
				$this.addClass('cur').siblings('li').removeClass('cur');
				
				_orderS['dedu']['debit']['amount'] = _amount - _oldUse + _mn;
				_orderS['also']  =  (_also + _oldUse - _mn ) > 0 ?  (_also + _oldUse - _mn ) : 0;
				_orderS['dedu']['debit']['use'][_id] = [_sn,_mn]
			}

			setOrderSs()
			_loadDedu();

		});
		$('#quanInfo').click(function(){
			luck.open({
				title:'抵用券使用规则',
				content:'<iframe width="100%" height="300" src="抵用券使用规则.html" frameborder="0"></iframe>',
				width:'500px',
				height:'340px',
				//addclass:'cotte-luck'	
			})	

		})
		
		
/*======================*
		立即下单
 *======================*/

		$('#orderDown').on('click',function(){
			$(this).parents('form').ajaxSubmit({
	            type:"post",
	            url : opt.createUrl,
	            data : JSON.parse(sessionStorage.getItem(_user+'_order')),
	            success:function(res){
	                var res = $.parseJSON(res);
	                if(res.done == true){
	                	location.href=opt.paycenterUrl+"?id="+res.retval.order_sn;
	                	//luck.close()
	                }else{
	                	alert(res.msg)
	                }
	             }
	        });
		})
 

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
