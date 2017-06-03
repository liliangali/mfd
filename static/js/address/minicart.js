/* author yhao.bai 2014.6.10; */
var miniCart = {};
miniCart.init = function(opt){
	if(!opt.miniCartUrl || !opt.miniDropUrl){
		return;
	}
	opt.otime;
	opt.show = 0;
	$(".m_qjs_on").mouseover(function(){
		$(".m_qjs_on").addClass("m_qjs_off");
		clearTimeout(opt.otime);
		if(!opt.show){
			opt.show=1;
			$("#miniCart").html("<p class='m_kgwcgjxg'>loading.....</p>");
			$.post(opt.miniCartUrl, function(res){
				var res = eval("(" + res + ")");;
				//alert(res.retval);
				$("#miniCart").html(res.retval);
				$("#itemlist .p3 a").unbind().bind("click", drop);
			})
		}
		
	}).mouseout(function(){
		opt.otime = setTimeout(hide, 200);
	})
	
	function hide(){
		$(".m_qjs_on").removeClass("m_qjs_off");
		opt.show = 0;
	}
	
	function drop(){
			var data = {};
			var $this = $(this);
			data.id    =  $this.data("id");
			data.type  =  $this.data("type");
			data.mincart = 1;
			$.get(opt.miniDropUrl, data, function(res){
				var res = eval("(" + res + ")");;
				if(res.done == false){
					 $.rc.tip({content:res.msg, icon:"error"});
				}else{
					 $this.parents("li").fadeTo(800,"0.1",function(){
						 $("#miniCart").html(res.retval.content);
						 $("#cartGoodsNum font").html(res.retval.goods_num);
						 $("#itemlist .p3 a").unbind().bind("click", drop);
					 })
			   }
		})
	}
}