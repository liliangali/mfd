$(function(){
	//购物车收货地址滑过
	$(".address_item").hover(function(){
		if(!$(this).hasClass("selected")){
			$(this).addClass("focus");
		}
	},function(){
		$(this).removeClass("focus")
	})
	//购物车收货地址选择
	$(".address_item").click(function(){
		if(!$(this).hasClass("selected")){
			$(this).addClass("selected").siblings().removeClass("selected");
		}
	})
	//修改收货地址
	$('#address .caozuoBtn .edit').click(function(){
		$.post("/my_address-getAddress.html",{addr_id:$(this).parents('li').attr('data-id')}, function(res){
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

									 var dataPara = getFormJson("#address_form");
									$.post("/my_address-uAddress.html",{data:dataPara}, function(res){
									    var res = $.parseJSON(res);
									    if(res.done == true){
									    	$("#address").html(res.retval);
									    	layer.close(lay)
									    	//orderok.init()
									    	location.reload();
									    }else{
									    	alert(res.msg);
									    }
									});
							});
						}
					});
			    }
			})
	})
	
	//ns add 设置默认地址
	$('#address .caozuoBtn .def').click(function(){
			if(window.confirm('确定要设置默认？')){
				var address_id = $(this).parents('li').attr('data-id');
					$.post("/my_address-def_addr.html",{id:address_id}, function(res){
					    var res = $.parseJSON(res);
					    if(res.done == true){
					    	alert(res.retval);
					    	history.go(0) 
					    }
					});
			}
	})

	//删除地址
		$('#address .caozuoBtn .del').click(function(){
			
			if(window.confirm('确定要删除？')){
				var address_id = $(this).parents('li').attr('data-id');
				$(this).parents('li').fadeOut('fast',function(){
					$.post("/my_address-dropAddr.html",{id:address_id}, function(res){
					    var res = $.parseJSON(res);
					    if(res.done == true){
					    	$("#address").html(res.retval);
					    	$(this).remove();
					    	//orderok.init();
					    	location.reload();
					    }
					});
					
					
				})	
			}
		});
		//添加
	$('#addAddress').click(function(){
			$.post("/my_address-getAddress.html",{addr_id:'0'}, function(res){
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

									 var dataPara = getFormJson("#address_form");
									$.post("/my_address-address.html",{data:dataPara}, function(res){
									    var res = $.parseJSON(res);
									    if(res.done == true){
									    	 $("#address").html(res.retval);
									    	layer.close(lay);
									    	//orderok.init();
									    	location.reload();
									    }else{
									    	alert(res.msg);
									    }
									});

							});
						}
					});
			    }
			})
		})

	//删除宠物
		$('#pet .caozuoBtn .del').click(function(){
			
			if(window.confirm('确定要删除？')){
				var pet_id = $(this).parents('li').attr('data-id');
				$(this).parents('li').fadeOut('fast',function(){
					$.post("/my_pet-dropPet.html",{id:pet_id}, function(res){
					    var res = $.parseJSON(res);
					    if(res.done == true){
					    	$("#pet").html(res.retval);
					    	$(this).remove();
					    	//orderok.init();
					    	location.reload();
					    }
					});
					
					
				})	
			}
		});
	//修改宠物
	$('#pet .caozuoBtn .edit').click(function(){
		$.post("/my_pet-getPet.html",{pet_id:$(this).parents('li').attr('data-id')}, function(res){
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
									 var dataPara = getFormJson("#pet_form");
									$.post("/my_pet-uPet.html",{data:dataPara}, function(res){
									    var res = $.parseJSON(res);
									    if(res.done == true){
									    	$("#pet").html(res.retval);
									    	layer.close(lay)
									    	//orderok.init()
									    	location.reload();
									    }else{
									    	alert(res.msg);
									    }
									});
							});
						}
					});
			    }
			})
	})

	
	//添加宠物
	$('#addPet').click(function(){
			$.post("/my_pet-getPet.html",{pet_id:'0'}, function(res){
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
									var dataPara = getFormJson("#pet_form");
									$.post("/my_pet-pet.html",{data:dataPara}, function(res){
									    var res = $.parseJSON(res);
									    if(res.done == true){
									    	 $("#pet").html(res.retval);
									    	layer.close(lay);
									    	//orderok.init();
									    	location.reload();
									    }else{
									    	alert(res.msg);
									    }
									});

							});
						}
					});
			    }
			})
		})


	$(".cancel").click(function(){
		$(".add_edit_box,#bg").hide();
	})
	//展开更多收货地址
	$(".more_btn").click(function(){
		var Ix = $(this).attr("index");
		if(Ix == "0"){
		$(".address_list dl:gt(2)").show();
		$(this).html("收起").attr("index","1");
		}else if(Ix == "1"){
		$(".address_list dl:gt(2)").hide();
		$(this).html("展开").attr("index","0");
		}
	})
	//核对订单信息选择信息切换
	$(".choice_list li").click(function(){
		$(this).addClass("cur").siblings().removeClass("cur");
	})
	//付款提示弹层
	$("#pay_btn").click(function(){
		var winHeight = $(window).height();
		var boxHeight = $("#pop_payment").outerHeight();
		_postop = $(window).scrollTop() + (winHeight-boxHeight)/2;
		$("#pop_payment").css("top",_postop).show();
		$("#bg").show();
	})
	$(".close").click(function(){
		$("#bg,#pop_payment").hide();
	})
})



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