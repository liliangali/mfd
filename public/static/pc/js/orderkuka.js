
var cotteFn={
	kukaInfo:function(opt){

	   $main = $(".mt");
	    _action();
	 	
		//初始化信息
		var _user = opt.userID ? opt.userID : 0;
		var _num  = opt.num    ? opt.num    : 1;
		
	
		var _orderS = {'pay':{},'dedu':{'debit':{'amount':0,'use':{}},'coin':0,'money':0},'amount':opt.amount,'also':opt.amount};
		
		//sessionStorage.clear()
		if(sessionStorage.getItem(_user+'_order')){
			var _orderS = JSON.parse(sessionStorage.getItem(_user+'_order'));
		}else{
			sessionStorage.setItem(_user+'_order', JSON.stringify(_orderS))
		}
		
		var ss = JSON.parse(sessionStorage.getItem(_user+'_order'));
		
	
	   //=== 初始化余额  麦富迪币
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
	   
	   //== 初始化还需支付
	   
	   _orderS['also']   = opt.amount - _orderS['dedu']['money'] - _orderS['dedu']['coin'];
		_Load();
		function _Load(){
			setOrderSs();
			_loadDedu();
			
		}
		function setOrderSs(){
			sessionStorage.setItem(_user+'_order', JSON.stringify(_orderS))
			ss = JSON.parse(sessionStorage.getItem(_user+'_order'));

		}
		
		//初始化优惠信息
		function _loadDedu(){

			var _dedu = ss['dedu'];
			var _ttLi = $('.accounts ').find('.priceList').find('li');
			if(_dedu['coin']>0){
				$('.accordion').eq(0).find('.tit').addClass('show');
				$('.accordion').eq(0).find('.layer').show()
				$('.accordion').eq(0).find('input:text').val(_dedu['coin'])
				
				$('.accordion').eq(0).find('p').find('b.orange').eq(0).text(_dedu['coin']);	
				$('.accordion').eq(0).find('p').find('b.orange').eq(1).text(_dedu['coin']);	
				
				_ttLi.eq(2).show();
				_ttLi.eq(2).find("span.fr").text('-'+_dedu['coin']+'元');
			}else{
				_ttLi.eq(2).hide();
				_ttLi.eq(2).find("span.fr").text('-0元');
			}

			if(_dedu['money']>0){
				$('.accordion').eq(1).find('.tit').addClass('show');
				$('.accordion').eq(1).find('.layer').show()
				$('.accordion').eq(1).find('input:text').val(_dedu['money'])
				_ttLi.eq(3).show();
				_ttLi.eq(3).find("span.fr").text('-'+_dedu['money']+'元');
			}else{
				_ttLi.eq(3).hide();
				_ttLi.eq(3).find("span.fr").text('-0元');
			}
			
			$('.priceNum').html(ss['also']+'<em>元</em>');
			if(_dedu['money']>0 || _dedu['coin']>0){
				$('.paypassword').show();
			}else{
				$('.paypassword').hide();
			}
		}



 
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
 			//   _orderS['also']   = opt.amount - _orderS['dedu']['money'] - _orderS['dedu']['debit']['amount'];
 			// 	var _orderS = {'pay':{},'dedu':{'debit':{'amount':0,'use':{}},'coin':0,'money':0},'amount':opt.amount,'also':opt.amount};
			//键入 麦富迪币 余额
			$('.surplus').keyup(function(){  //#JyueTxt #JcotteMoneyTxt
				
				var _this = $(this),_val = _this.val() ? _this.val() : 0; // 录入的 要使用的 余额

				var _dVal = parseFloat(_this.parents('.accordion').data('value'));// 自己帐号的 总共的钱 money
			
				var _type = _this.parents('.accordion ').data('type');    // 类型 money
			
				//var _amount = parseFloat($('.priceNum').data('amount'));
			
				 var k_sub    = $('#k_sub').val();
				 if(k_sub){
				 	var _amount = k_sub;
				 }else{
				 	var _amount = parseFloat($('.priceNum').data('amount'));   // 总额 
				 }
			  
				var _also   =  parseFloat(_orderS['also']) + parseFloat(_orderS['dedu'][_type]);
				
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

				setOrderSs()
				_loadDedu();

			});

						//键入 麦富迪币 余额
			$('.useCoinMoney').keyup(function(){  //#JyueTxt #JcotteMoneyTxt
				
				var _this = $(this),_val = _this.val() ? _this.val() : 0;
				
				var _dVal = parseFloat(_this.parents('.accordion').data('value'));
				
				var _type = _this.parents('.accordion ').data('type');
				
			   	var k_sub    = $('#k_sub').val();
				if(k_sub){
				 	var _amount = k_sub;
				}else{
				 	var _amount = parseFloat($('.priceNum').data('amount'));   // 总额 
				}

				var _also   =  parseFloat(_orderS['also']) + parseFloat(_orderS['dedu'][_type]);
				
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
				//if(parseFloat(_orderS['also']) <=0 && !$this.siblings('li').hasClass('cur')) return;
				if(parseFloat(_orderS['also']) <=0 ) return;
				
				//$this.addClass('cur').siblings('li').removeClass('cur');
				//$this.addClass('cur').siblings('.debit_'+_sn).removeClass('cur');
				$('.radioBox li').removeClass('cur');
				
				_orderS['dedu']['debit']['amount'] = _amount - _oldUse + _mn;
				_orderS['also']  =  (_also + _oldUse - _mn ) > 0 ?  (_also + _oldUse - _mn ) : 0;
				_orderS['dedu']['debit']['use'][_id] = [_sn,_mn];
				
				for ( var ps in  _orderS['dedu']['debit']['use']){
				  if(_orderS['dedu']['debit']['use'][ps][0] == _sn && ps != _id){
					  _orderS['dedu']['debit']['amount'] = parseFloat(_orderS['dedu']['debit']['amount']) - parseFloat(_orderS['dedu']['debit']['use'][ps][1]);
				      _orderS['also']  =  parseFloat(_orderS['also']) +  parseFloat(_orderS['dedu']['debit']['use'][ps][1]);
					  _orderS['dedu']['debit']['use'][ps] = ['',0];
				  }
				}
				
			}

			setOrderSs()
			_loadDedu();

		});
		$('#quanInfo').click(function(){
			luck.open({
				title:'抵用券使用规则',
				content:'<iframe width="100%" height="300" src="'+opt.debitRuleUrl+'" frameborder="0"></iframe>',
				width:'500px',
				height:'340px',
				//addclass:'cotte-luck'	
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


		 function _action(){
        
        $main.find(".jajia_box").find('a').unbind().bind("click", change);
        $main.find(".jajia_box").find('input').unbind().bind("keyup", _input);
        
    	}	
    	function change(){
    	
    	if($(this).hasClass('a_1')){
    		
    		$('.surplus').attr("value","0");
    		$('.useCoinMoney').attr("value","0");
    		var _ttLi = $('.accounts ').find('.priceList').find('li');
    		_ttLi.eq(2).find("span.fr").text('-0元');
    		_ttLi.eq(3).find("span.fr").text('-0元');
    	}


    	var _input=$(this).parent().children('input'),num=Number(_input.val());
    	
        if(!num || num < 1)num=1
        
        var val = $(this).hasClass('a_2') ? 1 : -1;
        
        var quantity = num + val;
        
        if(quantity < 1){

            _input.val(1);
            _input.data("num",1);
            return false;
        }
        
        _input.val(quantity);
        _input.data("num",quantity);
        update(quantity, this);
    }

    function _input(){
        
        var current = Number($(this).val());

        $(this).val(current);
        
        var _def    = $(this).data("num");

        if(current < 1 || !current){
            $(this).val(_def);
            return false;
        }

        if(current == _def){
            return false;
        }
        
        update(current, this);
    }
    
    function update(n,o){
    
        var _o    = $(o).parents('.qingdan');
        var _id   = _o.data("id");
        var _tp   = _o.data("type");
        $.post(opt.updateUrl,{id:_id,type:_tp,num:n},function(res){
                  if(res.done == true){
                  	_orderS['amount'] = res.retval.subtotal;
    				setOrderSs();
                	  _o.find('.jine').html(res.retval.subtotal)
                	  _o.find('#k_num').val(n)
                	   _o.find('#k_sub').val(res.retval.subtotal)
                	  upAll(res.retval.num,res.retval.amount)
                  }
        },"json");
    }

    function upAll(num,amount){
    	var obj=$('.accounts').find("span.fr");
    	var _m = $('.surplus').val();
    	var _c = $('.useCoinMoney').val();
        obj.eq(0).text(num+'件');
        obj.eq(1).text(amount+'元');
        
        if(_m && _c){
        	obj.eq(4).text((amount-_c -_m)+'元');
        }else if(_m){
        	obj.eq(4).text((amount-_m)+'元');
        }else if(_c){
        	obj.eq(4).text((amount-_c)+'元');
        }else{
        	obj.eq(4).text((amount)+'元');
        }

 	    _num  = num;
    	opt.amount = amount;
    	_orderS['also']   = opt.amount - _m - _c;
    	
    }

	}	
}
