
//弹层登录
var loginIn={
	callback:null,
	show:function(fn){
		loginIn.callback=fn;
	}
};
function Diy(opt){
	//this.data=opt.data;//缓存数据
	this.rule=opt.rule;//缓存规则
	this.mid=parseInt(opt.mid);//缓存会员ID
	this.cid=opt.cid;//缓存一级分类ID
	this.sid=opt.sid;//缓存二级分类ID
	this.gxmaxnum=parseInt(opt.gxmaxnum);//功效选择上限
    this.effectprice=opt.effectprice;//功效均价
    this.selectdata={};//缓存值
    this.quanzhong=opt.quanzhong;//缓存二级分类ID
    this.quanzhong_son=opt.quanzhong_son;//缓存二级分类ID
}
Diy.prototype={
	init:function(){
		_diy=this;
        _diy.getselectdata();
        _diy.bind();
        _diy.run();
        _diy.js();
        _diy.diyShare();
	},
	//检测登录状态
	checkLogin:function(callback){
		if(_diy.mid===0){
                luck.open({
                        title:'请先登录',
                        content:'<iframe width="420" height="550" style="display:block" src="/view/sc-utf-8/mall/default/user/ajax_login.html" frameborder="0"></iframe>',
                        addclass:'cotte-luck addCartlayer'
                });

                loginIn.show(function(res){
                        if(res.status>0){
                                _diy.mid=1;
                            setTimeout(callback,120);
                        }
                });
		}else{
			callback();
		}
	},
        getselectdata:function(){
            // 获取当前选中
            this.selectdata['diynav'] = $('.cdiy_nav .on').index();
//            alert(this.selectdata.diynav);
        },
        bind:function(){
            // 元素选择
            $('.ul_list li').live('click',function(){
// alert('aa11')
                var stype = $(this).parents('.ul_list').attr('s-type'); // 是否多选 1多选 其他单选
                if(stype === '1'){
                    if($(this).is('.on') === true){

                        $(this).removeClass('on');
                    }else{

                        if($(this).siblings('.on').length < _diy.gxmaxnum ){
                            $(this).addClass('on');
                        }
                        else
                        {
                            alert('亲，最多选择3种功效哦！');
                            return;
                            // layer.open({
                            //     content: '亲，最多选择3种功效哦！'
                            //     ,skin: 'msg'
                            //     ,time: 2 //2秒后自动关闭
                            // });

                        }
                    }
//                if($(this).parents('.swiper-slide').find('.on').html() === undefined){ // length
//                    $(this).addClass('on');
//                }
                }else{
   // alert('aa')
                    $(this).addClass('on').siblings().removeClass('on');
                    // $(this).addClass('on').siblings().each(function(i){
                    //     alert($(this).text())
                    // });
                }
                _diy.runrule();
                //_diy.js();
            });

            //加入购物车
            $('.addCart').on({
                "click":function() {
                    $("#jiesuan").text("结算中...");
                    _diy.checkLogin(function(){
                        var qid=$quanzhong_son;
                        var postObj = {};
                        //犬种选择
                        postObj[_diy.quanzhong] = _diy.quanzhong_son;
                        var is_ch = 1;
                        $('.ul_list').each(function(i){
                            if($(this).find('.on').length===0){
//                             alert($('.swiper-slide').eq(i).attr('data-name') + ' 没有选择');
                                is_ch = 0;
                                 // alert($('.ul_list').eq(i).attr('data-name') + ' 没有选择')
                                luck.poptip({
                                    con:$('.ul_list').eq(i).attr('data-name') + ' 没有选择'
                                });
                                // ;
                                // luck.open({
                                //     title:'请先登录',
                                //     content:$('.swiper-slide').eq(i).attr('data-name') + ' 没有选择',
                                //     addclass:'cotte-luck addCartlayer'
                                // });

                                return false;
                            }
                            if($(this).attr('s-type') === '1'){
                                var erj='';
                                var erjk='';
                                $(this).find('.on').each(function(){
                                    erj += $(this).attr('sid') + '|';
                                    erjk=$(this).attr('pid');
                                });
                                erj=erj.substring(0,erj.length-1);
// alert(erj);
                                postObj[erjk] = JSON.stringify(erj);
                            }else{
                                postObj[$(this).find('.on').attr('pid')] = $(this).find('.on').attr('sid');
                            }

                        });

                        if(!is_ch)
                        {
                            return false;
                        }
                        // var token = GetQueryString("token");
                        // token = token === null ? '' : token ;
                        var goods_num = $(".goods_num").val();
                        var data=[];
                        var dog_name = $("input[name=dog_name]").val();
                        var dog_date = $("input[name=dog_date]").val();
                        var dog_desc = $("textarea[name=dog_desc]").val();


                        $.ajax({
                            url:'/cart-add.html',
                            type:'POST',
                            timeout:5000,
                            dataType:"json",
                            data:{
                                type:'fdiy',
                                num:goods_num,
                                dog_name:dog_name,
                                dog_date:dog_date,
                                dog_desc:dog_desc,
                                params:JSON.stringify(postObj),//配置
                                fpic:$('.file_img').attr('src') === '/diy/images/sytu.png' ? '' : $('.file_img').attr('src'),//个性化标签设置
                                cid:_diy.cid+','+_diy.sid+','+qid,
                                // token:token
                            },
                            success:function(data){
                                //$("p").unbind("click");
                                if(data.done){
                                    window.location.href='/cart.html';
                                }else{
                                    $("#jiesuan").text("去结算");
                                    // $('#sizeSelect').modal('hide');
                                    //_diy.alert(data.msg);
                                    luck.poptip({
                                        con:data.msg
                                    })

                                }
                            },
                            error:function(){
                                luck.poptip({
                                    con:'加入购物车请求出错'
                                })
                            }
                        });

                    });
                }
            });
            //加入购物车
            $('.addCart1').on({
                "click":function() {
                    $("#jiesuan").text("结算中...");
                    _diy.checkLogin(function(){
                        var qid=$quanzhong_son;
                        var postObj = {};
                        //犬种选择
                        postObj[_diy.quanzhong] = _diy.quanzhong_son;
                        var is_ch = 1;
                        $('.ul_list').each(function(i){
                            if($(this).find('.on').length===0){
//                             alert($('.swiper-slide').eq(i).attr('data-name') + ' 没有选择');
                                is_ch = 0;
                                // alert($('.ul_list').eq(i).attr('data-name') + ' 没有选择')
                                luck.poptip({
                                    con:$('.ul_list').eq(i).attr('data-name') + ' 没有选择'
                                });
                                // ;
                                // luck.open({
                                //     title:'请先登录',
                                //     content:$('.swiper-slide').eq(i).attr('data-name') + ' 没有选择',
                                //     addclass:'cotte-luck addCartlayer'
                                // });

                                return false;
                            }
                            if($(this).attr('s-type') === '1'){
                                var erj='';
                                var erjk='';
                                $(this).find('.on').each(function(){
                                    erj += $(this).attr('sid') + '|';
                                    erjk=$(this).attr('pid');
                                });
                                erj=erj.substring(0,erj.length-1);
// alert(erj);
                                postObj[erjk] = JSON.stringify(erj);
                            }else{
                                postObj[$(this).find('.on').attr('pid')] = $(this).find('.on').attr('sid');
                            }

                        });

                        if(!is_ch)
                        {
                            return false;
                        }
                        // var token = GetQueryString("token");
                        // token = token === null ? '' : token ;
                        var goods_num = $(".goods_num").val();
                        var data=[];
                        var dog_name = $("input[name=dog_name]").val();
                        var dog_date = $("input[name=dog_date]").val();
                        var dog_desc = $("textarea[name=dog_desc]").val();


                        $.ajax({
                            url:'/cart-add.html',
                            type:'POST',
                            timeout:5000,
                            dataType:"json",
                            data:{
                                type:'fdiy',
                                num:goods_num,
                                dog_name:dog_name,
                                dog_date:dog_date,
                                dog_desc:dog_desc,
                                params:JSON.stringify(postObj),//配置
                                fpic:$('.file_img').attr('src') === '/diy/images/sytu.png' ? '' : $('.file_img').attr('src'),//个性化标签设置
                                cid:_diy.cid+','+_diy.sid+','+qid,
                                // token:token
                            },
                            success:function(data){
                                //$("p").unbind("click");
                                if(data.done){
                                    var last_ident = data.retval.lastkey;
                                    $.post("/cart-choice.html", { id: last_ident, ck: "y",tp: "fdiy" },
                                        function(res){
                                            //alert("Data Loaded: " + data);
                                            if(res.done)
                                            {

                                                window.location.href='/cart-checkout.html';
                                            }
                                            else
                                            {

                                                luck.poptip({
                                                    con:res.msg
                                                })
                                            }

                                        },'json');

                                }else{
                                    $("#jiesuan").text("去结算");
                                    // $('#sizeSelect').modal('hide');
                                    //_diy.alert(data.msg);
                                    luck.poptip({
                                        con:data.msg
                                    })

                                }
                            },
                            error:function(){
                                luck.poptip({
                                    con:'加入购物车请求出错'
                                })
                            }
                        });

                    });
                }
            });
        },



        fnbtn:function(){
		$('.cdiy_fnbtn').on('click','li',function(){
			switch(this.id){
				case 'reset':
					history.go(0);
				break;
				case 'rotate':
					_diy.rotate();
				break;
				case 'saveWork':
					_diy.saveWorks();
				break;
				case 'diyinfo':
					_diy.showInfo();
				break;
				case 'fullScreen':
                    (function launchFullscreen(element) {
                        if(element.requestFullscreen) {
							element.requestFullscreen();
						} else if(element.mozRequestFullScreen) {
							element.mozRequestFullScreen();
						} else if(element.webkitRequestFullscreen) {
							element.webkitRequestFullscreen();
						} else if(element.msRequestFullscreen) {
							element.msRequestFullscreen();
						}else{
							luck.poptip({
								con:'您的浏览器不支持全屏功能!'
							})
						}
					})(document.documentElement);
				break;
			}
		});
        },
        runrule:function(){
// alert('aa')
            $('.noselect').removeClass('noselect');
            $.each(_diy.rule,function(i,d){//循环冲突规则数据
                var collection=d.collection.split(','); // rules
                if(collection.length===1){//单一互斥处理
                    if($('.rule'+collection).is('.on')){
                        $.each(d.rules.split(','),function(i,d){
                            $('.rule'+d).removeClass('on').addClass('noselect');
                        });
                    }
                }else{//多项组合互斥处理 // 没有什么意义 现在的规则不存在多对一或多的互斥
//                    var lx=true;
//                    $.each(d.collection.split(','),function(i,d){
//                        if(!($('.rule'+collection).is('.on'))){
//                            lx=false;
//                        }
//                    });
//                    if(lx){
//                        $.each(d.rules.split(','),function(i,d){
//                            $('.rule'+d).removeClass('on').addClass('noselect');
//                        });
//                    }
                }
            });
        },
        // 价格计算 + 刷新选择区
        js:function(){
            _diy.fprice();
        },
        //处理价格
        fprice:function()
        {
            var qq = parseInt($('.xuan34').find('li.on').attr('sid')); // 犬期
            var gg = parseInt($('.xuan11').find('li.on').attr('sid')); // 规格
            var bz = parseInt($('.xuan16').find('li.on').attr('sid')); // 包装
            var gongxiao = "";
            $('.xuan1').find('li.on').each(function(){ // 功效
                //gxall += ($(this).attr('data-uprice')/gx);
                gongxiao += $(this).attr('sid')+"|";
            });
 // alert(bz);

            var jiliao_price = 0;
            var guige_price = 0;
            var is_alone = 0;
            //alert(qq);
            //计算基料的价格
            var ress = $.ajax({
                url:'/fdiy-formPrice.html',
                type:'POST',
                timeout:5000,
                dataType:"json",
                //async:false,
                data:{
                    cate_id:_diy.quanzhong_son,
                    age_id:qq,
                    baozhuang:bz,
                    guige:gg,
                    gongxiao:gongxiao,
                },
                success:function(res){
                    if(res.done)
                    {
                        //return 11;
                        jiliao_price = res.retval.jiliao_price;
                        guige_price  = res.retval.guige_price;
                        gp_price = res.retval.gp_price;
                        is_jianfei = res.retval.is_alone;
                        // $("#base_price").val(jiliao_price);
                        //console.log(ress);
                        //jiliao_price = $("#base_price").val();
                        var gx =  _diy.effectprice ;
                        var gxnum = parseInt( $('.xuan1 ').find('li.on').length ); // 功效
                        //alert(gxnum);
                        var gxall=0;//功效选中个数的钱数和
                        //var is_jianfei = 0;
                        $('.xuan1').find('li.on').each(function(){ // 功效
                            //gxall += ($(this).attr('data-uprice')/gx);
                            var sid = $(this).attr('sid');
                            //alert(gp_price[sid]);
                            gxall += (gp_price[sid]/gx);
                        });
                        gxall = gxall.toFixed(2);
                        //alert(gxall)
                        // var kw = ( $('.xuan6').find('li.on').attr('data-uprice') ); // 口味
                        var kw = jiliao_price;
                        var gg = ( $('.xuan11').find('li.on').attr('ve') ); // 规格
                        //var bz = ( $('.xuan16').find('li.on').attr('data-fprice') ); // 包装形式
                        var bz = guige_price;
                        //alert((gg * kw * (1 - gxnum * 0.25)) );
                        //alert((gg * 0.25 * gxnum * (_diy.effectprice) * (gxall) / gxnum ));
                        //alert((gg * kw * (1 - gxnum * 0.25))  + (gg * 0.25 * gxnum * (_diy.effectprice) * (gxall) / gxnum ) + bz*1);
    // alert('(规格重量 '+gg+' * 口味价格'+kw+' * (1 - 功效选择个数'+gxnum+' * 0.25)) + (规格重量'+gg+' * 0.25 * 功效选择个数'+gxnum+' * 功效均价'+(_diy.effectprice)+' * (选择功效总和'+gxall+') / 功效数量'+gxnum+' ) + 包装价格'+bz);
                        var num = $(".goods_num").val();
                        if(typeof(gg) == "undefined")
                        {
                            var price = 0;
                        }
                        else
                        {
                            if(gxnum > 0)
                            {
                                if(is_jianfei)//减肥瘦身没有 基料 功效占比为100%
                                {
                                    var price = ((gg * 1 * (_diy.effectprice) * (gxall) / gxnum ) +  bz*1)*parseInt(num);
                                }
                                else
                                {
                                    // alert(_diy.effectprice)
                                    var price = (((gg * kw * (1 - gxnum * 0.25)) + (gg * 0.25 * gxnum * (_diy.effectprice) * (gxall) / gxnum )) +  bz*1)*parseInt(num);
                                }
                            }
                            else
                            {
                                var price = (((gg * kw * (1 - gxnum * 0.25))) +  bz*1)*parseInt(num);
                            }
                        }


                        // alert(price)
                        price = price.toFixed(2);
                        $('.buy_info span').text("总价：¥"+ price );
                        _diy.xiaoguo();
                    }
                    else
                    {
                        // alert(res.msg);
                    }
                },
                error:function(){
                    //alert('error004');
                }
            });
        },

        //处理右上册
        xiaoguo:function()
        {
            var gongxiao = "";
            $('.xuan1').find('li.on').each(function(){ // 功效
                gongxiao += $(this).text()+"/";
            });

            gongxiao = gongxiao.substring(0,gongxiao.length-1);
   // alert(gongxiao)
            if(gongxiao)
            {

                $(".sxuan1").text(gongxiao);
                //$(".axuan1").addClass("on");
            }
            else
            {
                //$(".axuan1").removeClass("on");
                $(".sxuan1").text("未设置");
            }

            //处理功效相关内容
            var gongxiao_cotent = $('.xuan34').find('li.on').attr("data-content");
            $(".gongxiao_content").text(gongxiao_cotent);

            //alert(gongxiao);
            var qq = $('.xuan34').find('li.on').text(); // 犬期
            var kw = $('.xuan6').find('li.on').text(); // 口味
            var gg = $('.xuan11').find('li.on').text(); // 规格
            var bz = $('.xuan16').find('li.on').text(); // 包装形式
            if(gg && bz)//包装规格功能
            {
                //$(".axuan04").addClass("on");
                $(".sxuan6").text(kw);
            }
            else
            {
                $(".sxuan6").text("未设置");
                //$(".axuan04").removeClass("on");
            }



            if(kw)
            {
                //$(".axuan6").addClass("on");
                $(".sxuan6").text(kw);
            }
            else
            {
                $(".sxuan6").text("未设置");
                	//$(".axuan6").removeClass("on");
            }

            if(gg)
            {
                // $(".axuan11").addClass("on");
                $(".sxuan11").text(gg);
            }
            else
            {
                $(".sxuan11").text("未设置");
                //$(".axuan11").removeClass("on");
            }

            if(bz)
            {
                //$(".axuan16").addClass("on");
                $(".sxuan16").text(bz);
            }
            else
            {
                //$(".axuan16").removeClass("on");
                $(".sxuan16").text("未设置");
            }

            if(qq)
            {
                //$(".axuan34").addClass("on");
                $(".sxuan34").text(qq);
            }
            else
            {
                //$(".axuan34").removeClass("on");
                $(".sxuan34").text("未设置");
            }


            //个性设置
            var dog_name = $("input[name=dog_name]").val();
            var dog_date = $("input[name=dog_date]").val();
            var dog_desc = $("textarea[name=dog_desc]").val();
            var dog_src = $(".file_img").attr("src");
            /*if(dog_name && dog_date && dog_desc && dog_src)
            {
                $(".axuan05").addClass("on");
            }
            else
            {
                $(".axuan05").removeClass("on");
            }*/
        },

        run:function(){
            _diy.runrule();
            return;
            _diy.data;
            var diynavhtml=''; // 一级导航
            var diyconsolehtml=''; // 子级导航数据
            var diyc='';
            var diycall='';
//alert(JSON.stringify(_diy.data));
            $.each(_diy.data,function(key,val){
                if(val.if_show == 1){
                    diynavhtml += '<li nav="'+val.cate_name+'"><img src="'+val.small_img+'">'+val.cate_name+'</li>'; //  class="on"  // 一级导航
                    if(key==0){
                        diyconsolehtml+='<div class="show1 clearfix cdiy_qz" style="display:block;">';
                        diyconsolehtml+='<ul class="tbqh_2 tab_tit2 clearfix cdiy_nav_qz"><li class="on">全部</li>';
                            $.each(val.children,function(key2,val2){
                                diyconsolehtml += '<li>'+val2.cate_name+'<p class="xjt"></p></li>';
                                    diyc += '<div class="mCustomScrollbar show1_1 img_list" style="display:none;">';
                                    diyc += '<ul>';
                                    $.each(val2.children,function(key3,val3){
                                            diyc+='<li pid="'+val.cate_id+'" sid="'+val3.cate_id+'"><img src="'+val3.small_img+'" data-sort="'+val3.sort_order+'"><p>'+val3.cate_name+'</p></li>'; //  class="on"
                                            if(val3.is_def == '1'){
                                                diycall+='<li class="on" pid="'+val.cate_id+'" sid="'+val3.cate_id+'" data-sort="'+val3.sort_order+'"><img src="'+val3.small_img+'"><p>'+val3.cate_name+'</p></li>'; //  class="on"
                                            }else{
                                                diycall+='<li pid="'+val.cate_id+'" sid="'+val3.cate_id+'" data-sort="'+val3.sort_order+'"><img src="'+val3.small_img+'"><p>'+val3.cate_name+'</p></li>'; //  class="on"
                                            }
                                    });
                                    diyc += '</ul>';
                                    diyc += '</div>';

                            });
                        diyconsolehtml+='</ul>';
                        diycall = '<div class="mCustomScrollbar show1_1 img_list"><ul>'+diycall+'</ul></div>';
                        diyconsolehtml+=diycall+diyc;
                        diyconsolehtml+='</div>';
                    }else{
                        if(val.cate_name === '功效'){
                            if(val.if_show == 1){
                                diyconsolehtml+='<div class="mCustomScrollbar show1 text_list clearfix cdiy_qq" data="2"><h1>'+val.cate_name+'（多选）</h1>';
                            }
                        }else{
                            if(val.if_show == 1){
                                diyconsolehtml+='<div class="mCustomScrollbar show1 text_list clearfix cdiy_qq" data="1"><h1>'+val.cate_name+'</h1>';
                            }
                        }
                        diyconsolehtml+='<ul>';
                            $.each(val.children,function(key2,val2){
                                if(val2.is_def == 1){
                                    if(val2.if_show == 1){
                                        diyconsolehtml += '<li class="on rule'+val2.cate_id+'" pid="'+val.cate_id+'" sid="'+val2.cate_id+'" ve="'+val2.ve+'" data-uprice="'+val2.uprice+'"  data-fprice="'+val2.fprice+'" data-sort="'+val2.sort_order+'">'+val2.cate_name+'</li>'; //  class="on"
                                    }
                                }else{
                                    if(val2.if_show == 1){
                                        diyconsolehtml += '<li class="rule'+val2.cate_id+'" pid="'+val.cate_id+'" sid="'+val2.cate_id+'" ve="'+val2.ve+'" data-uprice="'+val2.uprice+'"  data-fprice="'+val2.fprice+'" data-sort="'+val2.sort_order+'">'+val2.cate_name+'</li>'; //  class="on"
                                    }
                                }
                            });
                        diyconsolehtml+='</ul>';
                        if(val.cate_name == '包装形式'){
                            diyconsolehtml+='<h1 class="clear" style="padding-top:10px;">个性化标签设计</h1>';
                            diyconsolehtml+='<div class="file_box">';
                            diyconsolehtml+='<input type="file"  id="picFile" onchange="readFile(this)" />  ';
                            diyconsolehtml+='<span></span>';
                            diyconsolehtml+='<div class="file_img"><img src="/diy/images/dog_2.png"></div>';
                            diyconsolehtml+='</div>';
                        }
                        diyconsolehtml+='</div>';
                    }
                }
            });
            $('.cdiy_nav').html(diynavhtml).find('li').eq(0).addClass('on'); // 一级导航
            $('.cdiy_data').html(diyconsolehtml);

            // 处理犬种选择
            if($('.cdiy_qz > div').find('.on').length===0){
                var defaultdata = $('.cdiy_qz > div').find('li').eq(0).addClass('on').find('p').html();
                $('.cdiy_qz > div').find('li p').each(function(){
                    if(defaultdata === $(this).html()){
                        if($(this).parent().is('.on') === false){
                            $(this).parent().addClass('on');
                        }
                    }
                });
            }
            // 处理除犬种其他的选择
//            $('.cdiy_data .cdiy_qq').each(function(i){
//                if($(this).find('.on').length===0){
//                    $(this).find('li:not(.noselect)').eq(0).addClass('on');
//                    _diy.runrule();
//                }
//            });
            for (var i=0;i<$('.cdiy_data .cdiy_qq').length;i++)
            {
                $('.cdiy_data .cdiy_qq').eq(i).each(function(i){
                        _diy.runrule();
//                        $(this).find('li:not(.noselect)').eq(0).addClass('on');
                });
            }

            // 已选择数据
            var sdata = '';
            $('.cdiy_data > div').each(function(i){
                if(i===0){
                    sdata += $(this).find('.on p').html();
                }else{
                    sdata += $(this).find('.on').html();
                }
                sdata += i < 5 ? '<i>-</i>' : '';
            });
            $('.cdiy_select').html('<b>已选择：</b>'+sdata);
            _diy.fnbtn();
            _diy.dataSort();
        },
        dataSort:function(){
            $.each($(".swiper-slide dl"),function(i){
                var ic = $(".swiper-slide dl").eq(i);
                var divs = ic.find("dd");
                var arr = divs.get();
                arr.sort(function(a, b) {
                    var ai = parseFloat($(a).attr("data-sort"), 10);
                    var bi = parseFloat($(b).attr("data-sort"), 10);
                    if (ai > bi) {
                        return 1;
                    } else if (ai < bi) {
                        return -1;
                    } else {
                        return 0;
                    }
                });
                ic.html(arr);
            });
        },


/*===========================*
	      分享
*============================*/
	diyShare:function(){
		var share_config = {
			Site:'麦富迪定制平台',
			Text:'麦富迪定制平台',
			Desc:'',
			Url :'',
			Pic :'http://'+document.domain+'/diy/images/cptu.jpg',
		},
		shareUrl={
			sina:function(){
				return 'http://service.weibo.com/share/share.php?url='+share_config.Url+'&title='+share_config.Text+'&pic='+share_config.Pic
				},
			qzone:function(){
				return 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='+share_config.Url+'&title='+share_config.Text+'&desc='+share_config.Desc+'&summary=&site='+share_config.Site+'&pic='+share_config.Pic
			},
			tqq:function(){
				return 'http://share.v.t.qq.com/index.php?c=share&a=index&url='+share_config.Url+'&title='+share_config.Text+'&appkey='
			},
			sqq:function(){
				return 'http://connect.qq.com/widget/shareqq/index.html?url='+share_config.Url+'&title='+share_config.Text+'&desc='+share_config.Desc+'&summary=&site='+share_config.Site+'&pics='+share_config.Pic
			}
		};
                $('.fenxia a').live('click',function(){
			var t=$(this).attr('data-cmd'),url='';
                        share_config.Url='http://'+document.domain+'/fdiy-1.html';//更新URL
                        switch(t){
                                case 'tsina':
                                        url=shareUrl.sina();
                                break;
                                case 'qzone':
                                        url=shareUrl.qzone();
                                break;
                                case 'tqq':
                                        url=shareUrl.tqq();
                                break;
                                case 'sqq':
                                        url=shareUrl.sqq();
                                break;
                                default :
                                        alert('未定义分享');
                        }
                        window.open(url);
		});

	}
};

    var diy=new Diy({
            //data: $basedata,//工艺数据
            rule: $rule,//冲突规则
            mid: $mid,//会员
            cid: $cid,//一级
            sid: $sid,//二级
            quanzhong: $quanzhong,//二级
            quanzhong_son: $quanzhong_son,//二级
            gxmaxnum: $gxmaxnum,//功效选择上限
            effectprice: $effectprice//功效均价
    });
    diy.init();

$('.jia').click(function(){
    //alert($(this).data("id"))
    var num = $(".goods_num").val();
    $(".goods_num").val(parseInt(num) + 1);
    diy.js();
});

$('.goods_num').keyup(function(){
    var num = $(".goods_num").val();

    if(num < 1)
    {
        $(".goods_num").val(1);
        return;
    }
    diy.js();
});

$('.jian').click(function(){
    //alert($(this).data("id"))
    var num = $(".goods_num").val();
    if(num == 1)
    {
        return;
    }
    $(".goods_num").val(parseInt(num) - 1);
    diy.js();

});

//删除背景，信息框并隐藏所有描述
$(".closes").click(function(){
    diy.js();
    return false;
});



function readFile(obj){
    var file = obj.files[0];
    //判断类型是不是图片
    if(!/image\/\w+/.test(file.type)){
        alert("请确保文件为图像类型");
        return false;
    }
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function(e){
        $('.file_img img').attr('src',this.result);
    };
}
