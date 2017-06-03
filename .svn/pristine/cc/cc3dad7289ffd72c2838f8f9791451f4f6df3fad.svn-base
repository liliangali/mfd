// twoMenu()// 顶部二级菜单
// anav()//地区导航

//ns add 发送验证码

function sendCode(){
    var sign = $('.sendValidate').attr('class')
    if( sign =='sendValidate fl disabled') return false;
	$('.sendValidate').attr('data-time',60)
    $('#error').html('')

    var user_name = $('.xubox_tabli:visible').find('#uphone').val()
    var reg=/^1[3|5|8|7]\d{9}$/;
    if(!reg.test(user_name)){
        $('#error').html('<i class="ico fl"></i><span class="fl">手机格式不正确</span>')
        $('.sendValidate').bind('click',sendCode);
        return false
    }

	$.ajax(
	{
        url  : '/tailor_customer-tel_code.html?ajax=1',
		type : 'get',
		async:false,
        data:{'user_name':user_name},
        success:function(data)
        {
           data=eval('('+data+')');
           if(data.done != false){
				$('.sendValidate').attr('data-time',60)
        	}else{
        		$('#error').html('<i class="ico fl"></i><span class="fl">'+data.msg+'</span>')
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



/*--个人资料/S--*/
$(document).on('click','[data-e]',function(){
	var e=this.getAttribute('data-e');
	switch(e){
		case 'edit-name'://编辑昵称
			$('.nickname').attr('disabled',false).addClass('nickname-cur');
			$(this).next('a').show().end().hide();
			break;
		case 'revoke-edit-name'://取消编辑昵称
			$('.nickname').attr('disabled',true).removeClass('nickname-cur').val($('.nickname').attr('data-value'));
			$(this).prev('a').show().end().hide();
			break;
		case 'login-out'://安全退出
			msg('退出成功');
			break;
	}
})
/*--个人资料/E--*/

//模拟下拉菜单
;(function($){
	$.fn.selects=function(options){
		defaults={
			speed:150,
			width:"90px",
                        fn:function (){}
		};
		var $set=$.extend(defaults,options),$this=$(this),$html=$this.html(),$defaultTxt=$this.children("a:eq(0)").text();
		var $content="<dl class='s_select' style='width:"+$set.width+"'><dt><span class='s_tit'>"+$defaultTxt+"</span><i></i></dt><dd>"+$html+"</dd></dl><input type='hidden' name='"+$this.attr('data-input-id')+"' value='"+$this.children("a:eq(0)").attr('data-id')+"' />";
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
			$this.find("input").val($(this).attr('data-id'));
			$this.find(".s_tit").text($txt);
			$this.find("dd").slideUp($set.speed);
			$this.removeClass('s_select_cur');
                        $set.fn($this);
                        return false;
		});
	}
})(jQuery);

//我的需求页面
$("#s_1").selects({width:"120px",speed:120});


//我的关注
$("#pinlei").selects({width:"160px",speed:120});


//我要晒单
function shaidan(){
	$('.shaidan .error-css').removeClass('error-css');
	$('.shaidan .error').text('');
	function error(t,e){
		$('.shaidan .error').text(t);
		if(e){
		e.addClass('error-css')
		}
	}
	var obj1=$('.shaidan [name=img_name]'),
		obj2=$('.shaidan [name=type_id]'),
		obj3=$('.shaidan [name=description]'),
		obj4=$('#ul_single');
	if(obj1.val()==''){
		error('标题不能为空',obj1)
		return false
	}
	if(obj2.val()=='0'){
		error('请选择款式风格');
		return false
	}
	if(obj3.val()==""){
		error('请填写描述信息',obj3);
		return false
	}
	if(obj4.children('div').length<=1){
		error('至少上传两张图片');
		return false
	}

	if (obj4.find('a:contains(已设封面)').length<=0) {
		error('至少选择一个封面图');
		return false
	};
	document.getElementById('myForm_single').submit();

}

//查看量体数据
$('.ltData a:contains("查看")').click(function(){
	var $this=$(this),obj=$this.parents('tr').next('tr');
	obj.find('input[type=text]').attr('disabled',true).css({'border-color':'#fff',color:'#666'});;
	obj.find('.m_baoc').hide();
	if($this.hasClass('cur')){
		obj.css('display','none');
		$this.removeClass('cur');
	}else{
		obj.parents('.ltData').find('.show').not(obj).hide()
		obj.css('display','table-row').addClass('show');
		$this.parents('.ltData').find('a').not($this).removeClass('cur').end()
		$this.addClass('cur');
	};
	return false;
});
//修改量体数据
$('.ltData a:contains("修改")').click(function(){
	var $this=$(this),obj=$this.parents('tr').next('tr');
	obj.find('input[type=text]').attr('disabled',false).css({'border-color':'#bbb8bb',color:'#333'});
	obj.find('.m_baoc').show();
	if($this.hasClass('cur')){
		obj.css('display','none');
		$this.removeClass('cur');
	}else{
		obj.parents('.ltData').find('.show').not(obj).hide()
		obj.css('display','table-row').addClass('show');
		$this.parents('.ltData').find('a').not($this).removeClass('cur').end()
		$this.addClass('cur');
	};
	return false;
});
//删除量体数据
$('.ltData a:contains(删除)').click(function(){
	if(window.confirm('确定删除这条数据')){
		$(this).parents('tr').next('tr').remove();
		$(this).parents('tr').remove();
	}
	return false;
});

/*--添加顾客信息/S--*/
//添加
function addLtData(){
	use('../../static/expand/layer/layer.min.js',function(){
		use('../../static/expand/layer/extend/layer.ext.js',function(){
			layer.tab({
				area: ['640px', '585px'],
				data: [
					{title: '顾客不是会员', content:$('#addLtData1').html()},
					{title: '顾客已是会员', content:$('#addLtData2').html()}
				]
			});
		});
	})


	/*$('.error-css').removeClass('error-css');
	$('.error').html('');
	$('#addLtData form')[0].reset();
	use('../../static/expand/layer/layer.min.js',function(){
		$.layer({
			type: 1,
			title:'新增顾客信息',
			shade: [0.3, '#000'],
			area: ['640px','585px'],
			moveType: 1,
			page: {dom:'#addLtData'},
			end:function(){
				$('#addLtData form')[0].reset();
			}
		})
	})*/
}
//昵称验证
function nickname() {
	var na = $('.xubox_main #nikename').val()
	if(!na){
		$(".xubox_tabli .item font").html(" ! 不能为空")
	}else{
		$(".xubox_tabli .item font").html("")
	}
	$.ajax({
		url:'/tailor_customer-check_nickname.html',
		type: "post",
		dataType: "json",
		data:{na:na},
		success: function(res){
			if(!res.done){
				$(".xubox_tabli .item font").html(res.msg)
			}
		},
		error: function(res){}
	})
}
//编辑
function editLtData(id){
	$('.error-css').removeClass('error-css');
	$('.error').html('');
	use('../../static/expand/layer/layer.min.js',function(){
		$.layer({
			type: 1,
			title:'编辑顾客信息',
			shade: [0.3, '#000'],
			area: ['640px','560px'],
			moveType: 1,
			page: {dom:'#editLtData'+id}
		})
	})
}
// 录入
function entryLtData(id){
	$('.error-css').removeClass('error-css');
	$('.error').html('');
	use('../../static/expand/layer/layer.min.js',function(){
		$.layer({
			type: 1,
			title:'录入顾客信息',
			shade: [0.3, '#000'],
			area: ['640px','560px'],
			moveType: 1,
			page: {dom:'#entryLtData'+id}
		})
	})
}

//ns add 添加非会员提交




//添加-提交验证
function addClient(){
	if(validate()){
		var flag=true,obj=$('.xubox_tabli:visible').find('.data input[type=text]');
		var pstr = '';
		obj.each(function(i,e) {
			if(e.value!=''){
				obj.each(function(i,e) {
					if(e.value==''){
						flag=false;
						return false
					}else{
						pstr += e.name+'='+e.value+',';
					}
				})
				return false
			}
		});
		if(flag){
				$.ajax({
					url:'tailor_customer-add.html',
					type: "POST",
					dataType: "json",
					data:{
						val:pstr,
						na:$('.xubox_tabli:visible').find('#nikename').val(),
						un:$('.xubox_tabli:visible').find('#uname').val(),
						up:$('.xubox_tabli:visible').find('#uphone').val(),
						code: $('#code').val(),

					},
					success: function(res){
						if(!res.done){
							$('.xubox_tabli:visible').find('.error').html("<i class='ico fl'></i><span class='fl'>"+res.msg+"</span>");
						}else{
							layer.closeAll();
							parent.location.reload();
						}
					},
					error: function(res){}
				})
		}else{
			$('.xubox_tabli:visible').find('.error').html('<i class="ico"></i>量体数据不完整')
		}
	}
}



//编辑-提交验证
function editClient(id){
	if(validate()){
		var flag=true,obj=$('#editLtData'+id+' .data input[type=text]');
		var pstr = '';
		obj.each(function(i,e) {
			if(e.value!=''){
				obj.each(function(i,e) {
					if(e.value==''){
						flag=false;
						return false
					}else{
						pstr += e.name+'='+e.value+',';
					}
				})
				return false
			}
		});
		if(flag){
			$.ajax({
				url:'tailor_customer-edit.html',
				type: "POST",
				dataType: "json",
				data:{
					val:pstr,
					eid:id,
					un:$('#euname'+id).val(),
					up:$('#euphone'+id).val(),

				},
				success: function(res){
					if(!res.done){
						$('.error').html("<i class='ico fl'></i><span class='fl'>"+res.msg+"</span>");
					}else{
						layer.closeAll();
						parent.location.reload();
					}
				},
				error: function(){}
			})
		}else{
			$('.error').html('<i class="ico"></i>量体数据不完整')
		}
	}
}
//录入-提交验证
function entryClient(id){
	var flag=true,obj=$('#entryLtData'+id+' .data input[type=text]');
	var pstr = '';
	obj.each(function(i,e) {
		if(e.value==''){
			flag=false;
			return false
		}else{
			pstr += e.name+'='+e.value+',';
		}
	})
	if(flag){
		$.ajax({
			url:'tailor_customer-entry.html',
			type: "POST",
			dataType: "json",
			data:{
				val:pstr,
				eid:id,
				un:$('#entry_uname'+id).val(),
				up:$('#entry_phone'+id).val(),

			},
			success: function(res){
				if(!res.done){
					$('.error').html("<i class='ico fl'></i><span class='fl'>"+res.msg+"</span>");
				}else{
					layer.closeAll();
					parent.location.reload();
				}
			},
			error: function(){}
		})
	}else{
		$('.error').html('<i class="ico"></i>量体数据不完整')
	}
}
//限制量体信息为数字
$('.myClient .data input[type=text]').keyup(function(){
	if(isNaN(this.value)){
		this.value='';
	}
})
//录入量体信息
function toggleData(obj){
	if($(obj).next('dd').is(':visible')){
		$(obj).next('dd').slideUp('fast');
		$(obj).removeClass('cur');
	}else{
		$(obj).next('dd').slideDown('fast');
		$(obj).addClass('cur');
	}
}
/*--添加顾客信息/E--*/

//裁缝中心-上传案例
$(document).on('focus','.error-css',function(){
	$(this).removeClass('error-css');
})
function uploadCase(){
	$('.anli .error-css').removeClass('error-css');
	$('.anli .error').text('');
	function error(t,e){
		$('.anli .error').text(t);
		if(e){
			e.addClass('error-css')
		}
	}
	var obj1=$('.anli [name=description]'),
		obj2=$('#ul_li');

	if(obj1.val()==""){
		error('请填写描述信息',obj1);
		return false
	}
	if(obj2.children('li').length<=1){
		error('至少上传两张图片');
		return false
	}
	$('#myForm').submit();
}

//修改密码
function pwdEdit(){
	$('.pwdEdit .error-css').removeClass('error-css');
	$('.pwdEdit .error').text('');
	function error(t,e){
		$('.pwdEdit .error').html('<i class="fl ico"></i><span class="fl">'+t+'</span>');
		if(e){
			e.addClass('error-css')
		}
	}
	//检测是否是默认值
	function isDefault(obj){
		return obj.val()==obj.attr('data-placeholder');
	}
	var oldpwd=$('.pwdEdit [name=orig_password]'),
		newpwd1=$('.pwdEdit [name=new_password]'),
		newpwd2=$('.pwdEdit [name=confirm_password]');
	if(oldpwd.val()==""){
		error('请输入旧的密码',oldpwd);
		return false
	}
	if(!/^\S{6,20}$/.test(newpwd1.val())||isDefault(newpwd1)){
		error('请输入6-20个大小写英文字母、符号或数字',newpwd1);
		return false
	}
	if(newpwd2.val()==""){
		error('请输入确认密码',newpwd2);
		return false
	}
	if(newpwd1.val()!=newpwd2.val()){
		error('两次的密码不一致请重新输入',newpwd2);
		return false
	}
	$('#password_form').submit();
}
//申请派工
function applyWork(){
		use('../../static/expand/layer/layer.min.js',function(){
			use('../../static/expand/my97date/wdatepicker.js',function(){
				$.layer({
					type: 1,
					title:'申请派工',
					shade: [0.3, '#000'],
					area: ['600px','auto'],
					moveType: 1,
					page: {dom:'.myForm'}
				})
			})
		})
}
//派遣量体
function applyWork_submit(){
	if(validate()){
		$.ajax(
		{
		url  : '/dispatching-add.html',
		type : 'post',
		async:false,
		data:{'owner_name':$('#owner_name').val(),'sex':$('#sex').val(),'tel':$('#tel').val(),'region_id':$('#region_id').val(),'region_name':$('#region_name').val(),'address':$('#address').val(),'date':$('#date').val(),'apply_date':$('#apply_date').val(),'description':$('#description').val()},
		success:function(data)
		{
			if(data != 'false'){
			   layer.closeAll();
			   msg('添加成功','330','150',function(){
				   window.location.href='/dispatching-tailor.html';
			   });
			}else{
			  layer.closeAll();
			  msg('添加失败');
			}
		}
		}
		);
	}
}
//申请采购
function applyCaigou(){
		use('../../static/expand/layer/layer.min.js',function(){
			use('../../static/expand/my97date/wdatepicker.js',function(){
				$.layer({
					type: 1,
					title:'填写采购信息',
					shade: [0.3, '#000'],
					area: ['600px','auto'],
					moveType: 1,
					page: {dom:'.myForm'}
				})
			})
		})
}
function applyCaigou_submit(){
	if(validate()){
		$.ajax(
		{
		url  : '/fabric_apply-add.html',
		type : 'post',
		async:false,
		data:{'goods_sn':$('#goods_sn').val(),'num':$('#num').val(),'date':$('#date').val(),'owner_name':$('#owner_name').val(),'phone':$('#phone').val(),'description':$('#description').val()},
		success:function(data)
		{
			if(data != 'false'){
			   msg('添加成功','','',function(){ window.location.href='/fabric_apply-tailor.html';});
			}else{
			   msg('添加失败');
			}
		}
		}
	);
	}
}


//评论弹出层
function applyComm(){
		use('../../static/expand/layer/layer.min.js',function(){
			use('../../static/expand/my97date/wdatepicker.js',function(){
				$.layer({
					type: 1,
					title:'发表评论',
					shade: [0.3, '#000'],
					area: ['600px','auto'],
					moveType: 1,
					page: {dom:'.comm_box'}
				})
			})
		})
}


// 表单验证
function validate(){
	$('#error,.error').html('');
	//未通过
	function error(t,obj){
		$('#error:visible,.error:visible').html("<i class='ico fl'></i><span class='fl'>"+t+"</span>");
		$(obj).addClass('error-css');
	}
	//检测是否是默认值
	function isDefault(obj){
		return obj.value==obj.getAttribute('data-placeholder');
	}
	var v={
			//ns add
			nikename:function(obj){
				if(obj.value==''||isDefault(obj)){
					var tip=$(obj).attr('data-tip');
					error((tip?tip:'')+'不能为空',obj);
					return false
				}
				return true
			},
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
					var tip=$(obj).attr('data-tip');
					error((tip?tip:'')+'不能为空',obj);
					return false
				}
				return true
			},
			num:function(obj){
				if(!/^\d+$/.test(obj.value)||isDefault(obj)){
					var tip=$(obj).attr('data-tip');
					error((tip?tip:'')+'请填写正整数',obj);
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
				}
				if(success==false){
					return false
				}
            });
			return success
	}()
}
//举报
function jubao(){
	use('../../static/expand/layer/layer.min.js',function(){
		$.layer({
			type: 1,
			title:'举报',
			shade: [0.3, '#000'],
			area: ['575px','480px'],
			moveType: 1,
			page: {html:$('#JUBAOLAYER').html()}
		})
	})
	function tijiao(){
		var $this=$(this);
		if($('#shopName').val()==""){
			$('#shopName').next('label').text('举报店铺名字不能为空').end().focus(function(){
				$(this).next('label').text('');
			});
			return
		}
		if($('.jubaoLayer .editor').val().length<10){
			$('.jubaoLayer .editor').parents('.item').find('label').text('内容不能少于10个字').end().end().focus(function(){
				$(this).parents('.item').find('.error').text('');
			});
			return
		}

		$.ajax({
			url:"/my_complaint-add.html",
			type : 'post',
			beforeSend: function(){
				$this.val('提交中...');
				$(document).off('click',tijiao);
			},
			data:{
				description:$("#myForm .editor").val(),
				shopName:$('#shopName').val(),
				img1_url:$('#input_1').val(),
				img2_url:$('#input_2').val(),
				img3_url:$('#input_3').val(),
				img4_url:$('#input_4').val(),
				img5_url:$('#input_5').val(),
				img6_url:$('#input_6').val(),
				img7_url:$('#input_7').val(),
				img8_url:$('#input_8').val(),
			},
			success: function(data){
				data = eval("(" + data + ")");
				if(data.res == 1){
					layer.closeAll();
					msg(data.message,345)
					window.location.href = window.location.href
				}else{
					$('#submitJb').val('提交')
					msg(data.message,345)
					$(document).on('click','#submitJb',tijiao)
				}

			}
		})
	}


	$(document).on('click','#submitJb',tijiao);
	$(document).on('keyup','#myForm .editor',function(){
		var $this=$(this),len=$this.val().length;
		if((300-len)<0){
			$this.val($this.val().substring(0,300));
			return false;
		}
		$this.next('.numLen').text('您还能输入'+(300-len)+'字')
	})
}
function jieguo(_msg){
	msg(_msg,420,250)
}

var jbUpDataPic={
	num:0,
	del:function(n){
		$("#li_"+n).remove();
		if(jbUpDataPic.num){jbUpDataPic.num--}
	},
	upData:function(){
		if($('#upimg_file').val()==""){
		  return
		}
		var li_html = '';
		if(jbUpDataPic.num == 4){
			msg('最多能上传4张图片');
			return
		}
		if($('#ul_li .loading').length<=0){
			$('#ul_li').append('<li class="loading">加载中..</li>')
		}
		var options = {
				url:'/my_complaint-upload.html?num='+jbUpDataPic.num+'&r='+(10000*Math.random()),
				success: function(data) {
					jbUpDataPic.num++;
					var dd = eval( "(" + data + ")" ),n=jbUpDataPic.num;
					li_html += '<li id="li_'+n+'"><img width="80" height="80" src="'+dd.src+'" />';
					li_html += "<input type='hidden' id='input_"+n+"' name='input_"+n+"' value='"+dd.file+"' >";
					li_html += '<a href="javascript:void(0);" onclick=jbUpDataPic.del(\''+n+  '\'); >删除</a></li>';
					$('#ul_li .loading').remove();
					$('#ul_li').append(li_html);
				}
		};
		use('../../static/expand/jquery.form.js',function(){
		$('#myForm').ajaxSubmit(options);
		})
	}
}







