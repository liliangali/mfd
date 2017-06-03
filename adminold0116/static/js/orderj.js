$.fn.extend({
    order: function(options) {
        return this.each(function() {
            new $.order(options);
        });
    }
});
$.order = function(opt){
	
    _actionA();
    function _actionA(){}
    
    _loadPage();
    function _loadPage(){
    	
    	$('.FormBox').hide();
    	$('#FormBox_'+opt.orderStep).show()
    	
    	//考虑是每次点击上一步时触发还是页面加载时触发，最后选择牺牲第一次加载的资源
    	//if(opt.orderStep == '1'){
    		var _order_id = $('#order_id').val();
    		if(opt._clothVal != null)ajaxLoad(opt._clothVal,_order_id);
    		$("input[name=clothingID]").unbind().bind("click", function(){
    		    var _this = $(this);
    		    if(_this.is(':checked')){
    		    	ajaxLoad(_this.val())
    		    }
    		});
    	//}
		
		// 如果是第三  加载渲染
		if(opt.orderStep == '3'){
			canSee(_order_id);
		}
    	
    	//保存 //下一步
    	$('.order_save').click(function(){
    		saveOrder($(this));
    	})
    	
    	//上一步  暂时不做保存操作了吧...
    	$('.order_prev').click(function(){
    		var _step = $(this).parents('form').find('input[name="step"]').val();
    		$("#FormBox_"+_step).hide();
    		$("#FormBox_"+(_step-1)).show();
    	})
    	
    	
    	
    	
    }
      
	//工艺、刺绣、面料
	function ajaxLoad(_id,_oid){
		var cData = sessionStorage.getItem('aacraft'+_id);
		if(cData == null){
			$.post(opt.ajaxCraftUrl,{id:_id,oid:_oid},function(res){
		        if(res != null && res.done == true){
		        	//sessionStorage.setItem('craft'+_id,JSON.stringify(res.retval));
		        	$('.craft').html(res.retval.content)
		        	//autoCraft($.makeArray(res.retval.data));
		        	//var dt = $.makeArray(res.retval.data);
	        		for(var key in res.retval.data){
	        			autoCraft(res.retval.data[key],key);
	                }
		        }
		    },"json");
		}else{
			var res = $.parseJSON(cData);
			$('.craft').html(res.content);
        	var dt = $.makeArray(res.data);
    		for(var key in res.data){
    			autoCraft(res.data[key],key);
            }
		}
	    
	    $.post(opt.ajaxEmbUrl,{id:_id,oid:_oid},function(res){
	        var res = $.parseJSON(res);
	        if(res != null && res.done == true){
	            $('.embs').html(res.retval.content)
	        }else{
	            $('.embs').html('')
	        }
	    });
	    $.post(opt.ajaxFresult,{id:_id,oid:_oid},function(res){
            var res = $.parseJSON(res);
            if(res != null && res.done == true){
            	if(!_oid){
            		$('#fabric').val('');
            	}
            	
                var data = $.makeArray(res.retval.content);
                autoFabric(data);
            }
        });
	    
	}
	
	//工艺自动完成
	function autoCraft(data,_id){
	    $('#craft_input_'+_id).unbind('autocomplete');
	    $('#craft_input_'+_id).autocomplete(data,{
            minChars: 1,//自动完成激活之前填入的最小字符
            max:20,//列表条目数
            width: 500,//提示的宽度
            scrollHeight: 200,//提示的高度
            matchContains: false,//是否只要包含文本框里的就可以
            autoFill:false,//自动填充
            //cacheLength:10000,
            formatItem: function(data, i, max) {//格式化列表中的条目 row:条目对象,i:当前条目数,max:总条目数
            	return data.code + '['+data.parentName+':'+data.name+']';
        	},
    		formatResult: function(data) {//定义最终返回的数据，比如我们还是要返回原始数据，而不是formatItem过的数据
    			return data.code; 
    		}
      }).result(function(event,data,formatted){
    	  var $this = $(this);
    	  var _f = true;
			$('.craft_li_'+_id).each(function(){
				if(data.parentId == $(this).attr('parent-id')){
					alert('同一级别不能选择两个');
					$this.val('');
					_f = false;
					return false;
				}
			});
			if(_f){
				inputCraft($this,_id,data);
			}
      });
	}
	
	function inputCraft(_obj,_id,dt){
		var _prt   = _obj.parents('tr');
		var _hm1 = '' +
		'<tr>' +
		'<td>'  +
		'    <input class="craft_li craft_li_'+_id+'" name="craft['+_id+']['+dt.parentId+'][code]" type="text" autocomplete="off" readonly="readonly" data-id="'+dt.id+'" cloth-id="'+_id+'" parent-id="'+dt.parentId+'" status-id="'+dt.statusID+'" value="'+dt.code+'">' +
		'	<span>' +
		'	  '+dt.parentName+':'+dt.name+
		'	  <input class="craft_dt" type="text" name="craft['+_id+']['+dt.parentId+'][value]" />' +
		'	</span>' +
		'</td>' +
		'<td style="width:30px" onclick="$(this).parent().remove()">X</td>' +
		'</tr>';
		
		var _hm2 = ''+
		'<tr>' +
		'	<td>' +
		'	    <input class="craft_li craft_li_'+_id+'" name="craft['+_id+']['+dt.parentId+'][code]" type="text" autocomplete="off" readonly="readonly" data-id="'+dt.id+'" cloth-id="'+_id+'" parent-id="'+dt.parentId+'" status-id="'+dt.statusID+'" value="'+dt.code+'">' +
		'	    <span>  '+dt.parentName+':'+dt.name+'</span>' +
		'	</td>' +
		'	<td style="width:30px" onclick="$(this).parent().remove()">X</td>' +
		'</tr>';
		if(dt.statusID == 10008){
			_prt.before(_hm1);
		}else{
			_prt.before(_hm2);
		}
		_obj.val('');
	}
	
	//面料自动完成
	function autoFabric(data){
	    $('#fabric').unbind('autocomplete');
	    $('#fabric').autocomplete(data,{
            minChars: 1,//自动完成激活之前填入的最小字符
            max:1200,//列表条目数
            width: 265,//提示的宽度
            scrollHeight: 200,//提示的高度
            matchContains: true,//是否只要包含文本框里的就可以
            autoFill:false,//自动填充
            formatItem: function(data, i, max) {//格式化列表中的条目 row:条目对象,i:当前条目数,max:总条目数
            	return '[' + data.code + ']';
        	},
			  
    		formatResult: function(data) {//定义最终返回的数据，比如我们还是要返回原始数据，而不是formatItem过的数据
    			return data.code; 
    		}
      }).result(function(event,data,formatted){
    	  checkFabric(data.code);
    	  $('#fabric').unbind('change');
      });
	}
	
	//检查面料
	function checkFabric(_code){
		//var _code = $("input[name=fabric]").val();
		var _id   = $('input[name=clothingID]:checked').val();
	    $.post(opt.checkFabricUrl,{code:_code,id:_id},function(res){
	        var res = $.parseJSON(res);
	        if(res != null && res.done == true){
	            $('#fabric').next('span').html("库存为:"+res.retval)
	        }else{
	            $('#fabric').next('span').html(res.msg)
	        }
	    });
	}
	
	
	function saveOrder($this){
		var _step = $this.parents('form').find('input[name="step"]').val();
		var _type = $this.data('type');
		var _next = (parseInt(_step)+1);
		if(_type == 'save'){
			_next = _step;
		}
		
		var _order_id = $('#order_id').val(),_jp_id = $('#jp_id').val(),_user_id = $('#user_id').val(),_user_name = $('#user_name').val();
		
		//$this.attr('disabled',"true");
		$this.parents('form').ajaxSubmit({
	        type:"post",
	        url : opt.SaveUrl,
	        data : {next:_next,order_id:_order_id,jp_id:_jp_id,user_id:_user_id,user_name:_user_name},
	        success:function(res){
	            var res = $.parseJSON(res);
	            if(res.done==true){
	            	if(_next == 3){
	            		canSee(res.retval);
	            	}
	            	
	            	if(_type != 'save'){
	            		$("#FormBox_"+_step).hide();
		        		$("#FormBox_"+_next).show();
	            	}
	            	$('#order_id').val(res.retval);
	            }else{
	                alert(res.msg);
	            }
	            $this.removeAttr("disabled");
	        }
	    });
	}
	
	function canSee(_oid){
		$.post(opt.ajaxSeeUrl,{oid:_oid},function(res){
	        if(res != null && res.done == true){
	            $('#Infos').html(res.retval)
	        }
	    },"json");
	}
	
    

	

	
	
}//obj end











