/* author yhao.bai 2014.6.10; */
$.fn.extend({
	cart: function(options) {
		return this.each(function() {
			new $.cart(options);
		});
	}
});

$.cart = function(opt){
	
	$main = $(".cart_main");
	
	_action();

	function drop(){
		 $this = $(this);
		 var l       = $this.offset().left;
		 var t       = $this.offset().top;
		 var box     = $(".warning");
		 var id      = $this.data("item");
		 var type    = $this.data("type");
		 box.css({
			 "left":l-80,
			 'top' :t-20
		 });
		 box.find("input").unbind().bind("click", function(){
			 if($(this).attr("name") == "ok"){
				 $.get(opt.dropUrl,{id:id,type:type}, function(res){
					  var res = $.evalJSON(res);
					  if(res.done == false){
						  artDialog.alert(res.msg);
						  return;
					  }else{
						  if(!res.retval.goods_num){
							  location.href=opt.cartUrl;
						  }else{
							  $this.parents("tr").fadeTo(800,"0.1",function(){
								  $main.html(res.retval.content);
								  _action();
							  });
							  
						  }
					  }
				  })
			 }
			 box.hide();
		 });
		 box.show();
	}
	
	function clear(){
		artDialog.confirm("确定要清空购物车吗？", function(){
			this.close();
			$.get(opt.clearUrl, function(res){
				location.reload();
			})
		})
	}
	
	function up(){
		var num = parseInt($(this).parents("td").find("input[name=change]").val());
		change(num);
	}
	
	function down(){
		var num = $(this).parents("td").find("input[name=change]").val();
		if(num < 1) num = 1;
		update(num);
	}
	
	function change(){
		var oi = $(this).parents("td").find("input[name=change]");
		var num = parseInt(oi.val());
		var val = $(this).attr("name") == "up" ? 1 : -1;
		var quantity = num + val;
		if(quantity < 1){
			oi.val(1);
			return false;
		}
		oi.val(quantity);
		update(quantity, this);
	}

	function _input(){
		
		var current = parseInt($(this).val());
		
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
		var _o   = $(o).parents("td");
		var id   = _o.data("item");
		var type = _o.data("type");
		var gid  = _o.data("goodsid");
		$.get(opt.updateUrl, {type:type,id:id,num:n,goodsid:gid},function(res){
			  var res = $.evalJSON(res);
			  if(res.done == false){
				  artDialog.alert(res.msg);
				  var o = _o.find("input[name=change]");
				  o.val(o.data('num'));
				  return;
			  }else{
				  
				  $main.html(res.retval.content);
				  
				  _action();
			  }
		})
	}
	
	function _action(){
		$main.find(".item-drop").each(function(){
			$(this).unbind().bind("click", drop);
		})
		
		$main.find(".clear_cart").unbind().bind("click", clear);
		
		$main.find("._action input").each(function(){
			var name = $(this).attr("name");
			switch(name){
				case "up":
					$(this).unbind().bind("click", change);
				break;
				
				case "down":
					$(this).unbind().bind("click", change);
				break;
				
				case "change":
					$(this).unbind().bind("blur", _input);
				break;
			}
		})
		
		$(".checkout").unbind().bind("click",function(){
			$.get(opt.checkloginUrl, function(res){
				var res = $.evalJSON(res);
				if(res.done == false){
					$(".thickbox").click();
				}else{
					location.href=opt.checkoutUrl;
				}
			});
		})
		
		$("#cartLogin").click(function(){
			var data = {};
			data.username =$('.cart_login input[name="username"]').val();
			data.pwd = $('.cart_login input[name="password"]').val();
			data.remember = $('.cart_login input[name="remember"]:checked').val();
			
			if(data.username.length == 0 || data.pwd.length == 0){
				$("#errorShow").html('用户名或者密码不能为空！');
				return false;
			}
			data.ajax = 1;
			$.post(opt.cartLoginUrl, data, function(res){
				var res = $.evalJSON(res);
				if(res.status=="-1"){
					$("#errorShow").html(res.msg);
					return false;
				}else{
					location.href=opt.checkoutUrl;
				}
			})
		})
	}
}