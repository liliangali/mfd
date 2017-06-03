/*--门店信息/S--*/
$('#selectCity').change(function(){
$.post("/cart-getServe.html",{region_id:$(this).children('option:selected').val()}, function(res){
    var res = $.parseJSON(res);
    if(res.done == true){
        $("#mendian").html(res.retval.content);
    }
});
});

$('#selectCityAddress').change(function(){ 
$.post("/cart-getServe.html",{region_id:$(this).children('option:selected').val(),sty:'qhselectss'}, function(res){
    var res = $.parseJSON(res);
    if(res.done == true){
        $("#mendianAddress").html(res.retval.content);
    }
});
});



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


var orderok={
	init:function(){
		orderok.size();	
		orderok.address();
		orderok.pay();
		orderok.invoice();
		orderok.orderDown();
		//初始化清空表单
		$('#_sizeinfo,#_address,#_invoice').val('')
	},
	//尺码及量体信息
	size:function(){
		$('#selectSize .select')[0].options[0].selected=true;
		$('#selectSize .select').change(function(){
			var index=this.options[this.selectedIndex].getAttribute('data-n')-1;
			$(this).prev('.selected').text(this.value);
			if(index<0){
				$('#setSize>div').hide();
				return
			}
			$('#setSize>div').eq(index).siblings().hide().end().show();
		})
		$('#setSize .existing li').click(function(){
			$(this).addClass('selected').siblings().removeClass();	
		});
		//门店选择
		$('#mendian').on('click','dl',function(){
			$(this).siblings().children('dd').removeClass();
			$(this).children('dd').addClass('selectss');		
		})

		//选择尺码下一步
		$('#sizeTable .ddnext').click(function(){
			//验证
			var b=$('#sizeTable div:eq(0)').validate({acticle:true});
			if(b){
				$('#sizeTable div:eq(1)').siblings().hide().end().show();	
			}
			return false	
		})
		//体形选择
		$('#sizeTable div:eq(1) li').click(function(){
			$(this).addClass('cur').siblings().removeClass('cur');	
		})
	},
	//配送信息
	address:function(){
		//选项卡
		var $tab_li = $('#tab .tab_menu li');
		$tab_li.click(function(){
			$(this).addClass('selected').siblings().removeClass('selected');
			var index = $tab_li.index(this);
			$('div.tab_box > div').eq(index).show().siblings().hide();
		});
		//选择
		$('#address>ul>li').not('#addAddress').click(function(){
			$(this).addClass('psxxdq').siblings().removeClass('psxxdq')	
		});
		//删除
		$('#address .caozuoBtn .del').click(function(){
			if(window.confirm('确定要删除？')){
				$(this).parents('li').fadeOut('fast',function(){
					
					$.post("/cart-dropAddr.html",{id:$('.psxxdq').data('id')}, function(res){
					    var res = $.parseJSON(res);
					    if(res.done == true){
					    	  $("#address").html(res.retval);
					    	$(this).remove();
					    	orderok.init();
					    }
					});
					
					
				})	
			}
		});
		//添加
		$('#addAddress').click(function(){
			$.post("/cart-getAddress.html",{addr_id:'0'}, function(res){
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
									$.post("/cart-address.html",{data:dataPara}, function(res){
									    var res = $.parseJSON(res);
									    if(res.done == true){
									    	 $("#address").html(res.retval);
									    	layer.close(lay);
									    	orderok.init();
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
		})


		//修改
		$('#address .caozuoBtn .edit').click(function(){

			$.post("/cart-getAddress.html",{addr_id:$("#address ul li").data('id')}, function(res){
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
									$.post("/cart-uAddress.html",{data:dataPara}, function(res){
									    var res = $.parseJSON(res);
									    if(res.done == true){
									    	$("#address").html(res.retval);
									    	layer.close(lay)
									    	orderok.init()
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

			
		})
	},
	//支付方式
	pay:function(){
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
	},
	//发票信息
	invoice:function(){
		$('#fapiao p').click(function(){
			$(this).addClass('current').siblings().removeClass('current');
			if($(this).index()==1){
				$('#taitou').show()	
			}else{
				$('#taitou').hide()	
			}
			return false	
		});
	},
	//立即下单
	orderDown:function(){
		
		var _order = [];
		function error(txt){
			layer.msg(txt)	
		}
		function _scroll(obj){
			var t=$(obj).offset().top;
			$('body,html').animate({scrollTop:t},'fast')
		}

		$('#orderDown').click(function(){
			//检测尺码信息
			switch($('#selectSize option:selected').attr('data-n')){
				case '0':
					error('尺码信息不完整');
					_scroll('#p1');
					return	
				break;
				case '1'://现有量体数据
					if($('#setSize>div:eq(0) .selected').length<=0){
						error('尺码信息不完整');
						_scroll('#p1');
						return	
					}else{
						$('#_sizeinfo').val($('#setSize>div:eq(0) .selected').text())	
					}
				break;
				case '2'://附近门店量体
					var obj=$('#setSize>div:eq(1)');
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
				case '3'://预约上门量体
					var obj=$('#setSize>div:eq(2)');
					if(!obj.validate({acticle:true})){
						_scroll('#p1');
						return
					}else{
						var figure = [];
						figure += '{"ty":"3",';
						for(var i=0;i<obj.find('.data').length;i++){
							figure += '"'+obj.find('.data').eq(i).attr('name')+'":"'+obj.find('.data').eq(i).val()+'",';
						}
						figure = _order.substring(0,_order.length - 1)+'}';
					};
				break
				case '4'://标准尺码
					var obj=$('#setSize>div:eq(3)');
					if(!$('#setSize>div:eq(3)').validate({acticle:true})){
						_scroll('#p1');
						return
					}else{
						if($('#tixing .cur').length<=0){
							error('请选择体形')
							_scroll('#p1');
							return
						}else{
							var figure = [];
							figure += '{"ty":"4",';
							for(var i=0;i<obj.find('.input').length;i++){
								figure += '"'+obj.find('.input').eq(i).attr('name')+'":"'+obj.find('.input').eq(i).val()+'",';
							}
							figure += '"size":"'+$('#tixing .cur').attr('data-tixing')+'"}';
						}	
					};
			}
			//检测配送信息
			if($('#psInfo li.selected').attr('data-id')==1){
				var address = [];
				address += '{"ty":"1",';
				address += '"addr_id":"'+$('#address .psxxdq').attr('data-id')+'"}';
			}else{
				var address = [];
				address += '{"ty":"2",';
				address += '"city":"'+$('#selectCityAddress').val()+'",';
				address += '"'+$('#mendianAddress .qhselectss').attr('name')+'":"'+$('#mendianAddress .qhselectss').data('asid')+'"}';
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
//			console.log(_order);
			SetCookie('_order',_order);
			$.post("/order-create.html",{}, function(res){
			    var res = $.parseJSON(res);
			    if(res.done == true){
			        $("#mendian").html(res.retval.content);
			    }
			});

		})
	}
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
$('#useOver').unbind().bind('change',function(){
	var _val = $(this).val();
	var _money = $(this).data('money')
	var _amount = $(this).data('amount');
	
	if(!/^\d+(\.\d+)?$/.test(_val) || _val > _money){
        $(this).val('')
        return;
    }
	if(_val > _amount){
		var _val = _amount;
		$(this).val(_val)
        return;
	}
	
	var _also = parseFloat(_amount - _val).toFixed(2);
	var _remaining = parseFloat(_money - _val).toFixed(2);
	$('#also_amount').html('￥'+_also);
	$('#remaining').html('￥'+_remaining);
})

function SetCookie(name,value)//两个参数，一个是cookie的名子，一个是值
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
orderok.init()
