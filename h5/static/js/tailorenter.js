//发送验证码
function sendCode(){
	$('.sendValidate').attr('data-time',60)
	$.ajax(
	{  
        url  : '/apply-tel_code.html?ajax=1',
		type : 'get',  
		async:false,
        data:{'user_name':$('#tel').val()},
        success:function(data)  
        {
           if(data != 'false'){
				$('.sendValidate').attr('data-time',60)
        	}else{
        		msg('发送失败');
        		$('.sendValidate').attr('data-time',5)
        	}
        } 
     }  
    ); 
    countdown();
	
}


//倒计时按钮
function countdown(){
	var obj=$('.sendValidate'),
		time=obj.attr('data-time');
	if(time>0){
		obj.addClass('disabled').unbind('click');
		var n=time;
		var timer=setInterval(function(){
			n--
			if(n<0){
				obj.text('重新获取').removeClass('disabled');
				obj.bind('click',sendCode);
				clearInterval(timer);	
				return
			}
			obj.text('('+n+')秒重新发送');
		},1000)	
	}
}
countdown();
$('.sendValidate').bind('click',sendCode);

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
				var reg=/^1[3|5|8|7]\d{9}$/,success =true;
				if(!reg.test(obj.value)){
					error('手机号码格式不正确',obj);
					success = false
				}else{
					$.ajax(
						{  
		                    url  : '/apply-check_tel.html?ajax=1',
		        			type : 'get',  
		        			async:false,
		                    data:{'tel':$('#tel').val()},
		                    success:function(data)  
		                    {
		                       if(data != 'false'){
		                    		success = true
		                    	}else{
		                    		error('电话已经被占用',obj);
		                    		success= false
		                    	}
		                    } 
		                 }  
		                ); 
				
				}
				return success
			},
			required:function(obj){
				if(obj.value==''){
					var tip=obj.getAttribute('data-tip');
					error(tip?tip+'不能为空':'不能为空',obj);	
					return false
				}
				return true
				
			},
			store_name:function(obj){
				if(obj.value==''){
					var tip=obj.getAttribute('data-tip');
					error(tip?tip+'不能为空':'不能为空',obj);	
					return false
				}else{
					$.ajax(
						{  
		                    url  : '/apply-check_name.html?ajax=1',
		        			type : 'get',  
		        			async:false,
		                    data:{'store_name':$('#store_name').val()},
		                    success:function(data)  
		                    {
		                       if(data != 'false'){
		                    		success = true
		                    	}else{
		                    		error('店铺名称已经被占用',obj);
		                    		success= false
		                    	}
		                    } 
		                 }  
		                ); 
				}
				return success
			},
			// emall:function(obj){
			// 	var reg=/^\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$/;
			// 	if(!reg.test(obj.value)||isDefault(obj)){
			// 		error('邮箱格式不正确',obj);
			// 		return false
			// 	}
			// 	return true
			// }

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
				if($('#server input[type=checkbox]:checked').length<=0){
					$('#error').html("<i class='ico fl'></i><span class='fl'>至少选择一项服务方式</span>");
					$('#server').parents('.item').addClass('error-css2');
					return false
				}
				if($('#leixing input[type=checkbox]:checked').length<=0){
					$('#error').html("<i class='ico fl'></i><span class='fl'>至少选择一种风格</span>");
					$('#leixing').parents('.item').addClass('error-css2');
					return false
				}
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
		};
		var $set=$.extend(defaults,options),$this=$(this),$html=$this.html(),$defaultTxt=$this.children("a:eq(0)").text();
		var $content="<dl class='s_select' style='width:"+$set.width+"'><dt><span class='s_tit'>"+$defaultTxt+"</span><i></i></dt><dd>"+$html+"</dd></dl><input type='hidden' name='"+$this.attr('data-input-id')+"' value='"+$defaultTxt+"' />";
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
        
        $("input[name='region_name']").val(
                $this.parent().find('#s_1 input').attr('data-value') + ' ' + 
                $this.parent().find('#s_2 input').attr('data-value') + ' ' + 
                $this.parent().find('#s_3 input').attr('data-value')
            );
        $("input[name='city_id']").val(
                $this.parent().find('#s_2 input').attr('value')
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
		$('#error').html("<i class='ico fl'></i><span class='fl'>请先选择地区</span>");
        return false;
    }
	return true;
    
}
$("#s_1").selects({width:"110px",speed:120,fn:areaSelectFn});
//$("#s_2").selects({width:"110px",speed:120});
//$("#s_3").selects({width:"110px",speed:120});
$("#s_4").selects({width:"110px",speed:120});

//字数限制
function textNum(options){
	var setttings = {
		defaultTxt:"请输入",
		ShowWordNumArae: "#wnum",
		WordNum: 100
	};
	if (options) {
		$.extend(setttings, options);
	};
	var content, length, Rnum;
	var $this=$(setttings.textArea)
	$this.val(setttings.defaultTxt)
	$this.focus(function(){
		if($this.val()==setttings.defaultTxt){
			$this.val("").css("color","#000");	
		}
	})
	$this.blur(function(){
		if($this.val()==""){
			$this.val(setttings.defaultTxt).css("color","#999");	
		}
	})
	
	$(setttings.ShowWordNumArae).html("还可以输入" + setttings.WordNum + "字");
	$this.keyup(function (e) {
		length = $this.val().length;
		Rnum = setttings.WordNum - length;
		if (e.keyCode != 8) {
			if (Rnum <= 0) {
				content = $this.val();
				content = content.substring(0, setttings.WordNum);
				$this.val(content);
				$(setttings.ShowWordNumArae).html("还可以输入0字");
			} else {
				content = $this.val();
				$this.val(content);
				$(setttings.ShowWordNumArae).html("还可以输入" + Rnum + "字");
			}
		} else {
			$(setttings.ShowWordNumArae).html("还可以输入" + Rnum + "字");
		}
	});
}
textNum({
    defaultTxt: "", //默认文字
    textArea:".depict", //输入框id
    ShowWordNumArae:".numLen", //显示剩余输入个数id
    WordNum: 50 //设置最多输入字数	
})











