/**
* 试衣间 js
* @author xusl,xiao5 <xiao5.china@gmail.com>
* @version $Id: fitting.js 318 2015-04-27 00:21:26Z xiao5 $
* @copyright Copyright 2014 mfd.com
*/

function Fitting(setp, build, type, angle) {
	//步骤（款式：0，面料：1），体形（m1-m9），类别（男：m，女：w），角度（a-c）
    this.setp = setp;
    this.build = build;
    this.type = type;
    this.angle = angle;
	this.bg=1;
}

Fitting.prototype.url = function() {
    return "/upload/images/fitting/" + this.build + this.type + this.angle;
};

Fitting.prototype.init = function(t) {
	var $this=this;
	$('#revolve').click(function(){$this.rotate.call($this)});//旋转
	$('#reset').click(function(){$this.clear.call($this,$(this).attr('d'))});//重置
	$('#scene').click(function(){$this.scene.call($this)});
    $this.upDate(getCookie("fitting"));//初始化模特数据
    $this.history(getCookie("history"));//历史记录
    $this.bindevent();//绑定事件
	$this.ajaxPageSet();//重置ajax分页组件
    $this.ajaxPage(t);//初始化商品列表
	$this.filter();//筛选按钮绑定事件
};

Fitting.prototype.scene = function(){
	this.bg++
	if(this.bg>3){
		this.bg=1	
	}
	$('body').css({background:'url(/upload/images/fitting/bg/bg'+this.bg+'.jpg) no-repeat center top'})
}

Fitting.prototype.clear = function(t) {
	if(t!=null&&t!=''){
		setCookie('fitting',t);
		this.upDate(t);
		return
	}
    setCookie("tzId", "", 0);
    setCookie("fitting", "", 0);
    this.upDate();
};

Fitting.prototype.rotate = function() {
    switch (this.angle) {
      case "a/":
        this.angle = "b/";
        break;

      case "b/":
        this.angle = "a/";
        break;

//      case "c/":
//        this.angle = "a/";
//        break;
    }
    this.upDate(getCookie("fitting"));
};

Fitting.prototype.history = function(t) {
    t = t ? t :"{}";
    var obj = $.parseJSON(t), html = [];
    for (var x in obj) {
        var obj2 = $.parseJSON(obj[x]);
        html.push('<li id="d' + obj2.id + '" data-class="' + (obj2.aclass ? obj2.aclass :"taozhuang") + "\" data='" + obj[x] + "'><img src=\"" + this.url() + obj2.id + '.png" /></li>');
    }
    $(".fs_thmb_list").html(html.join("")).css({
        width:$(".fs_thmb_list li").length * 128,
        left:0
    });
};

Fitting.prototype.upDate = function(t) {
    $("#modelShow").css("background", "url(" + this.url() + "model.png) no-repeat center top");
    $("#item>li").removeClass("cur");
    if (t != null && t != "") {
	//单品数据[商品id，价格，名称，分类，分类id]
        var arr = eval(t), goodsList = [], goodsPrice = 0;
        for (var i = 0; i < arr.length; i++) {
            var imgsrc = this.url() + arr[i].id + ".png";
            $("#modelShow ." + arr[i].aclass + "").attr({
                src:imgsrc,
                "data-cookie":'{"id":"' + arr[i].id + '","price":"' + arr[i].price + '","name":"' + arr[i].name + '","aclass":"' + arr[i].aclass + '","cid":"' + arr[i].cid + '","f":"' + (arr[i].f?arr[i].f:'') + '"}'
            }).fadeIn(150);
            $("#r" + arr[i].id).addClass("cur");
            goodsPrice += Number(arr[i].price);
            goodsList.push("<li><span>" + arr[i].name);
            if (this.setp == 1) {
                goodsList.push("&nbsp;&nbsp;&nbsp;&nbsp;￥" + arr[i].price);
            }
            goodsList.push('</span><em data-class="' + arr[i].aclass + '">×</em></li>');
        }
        if (this.setp == 1) {
            $("#listInfo").html('<li class="tit">PURCHASE&nbsp;&nbsp;PRICE</li>' + goodsList.join(""));
            $("#priceNum").html("总价：￥" + goodsPrice);
        } else {
            $("#listInfo").html('<li class="tit">已穿服装</li>' + goodsList.join(""));
            $("#listInfo").addClass("setp-0");
        }
        //如果有西服让裤子在衬衣里面
        if (t.indexOf("xifu") > 0) {
            $(".xiku").css("z-index", 1);
            $(".chenyi").css("z-index", 0);
        } else {
            $(".xiku").css("z-index", 0);
            $(".chenyi").css("z-index", 1);
        }
    } else {
        $("#modelShow img").not(".loading").hide();
        $("#item .cur").removeClass("cur");
        $("#priceNum").html("");
        $("#listInfo").html("");
    }
    if (getCookie("tzId")) {
        $("#" + getCookie("tzId")).addClass("cur");
    }
};

Fitting.prototype.bindevent = function() {
	var $this=this;
    //点选
    $(document).on("click", "#item>li,.fs_thmb_list>li", function() {
        //缓存历史记录
        if (!$(this).parent().hasClass("fs_thmb_list")) {
            if (getCookie("history")) {
                var historyCookie = getCookie("history");
            } else {
                var historyCookie = "{}";
            }
            var history = $.parseJSON(historyCookie);
            if (!history.hasOwnProperty(this.id)) {
                history[this.id] = $(this).attr("data");
                setCookie("history", JSON.stringify(history));
                $this.history(getCookie("history"));
            }
        }
        //如果是套装
        if ($(this).attr("data-class") == "taozhuang") {
            if ($(this).hasClass("cur")) {
                $this.clear();
            } else {
                setCookie("tzId", this.id);
                $this.upDate($this.attr("data"));
                $this.setCookie();
            }
            return;
        }
        //点击单品清除套装
        if (getCookie("tzId")) {
            if ($this.setp != 1) {
                //如果不是切换面料
                $("#modelShow>img").hide();
                setCookie("tzId", "", 0);
            }
        }
        var data = $(this).attr("data"), classname = $(this).attr("data-class"), imgsrc = $this.url() + $(this).attr("id").substring(1) + ".png";
        $("[data-class=" + classname + "]").not(this).removeClass("cur");
        //确保图片加载完毕后更新
        if (!$(this).hasClass("cur")) {
            $(this).addClass("cur");
            $(".model .loading").show();
            $("<img />").bind("load", function() {
                setTimeout(function() {
                    $("#modelShow>." + classname + "").attr({
                        src:imgsrc,
                        "data-cookie":data
                    }).hide().fadeIn(400, function() {
                        $this.setCookie();
                    });
                    $(".model .loading").hide();
                }, 200);
            }).attr("src", imgsrc);
        } else {
            if ($("#fabric").val() != 1) {
                //如果是单品第二次点击脱掉单品
                $(this).removeClass("cur");
                $("#modelShow>." + classname + "").fadeOut(400, function() {
                    $this.setCookie();
                });
            }
        }
    });
    //列表删除
    $(document).on("click", "#listInfo em", function() {
        if (getCookie("tzId")) {
            if (window.confirm("您现在选择的是套装，删除一条就会去掉全部套装，是否继续？")) {
                $this.clear();
            }
        } else {
            $($("." + $(this).attr("data-class"))).fadeOut(400, function() {
                $this.setCookie();
            });
        }
    });
};

Fitting.prototype.setCookie = function() {
    //处理需要存储的数据
    var arr1 = [];
    $("#modelShow>img:visible").not(".loading").each(function(i, e) {
        var data1 = $(e).attr("data-cookie");
        arr1 ? arr1.push(data1) :"";
    });
    var c1 = "[" + arr1.join() + "]";
    setCookie("fitting", c1, 1);
    //更新模特数据
    this.upDate(c1);
};

Fitting.prototype.ajaxPage = function(t) {
	var $this=this;
    $("#item").bigPage({
        data:t,
        pageSize:6,
        toPage:1,
        cssWidgetIds:[ "ajaxpageBar3" ],
        callback:function() {
            $this.upDate(getCookie("fitting"));
			//增加单品筛选条件
			if($this.setp==0){
				var fil=[];
				$('.filter .select').each(function(i, e) {
					var str=$(e).children('dt').attr('data-value');
					if(str){
						fil.push(str)
					}
				});
				if(fil.length>0){
					if($('.filter>.hot').hasClass('cur')){fil.push('hot')};
					$('#item>li').each(function(i, e) {
						var d=$(e).attr('data');
						$(e).attr('data',d.substring(0,d.length-1)+',"f":"'+fil.join('|')+'"}')
					});
				}
			}	
        }
    });
};

Fitting.prototype.ajaxPageSet=function(){
	/**
	* @重置Ajax分页DOM结构/S
	* @套装数据[商品id，价格，名称，' '，分类id，套装封面图，[{单品数据},{单品数据}...]]
	* @单品数据[商品id，价格，名称，分类，分类id]
	* @面料数据[商品id，价格，名称，分类，分类id, 封面图]
	*/
	var $this=this;
	$.bigPage.addCssWidget({
		id:"appendToTable",
		format :function($table){
			var subData = $table.getSubData();
			var $tBody = $table;
			var trsArray = [];
			for(var i=0;i<subData.length;i++){
				var cellVaues = subData[i],
					trArray =[],
					mSrc=cellVaues[5]?cellVaues[5]:$this.url()+cellVaues[0]+".png";
					if(cellVaues.length==7){
						var data=JSON.stringify(cellVaues[6]);
					}else{
						var data='{"id":"'+cellVaues[0]+'","price":"'+cellVaues[1]+'","name":"'+cellVaues[2]+'","aclass":"'+cellVaues[3]+'","cid":"'+cellVaues[4]+'"}';
					}
				trArray.push("<li data='"+data+"' data-class='"+(cellVaues[3]?cellVaues[3]:'taozhuang')+"' id='r"+cellVaues[0]+"'>");
				trArray.push('<img width="93" height="183" src="'+mSrc+'">');
				trArray.push("</li>");
				trsArray.push(trArray.join(""));
			}
			$tBody.html(trsArray.join(""));
			//alert(trsArray[0])
		}
	});
	/*--重置Ajax分页DOM结构/E--*/
	/*--重置分页条组件/S--*/
	$.bigPage.addCssWidget({
		id:"ajaxpageBar3",
		format :function($table){
			var prevClass = "current prev";
			var nextClass = "current next";
			if($table.config.toPage > 1){
				prevClass = "prev"
			} 
			if($table.config.toPage < $table.config.totalPage){
				nextClass = "next";
			}
			var $footDiv = $table.siblings("div[ajaxpage='foot']");
			if($footDiv.length <= 0){
				var footPageHtml = '<div ajaxpage="foot" class="bigpage"><a class="' + prevClass +'" href="javascript:void(0)" ajaxpage="prev" title="上一页"></a><a class="' + nextClass + '" href="javascript:void(0)" ajaxpage="next" title="下一页"></a>共<span ajaxpage="count" >0</span>页，当前第<span ajaxpage="current"></span>页</div>';
				if($table.config.position == "up"){
					$table.before(footPageHtml);
				}else if($table.config.position == "both"){
					$table.before(footPageHtml);
					$table.after(footPageHtml);
				}else{
					$table.after(footPageHtml);
				}
				
				$footDiv = $table.siblings("div[ajaxpage='foot']");
				$footDiv.data("table",$table);
				
				//a链接注册事件
				$footDiv.find("a").click(function(){
					var $a = $(this);
					var table2 = $a.parent().data("table");
					var opType = $a.attr("ajaxpage");
					if(opType == "prev"){
						table2.prevPage();
					}else if(opType == "next"){
						table2.nextPage();
					}
				});
			}
			$footDiv.find("a").each(function(i,v){
				var opType = $(v).attr("ajaxpage");
				if(opType == "first" || opType == "prev"){
					$(v).removeClass().addClass(prevClass);
				}else if(opType == "next" || opType == "last"){
					$(v).removeClass().addClass(nextClass);
				}
			})
			$footDiv.find("span[ajaxpage='count']").html($table.config.totalPage);
			$footDiv.find("span[ajaxpage='current']").html($table.config.toPage);
			
		}
	});
	/*--重置分页条组件/E--*/
}

Fitting.prototype.filter=function(){
	var $this=this;
	$('.filter>.search,.filter>.hot').click(function() {
		var sval = '';
		$(".filter>.select").each(function() {
			var data=$(this).children('dt').attr('data-value');
			sval += (data?data+',':'');
		});
		var stype = $("#st").val();
		var datas={sval:sval,st:stype};
		var hot=$(this).hasClass('hot');
		if(hot){
			datas=	{sval:sval,st:stype,hot:1}
		}
		if(sval){
			$.ajax({
				type: "POST",
				url: "/index.php/fitting-jsonData-2-"+$("#cid").val()+".html",
				data: datas,
				dataType: "json",
				success: function(res){
					if(res.state){
						if(hot){
							$('.filter>.hot').addClass('cur')
						}else{
							$('.filter>.hot').removeClass('cur')	
						}
						$this.ajaxPage(res.data);
					}
				}
			});
		}
	});
}


/*--cookie操作/S--*/
function setCookie(c_name,value,expiredays){
    var exdate=new Date()
    exdate.setDate(exdate.getDate()+expiredays)
    document.cookie=c_name+ "=" +value+
    ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())+';path=/';
}
function getCookie(c_name){
    if (document.cookie.length>0){
        c_start=document.cookie.indexOf(c_name + "=")
        if (c_start!=-1){ 
            c_start=c_start + c_name.length+1 
            c_end=document.cookie.indexOf(";",c_start)
            if (c_end==-1) c_end=document.cookie.length
            return unescape(document.cookie.substring(c_start,c_end))
        } 
    }
    return ""
}
/*--cookie操作/E--*/

/*--分类筛选模拟select/S--*/
$('.select').click(function(e){
	$(this).siblings('.select').find('dd').hide();
	var obj=$(this).children('dd');
	if(obj.is(':visible')){
		obj.fadeOut('fast');
	}else{
		obj.css('left',-(50-$(this).width()/2)).fadeIn('fast');	
	}
	return false;
});
$('.select dd span').click(function(){
	$(this).parents('dd').prev('dt').text($(this).text()).attr('data-value',$(this).attr('data-value'));
	$(this).parents('dd').fadeOut('fast');
	//选择面料
	if($(this).attr('data-df')&&($(this).attr('data-df')!=$(this).attr('data-value'))){
		window.location.href="/index.php/fitting-n-"+$(this).attr('data-value')+".html";//页面跳转并传参
	}
	return false;
});
$(document).click(function(){
	$('.select>dd').hide();	
})
/*--分类筛选模拟select/E--*/

$('#nextBtnf').bind('click', function() {
	var f = getCookie('fitting');
	setCookie('fdata_f',f,1);
	
});

//下一步按钮
$('#nextBtn').bind('click', function() {
	var f = getCookie('fitting');
	 if(f!=null && f!=""){
         var arr=eval(f),goodsId='',goodsPrice=0;
         for(var i=0;i<arr.length;i++){
             goodsPrice+=Number(arr[i].price);
             goodsId+=arr[i].id+'-';
         }
         var height = '175.2';	//身高
         var weight = '65.2';	//体重
         $.ajax({
             type: "POST",
             url: "/index.php/cart-add.html",
             data: {ids:goodsId.substring(0,goodsId.length-1),total:goodsPrice,h:height,w:weight},
             dataType: "json",
             success: function(res){
             	//TODO 
             }
         });
     }
});

/*--更改面料提示/S--*/
if($('.category>select').length>0){
	layer.tips('尊敬的用户，您好！您定制的产品还没有更换面料，如需更换，请点击左边下拉按钮进行切换面料','.category>select', {
		style: ['background:#000;opacity:0.7;color:#fff;padding:10px 28px 10px 13px', '#000'],
		maxWidth:185,
		guide:1,
		time:60,
		closeBtn:[0, true]
	});
}
/*--更改面料提示/E--*/

/*--分享/S--*/
$(document).on('click','.btnBox .share',function(){
	layer.tips('<p style="margin-bottom:5px">分享到：</p><span class="sina">sina</span><span class="weixin">weixin</span>',this, {
		style: ['background:#fff; color:#000;padding-bottom:10px;','#fff'],
		maxWidth:185,
		guide:2,
		closeBtn:[0, true]
	});
	return false;
})
$(document).on('click','.xubox_tipsMsg>span',function(){
	var cla=this.className,
		url='',
		title=document.title,
		href=window.location.href;
	if(cla=='weixin'){
		$.layer({
			type: 1,
			title: '分享到微信朋友圈',
			area: ['305px', '330px'],
			moveType: 1,
			page: {html: '<div style="padding-left:22px;text-align:center;color:#666"><img src="http://qr.liantu.com/api.php?text='+href+'" alt="微信二维码" width="260" height="260"><br>使用微信“扫一扫”即可将网页分享到我的朋友圈</div>'}
		});	
		return	
	}
	switch(cla){
		case 'sina':
			url="http://v.t.sina.com.cn/share/share.php?appkey=3273986941&title="+title+"&url="+href;
			break;
	}
	window.open(url,"","height=500, width=600");
});
/*--分享/E--*/

/*--历史记录/S--*/
!function history(){
	$viewport = $('.fs_thmb_viewport');
	$viewport.width($(window).width()-80)
		.mouseenter(function () {
			var h = jQuery(this).width(),
				tlist = jQuery('.fs_thmb_list');
			window._s_top = parseInt(tlist.css('left'));
			window._sh = setInterval(function () {
				if (
					(window._s_top >= 0 && window._sp > 0) ||
						(window._s_top < 0 && window._s_top > -(tlist.width() - h)) ||
						(window._sp < 0 && window._s_top <= -(tlist.width() - h))
					) {
					var sign = (window._sp >= 0),
						val = Math.pow(window._sp * 15, 2),
						val = (sign) ? val : -val;
					window._s_top -= val;
					if (window._s_top > 0) {
						window._s_top = 0;
					}
					if (window._s_top < -(tlist.width() - h)) {
						window._s_top = -(tlist.width() - h);
					}
					if (jQuery('.fs_thmb_list').width() > $viewport.width()) {
						tlist.stop().animate({
							left: window._s_top
						}, 500);
					}
				}
			}, 100);
		}).mouseleave(function () {
			clearInterval(window._sh);
		}).mousemove(function (e) {
			y = e.pageX;
			h = $(this).width(),
				p = y / h;
	
			if (y > ($(window).width()) * 0.8) {
				window._sp = Math.round((p - 0.5) * 50) / 50;
			}
			else if (y < (jQuery(window).width()) * 0.2) {
				window._sp = Math.round((p - 0.5) * 50) / 50;
			}
			else {
				window._sp = 0
			}
		});
	$(window).resize(function () {
		$('.fs_thmb_viewport').width(jQuery(window).width()-80);
		$('.fs_thmb_list').css('left', '0px');
	});
	$('.fs_thmb_list').css({'left':'0px',width:$('.fs_thmb_list li').length*128});
}()
/*--历史记录/E--*/

/*--个性定制/S--*/
$('#select_font').click(function(){
	$('#select_font_list').slideDown('fast');
	return false	
});
$('#select_font_list li').click(function(){
	$('#select_font').html($(this).html()).attr('p_value',$(this).attr('p_value'));
	$('#select_font_list').slideUp('fast');	
})
$('#select_color').click(function(){
	$('#select_color_list').slideDown('fast');
	return false	
});
$('#select_color_list li').click(function(){
	$('#select_color').html($(this).html()).attr('p_value',$(this).attr('p_value'));
	$('#select_color_list').slideUp('fast');	
})

$(document).click(function(){
	$('#select_font_list,#select_color_list').hide();	
})

$('.itemBox>li').click(function(){
	$(this).siblings('li').removeClass('cur').end().addClass('cur');	
})
/*--个性定制/E--*/























