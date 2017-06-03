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
	
	$('.checkFabric').unbind().bind("click", checkFabric);
	
	$('.order_sub').click(function(){
	    $('#orderForm').ajaxSubmit({
	        type:"post",
	        url : opt.submitUrl,
	        success:function(res){
	            var res = $.parseJSON(res);
	            if(res.done==true){
	                msg(res.msg,'','',function(){location.href=opt.paycentUrl+"?"+res.retval.sn});
	                
	            }else{
	                msg(res.msg)
	            }
	        }
	    });
	})
	
    function _actionA(){
	    $('.source_id').unbind().bind("change", sourceChange);
    }

	//检查面料
	function checkFabric(){
		var _code = $("input[name=fabric]").val();
		var _id   = $('input[name=clothingID]:checked').val();

	    $.post(opt.checkFabricUrl,{code:_code,id:_id},function(res){
	        var res = $.parseJSON(res);
	        if(res != null && res.done == true){
	            var con = res.retval.content;
	            $('.fabric_result span').html("<font>库存为: <code>"+con.stock+"</code></font>")
	        }else{
	            $('.fabric_result span').html("<font><code>"+res.msg+"</code></font>")
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











