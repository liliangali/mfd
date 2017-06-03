$.fn.extend({
    order: function(options) {
        return this.each(function() {
            new $.order(options);
        });
    }
});
$.order = function(opt){

    if(opt._clothVal != null)ajaxCraft(opt._clothVal);
    if(opt._sourceVal != null)checkSource(opt._sourceVal);
    
    _actionA();

	$("input[name=clothingID]").unbind().bind("click", function(){
	    var _this = $(this);
	    if(_this.is(':checked')){
	        ajaxCraft(_this.val())
	    }
	});
	$("input[name=source]").unbind().bind("click", function(){
	    var _this = $(this);
	    if(_this.is(':checked')){
	        checkSource(_this.val())
	    }
	});
	
	function autoFabric(data){
	    $('#fabric').unbind('autocomplete');
	    $('#fabric').autocomplete(data,{
            minChars: 0,//自动完成激活之前填入的最小字符
            max:12,//列表条目数
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
         //$('#fabric').next('span').html('库存为：'+data.stock+'&nbsp;&nbsp;&nbsp;面料价格：￥'+data.goods+'&nbsp;&nbsp;&nbsp;服务费：￥'+data.serve+'&nbsp;&nbsp;&nbsp;总金额：￥'+(data.goods+data.serve));
          checkFabric();
      });
	}
				
	
	$('.order_sub').click(function(){
		$(this).attr('disabled',"true");
	    $('#orderForm').ajaxSubmit({
	        type:"post",
	        url : opt.submitUrl,
	        success:function(res){
	            var res = $.parseJSON(res);
	            if(res.done==true){
	                msg(res.msg,'','',function(){location.href=opt.paycentUrl+"?"+res.retval.sn});
	            }else{
	                msg(res.msg);
	                $(this).removeAttr("disabled");
	            }
	        }
	    });
	})
	
    function _actionA(){
	    $('.source_id').unbind().bind("change", sourceChange);
    }
	$('#fabric').change(function(){
        checkFabric();
    })

	//检查面料
	function checkFabric(){
		var _code = $("input[name=fabric]").val();
		var _id   = $('input[name=clothingID]:checked').val();
	    $.post(opt.checkFabricUrl,{code:_code,id:_id},function(res){
	        var res = $.parseJSON(res);
	        if(res != null && res.done == true){
	            var con = res.retval.content;
	            $('#fabric').next('span').html("库存为:"+con.stock+",面料价格:"+con.goods+",加工费:"+con.serve+",总金额:"+(con.goods+con.serve))
	        }else{
	            $('#fabric').next('span').html(res.msg)
	        }
	    });
	}

	//切换clothID ajax请求工艺、刺绣
	function ajaxCraft(_id){
	    $.post(opt.ajaxCraftUrl,{id:_id},function(res){
	        var res = $.parseJSON(res);
	        if(res != null && res.done == true){
	            $('.craft').html(res.retval.content)
	        }else{
	            $('.craft').html('')
	        }
	    });
	    $.post(opt.ajaxEmbUrl,{id:_id},function(res){
	        var res = $.parseJSON(res);
	        if(res != null && res.done == true){
	            $('.embs').html(res.retval.content)
	        }else{
	            $('.embs').html('')
	        }
	    });
	    $.post(opt.ajaxFresult,{id:_id},function(res){
            var res = $.parseJSON(res);
            if(res != null && res.done == true){
                var data = $.makeArray(res.retval.content);
                autoFabric(data);
            }else{
                alert('asd');
            }
        });
	    
	}
	//根据订单来源选择 ajax请求相应数据
	function checkSource(_id){
	    $.post(opt.checkSourceUrl,{id:_id},function(res){
	        var res = $.parseJSON(res);
	        if(res != null && res.done == true){
	            $('#source').html(res.retval.content)
	            _actionA();
	        }else{
	            $('#source').html(res.msg)
	        }
	    })
	    
	}
	
    //切换订单来源值，调取不同量体数据
    function sourceChange(){
	    if($(this).val() == '')return;
	    var _tp = $(this).data('type');
	    $.post(opt.getFigureUrl,{tp:_tp,id:$(this).val()},function(res){
	        var res = $.parseJSON(res);
            if(res != null && res.done == true){
                $('#figures').html(res.retval.content);
                $('#kh').html(res.retval.kh);
                _actionA();
            }	        
	    })
    }

}//obj end











