
$.fn.extend({
    cart: function(options) {
        return this.each(function() {
            new $.cart(options);
        });
    }
});

$.cart = function(opt){
	
	
	var _carts = {'list':{},'amount':opt.amount,'num':opt.number,'all':1};
	
//	$('.wdgwc_box').find('.gwclb').each(function(i){
//		var _this = $(this),_obj = _this.find('.m_gwctu'),_id=_this.data('id'),_num=_this.find('.change').val()
//		
//		var _list = {'num':_num,'cate':_this.data('cate'),'price':_this.data('price'),'check':'n'};
//		
//		if(_obj.find('span').hasClass('on')){
//			_list['check'] = 'y';
//    	}else{
//    		_carts['all'] = 0;
//    	}
//		_carts['list'][_id] = _list;
//	})
	
	function _loadData(){
		
		_carts['num'] = 0;
		_carts['amount'] = 0;
		_carts['all'] = 1;
		var __all = 0;
		//for( var i in _carts['list']){ }
		$('.wdgwc_box').find('.gwclb').each(function(i){
			var _this = $(this),_obj = _this.find('.m_gwctu'),_id=_this.data('id'),_num=_this.find('.change').val(),_price=_this.data('price'),_cate=_this.data('cate')
			
			if(_obj.find('span').hasClass('on')){
				_carts['num'] += (_num*_cate);
				_carts['amount'] += (_num*_price)
	    	}else{
	    		_carts['all'] = 0;
	    	}
			__all +=1
		})
		if(__all == 0){
			location.reload();
			//location.href=opt.cartUrl;
		}
		//console.log(_carts);
		if(_carts['all'] == 1){
        	$('#checkAll').find('span').attr('class','on');
        }else{
        	$('#checkAll').find('span').attr('class','off');
        }
		
		$('.jiesua').find('a').html('结算 ('+_carts['num']+')')
  	  	//$(".houji").find('span').html('￥'+parseFloat(_carts['amount']).toFixed(2));
		$(".houji").find('span').html('￥'+parseFloat(_carts['amount']).toFixed(0));
	}
	
	_loadData();
	
	//delete _carts['dedu']['coin']
	//console.log('初始化购物车数据');
	//console.log(_carts);
	
    $main = $(".main");
    
    //加载初始化
    _action();
    
    function drop(){
    	$this = $(this);
    	var msga=layer.open({
	        content: '确认清除该商品？',
	        btn: ['确定', '取消'],
	        shadeClose: false,
	        yes: function(){
	        	layer.close(msga);
	            var id   = $this.parents(".gwclb").data("id");
	            var _tp = $this.parents(".gwclb").data('tp');
	            $this.unbind('click');
	            $.post(opt.dropUrl,{id:id,tp:_tp}, function(res){
	            	$this.css('background','#ccc');
	                if(res.done == false){
	                	_alert(res.msg);
	                    return;
	                }else{
                        //$this.parents(".gwclb").fadeTo(1000,"0.1",function(){
	                	    if($this.parents(".gwclb").next('p').hasClass('sintaop')){
	                	    	$this.parents(".gwclb").next('.sintaop').remove();
	                	    }else{
	                	    	if($this.parents(".gwclb").prev('p').hasClass('sintaop')){
		                        	$this.parents(".gwclb").prev('.sintaop').remove();
	                	    	}else{
	                	    		if(!$this.parents(".gwclb").next('p').hasClass('gwchst')){
	                	    			$this.parents(".gwclb").prev('.gwchst').remove();
	                	    		}else{
	                	    			$this.parents(".gwclb").next('.gwchst').remove();
	                	    		}
	                	    	}
	                	    }
	                	    $this.parents('.listBox').remove();
                            $this.parents(".gwclb").remove();
                            _loadData();
                      	    $this.bind("click", drop);
                      	    
                        //});
	                    
	                }
	            },'json')
	        },
	        no : function(){
	        	layer.close(msga);
	        	return;
	        }
	    });
    }
    
    function clear(){
        artDialog.confirm("确定要清空购物车吗？", function(){
            this.close();
            $.get(opt.clearUrl, function(res){
                location.reload();
            })
        })
    }
    
    function change(){
        var oi = $(this).parents(".jiajian_2").find("input[class=change]");
        var num = parseInt(oi.val());
        if(!num)num=1
        var val = $(this).data("do") == "jia" ? 1 : -1;
        var quantity = num + val;
        if(quantity < 1){
            oi.val(1);
            oi.data("num",1);
            return false;
        }
        
        var _o    = $(this).parents(".jiajian_2");
        var _ft   = _o.data('ft');
        if(_ft) return false;
        
        oi.val(quantity);
        oi.data("num",quantity);
        update(quantity, this);
    }
    
    $main.find(".change").unbind().bind("keyup", function(){
        if(!/^\d+$/.test($(this).val())){ //!/^\d+(\.\d+)?$/.test(_val)   !/^[1-9]+(\d+)?$/
        	$(this).val($(this).data("num"));
            return false;
        }
    });

    function _input(){
        
        var current = parseInt($(this).val());

        var _def    = $(this).data("num");

        if(!/^\d+$/.test(current)){ //!/^\d+(\.\d+)?$/.test(_val)   !/^[1-9]+(\d+)?$/
        	$(this).val(_def);
            return false;
        }
        
        $(this).val(current);
        
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
        var _o    = $(o).parents(".gwclb");
        var id    = _o.data("id");
        var _tp   = _o.data('tp');
        var _ft   = _o.data('ft');
        if(_ft) return;
        $.post(opt.updateUrl,{id:id,tp:_tp,num:n},function(res){
                  if(res.done == true){
                	  _loadData();
                  }else{
                	  _alert(res.msg)
                  }
        },'json')
    }

    function _submit(){
    	var $this = $(this);
    	$this.unbind();
    	var _one = $('.one_to_one').find('span').attr('class');
    	if(_one == 'on'){
    		_one = $('.one_to_one').find('span').data('id')
    	}
		$('#CartListForm').ajaxSubmit({
			type:"post",
			url : opt.checkUrl,
			data : {one:_one},
			success:function(res){
				var res = $.parseJSON(res);
				if(res.done == true){
					location.href=opt.checkoutUrl;
				}else{
			    	layer.open({
				        content: res.msg,
				        btn: ['确定'],
				    });
			    	$this.unbind().bind("click",_submit);
				}
			}
		});
    }
    
    function _check(){
    	var _this = $(this);
    	var _ck = 'n';
    	var _tp = _this.parents('.gwclb').data('tp')
    	var _id = _this.parents('.gwclb').data('id')
    	var _span = _this.find('span');
    	 if(_span.hasClass('on')){
    		 _span.attr('class','off')
    	 }else if(_span.hasClass('off')){
    		 _span.attr('class','on')
    		 _ck = 'y'
    	 }else{
    		 return;
    	 }
    	 $.post(opt.choiceUrl,{id:_id,tp:_tp,ck:_ck},function(res){
			if(res.done == false){
				if(_ck == 'y'){
					_this.find('span').attr('class','off')
				}else{
					_this.find('span').attr('class','on')
				}
			}else{
				_loadData();
			}
         },"json")
    }
    
    function _checkAll(){
    	var _this = $(this).find('span'),_cls = _this.attr('class')
    	
    	var _list = $('.wdgwc_box').find('.gwclb').find('.m_gwctu')
    	var _hd = 'n';
    	if(_cls == 'off'){
    		_list.find('span').attr('class','on')
    		_hd = 'y'
    	}else{
    		_list.find('span').attr('class','off')
    		_hd = 'n'
    	}
    	
    	$.post(opt.choiceAllUrl,{hd:_hd},function(res){
    		if(res.done == true){
    			_loadData();
    		}
    	},"json");
    	
    }
    
    function _alert(msg){
    	layer.open({
	        content: msg,
	        btn: ['确定'],
	    }); 
    }
    
    function _action(){
        
        $main.find(".jia").unbind().bind("click", change);
        $main.find(".jian").unbind().bind("click", change);
        $main.find(".change").unbind().bind("blur", _input);
        
        $main.find(".scal").unbind().bind("click", drop);
        
        //$main.find('.backshop').unbind().bind("click",function(){location.href="/";})
        $main.find(".jiesua").unbind().bind("click",_submit);
        
        $main.find('.m_gwctu').unbind().bind('click',_check);
        $('#checkAll').unbind().bind('click',_checkAll);

        if(document.referrer){
        	var strs = document.referrer.split("/");
        	var url = '/'+strs[strs.length-1];
        	if(url == opt.checkoutUrl){
        		return
        	}else{
        		$('#cartback').attr('href',url);
        		//$.post(opt.setPreUrl,{url:url},function(res){})
        		
        	}
        }
    	
        
    }
}