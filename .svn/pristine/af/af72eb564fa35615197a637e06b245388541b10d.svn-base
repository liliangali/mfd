//发送验证码
function sendCode(){
	var reg=/^1[3|5|8|7]\d{9}$/;
	var phone = $('#phone').val();
	if(reg.test(phone)){
		$('.sendValidate').attr('data-time',60);
		$.ajax({
			url  : 'demand-ajaxcode.html',
			data : '&mobile='+phone,
			type : 'post',
			success : function(res){
				var res = $.parseJSON(res);
				if(res.done == true){
					countdown();
				}else{
					msg(res.msg);
				}
			}
		});
	}else{
		$('#error').html("<i class='ico fl'></i><span class='fl'>请正确输入手机号码！</span>");
        $('#phone').addClass("error-css");		
	}
}
//倒计时按钮
function countdown(){
	var obj=$('.sendValidate'),
		time=obj.attr('data-time');
	if(time>0){
		obj.addClass('disabled').attr('disabled',true).val('('+time+')秒重新发送');
		var n=time;
		var timer=setInterval(function(){
			n--
			if(n<0){
				obj.val('重新获取').removeClass('disabled').attr('disabled',false);
				clearInterval(timer);	
				return
			}
			obj.val('('+n+')秒重新发送');
		},1000)	
	}
}
$('.sendValidate').bind('click',sendCode);

//表单默认提示
$('input[data-placeholder]').each(function(i, e) {
    var t=e.getAttribute('data-placeholder');
	e.value=t;
	e.style.color='#666'
	if(e.type=='password'){
		e.type='text';
		e.setAttribute('d',1);	
	}
	$(e).focus(function(){
		if(e.value==t){
			e.value='';
			e.style.color='#222'
			$(e).addClass('cur');
			if(e.getAttribute('d')==1){
				e.type='password';
			}
		}
	});
	$(e).blur(function(){
		if($(e).val()==''){
			e.value=t;
			e.style.color='#666'
			$(e).removeClass('cur');
			if(e.getAttribute('d')==1){
				e.type='text';
			}
		}
	});
});


// 表单验证
$('input[data-type]').focus(function(){
	$(this).removeClass('error-css')	
});
$(document).on('mouseover','.error-css2',function(){
	var $this=$(this);
	setTimeout(function(){
		$this.removeClass('error-css2');	
	},1000)
})
function validate(){
	$('#error').html('');
	//未通过
	function error(t,obj){
		$('#error').html("<i class='ico fl'></i><span class='fl'>"+t+"</span>");
		$(obj).each(function(i,e){
			if($(e).is(':visible')){
				$(e).addClass('error-css');	
			}	
		});	
	}
	
	//检测是否是默认值
	function isDefault(obj){
		return obj.value==obj.getAttribute('data-placeholder');
	}

	var v={
			phone:function(obj){
				var reg=/^1[3|5|8|7]\d{9}$/;
				if(!reg.test(obj.value)||isDefault(obj)){
					error('手机号码格式不正确',obj);
					return false
				}
				return true
			},
			required:function(obj){
				if(obj.value==''||isDefault(obj)){
					error(obj.getAttribute('data-tip')+'不能为空',obj);	
					return false
				}
				return true
			},
			emall:function(obj){
				var reg=/^\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$/;
				if(!reg.test(obj.value)||isDefault(obj)){
					error('邮箱格式不正确',obj);
					return false
				}
				return true
			}

		}
	return function(){
			var success =true;
			$('input[data-type]:visible').each(function(i,e) {
				var p=e.getAttribute('data-type');
				if(v.hasOwnProperty(p)){
                	success=v[p](e);
				}else{
					var p2=p.replace('|','_');
					if(v.hasOwnProperty(p2)){
						success=v[p2](e);		
					}	
				}
				if(success==false){
					return false
				}
            });
			if(success){
				$('.vradio').each(function(index,e) {
                    if($(this).find(':radio:checked').length<=0){
						$('#error').html("<i class='ico fl'></i><span class='fl'>请至少选择一项</span>");
						$(e).addClass('error-css2');
						success=false;
						return false;
					}
                });
			}
			return success
			
	}()
}

//顶部二级菜单
$('.head .btn-angle').hover(function(){
	var $this=$(this);
	delay=setTimeout(function(){
		var leftNum=($this.offset().left-60+$this.width()/2);
		$('.head .bd-menu').children('.'+$this.attr('data-class')).show().end().css({left:leftNum}).slideDown(200);
		$this.addClass('btn-angle-hover')
	},100)
},function(){
	if(delay){
		clearTimeout(delay);
	}
	var $this=$(this);
	_menu=setTimeout(function(){
		$('.head .bd-menu').children('.'+$this.attr('data-class')).hide().end().hide();
		$this.removeClass('btn-angle-hover');
	},100);
});
$('.head .bd-menu').hover(function(){
	clearTimeout(_menu);
},function(){
	$(this).children('div:visible').hide().end().hide();
	$('.head').find('.btn-angle').removeClass('btn-angle-hover')
	_menu=null;	
});

//模拟下拉菜单
(function($){
	$.fn.selects=function(options){
		defaults={
			speed:150,
			width:"90px",
			fn:function (){}
		}
		var $set=$.extend(defaults,options),$this=$(this),$html=$this.html(),$defaultTxt=$this.children("a:eq(0)").text();
		var $content="<dl class='s_select' style='width:"+$set.width+"'><dt><span class='s_tit'>"+$defaultTxt+"</span><i></i></dt><dd>"+$html+"</dd></dl><input type='hidden' id='"+$this.attr('data-input-id')+"' name='"+$this.attr('data-input-id')+"' value='"+$defaultTxt+"' />";
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
			var $txt=$(this).text();
			$this.find("input").val($(this).attr('value')).attr('data-value',$txt);
			$this.find(".s_tit").text($txt);
			$this.find("dd").slideUp($set.speed);
			$this.removeClass('s_select_cur');
                        $set.fn($this);
                        return false;
		});
	} 
})(jQuery);


var areaSelectFn = function($this){
        var dataValue = $this.find("input").val();
        var areaSelectIndex = $this.index('#s_1,#s_2,#s_3')+2;
        var areaIndex = $this.index()+1;
        var areaName = areaSelectIndex == 2 ? 'sheng' : 'region_id';
        $this.nextAll("div").remove();
        
        $("input[name='region[name]']").val(
                $this.parent().find('#s_1 input').attr('data-value') + ' ' + 
                $this.parent().find('#s_2 input').attr('data-value') + ' ' + 
                $this.parent().find('#s_3 input').attr('data-value')
            );
        $("input[name='region[id]']").val($('#region_id').val());
        $("input[name='regions']").val(
                $this.parent().find('#s_1 input').val() + ',' + 
                $this.parent().find('#s_2 input').val() + ',' + 
                $this.parent().find('#s_3 input').val()
            );
        var url = '/mlselection.html?type=region';
        if(dataValue){
            $.getJSON(url, {'pid':dataValue}, function(data){
                if (data.done){
                    if (data.retval.length > 0){
                        $this.after('<div id="s_'+areaSelectIndex+'" data-input-id="'+areaName+'"> <a href="#" value="">请选择...</a> </div>');
                        var data  = data.retval;
                        for (i = 0; i < data.length; i++){
                            $this.next("div").append("<a href='#' value='" + data[i].region_id + "'>" + data[i].region_name + "</a>");
                        }
                        $("#s_"+areaIndex).selects({width:"110px",speed:120,fn:areaSelectFn});
                    }
                }
                else
                {
                    msg(data.msg);
                }
            });
        }
};
function addressverify(){
    if(!($('#s_3 input').val() > 0)){
        $('#error').html("<i class='ico fl'></i><span class='fl'>您还没有选择地区</span>");
		$('#s_1').parents('.item').addClass('error-css2')
        return false;
    }else{
        return true;
    }
}
$("#s_1").selects({width:"110px",speed:120,fn:areaSelectFn})
//$("#s_2").selects({width:"110px",speed:120})
//$("#s_3").selects({width:"110px",speed:120})







