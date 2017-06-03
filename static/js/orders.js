$.fn.extend({
    order: function(options) {
        return this.each(function() {
            new $.order(options);
        });
    }
});
$.order = function(opt){
	_init();
	function _init(){
		_actionA();
		$('#Measures').unbind().bind("change", measure);
		
		$('#setSize .existing li').click(function(){
			$(this).addClass('selected').siblings().removeClass();
			$(this).find('#history_id').val($(this).data('id'));
		});
		
		$('#psInfo li').click(function(){
			$(this).addClass('selected').siblings().removeClass('selected');
			var index = $('#psInfo li').index(this);
			$('div.tab_box > div').eq(index).show().siblings().hide();
		});
		
		$('#mendianAddress dd').click(function(){
			$('#mendianAddress dd').removeClass('qhselectss');
			$(this).addClass('qhselectss');
		});
		
		
	}
	
	
	$('.region_id').change(function(){
		var _id = $(this).attr('data-id');
		if(_id == 'mendianAddress') var _sty = 'qhselectss'
		$.post(opt.serveUrl,{region_id:$(this).val(),sty:_sty}, function(res){
		    var res = $.parseJSON(res);
		    if(res.done == true){
		        $("#"+_id).html(res.retval.content);
		        _actionA();
		    }
		});
	});
	
	$('#fapiao p').click(function(){
		$(this).addClass('current').siblings().removeClass('current');
		if($(this).index()==1){
			$('#taitou').show()	
		}else{
			$('#taitou').hide()	
		}
		return false	
	});
	
	$('#total p').click(function(){
		$(this).addClass('current').siblings().removeClass('current')
		return false
	});
	$('#useOver').keyup(function(){
		if(isNaN(this.value)){
			this.value=''
			return		
		}
		if($('#_over').val()-this.value>=0){
			$('#curPrice').text($('#_price').val()-this.value);
		}else{
			this.value=$('#_over').val();
			$('#curPrice').text($('#_price').val()-this.value)	
		}
	});

	
	function _actionA(){
		
		$('#address>ul>li').not('#addAddress').click(function(){
			$(this).addClass('psxxdq').siblings().removeClass('psxxdq')	
		});
		
		$('#mendian').on('click','dl',function(){
			$(this).siblings().children('dd').removeClass();
			$(this).children('dd').addClass('selectss');		
		})
		
		$('#address .caozuoBtn .del').unbind().bind('click',delAddress);
		$('#addAddress').unbind().bind('click',addAddress);
		$('#address .caozuoBtn .edit').unbind().bind('click',editAddress);
		
		
		$("#orderDown").unbind().bind('click',create);
    }
	
	function measure(){
		
		var _this = $(this);
		var _id  = _this.val();
		var _txt = _this.find("option:selected").text();
		
		$('#setSize .near').hide();
		$('#setSize .near').find('input').attr('disabled','disabled');
		$('#setSize .near').find('select').attr('disabled','disabled');
		$('#meaBox_'+_id).show();
		$('#meaBox_'+_id).find('input').attr('disabled',false);
		$('#meaBox_'+_id).find('select').attr('disabled',false);
		$(this).prev('.selected').text(_txt);
		return;
	}
	
	function delAddress(){
		if(window.confirm('确定要删除？')){
			$(this).parents('li').fadeOut('fast',function(){
				$.post(opt.delAddrUrl,{id:$('.psxxdq').data('id')}, function(res){
				    var res = $.parseJSON(res);
				    if(res.done == true){
				    	$("#address").html(res.retval);
				    	$(this).remove();
				    	_actionA();
				    }
				});
			})
		}
	}
	
	function addAddress(){
		$.post(opt.getAddrUrl,{addr_id:'0'}, function(res){
		    var res = $.parseJSON(res);
		    if(res.done == true){
				var lay=$.layer({
					type: 1,
					title: false,
					area: ['300px', '367px'],
					border: [0], //去掉默认边框
					closeBtn: [1,true], //去掉默认关闭按钮
					page: {
						html:res.retval.content,
					},
					success:function(obj){
						obj.css({'border':'solid 1px #ff3300',background:'#fff'});
						obj.find('#quxiao').click(function(){
							layer.close(lay);		
						});
						obj.find('#save').click(function(){
							if(obj.find('.addto').validate({acticle:true})){
								 var dataPara = getFormJson("#address_form");
								$.post(opt.addressUrl,{data:dataPara}, function(res){
								    var res = $.parseJSON(res);
								    if(res.done == true){
								    	 $("#address").html(res.retval);
								    	layer.close(lay);
								    	_actionA();
								    }else{
								    	alert(res.msg);
								    }
								});
							}
						});
					}
				});
		    }
		})
	}
	
	function editAddress(){
		$.post(opt.getAddrUrl,{addr_id:$(this).parents('li').data('id')}, function(res){
		    var res = $.parseJSON(res);
		    if(res.done == true){
		    	
				var lay=$.layer({
					type: 1,
					title: false,
					area: ['300px', '367px'],
					border: [0], //去掉默认边框
					closeBtn: [1,true], //去掉默认关闭按钮
					page: {
						html:res.retval.content,
					},
					success:function(obj){
						obj.css({'border':'solid 1px #ff3300',background:'#fff'});
						obj.find('#quxiao').click(function(){
							layer.close(lay);		
						});
						obj.find('#save').click(function(){
							if(obj.find('.addto').validate({acticle:true})){
								 var dataPara = getFormJson("#address_form");
								$.post(opt.editAddrUrl,{data:dataPara}, function(res){
								    var res = $.parseJSON(res);
								    if(res.done == true){
								    	$("#address").html(res.retval);
								    	layer.close(lay)
								    	_actionA();
								    }else{
								    	alert(res.msg);
								    }
								});
								
							}
						});
					}
				});
		    }
		})
	}
	
	
	
	
	//立即下单
	function create(){
		
		var _order = [];
		//检测尺码信息
		switch($('#selectSize option:selected').attr('data-n')){
			
			case '1':
				var obj=$('#meaBox_1');
				if(!obj.validate({acticle:true})){
					_scroll('#p1');
					return
				}else{
					var figure = [];
					figure += '{"ty":"1",';
					for(var i=0;i<obj.find('.data').length;i++){
						figure += '"'+obj.find('.data').eq(i).attr('name')+'":"'+obj.find('.data').eq(i).val()+'",';
					}
					figure = figure.substring(0,figure.length - 1)+'}';
				};
				
			break;
			case '2'://附近门店量体
				var obj=$('#meaBox_2');
				if(!obj.validate({acticle:true})){
					_scroll('#p1');
					return
				}else{
					var figure = [];
					figure += '{"ty":"2",';
					if($('#mendian .selectss').length>0){
						for(var i=0;i<obj.find('.data').length;i++){
							figure += '"'+obj.find('.data').eq(i).attr('name')+'":"'+obj.find('.data').eq(i).val()+'",';
						}
						figure += '"'+obj.find('#mendian .selectss').attr('name')+'":"'+obj.find('#mendian .selectss').data('sid')+'"}';
					}else{
						error('请选择门店')
						_scroll('#p1');
						return	
					}
				};
			break;
			case '3':
				if($('#meaBox_3 .selected').length<=0){
					error('尺码信息不完整3');
					_scroll('#p1');
					return	
				}else{
					var figure = [];
					figure += '{"ty":"3",';
					figure += '"history_id":"'+$('#meaBox_3').find('#history_id').val()+'"}';
					//$('#_sizeinfo').val($('#meaBox_3 .selected').text());
				}
			break
			default:
				error('尺码信息不完整0');
				_scroll('#p1');
			break;
		}
		
		//检测配送信息
		if($('#psInfo li.selected').attr('data-id')==1){
			var address = [];
			address += '{"ty":"address",';
			address += '"addr_id":"'+$('#address .psxxdq').attr('data-id')+'"}';
		}else{
			var address = [];
			address += '{"ty":"store",';
			address += '"city":"'+$('#selectCityAddress').val()+'",';
			address += '"'+$('#mendianAddress .qhselectss').attr('name')+'":"'+$('#mendianAddress .qhselectss').data('sid')+'"}';
		}
		
		//检测发票信息
		var invoice = [];
		switch($('#fapiao .current').attr('data-id')){
			case '1':
				invoice += '{"ty":"1"}';
			break
			case '2':
				if(!$('#taitou').validate({acticle:true})){
					_scroll('#p3');
					return
				}
				invoice += '{"ty":"2","invoiceup":"'+$('#taitou input').val()+'"}';
			break
			case '3':
				invoice += '{"ty":"3"}';
			break	
		}
			
		//支付方式
		var payment = [];
		payment += '{"ty":"'+$('#total .current').data("code")+'","payid":'+$('#total .current').data("payid")+'}';
		
		_order = '{"figure":'+figure+',"address":'+address+',"invoice":'+invoice+',"payment":'+payment+',"remaining":{"money":"'+$('#useOver').val()+'"}}';
		//console.log(_order);
		//return;
		//SetCookie('_order',_order);
		$.post(opt.createUrl,{_order:_order}, function(res){
		    var res = $.parseJSON(res);
		    if(res.done == true){
		    	location.href=opt.paycenterUrl+'?id='+res.retval.order_sn;
		    }
		});

	}
	
	
	function getFormJson(frm) {
	    var o = {};
	    var a = $(frm).serializeArray();
	    $.each(a, function () {
	        if (o[this.name] !== undefined) {
	            if (!o[this.name].push) {
	                o[this.name] = [o[this.name]];
	            }
	            o[this.name].push(this.value || '');
	        } else {
	            o[this.name] = this.value || '';
	        }
	    });

	    return o;
	}
	
	
	
	
	/*--表单验证/S--*/
	;(function($){
		$.fn.validate=function(options){
			//提交按钮状态
			var submitBtn={
				flag:false,
				id:$(this).find("input[type='submit']"),
				txt:'提交中...'
			}
			//验证规则
			var validate={
					required:function(e){
						if(e.val()==''){
							_error(e,'required');
							return false
						}
					},
					email:function(e){
						var reg=/^\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$/,val=e.val();
						if(val&&!reg.test(val)){
							_error(e,'email');
							return false
						}
					},
					phone:function(e){
						var reg=/^1[3|5|8|7]\d{9}$/,val=e.val();
						if(val&&!reg.test(val)){
							_error(e,'phone');
							return false
						}
					},
					number:function(e){
						var val=e.val()
						if(val&&isNaN(e.val())){
							_error(e,'number');
							return false
						}
					},
					url:function(e){
						var reg=/^http:\/\/.+\./,val=e.val();
						if(val&&!reg.test(val)){
							_error(e,'url');
							return false
						}
					},
					maxlength:function(e){
						var len=e.attr('maxlength'),val=e.val();
						if(!len){return true}
						if(val&&!(val.length<=len)){
							_error(e,'maxlength');
							return false
						}
					},
					minlength:function(e){
						var len=e.attr('minlength'),val=e.val();
						if(!len){return true}
						if(val&&!(val.length>=len)){
							_error(e,'minlength');
							return false
						}
					},
					idcard:function(e){
						var reg=/(^\d{15}$)|(^\d{17}([0-9]|X|x)$)/,val=e.val();
						if(val&&!reg.test(val)){
							_error(e,'idcard');
							return false	
						}
					}

			}
			//提示信息
			messages = {
				required: "不能为空",
				email: "格式不正确",
				phone:'格式不正确',
				url: "请输入合法的 URL地址",
				number: "请输入数字",
				maxlength: '超出字数限制',
				minlength: '字数长度不够',
				idcard:'输入不正确'
			}
			//合并对象
			if(options){
				if(options.messages){
					messages=$.extend(messages,options.messages)
				}
				if(options.validate){
					validate=$.extend(validate,options.validate)
				}
				if(options.submitBtn){
					submitBtn=$.extend(submitBtn,options.submitBtn)
				}
			}
			//错误提示
			window._error=function(obj,error){
				obj.addClass('error');
				layer.msg(obj[0].title+messages[error])
			}
			//校验
			function check(){
				 var rule=$(this).attr('validate').split('|'),
						len=rule.length;
					for(var i=0;i<len;i++){
						if(validate.hasOwnProperty(rule[i])){
							if(validate[rule[i]]($(this))==false){
								return false
								break
							}
						}
					}
			}
			$('[validate]',this).on('focus',function(e) {
				$(this).removeClass('error');
			})
			$('[validate]',this).on('blur',function(e) {
				//check.call(e.currentTarget)
	        });
			function yanzheng(){
				var success=true;
				$(this).find('[validate]').each(function(i, e) {
				   if(check.call(e)==false){
						success=false
						return false
				   }
	            });
				if(success){
					if(submitBtn.flag){
						submitBtn.id.val(submitBtn.txt)
					}
				}
				return success	
			}
			$(this).submit(yanzheng);
			if(options.acticle){
				return yanzheng.call(this)
			}
		}
	})(jQuery);
	/*--表单验证/E--*/
	
	function error(txt){
		layer.msg(txt)	
	}
	function _scroll(obj){
		var t=$(obj).offset().top;
		$('body,html').animate({scrollTop:t},'fast')
	}
	function SetCookie(name,value)
	{
		 var nameString = name + '=' + encodeURI(value);
		    var expiryString = "";
		    var time = null;
		    if(time !== 0) {
		        var expdate = new Date();
		        if(time == null || isNaN(time)) time = 60*60*1000;
		        expdate.setTime(expdate.getTime() +  time);
		        expiryString = ' ;expires = '+ expdate.toGMTString();
		 }
		 var path = " ;path =/";
		 document.cookie = nameString + expiryString + path;
	}
	
	
	

}//obj end











