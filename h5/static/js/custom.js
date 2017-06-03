twoMenu();
anav();
fixedNav('.fixedNav');

//单品大图切换
$('.danpin>li').click(function(){
	var $this=$(this);
	$this.parent().prev('.bigPic').children('img').attr('src',this.getAttribute('data-pic'));
	$this.siblings().removeClass('cur').end().addClass('cur');
});

//套装大图弹层
$('.taozhuang li,.taozhuang .dingzhi').click(function(){
    var $this=$(this),data=eval($this.parents('.taozhuang').attr('data-pic')),arr=[],name=$this.parents('.taozhuang').attr('data-name'),disid=$this.parents('.taozhuang').attr('data-id');
 	for(var i=0;i<data.length;i++){
 		arr.push({'src':data[i]})
 	}
 	if($this.hasClass('dingzhi')){
 		var index=0
 	}else{
 		var index=$this.index();	
 	}
 	showSlide(arr,name,index, disid);
 	return false;
});

function showSlide(data,title,index,disid){
	var layhtml =     '<div class="tzForm">'+
    '<form action="" method="post" id="diyForm">'+
    '<h2>我要定制该套装</h2><dl><dt>定制标题<em>*</em></dt>'+
    '<dd><input type="text" data-tip="标题" data-placeholder="好的标题可以吸引裁缝的关注哦（5~30字）" value="好的标题可以吸引裁缝的关注哦（5~30字）" name="title" maxlength="30" class="txt" data-type="required" /></dd>'+
    '<dt>定制预算<em>*</em></dt>'+
    '<dd><select name="item[price]">'+opts+
    '</select></dd>'+
    '<dt>真实姓名<em>*</em></dt>'+
    '<dd><input type="text" class="txt" name="name" data-tip="真实姓名" data-type="required" /></dd>'+
    '<dt>所在地区<em>*</em></dt>'+
    '<dd id="regionCity"><select>'+
                              '<option value="0">请选择</option>'+cityOption+
                         	  
    '</select>'+
    '<input type="hidden" name="region[id]" value="" id="region_id" class="mls_id" />'+
    '<input type="hidden" name="region[name]" value="" class="mls_names" id="region_name"/>'+
    '</dd>'+
    '<dt>手机号码<em>*</em></dt>'+
    '<dd><input type="text" class="txt" name="contact[mobile]" data-type="phone" id="telphone"/>'+
    '<div class="validate clearfix">'+
    '<input type="text" class="txt" name="code" data-type="valideCode" id="telcode" data-tip="短信验证码"/>'+
    '<span class="sendValidate" onclick="sendCode()">获取验证码</span></div></dd>'+
    '<dt>备注</dt><dd><textarea id="remark" name="remark" onfocus="if($(this).val()==\'没有特殊要求不用填写\')$(this).val(\'\')" onblur="if($(this).val()==\'\')$(this).val(\'没有特殊要求不用填写\')">没有特殊要求不用填写</textarea></dd>'+
    '<p class="tip"><em class="red">小提示：</em> 提交需求后，所有裁缝会看到您的需求并参与报价， 你可以选择中意的裁缝为你定制</p>'+
    '<div id="error"></div>'+
    '<input type="button" value="提交需求" class="submit" id="subDiy"/><input type="hidden" name="type" value="suit"></dl>'+
    '<input type="hidden" name="type_id" value="'+disid+'">'+
    '</form>'+
    '</div>';
	
	use('../../static/expand/layer/layer.min.js',function(){
		use('../../static/expand/layer/extend/layer.ext.js',function(){
			layer.photos({
				html: layhtml,
				 json: { 
					"status": 1,
					"title":title,
					"start":index,
					"data": data
					}
			})
			regionInit("regionCity");
			$('#subDiy').click(function(){
				//验证登陆
				if(hasLogin()== 0){
					login(function(){
						$.cookie("hasLogin",1);
						$('#subDiy').click();
					})
					return
				}
				// 验证用户
				var res = $.ajax({ url: "gallery-checkTailor.html", async: false }).responseText;
				if(res == 1){
					$.layer({
						title:'系统提示',
						area: ['335px','150px'],
						moveType: 1,
						dialog: {
							msg: '裁缝不可以发布定制需求!',
							btns: 1,                    
							type: 0,
							btn: ['确定']
						}
					});
					return;
				}
				//表单验证
			    if(!validate())return;
			    
			    var remark = $("#remark");
			    if(remark.val() == '没有特殊要求不用填写'){
			    	remark.val('');
			    }
			    
			    $('#diyForm').ajaxSubmit({
			        type:"post",
			        url : "demand-subsue.html",
			        success:function(res){
			            var res = eval("("+res+")");
			            if(res.done==true){
			                location.href='demand-lists.html'
			            }else{
			                msg(res.msg)
			            }
			        }
			    });
			});
		})
	})
}

//发送验证码
function sendCode(){
	var phone = $("#telphone").val();
	var reg=/^1[3|5|8|7]\d{9}$/;
	if(!reg.test(phone)){
		$('#error').text('手机号不合法');
		$("#telphone").addClass('error-css');	
		return false;
	}
	$('.sendValidate').attr('data-time',60).removeAttr('onclick');
	$.get(sendUrl, {phone:phone, category:'suitdiy', type:'suitdiy'}, function(res){
		var res = eval("("+res+")");
//		if(res.done == false){
//			alert(res.msg);
//		}
	});
    countdown();
}
//倒计时按钮
function countdown(){
	var obj=$('.sendValidate'),
		time=obj.attr('data-time');
	if(time>0){
		obj.text('(60)秒重新获取').addClass('disabled');
		var n=time;
		var timer=setInterval(function(){
			n--
			if(n<0){
				obj.text('重新获取').removeClass('disabled');
				obj.attr('onclick','sendCode()');
				clearInterval(timer);	
				return
			}
			obj.text('('+n+')秒重新获取');
		},1000)	
	}
}



//定制套装验证
$(document).on('focus','[name=title]',function(){
	if($(this).val()==$(this).attr('data-placeholder')){
		$(this).val('');
		$(this).css('color',"#000");
	}
});
$(document).on('blur','[name=title]',function(){
	if($(this).val()==''){
		$(this).val($(this).attr('data-placeholder'));
		$(this).css('color','#666');	
	}
})


// 表单验证
$(document).on('focus','input[data-type]',function(){
	$(this).removeClass('error-css')
});
function validate(){
	$('#error').html('');
	//未通过
	function error(t,obj){
		$('#error').text(t);
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
					var tip=obj.getAttribute('data-tip');
					error(tip?tip+'不能为空':'不能为空',obj);	
					return false
				}
				return true
			},
			valideCode:function(obj){
				var success=true;
				if(obj.value==''||isDefault(obj)){
					var tip=obj.getAttribute('data-tip');
					error(tip?tip+'不能为空':'不能为空',obj);	
					return false
				}else{
					var phone = $("#telphone").val();
					var code = $("#telcode").val();
					$.ajax({
				        url  : checkCode,
				        data : 'phone='+phone+"&code="+code,
				        type : 'get',
				        async:  false, 
				        success : function(res){
				            var res = eval("("+res+")");
				            if(res.done==false){
				            	success=false;
				            	error('手机验证码不正确','#telcode');	
				            	return false;
				            }
				        }
				    })
				    return success
				}
			}
		}

	return function(){
			var success=true;
			$('input[data-type]:visible').each(function(i,e) {
				var p=e.getAttribute('data-type');
				if(v.hasOwnProperty(p)){
                	success=v[p](e);
				}
				if(success==false){
					return false
				}
           });
			if(success){
				$("#regionCity select").each(function(){
					if(this.value == 0||this.value==''){
						success = false;
						error('请选择所在地区','#regionCity');	
						return false;
					}
				})
			}
			return success;
	}()
}



























