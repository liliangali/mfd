
var cotteFn={
	orderInfo:function(opt){
		//初始化信息
		var _user = opt.userID ? opt.userID : 0;
		var _ssName = _user+'_order';
		var _orderS = {'gift':0,'pay':'','dedu':{'coin':0,'money':0},'debit':opt.useDebit,'amount':opt.amount,'also':opt.amount,'payPwd':''};
		
		//##============ 初始化 Bgn
		//sessionStorage.clear()
		if(sessionStorage.getItem(_ssName)){
			var _orderS = JSON.parse(sessionStorage.getItem(_ssName));
		}else{
			sessionStorage.setItem(_ssName, JSON.stringify(_orderS))
		}
		
		var ss = JSON.parse(sessionStorage.getItem(_ssName));
	   
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
	   
	   if(opt.amount <= 0){
		   _orderS['dedu']['money'] = 0;
		   _orderS['dedu']['coin'] = 0;
	   }
	   
	   //== 初始化还需支付
	   _orderS['also']   = opt.amount - _orderS['dedu']['money'] - _orderS['dedu']['coin'];
	   
	   console.log(_orderS)
	   _Load();
		function _Load(){
			setOrderSs();
			_loadDedu();
		}
		
		function setOrderSs(){
			sessionStorage.setItem(_ssName, JSON.stringify(_orderS))
			ss = JSON.parse(sessionStorage.getItem(_ssName));
		}
		
		//初始化优惠信息
		function _loadDedu(){
			
			var _dedu = ss['dedu'];
			for( var p in  _dedu){
				if(_dedu[p]>0){
					$('.use_'+p).find('.switch').addClass('cur');
			        
			        $('.use_'+p).find('dd').show();
			        $('.use_'+p).find('dd').find('input').val(_dedu[p]);
			        $('.use_'+p).find('dd').find('.orange').text(parseFloat(_dedu[p]).toFixed(0)+'元')
			        
			        $('.'+p+'_fee').show();
		        	$('.'+p+'_fee').find('.jesl').text('-￥'+parseFloat(_dedu[p]).toFixed(0));
			        
			        //if(p == 'coin')
			        //	$('.debitA').attr('href',"javascript:void(0)")
				}else{
					
					$('.use_'+p).find('.switch').removeClass('cur');
			    	
			    	$('.use_'+p).find('dd').find('input').val('');
			        $('.use_'+p).find('dd').find('.orange').text('0.00元')
			        $('.use_'+p).find('dd').hide();
			        
			        $('.'+p+'_fee').hide();
			        
			        //if(p == 'coin')
			        //	$('.debitA').attr('href',opt.debitUrl)
				}
			}
			
			$('#final_amount').data('amount',ss['also']);
			$('#final_amount').html('￥'+parseFloat(ss['also']).toFixed(0));
//			if(_dedu['money']>0 || _dedu['coin']>0){
//				$('.paypassword').show();
//			}else{
//				$('.paypassword').hide();
//			}
		}
		
		
		bindAction();
		//礼品
//		if(sessionStorage.getItem('is_gift')){
//		    $('.lipin .switch').addClass('cur');
//		}
		//支付方式
		$('.payment_li').each(function(){
			if($(this).hasClass('on')){
				$('#payment').val($(this).data('id'));
				return false;
			}
		})
		//============ 初始化 End
		
		function bindAction(){
		    
			$('.coupon dl').find('input').unbind().bind('keyup change input propertychange',_change_use)    //keyCode == 8   //keypress
			
			$('#check_one').unbind().bind('click',checkEmp);
			$('.payment_li').unbind().bind('click',checkPayment);
			$('.jiesua a').unbind().bind('click',_submitOrder);
			//$('.lipin .switch').unbind().bind('click',checkGift);
		}
		
		//礼品标记
//		function checkGift(){
//			_switch.call(this,function(obj){
//				if(obj){
//					sessionStorage.setItem('is_gift','yes');
//				}else{
//					sessionStorage.removeItem('is_gift');
//				}
//			})
//		}
		
		//切换支付方式
		function checkPayment(){
			$('.payment_li').removeClass('on');
			$(this).addClass('on');
			$('#payment').val($(this).data('id'))
		}
		
		//验证工号
		function checkEmp(){
			var _val = $(this).parent('p').find('#one_num').val();
		    if(!_val) return;
		    $.post(opt.checkEmpUrl, {num:_val},function(res){
		        if(res.done == false){
		            _alert(res.msg);
		        }else{
		        	$('#has_one').val('1');
		            _alert('工号可用');
		        }
		  },'json')
		}
		
		
		//公用切换
		function _switch(fn){
			var $this=$(this),flag=false;
			if($this.hasClass('cur')){
				$this.removeClass('cur');
				flag=false;	
			}else{
				$this.addClass('cur');
				flag=true;		
			}
			if(typeof fn =='function'){
				fn(flag)	
			}
			return flag	
		}
		
		//弹窗
		function _alert(msg){
		    layer.open({
		        content: '<p style="text-align:center">'+msg+'</p>',
		        shadeClose: false,
		        btn: ['确定'],
		    }); 
		}
		//提交订单
		function _submitOrder(){
			var _thisBtn = $(this);
			_thisBtn.unbind();
			
			var _data = JSON.parse(sessionStorage.getItem(_ssName));
			
			if(_data['dedu']['coin']>0 || _data['dedu']['money']>0){
				var msgPay = layer.open({
	        		content:'<div class="payPwd"><p class="tit">请输入支付密码</p><input type="password" class="zfmm">',
	        		btn:['取消','确定'],
	        		shadeClose: false,
	        		yes:function(){
	        			layer.close(msgPay);
	        			_thisBtn.bind('click',_submitOrder)
	        		},
	        		no:function(){
	        			var pwd = $(".payPwd input[type=password]").val();
	        			if(pwd.length < 8){
	        				_alert('支付密码错误！');
	        				_thisBtn.bind('click',_submitOrder)
	        			    return false;
	            	    }
	            	    _data['payPwd'] = pwd;
	            	    _createOrder(_data)
	            	    _thisBtn.bind('click',_submitOrder)
	        		}
	        	})
			}else{
				_createOrder(_data)
				_thisBtn.bind('click',_submitOrder)
			}
			
		}
		
		function _createOrder(_data){
			console.log(_data)
			$('#OrderForm').ajaxSubmit({
	            type:"post",
	            data : _data,
	            url:opt.createUrl+"?time="+ (new Date()).getTime(),
	            success:function (res,statusText) {
	                var res = $.parseJSON(res); 
	                if(res.done == false){
	                	_alert(res.msg)
	                }else{
	                	sessionStorage.removeItem(_ssName)
	                    location.href=opt.payUrl+"?id="+res.retval.order_sn;
	                }
	            }
	        });
		}

		//麦富迪币
		$('.coupon .switch').click(function(){
		    var _self=this;
		    if($(_self).data('dis') == 'yes') return;
		    var _prt  = $(_self).parents('dl');
		    var _type = _prt.data('type');
		    _switch.call(_self,function(obj){
		        if(obj){
		        	
		            _prt.find('dd').slideDown('fast');
		            $('.'+_type+'_fee').show();
		            
		            //if(_type == 'coin'){
		            //    $('.debitA').attr('href',"javascript:void(0)")
		            //}
		            
		            if(opt.hasPayPwd == 0){
		            	var msga=layer.open({
		    				content:'为了您的账户资金安全，余额支付时需要启用支付密码。',	
		    				btn:['取消','确定'],
		    				yes:function(){
		    					layer.close(msga);
		    				},
		    				no:function(){
		    					layer.close(msga);
		    					try{
		    						app.toSetPayPassword()
		    				    }catch(e){}
		    					location.href="http://www.gosetpassword.com";
		    				}
		    			});
		        		return;
		        	}
		            
		            if(_prt.data('value') > 0 && _orderS['also'] > 0){
		            	if(_prt.data('value') >= _orderS['also']){
			            	_orderS['dedu'][_type] = _orderS['also'];
			            }else{
			            	_orderS['dedu'][_type] = _prt.data('value');
			            }
		            	_orderS['also'] = _orderS['also'] - _orderS['dedu'][_type];
		            	_Load();
		            }
		            
		        }else{
		            _prt.find('dd').slideUp('fast');
		            $('.'+_type+'_fee').hide();
		            _orderS['also'] = parseFloat(_orderS['also']) + parseFloat(_orderS['dedu'][_type])
		            _orderS['dedu'][_type] = 0;
		            _Load();
		            //if(_type == 'coin'){
		            //    $('.debitA').attr('href',opt.debitUrl)
		            //}
		        }
		    })
		});

		function _change_use(){
		    var _this = $(this);
		    var _val = _this.val() ? _this.val()  : 0;
		    
		    var _dVal = parseFloat(_this.parents('dl').data('value'));
		    var _type = _this.parents('dl').data('type');
		    var _amount =  parseFloat(opt.amount);
		    var _also   =  parseFloat(_orderS['also']) + parseFloat(_orderS['dedu'][_type]);
		    
		    var _type = _this.parents('dl').data('type')  
		    
		    if(parseFloat(_val) == parseFloat(_orderS['dedu'][_type])) return
		    
		    if(isNaN(_this.val()) || !/^\d+$/.test(_val)){ //!/^\d+(\.\d+)?$/.test(_val)   !/^[1-9]+(\d+)?$/
				this.value=''
				return false
			}
		    
		    if(_val > _dVal) _val = _dVal
			if(_val > _amount) _val = _amount
			if(_val > _also) _val = _also
			
			_this.val(_val)
				
			_orderS['dedu'][_type] = _val
			_orderS['also'] = _also - _val
			_Load();
		}
		
	}
}