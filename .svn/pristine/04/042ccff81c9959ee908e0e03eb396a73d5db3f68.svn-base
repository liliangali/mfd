//模拟下拉菜单
;(function($){
	$.fn.selects=function(options){
		defaults={
			speed:150,
			width:"90px"
		};
		var $set=$.extend(defaults,options),$this=$(this),$html=$this.html(),$defaultTxt=$this.children("a:eq(0)").html();
		var $content="<dl class='s_select' style='width:"+$set.width+"'><dt><span class='s_tit'>"+$defaultTxt+"</span><i></i></dt><dd>"+$html+"</dd></dl><input type='hidden' name='"+$(this).attr('data-id')+"' value='"+$this.children("a:eq(0)").attr('value')+"' />";
		$this.html($content);
				
		//事件
		$("dt",$this).click(function(){
			var obj=$(this).next("dd");
			if(obj.is(':visible')){
				obj.slideUp($set.speed);
				$this.removeClass('s_select_cur');
			}else{
				obj.slideDown($set.speed);
				$this.addClass('s_select_cur');
			}
			return false;
		});
		$(document).click(function(){
			$('.s_select>dd').hide();	
			$('.s_select_cur').removeClass('s_select_cur');
		});

		$("a",$this).click(function(){
			var $txt=$(this).html();
			$this.find("input").val($(this).attr('value'));
			$this.find(".s_tit").html($txt);
			$this.find("dd").slideUp($set.speed);
			$this.removeClass('s_select_cur');
			return false;
		});
	} 
})(jQuery);

$(".mnSelect").each(function(index, element) {
   $(this).selects({width:"120px",speed:120}); 
});

//刺绣折叠展开
$('.cixiu h4').click(function(){
	var obj=$(this).next('.cixiucon')
	if($(this).hasClass('cur')){
		$(this).removeClass('cur');
		obj.hide()	
	}else{
		$(this).addClass('cur');
		obj.show()	
	}	
})

//刺绣位置选择
$('.cxPosition li').click(function(){
	if($(this).hasClass('cur')){
		$(this).removeClass()
	}else{
		$(this).addClass('cur')
	}
})

//删除
$('.guanbi .del').click(function(){
	if(window.confirm('确定要删除么？')){
		//add by xiao5 drop
		 $this = $(this);
	      var id   = $this.data("id");
		   $.post('/cart-drop.html',{id:id}, function(res){
	            var res = $.parseJSON(res);
	            if(res.done == false){
	                alert(res.msg);
	                return;
	            }else{
	                if(!res.retval.goods_num){
	                    location.href='/cart.html';
	                }else{
	                	$(this).parents('.item').fadeOut('fast',function(){
		        			$(this).remove();
		        		});	
	                	   $("#num").html(res.retval.goods_num);
	                  	   $("#totalPrice").html(res.retval.final_amount);
	                }
	            }
	        })
	}
})
//计算总价
function totalPrice(){
	var price=null,num=null
	$('.trolley .item').each(function(index, element) {
        price+=Number($(this).find('.money').children('em').text());
		num+=Number($(this).find('[data-price]').val())
    });
	$('#totalPrice').text(price.toFixed(2));
	$('#num').text(num)
}

//加减
$('.subtraction .btn').click(function(){
	var obj=$(this).parents('.item').find('[data-price]');
	var n=Number(obj.val());
	$('.subtraction p').removeClass('null')
	if($(this).hasClass('add')){
		obj.val(n+1);
	}else{
		if(n>1){
			obj.val(n-1);
		}else{
			$(this).addClass('null');	
		}
	}
	
	var price=obj.val()*obj.attr('data-price')
	$(this).parents('.item').find('.money').children('em').text(price.toFixed(2))
	totalPrice()
});
//初始化单品价格
$('.trolley>.item').each(function(index, element) {
	var obj=$(this).find('[data-price]');
	var price=obj.val()*obj.attr('data-price')
    $(this).find('.money').children('em').text(price.toFixed(2));
	totalPrice()
});
//去结算
$('#gopay').click(function(){
	var data={},l=[];
	$('.trolley>.item').each(function(i, e) {
		var obj=data[$(e).find('.guanbi a').attr('data-id')]={};
        obj.quantity=$(e).find('.subtraction input').val();
		obj.f=$(e).find('[name=font]').val();
		obj.c=$(e).find('[name=color]').val();
		obj.p=$(e).find('.guanbi a').attr('data-p');
		$(e).find('.cxPosition .cur').each(function(i, e) {
			l.push($(e).attr('value'))
        });
		obj.l=l.join('|');
		obj.content=$(e).find('.cixiutxt').val();
    });
	var dstr = JSON.stringify(data);
	   $.post('/cart-check.html',{pstr:dstr}, function(res){
           var res = $.parseJSON(res);
           if(res.done == false){
               alert(res.msg);
               return;
           }else{
               if(!res.retval.goods_num){
                   location.href='/cart.html';
               }else{
            	   location.href='/cart-checkout.html';
               }
           }
       })
})



