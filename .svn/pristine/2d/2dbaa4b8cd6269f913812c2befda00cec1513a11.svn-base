var $_CNC_PHOTO={
      par_div: '#custom',
	  chil   : '.cus_l_menu',
	  chil2  : '.cus_list',
	  chil3  : '.cus_mianl',
	  chil4  : '.styles_menu',
	  chil5 : '.cus_sty_box',
	  chil6 : '.sty',
	  Find  : '#sty_box p',
	  KouObj  : '.kouzi_sel',
	  liliao : '.cus_lil',
	  Oweizi : '.weizi_style',
	  Diy_src: '#diy-img',
      init : function()
     {
	    var t = this;
	    t.url = $('.cus_l').data('imgurl');
	  
	    t.direction = '10004/';
	    t.fabric = $('.cus_mianl div').filter('.on').data('fabric')+'/';
		t.Click(t);
		t.ChilShow(t);
		t.ChildStyles(t);
		t.Kouzi(t);
		t.liliaoborder(t);
		t.switchImg(t);
	  },
	  
	  Click:function(t){
	     $(t.chil).find('ul li').click(function(){
				
				if($(this).attr("class") == "lihover"){
				   return;	
				}
							
			 //先清除所有的样式								
			  $(t.chil).find('ul li').each(function(){
		       $(this).removeClass('lihover').stop().animate({'width':'91'},500);
			   $(this).find("b").stop().animate({'opacity':'0'},800).hide();
		      });
			  
			  //为当前的添加样式
			  //alert($(this).attr('class'));

			    $(this).addClass('lihover').stop().animate({'width':'126'},500);
			    $(this).find("b").stop().animate({'opacity':'1'},800).show();
				
			 //显示某个对应的层
			 
		     $(t.chil2).find('ul li').each(function(){
			      $(this).stop().animate({'opacity':'0'},500).hide();
			 });   
			 $(t.chil2).find('ul li').eq($(this).index()).stop().animate({'opacity':'1'},500).show();	
			 
			 
				 $('.cus_diy_list').height(410).jScrollPane({
						showArrows: true,
						animateScroll: true
					 });			 
			 
			 
		 }); 
	  },
	  switchImg : function(t){
	  	var label = arguments[1] ? arguments[1] : 0;
	  		t.setTotal();
	  	 	//点击面料 更换大图
		   	var sequence = $('.cus_l').data('sequence');
		   	var a = new Array();
		   	a[$('.kouzi_sel div').filter('.on').data('alias')] = $('.kouzi_sel div').filter('.on').data('pid')
		   	$('.cus_sty_list_box div').each(function(i,o){
        		var $o=$(this);
        		a[$o.filter('.on').data('alias')] = $o.filter('.on').data('pid')
    		});
    		var img = "";  
    		$.each(sequence,function(n,value) {   
	           if(a[value]){
	           	img += value+a[value]+"-";
	           }       
           });  
            $('input[name=imgcode]').val(img+$('.cus_mianl div').filter('.on').data('alias')+$('.cus_mianl div').filter('.on').data('fabric'));
    		img += $('.cus_mianl div').filter('.on').data('alias')+$('.cus_mianl div').filter('.on').data('fabric')+'.jpg';
    		
    		if(label) $('.diy_bigimg img').attr('src',t.url+t.fabric+t.direction+img);
    		 
	  },
	  
	  ChilShow : function(t){
		   $(".cus_mianl div").click(function(){
		  
        		t.switchImg(t,1);
    		 
				if(!$(this).hasClass("on")){
					$(this).addClass("on").siblings().removeClass("on");
				}
			})			 
	  },
	
	  
	  liliaoborder : function(t){
		   $(".cus_lil div").click(function(){
				if(!$(this).hasClass("on")){
					$(this).addClass("on").siblings().removeClass("on");
				}
			})	
	  },
	   clearNoNum : function (obj){
        //先把非数字的都替换掉，除了数字和.
        obj.value = obj.value.replace(/[^\d.]/g,"");
        //必须保证第一个为数字而不是.
        obj.value = obj.value.replace(/^\./g,"");
        //保证只有出现一个.而没有多个.
        obj.value = obj.value.replace(/\.{2,}/g,".");
        //保证.只出现一次，而不能出现两次以上
        obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
        this.setTotal()
    },
    
	 setTotal : function (){ 
	 	
//	 	this.getCode(1);
	 	
	 	var label = arguments[0] ? arguments[0] : 0;
	 	var diy = 0;
	 	diy = $('.diy_size div').filter('.on').data('diy')
	 	
	 	/********************************组件信息*************************************/
    	var price  = $('.cus_l').data('dprice'); //商品价格
  	    var oFee = 0;

    	/********************************计算价格**********************************/
    	//面料价格
    	price += $('.cus_mianl div').filter('.on').data('fprice') * $('.cus_l').data('consumption').fabric_m;

    	//里料价格
    	price += $('.cus_lil div').filter('.on').data('mprice') ? $('.cus_lil div').filter('.on').data('mprice') * $('.cus_l').data('consumption').lining_m : 0;
    	//工艺费
        var objs = eval($('input[name=process]').val());
		$.each( objs, function(index, value){ 
			if(value.price > 0){
				price += value.price;  
			}
		}); 
    	
    	//身高体重超出部份费用
    	if(diy == 1){
    		var weight = $('input[name=weight]').val();
    		var height = $('input[name=height]').val();
	    	oFee = this._figure_body(height, weight);
	    	if(oFee){
	    		price += $('.cus_mianl div').filter('.on').data('fprice') * oFee;
	    	}
    	}
    	price = Math.round(price);
    	
		if(label == 1) return price;
			
		$(".cus_cuont font").html(this.formatMoney(price)); 
		
	},
	getCode : function(){
		var label = arguments[0] ? arguments[0] : 0;
		var code = '';
		var tips = '';
		
		$('.cus_mianl div').each(function(i,o){
        		var $o=$(this);
        		if($o.filter('.on').data('tcate')){
        			code += $o.filter('.on').data('tcate')+':'+$o.filter('.on').data('pid')+'|';
        			tips += "<font>面料：</font>"+$o.filter('.on').data('fabric')+"<br>";
        		}
        		
    	});
    	$('.cus_lil div').each(function(i,o){
        		var $o=$(this);
        		if($o.filter('.on').data('tcate')){
        		code += $o.filter('.on').data('tcate')+':'+$o.filter('.on').data('pid')+'|';
        		tips += "<font>里料：</font>"+$o.filter('.on').data('material')+"<br>";
        		}
        		
    	});
    	tips += "<font>款式：</font>";
	 	$('.cus_sty_list_box div').each(function(i,o){
        		var $o=$(this);
        		if($o.filter('.on').data('tcate')){
        			code += $o.filter('.on').data('tcate')+':'+$o.filter('.on').data('pid')+'|';
        			tips += $o.filter('.on').data('process')+" ";
        		}
        		
        		
    	});
    	tips += "<br>";
    	tips += "<font>工艺：</font>";
    	 var objs = eval($('input[name=process]').val());
		$.each( objs, function(index, value){ 
				tips += value.part_name+" ";  
		}); 
    	tips += "<br>";
    	$('.kouzi_sel div').each(function(i,o){
        		var $o=$(this);
        		
        		if($o.filter('.on').data('tcate')){
        			code += $o.filter('.on').data('tcate')+':'+$o.filter('.on').data('pid')+'|';
        			tips += "<font>扣子：</font>"+$o.filter('.on').data('buttons')+"<br>";
        		}
        		
        		
    	});
    	
    	tips += '<font>签名：</font>'+$("#TextArea1").val();   
    	
    	$('.zt_style div p').each(function(i,o){
        		var $o=$(this);
        		if($o.filter('.on').data('tcate')){
        			code += $o.filter('.on').data('tcate')+':'+$o.filter('.on').data('pid')+'|';
        			if($o.filter('.on').data('font')){
        				tips += $o.filter('.on').data('font')+" ";
        			}
        			
        		}
        		
    	});
    	
    	$('.yanse div p').each(function(i,o){
        		var $o=$(this);
        		if($o.filter('.on').data('tcate')){
        			code += $o.filter('.on').data('tcate')+':'+$o.filter('.on').data('pid')+'|';
        			tips += $o.filter('.on').data('color')+" ";
        		}
        		
    	});
    	
    	$('.location p').each(function(i,o){
        		var $o=$(this);
        		if($o.filter('.on').data('tcate')){
        			code += $o.filter('.on').data('tcate')+':'+$o.filter('.on').data('pid')+'|';
        			tips += $o.filter('.on').data('location')+" ";
        		}
        		
        		
    	});
    	
    	if(label == 1)  $(".tanchu_bg").html(tips); 
	 return code;
	},
	addToCartMobile : function(gid,img){
	 var items = '';
	 	 items = this.getCode();
	 if(!items) return;
	 
	 var is_diy = 0;
	 
		 is_diy = $('.diy_size div').filter('.on').data('diy') == 1?1:0;
	 
	 var size = '';
	     size = $(".size_box font").text();
	     
	 if(!size && !is_diy){
	 	$.rc.tip({content:"请选择尺码", icon:"error"});
	 	return ;
	 }
	 
	 var url = "/index.php/cart-add.html";	 
	 var data = {};
	 data.goods_id  = gid;
	 data.rec_id    = '';
	 data.is_diy    = $('.diy_size div').filter('.on').data('diy') == 1?1:0;
	 data.items     = items;
	 data.height    = $('input[name=height]').val();
	 data.weight    = $('input[name=weight]').val();
	 data.spec      = $(".size_box font").text();
	 data.emb_con   = $("#TextArea1").val();
	 data.total		= this.setTotal(1);
	 data.imgcode   =   $('input[name=imgcode]').val();
//	 data.imgcode   =  $('.diy_bigimg img').attr('src');
//	 console.log(data);
	 $.post(url, data,function(res){
		 var res = $.evalJSON(res);
		 if(res.done == false){
			 $.rc.tip({content:res.msg, icon:"error"});
			 return false;
		 }else{
			 try{
			 	$("#cartGoodsNum font").html(res.retval.goods_num);
			 }catch(e){}
			// $.rc.tip({content:'添加购物车成功'});
			location.href="/index.php/cart.html";
		 }
	 })
	},
	formatMoney : function (number, places, symbol, thousand, decimal) {
        number = number || 0;
        places = !isNaN(places = Math.abs(places)) ? places : 2;
        symbol = symbol !== undefined ? symbol : "¥";
        thousand = thousand || ",";
        decimal = decimal || ".";
        var negative = number < 0 ? "-" : "",
            i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
    },
	//根据量体数据计算倍数
	    _figure_body : function (height, weight){
	    	 var m = 0;
	    	if(height >= 191 || (weight >= 101 && weight <= 120)){
	    		m = 1.5;
	    	}
	    	
	    	if(height > 200 || weight >= 121){
	    		m = 2;
	    	}
	    	
	    	return m;
	    },
	  ChildStyles : function(t){
		  
		  $(t.chil5).children().each(function(){
			  
				  $(this).find("span").click(function(){
                      
					  if(!$(this).next(".cus_sty_list_box").is(":visible")){
						  $(t.chil5).children().each(function(){
							  $(this).find("span").removeClass("hover_span");
							  $(this).find(".cus_sty_list_box").slideUp(100);
						  });
						  $(this).addClass("hover_span");
						  $(this).next(".cus_sty_list_box").slideDown(100);
					}
					  
				  });
				  
				  
				 $(".list_div div").click(function(){
				 	//里料选择
				 	
					  if(!$(this).hasClass("on")){
					  	var tp = $(this).data('type')
					  	if ('material' == tp){
					  		var img = $(this).data('img')
					  		 $('.diy_bigimg img').attr('src',t.url+t.fabric+img);
					  	}
					  	
					  	if('buttons' == tp){
					  		t.switchImg(t,1);
					  	}
					  	
//					  	console.log(tp);
					  	
						  $(this).addClass("on").siblings().removeClass("on");
					  }
				  })					  
	
          });
	   
	  },
	  
	  
	  Kouzi : function(t){
        $('.list_div div').click(function(){
		  if(!$(this).has('on')){
			  $(this).addClass('on').siblings().removeClass('on');  
			}
	    })
	  }
	  
	
  }
  

$(function(){
	
   $(".img_div p").click(function(){
		if(!$(this).hasClass("on")){
			$(this).addClass("on").siblings().removeClass("on");
		}
	});
	
   $(".location p").click(function(){
		if(!$(this).hasClass("on")){
			$(this).addClass("on").siblings().removeClass("on");
		}
	});	
	
	$(".sm").mouseover(function(){
		$_CNC_PHOTO.getCode(1);
		$(".tanchu_box").stop(true,true).fadeIn(700);
		setTimeout("$('.tanchu_box').hide()",5000);
	});
	$(".sm").mouseleave(function(){
	    $(".tanchu_box").stop(true,true).fadeOut(700);
	});	
	
	
		
		
})










