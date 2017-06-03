
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
}
Diy.prototype={
    init:function(){
            _diy=this;
            _diy.bind();
            _diy.run();
            _diy.js();
    },
    //检测登录状态
    checkLogin:function(callback){
        var token = GetQueryString("token");
            token = token === null ? '' : token ;
            if(_diy.mid===0 && token === ''){
                window.location.href='/member-login.html';
            }else{
                callback();
            }	
    },
    bind:function(){
            
        // 元素选择
        $('.swiper-slide dd').live('click',function(){
            var stype = $(this).parents('.swiper-slide').attr('s-type'); // 是否多选 1多选 其他单选
            if(stype === '1'){
                if($(this).is('.on') === true){
                    $(this).removeClass('on');
                }else{
                    if($(this).siblings('.on').length < _diy.gxmaxnum ){
                        $(this).addClass('on');
                    }
                }
                if($(this).parents('.swiper-slide').find('.on').html() === undefined){ // length
                    $(this).addClass('on');
                }
            }else{
                $(this).addClass('on').siblings().removeClass('on');
            }
            _diy.runrule();
            _diy.js();
        });
            
        //加入购物车
        $('.addCart').live({
            click: function() {
                _diy.checkLogin(function(){
                    var qid='';
                    var postObj = new Object();
                    $('.swiper-slide').each(function(i){
                        if(i===0){
                            postObj[$(this).find('.on').attr('pid')] = qid = $(this).find('.on').attr('sid');
                        }else{
                            if($(this).find('.on').length===0){
                                alert($('.swiper-slide').eq(i).attr('data-name') + ' 没有选择');
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
                                postObj[erjk] = JSON.stringify(erj);
                            }else{
                                postObj[$(this).find('.on').attr('pid')] = $(this).find('.on').attr('sid');
                            }
                        }
                    });
                    var token = GetQueryString("token");
                        token = token === null ? '' : token ;
                    var data=[];
                    $.ajax({
                            url:'/cart-add.html',
                            type:'POST',
                            timeout:5000,
                            dataType:"json",
                            data:{
                                type:'fdiy',
                                params:JSON.stringify(postObj),//配置
                                fpic:$('.file_img img').attr('src') === '/fdiy/images/gg.jpg' ? '' : $('.file_img img').attr('src'),//个性化标签设置
                                cid:_diy.cid+','+_diy.sid+','+qid,
                                token:token
                            },
                            success:function(data){
                                if(data.done){
                                        window.location.href='/cart.html';
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
            }else{
            }
        });
    },
    // 价格计算 + 刷新选择区
    js:function(){
        var gx = parseInt( _diy.effectprice );
        var gxnum = parseInt( $('.swiper-slide').eq(2).find('dd.on').length ); // 功效
        var gxall=0;
        $('.swiper-slide').eq(2).find('dd.on').each(function(){ // 功效
            gxall += parseInt($(this).attr('data-uprice'));
        });
        var kw = parseInt( $('.swiper-slide').eq(3).find('dd.on').attr('data-uprice') ); // 口味
        var gg = parseInt( $('.swiper-slide').eq(4).find('dd.on').attr('ve') ); // 规格
        var bz = parseInt( $('.swiper-slide').eq(5).find('dd.on').attr('data-uprice') ); // 包装形式
        //alert('(规格重量 '+gg+' * 口味价格'+kw+' * (1 - 功效选择个数'+gxnum+' * 0.25)) + (规格重量'+gg+' * 0.25 * 功效选择个数'+gxnum+' * 功效均价'+parseInt(_diy.effectprice)+' * (选择功效总和'+gxall+') / 功效数量'+gxnum+' ) + 包装价格'+bz);
        $('.cdiy_price').text( ((gg * kw * (1 - gxnum * 0.25)) + (gg * 0.25 * gxnum * parseInt(_diy.effectprice) * (gxall) / gxnum ) + bz)  );
    },
    run:function(){
        _diy.data;
        var diyconsolehtml=''; // 子级导航数据
        var diycall='';
        $.each(_diy.data,function(key,val){
            if(key===0){
                diyconsolehtml += '<div class="swiper-slide"><div class="content-slide"><dl class="dzxx" >';
                    $.each(val.children,function(key2,val2){
                        $.each(val2.children,function(key3,val3){
                            if(val3.is_def === '1'){
                                diycall += '<dd class="on" pid="'+val.cate_id+'" sid="'+val3.cate_id+'">'; //  class="on"
                            }else{
                                diycall += '<dd pid="'+val.cate_id+'" sid="'+val3.cate_id+'">';
                            }
                            diycall += '    <p class="p1"><img src="'+val3.small_img+'"></p>';
                            diycall += '    <p class="p2">'+val3.cate_name+'</p>';
                            diycall += '</dd>';
                        });
                    });
                diyconsolehtml += diycall+'</dl></div></div>';
            }else if(val.cate_name === '包装形式'){
                diyconsolehtml+='<div class="swiper-slide" s-type="0" data-name="'+val.cate_name+'"><div class="content-slide"><dl class="packing clearfix">';
                $.each(val.children,function(key2,val2){
                    if(val2.is_def === '1'){
                        diyconsolehtml += '<dd class="on rule'+val2.cate_id+'" pid="'+val.cate_id+'" sid="'+val2.cate_id+'" ve="'+val2.ve+'" data-uprice="'+val2.uprice+'"  data-fprice="'+val2.fprice+'">'+val2.cate_name+'</dd>'; //  class="on"
                    }else{
                        diyconsolehtml += '<dd class="rule'+val2.cate_id+'" pid="'+val.cate_id+'" sid="'+val2.cate_id+'" ve="'+val2.ve+'" data-uprice="'+val2.uprice+'"  data-fprice="'+val2.fprice+'">'+val2.cate_name+'</dd>';
                    }
                });
                diyconsolehtml+='</dl>';
                if(val.cate_name === '包装形式'){
                    diyconsolehtml+='    <h6 class="gxhbqsj">个性化标签设计</h6>';
                    diyconsolehtml+='    <dl class="design">';
                    diyconsolehtml+='        <dd class="scsj"><input name="" type="file" id="picFile" onchange="readFile(this)"><img src="/fdiy/images/sctu.png"></dd>';
                    diyconsolehtml+='        <dd class="file_img"><img src="/fdiy/images/gg.jpg"></dd>';
                    diyconsolehtml+='    </dl>';
                }
                diyconsolehtml+='</div></div>';
            }else{
                
                if(val.cate_name === '功效'){
                    diyconsolehtml+='<div class="swiper-slide" s-type="1" data-name="'+val.cate_name+'">';
                }else{
                    diyconsolehtml+='<div class="swiper-slide" s-type="0" data-name="'+val.cate_name+'">';
                }
                diyconsolehtml+='<div class="content-slide">';
                diyconsolehtml+='    <dl class="packing clearfix">';
                    $.each(val.children,function(key2,val2){
                        if(val2.is_def === '1'){
                            diyconsolehtml+='        <dd class="on rule'+val2.cate_id+'" pid="'+val.cate_id+'" sid="'+val2.cate_id+'" ve="'+val2.ve+'" data-uprice="'+val2.uprice+'"  data-fprice="'+val2.fprice+'">'+val2.cate_name+'</dd>'; //  class="on"
                        }else{
                            diyconsolehtml+='        <dd class="rule'+val2.cate_id+'" pid="'+val.cate_id+'" sid="'+val2.cate_id+'" ve="'+val2.ve+'" data-uprice="'+val2.uprice+'"  data-fprice="'+val2.fprice+'">'+val2.cate_name+'</dd>';
                        }
                    });
                diyconsolehtml+='    </dl>';
                diyconsolehtml+='</div>';
                diyconsolehtml+='</div>';
            }
        });
//        $('.cdiy_nav').html(diynavhtml).find('li').eq(0).addClass('on active'); // 一级导航
        $('.swiper-wrapper').html(diyconsolehtml);

        // 处理犬种选择
        if($('.swiper-slide').eq(0).find('.on').length===0){
            $('.swiper-slide').eq(0).find('dd').eq(0).addClass('on');
        }
        for (var i=0;i<$('.swiper-slide').length;i++)
        {
            if(i>0){
                $('.swiper-slide').eq(i).each(function(i){
                        _diy.runrule();
                        $(this).find('dd:not(.noselect)').eq(0).addClass('on');
                });
            }
        }
    },
    //分享（ios/android）
    share:function(){//ios
        return 'http://'+document.domain+'/fdiy-1-3.html|||http://'+document.domain+'/diy/images/dzdtu.jpg';
    },
    shareAndroid:function(){//Android
        app.share(true,'http://'+document.domain+'/fdiy-1-3.html|||http://'+document.domain+'/diy/images/dzdtu.jpg');
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
function GetQueryString(name){
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!==null)return  unescape(r[2]); return null;
}