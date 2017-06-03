
$.fn.extend({
    cart: function(options) {
        return this.each(function() {
            new $.cart(options);
        });
    }
});

$.cart = function(opt){
	
    $main = $(".cartMain");
    _action();
    
	//checbox美化
	$("form").checkBo({
		checkAllButton:"#check-all",
		checkAllTarget:".checkbox-row",
		checkAllTextDefault:"Check All",
		checkAllTextToggle:"Un-check All"
	});

	//签名信息
	$('#JcartList .Jqianming').click(function(){
		var _this = $(this),_id = $(this).parents('.cartList').data('id'),_tp = $(this).parents('.cartList').data('type');
		$.post(opt.embUrl,{id:_id,tp:_tp},function(res){
			if(res.done == true){
				luck.open({
					title:'设置签名',
					content:res.retval.content,
					width:'660px',
					height:'600px',
					addclass:'mfd-luck',
					callback:function(obj){
						
						var _tabId = $(obj).find('.xfxk').find('.now_hover').data('id');
						$(obj).find('.embBox_'+_tabId).show();
						
						$(obj).find('.qmwz').checkBo();
						
						$(obj).find('.xfxk').children('li').click(function(){
							$(this).addClass('now_hover').siblings().removeClass('now_hover');
							$(this).parents('.qianming').children('div').eq($(this).index()).siblings('div').hide().end().show();	
						});
						$(obj).find(".productshow").Xslider({
							unitdisplayed:6,
							numtoMove:3,
							unitlen:77,
						});
						$(obj).find(".productshow li").click(function(){
							$(this).addClass('cur').siblings().removeClass()
							$(this).parent('ul').find('input').val($(this).data('value'))
						})
						$(obj).find('.qmwz li').click(function(){
							$(this).addClass('current').siblings().removeClass()
							//$(this).find('input').attr('checked','checked').siblings().removeAttr('checked')
							//$(this).parents('.qmwz').find('input').siblings('input').attr('checked','checked').end().removeAttr('checked')
							$(this).find('input').parents('.qmwz').find('input').removeAttr('checked');
							$(this).find('input').attr('checked','checked');
						})
						
						$(obj).find('.baoz').click(function(){
							
							/**  
							* 中英文统计(一个中文算两个字符)  
							*/  
							function chEnWordCount(str){  
								var count = str.replace(/[^\x00-\xff]/g,"**").length;  
								return count;  
							} 
							var _E = 0;
							$('.qianming').find('.embBox').each(function(){
								
								if($(this).find('.colorBox:eq(0)').find('.cur').length<=0){
									alert('请选择字体');
									_E = 1;
									return	false;
								}
								if($(this).find('.colorBox:eq(1)').find('.cur').length<=0){
									alert('请选择颜色');
									_E = 1;
									return	 false;
								}
								
								if($(this).find('.cxlr').val()==""){
									alert('请输入签名信息');
									_E = 1;
									return	 false;
								}
								
								if(chEnWordCount($(this).find('.cxlr').val())>14){
						        	alert('签名信息不能大于14个字符');
						        	_E = 1;
						            return   false;
						        }
						        
						        if(!/^([A-Za-z0-9]|[\u4e00-\u9fa5]|\s)*$/.test($(this).find('.cxlr').val())){
						        	alert('签名信息仅限数字/字母/汉字');
						        	_E = 1;
						            return   false;
						        }
								if($(this).find('.qmwz .current').length<=0){
								    alert('请选择签名位置');
								    _E = 1;
									return false;
								}
								
							})
							if(_E) return false;
							
						        
							$(this).parents('#embsForm').ajaxSubmit({
					            type:"post",
					            url : opt.embSaveUrl,
					            success:function(res){
					                var res = $.parseJSON(res);
					                if(res.done == true){
					                	luck.close();
					                	if(res.retval.res > 0){
					                		_this.text('已设置');
					                		_this.parent('p').attr('class','p3');
					                	}else{
					                		
					                	}
					                }else{
					                	alert(res.msg)
					                }
					             }
					        });
							return false;
						});
						return false;
					}
				});
			}
		},"json");
		
		return false
	});
    
    function drop(){
        _this = $(this);
        
        var _id  = _this.parents('.cartList').data("id");
        var _tp  = _this.parents('.cartList').data('type');
        
        if(window.confirm('确定要删除么？')){
        	$.post(opt.dropUrl,{id:_id,tp:_tp}, function(res){
                if(res.done == true){
                    if(!res.retval.goods_num){
                        location.href=opt.cartUrl;
                    }else{
//                    	_this.parents("li").fadeTo(300,"0.1",function(){
//                        	_this.parent('li').remove();
//                        	upAll(res.retval.goods_num,res.retval.amount);
//                        });
                    	_this.parents(".cartList").fadeTo(300,"0.1",function(){
                    		
                			if(_this.parents('li').children('.cartList').length == 1){
                				_this.parents('li').remove();
                    		}else{
                    			_this.parent('.cartList').prev('.xuxian').remove();
                        		_this.parent('.cartList').next('.xuxian').remove();
                    			_this.parent('.cartList').remove();
                    		}
                			
                    		upAll(res.retval.goods_num,res.retval.amount);
                    	});
                    }
                }
            },"json")
        }
    }
    
    function clear(){
    	var _has = false;
    	$('#JcartList').find('li').each(function(){
    		if($(this).find('.cb-checkbox').find('input:checkbox').is(':checked')){
    			_has = true;   //预留扩展
    		}
    	})
    	if(!_has) return;
    	if(window.confirm('确定要删除所选么？')){
    		 $.get(opt.clearUrl, function(res){
                 if(res.done == true)
    			 location.reload();
             },'Json')
    	}
    }
    
    function change(){
    	
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
    	
        var _o    = $(o).parents('.cartList');
        var _id   = _o.data("id");
        var _tp   = _o.data("type");
        
        $.post(opt.updateUrl,{id:_id,type:_tp,num:n},function(res){
                  if(res.done == true){
                	  _o.find('.jine').html(res.retval.subtotal)
                	  upAll(res.retval.goods_num,res.retval.amount)
                  }
        },"json");
    }
    
    //选择购物车项
    $main.find('#JcartList label.cb-checkbox').on('click',function (){
    	
    	var _this = $(this),_cb = _this.find('input:checkbox');
    	var _ck = 'n';
    	var _id = _this.parents('.cartList').data('id');
    	var _tp = _this.parents('.cartList').data('type');
    	
    	if(_cb.is(':checked')){
    		_ck = 'y';
		}
    	
    	$.post(opt.choiceUrl,{id:_id,ck:_ck,tp:_tp},function(res){
    		if(res.done == true){
    			upAll(res.retval.goods_num,res.retval.amount)
    		}
    	},"json");
    	
    });
    
    //全选
    $main.find('#check-all').on('click',function(){
    	
    	var _this = $(this),_cb = _this.find('input:checkbox');
    	var _ck = 'n';
    	
    	if(_cb.is(':checked'))
    		_ck = 'y';
    	
    	$.post(opt.choiceAllUrl,{ck:_ck},function(res){
    		if(res.done == true){
    			upAll(res.retval.goods_num,res.retval.amount)
    		}
    	},"json");
    });
    
    function upAll(num,amount){
    	$main.find('.dzsp').find('span').first().text(num);
		$main.find('.dzsp').find('span').last().text(amount);
    }
    
    function _submit(){
    	$(this).unbind();
		$('.cartMain').ajaxSubmit({
			type:"post",
			url : opt.checkUrl,
			success:function(res){
				var res = $.parseJSON(res);
				if(res.done == true){
					location.href=opt.checkoutUrl;
					$(this).unbind().bind("click",_submit);
				}else{
					$(this).unbind().bind("click",_submit);
				}
			}
		});
    }
    
    function _action(){
        
        $main.find(".jajia_box").find('a').unbind().bind("click", change);
        $main.find(".jajia_box").find('input').unbind().bind("keyup", _input);
        
        $main.find('#JcartList .cazua').unbind().bind("click", drop);
        $main.find('.clearCart').unbind().bind("click", clear);
        
        $main.find("input:button.qjs").unbind().bind("click",_submit);
        
        
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