
//弹层登录
var loginIn={
	callback:null,
	show:function(fn){
		loginIn.callback=fn;
	}
};
function Diy(opt){
	this.data=opt.data;//缓存数据
	this.rule=opt.rule;//缓存规则
	this.mid=parseInt(opt.mid);//缓存会员ID
	this.cid=opt.cid;//缓存一级分类ID
	this.sid=opt.sid;//缓存二级分类ID
	this.gxmaxnum=parseInt(opt.gxmaxnum);//功效选择上限
        this.effectprice=opt.effectprice;//功效均价
        this.selectdata={};//缓存值
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
            // 控制台一级切换
            $(".tab_tit li").live({
                click: function() {
                    $(this).eq($(this).index(this)).addClass("on").siblings().removeClass("on");
                    var ss = $(".show1").eq($(".tab_tit li").index(this));
                    ss.show().siblings().hide();
                }
            });
            // 犬种)二级切换
            $(".tab_tit2 li").live({
                click: function() {
                    $(this).eq($(this).index(this)).addClass("on").siblings().removeClass("on");
                    var ss = $(".show1_1").eq($(".tab_tit2 li").index(this));
                    ss.show().siblings("div").hide();
                }
            });
            // 犬种选择处理
            $(".img_list li").live({
                click: function() {
                    $(".img_list li").removeClass('on');
                    $(this).addClass('on');
                    var defaultdata = $(this).find('p').html();
                    $('.cdiy_qz > div li p').each(function(){
                        if(defaultdata === $(this).html()){
                            if($(this).parent().is('.on') === false){
                                $(this).parent().addClass('on');
                            }
                        }
                    });
                    _diy.runrule();
                    _diy.js();
                }
            });
            // （单选|复选）普通切换
            $(".text_list li").live({
                click: function() {
                    if($(this).parents('.text_list').attr('data') === '1'){
                        $(this).addClass('on').siblings().removeClass('on');
                    }else{
                        if($(this).is('.on') === true){
                            $(this).removeClass('on');
                        }else{
                            if($(this).siblings('.on').length < _diy.gxmaxnum ){
                                $(this).addClass('on');
                            }
                        }
                        if($(this).parents('.text_list').find('.on').html() === undefined){ // length
                            $(this).addClass('on');
                        }
                    }
                    _diy.runrule();
                    _diy.js();
                }
            //重置
            });
            //加入购物车
            $('.addCart').live({
                click: function() {
                    _diy.checkLogin(function(){
                        var qid='';
                        var postObj = {};
                        $('.cdiy_data > div').each(function(i){
                            if(i===0){
                                postObj[$(this).find('.img_list .on').attr('pid')] = qid = $(this).find('.img_list .on').attr('sid');
                            }else{
                                if($(this).find('.on').length===0){
                                    alert($('.cdiy_nav li').eq(i).attr('nav') + ' 没有选择');
                                    return false;
                                }
                                if($(this).attr('data') === '2'){
                                    var erj='';
                                    var erjk='';
                                    $(this).find('.on').each(function(){
                                        erj += $(this).attr('sid') + '|';
                                        erjk=$(this).attr('pid');
                                    });
                                    erj=erj.substring(0,erj.length-1);
                                    postObj[erjk] = JSON.stringify(erj);
                                }else{
                                    postObj[$(this).find('.on').attr('pid')] = $(this).find('.on').attr('sid');
                                }
                            }
                        });
                        var data=[];
                        $.ajax({
                                url:'/cart-add.html',
                                type:'POST',
                                timeout:5000,
                                dataType:"json",
                                data:{
                                    type:'fdiy',
                                    params:JSON.stringify(postObj),//配置
                                    fpic:$('.file_img img').attr('src') === '/diy/images/dog_2.png' ? '' : $('.file_img img').attr('src'),//个性化标签设置
                                    cid:_diy.cid+','+_diy.sid+','+qid
                                },
                                success:function(data){
                                    if(data.done){
                                            window.location.href='/cart.html'
                                    }else{
                                            $('#sizeSelect').modal('hide');
                                            _diy.alert(data.msg);
                                    }
                                },
                                error:function(){
                                    alert('加入购物车请求出错');
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
            var gx = parseInt( _diy.effectprice );
            var gxnum = parseInt( $('.cdiy_qq').eq($("li[nav='功效']").index()-1).find('li.on').length );
            var gxall=0;
            $('.cdiy_qq').eq($("li[nav='功效']").index()-1).find('li.on').each(function(){
                gxall += parseInt($(this).attr('data-uprice'));
            });
            var kw = parseInt( $('.cdiy_qq').eq($("li[nav='口味']").index()-1).find('li.on').attr('data-uprice') );
            var gg = parseInt( $('.cdiy_qq').eq($("li[nav='规格']").index()-1).find('li.on').attr('ve') );
            var bz = parseInt( $('.cdiy_qq').eq($("li[nav='包装形式']").index()-1).find('li.on').attr('data-uprice') );
//alert('(规格重量 '+gg+' * 口味价格'+kw+' * (1 - 功效选择个数'+gxnum+' * 0.25)) + (规格重量'+gg+' * 0.25 * 功效选择个数'+gxnum+' * 功效均价'+parseInt(_diy.effectprice)+' * (选择功效总和'+gxall+') / 功效数量'+gxnum+' ) + 包装价格'+bz);

            $('.cdiy_price').text( ((gg * kw * (1 - gxnum * 0.25)) + (gg * 0.25 * gxnum * parseInt(_diy.effectprice) * (gxall) / gxnum ) + bz)  );
            
            var sdata='';
            $('.cdiy_data > div').each(function(i){
                if(i===0){
                    sdata += $(this).find('.on p').html();
                }else{
                    if($(this).attr('data') === '2'){
                        $(this).find('.on').each(function(){
                            sdata += $(this).html() + '/';
                        });
                        sdata=sdata.substring(0,sdata.length-1);
                    }else{
                        sdata += $(this).find('.on').html();
                    }
                }
                sdata += i < 5 ? '<i>-</i>' : '';
            });
            $('.cdiy_select').html('<b>已选择：</b>'+sdata);
        },
        run:function(){
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
            data: $basedata,//工艺数据
            rule: $rule,//冲突规则
            mid: $mid,//会员
            cid: $cid,//一级
            sid: $sid,//二级
            gxmaxnum: $gxmaxnum,//功效选择上限
            effectprice: $effectprice//功效均价
    });
    diy.init();
    

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
    