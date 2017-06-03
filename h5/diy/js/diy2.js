
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
	this.quanzhong=opt.quanzhong;//缓存二级分类ID
	this.quanzhong_son=opt.quanzhong_son;//缓存二级分类ID
    this.is_try=opt.is_try;//缓存二级分类ID
	this.gxmaxnum=parseInt(opt.gxmaxnum);//功效选择上限
        this.effectprice=opt.effectprice;//功效均价
}
Diy.prototype={
    init:function(){
            _diy=this;

            _diy.bind();
            _diy.run();
            _diy.js();
            _diy.feedtme();
            //动画效果
            $(document).ready(function(){
                    var offset = $(".xlrq").offset();
                    $(".bzxs li").click(function(event){
//                            if($(this).hasClass("on"))
//                            {
//                                return;
//                            }
                            var addcar = $(this);
                            var img = addcar.find('img').attr('src');
                            var flyer = $('<img class="fly_img" src="'+img+'">');
                            flyer.fly({
                                    start: {
                                            left: event.pageX,
                                            top: event.pageY
                                    },
                                    end: {
                                            left: offset.left+10,
                                            top: offset.top+10,
                                            width: 0,
                                            height: 0
                                    }
                            });
                    });
            });
    },
    //检测登录状态
    checkLogin:function(callback){
        if(_diy.mid===0)
        {
            window.location.href='/member-login.html';
            return;
        }

        var w = $("input[name='weight']").val();
        if (w <= 0)
        {
            luck.open({
                type:1,
                content: '<div style="padding:20px;">为准确计算您爱宠的饲喂量，请在包装规格处填写爱宠体重!<p style="text-align:center;margin-top:20px;"><input type="button" style="border-radius:4px; background:#e66800; color:#fff; display: inline-block; height:30px; line-height:30px; width:80px;" class="button button-small button-primary button-rounded" value="我知道了" onclick="luck.close()"></p></div>'
            });
            return false;
        }

        var dog_name = $("input[name=dog_name]").val();
        var dog_date = $("input[name=dog_date]").val();
        if(!dog_name || !dog_date)
        {
            luck.open({
                content: '亲，给您的爱宠设计一个独一无二的包装吧!',//内容
                btn:['开始设计','先不设计了'],
                yes:function(){
                    luck.close();
                    $('#customSize5').fadeIn(500);
                },
                no: function(){
                    luck.close();
                    callback();
                }
            });

        }
        else
        {
            callback();
        }
    },
    strlen: function (str){
        var len = 0;
        for (var i=0; i<str.length; i++) {
            var c = str.charCodeAt(i);
            //单字节加1
            if ((c >= 0x0001 && c <= 0x007e) || (0xff60<=c && c<=0xff9f)) {
                len++;
            }
            else {
                len+=2;
            }
        }
        return len;
    },
    bind:function(){
            
        // 元素选择
        $('.swiper-slide_mfd li').live('click',function(){
            if($(this).hasClass("jj_1"))
            {
                return ;
            }



            var stype = $(this).parents('.swiper-slide_mfd').attr('s-type'); // 是否多选 1多选 其他单选
            var pid = $(this).attr("pid");
            if(pid == 34)//犬期重新计算饲料
            {
                if($(this).is('.on') === true){
                    return ;
                }
            }

            if(stype === '1'){
                if($(this).is('.on') === true){
                	
                    $(this).removeClass('on');
                }else{
                	
                    if($(this).siblings('.on').length < _diy.gxmaxnum ){
                        $(this).addClass('on');
                    }
                    else
                	{
                	    if(_diy.gxmaxnum == 2)
                        {
                            luck.open({
                                content: '亲，本次付邮0元定制活动只能选择2中功效噢！',
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });
                            // layer.open({
                            //     content: '亲，本次付邮0元定制活动只能选择2中功效噢！'
                            //     ,skin: 'msg'
                            //     ,time: 2 //2秒后自动关闭
                            // });
                        }
                        else
                        {
                            luck.open({
                                content: '亲，最多选择'+_diy.gxmaxnum+'种功效哦！',
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });
                            // layer.open({
                            //     content: '亲，最多选择'+_diy.gxmaxnum+'种功效哦！'
                            //     ,skin: 'msg'
                            //     ,time: 2 //2秒后自动关闭
                            // });
                        }


                	}
                }

            }else{
                $(this).addClass('on').siblings().removeClass('on');
            }



            if(pid == 34)//犬期重新计算饲料
            {

                // if($(this).is('.on') === true){
                //     return;
                // }
                _diy.feedtme();
                $(".rule107").removeClass("on");
            }
            _diy.runrule();
            _diy.js();

        });
            
		
        //加入购物车
        $('.addCart2').on({
            "click":function() {
                _diy.checkLogin(function(){

                    var qid=$quanzhong_son;
                    var postObj = {};
                    //犬种选择
                    postObj[_diy.quanzhong] = _diy.quanzhong_son;
                    var is_ch = 1;
                    $('.swiper-slide_mfd').each(function(i){
                    	 if($(this).find('.on').not(".noselect").length===0){
                    		 is_ch = 0;

                             luck.open({
                                 content: $('.swiper-slide_mfd').eq(i).attr('data-name') + ' 没有选择',
                                 style:'background:rgba(0,0,0,0.8); color:#fff;',
                                 time:2000
                             });
                             return false;
                         }
                         if($(this).attr('s-type') === '1'){
                             var erj='';
                             var erjk='';
                             $(this).find('.on').not(".noselect").each(function(){
                                 erj += $(this).attr('sid') + '|';
                                 erjk=$(this).attr('pid');
                             });
                             erj=erj.substring(0,erj.length-1);
                             postObj[erjk] = JSON.stringify(erj);
                         }else{
                             postObj[$(this).find('.on').attr('pid')] = $(this).find('.on').attr('sid');
                         }
                         
                    });
                    
                    if(!is_ch)
                	{
                    	return false;
                	}
                    var token = GetQueryString("token");
                        token = token === null ? '' : token ;
                    var goods_num = $(".goods_num").val();
                    var data=[];
                    var dog_name = $("input[name=dog_name]").val();
                    var dog_date = $("input[name=dog_date]").val();
                    var dog_desc = $("textarea[name=dog_desc]").val();
                    //计算饲喂量
                    var w = $("input[name='weight']").val();
                    // var dog_nums = $("input[name='dog_nums']").val();
                    var dog_nums = $("select[name='dog_nums']").val();
                    if (w <= 0)
                    {
                        luck.open({
                            type:1,
                            content: '<div style="padding:20px;">为准确计算您爱宠的饲喂量，请在包装规格处填写爱宠体重!<p style="text-align:center;margin-top:20px;"><input type="button" class="button button-small button-primary button-rounded" value="我知道了" onclick="luck.close()"></p></div>'
                        });

                        return false;
                    }
                    if(!($(".dog_nums").hasClass("noselects")))
                    {
                        if (dog_nums <= 0)
                        {
                            luck.open({
                                content: '请填写哺乳小狗数目',
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });
                            return false;
                        }
                    }

                    if (dog_name)
                    {
                        var len = _diy.strlen(dog_name);
                        if(len > 10)
                        {
                            luck.open({
                                content: '狗狗名称过长,建议10个字符以内',
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });
                            return false;
                        }
                    }
                    if (dog_desc)
                    {
                        var len = _diy.strlen(dog_desc);
                        if(len > 50)
                        {

                            luck.open({
                                content: '主人寄语过长,建议25个汉字以内',
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });
                            return false;
                        }
                    }
                    if ( $(".swl_dl").is('.noselects'))
                    {
                        var time_id = 0;
                    }
                    else
                    {
                        var time_id = $('.swl_dl input:checked').val()*1;
                    }
                    var body_id = $(".dogs").not('.noselects').find("dd.on").data("id")*1;
                    var run = $(".slider-status").not('.noselects').text()*1;
                    var run_id = 0;
                    if( $(".slider-status").is('.noselects'))
                    {
                    }
                    else
                    {
                        if (run >= 0 && run <= 0.5)
                        {
                            run_id = 1;
                        }
                        else if(run > 0.5 && run <= 1)
                        {
                            run_id = 2;
                        }
                        else if(run > 1 && run < 3)
                        {
                            run_id = 3;
                        }
                        else if(run >= 3)
                        {
                            run_id = 4;
                        }
                    }
                    $("#jiesuan").text("结算中...");
                    $.ajax({
                            url:'/cart-add.html?ajax',
                            type:'POST',
                            dataType:"json",
                            data:{
                                type:'fdiy',
                                num:goods_num,
                                dog_name:dog_name,
                                dog_date:dog_date,
                                dog_desc:dog_desc,
                                body_condition:body_id,
                                run_time:run_id,
                                time_id:time_id,
                                weight : w,
                                is_try:_diy.is_try,
                                dog_nums:dog_nums,
                                params:JSON.stringify(postObj),//配置
                                fpic:$('.file_img').attr('src') === '/diy/images/sytu.png' ? '' : $('.file_img').attr('src'),//个性化标签设置
                                cid:_diy.cid+','+_diy.sid+','+qid,
                                mfd_reload_mid:_diy.mid
                            },
                            success:function(data){
                                if(data.done){
                                    luck.open({
                                        content: '购物车加入成功',
                                        style:'background:rgba(0,0,0,0.8); color:#fff;',
                                        time:2000
                                    });
                                    var goods_num = data.retval.goods_num;
                                    $(".cart_icon").text(goods_num);

                                }else{
                                	$("#jiesuan").text("去结算");
                                    luck.open({
                                        content:  data.msg,
                                        style:'background:rgba(0,0,0,0.8); color:#fff;',
                                        time:2000
                                    });
                                }
                            },
                            error:function(){
                                luck.open({
                                    content:  "加入购物车失败!请联系客服",
                                    style:'background:rgba(0,0,0,0.8); color:#fff;',
                                    time:2000
                                });
                            }
                    });

                });
            }
        });


        //直接购买
        //加入购物车
        $('.addCart3').on({
            "click":function() {
                _diy.checkLogin(function(){

                    var qid=$quanzhong_son;
                    var postObj = {};
                    //犬种选择
                    postObj[_diy.quanzhong] = _diy.quanzhong_son;
                    var is_ch = 1;
                    $('.swiper-slide_mfd').each(function(i){
                        if($(this).find('.on').not(".noselect").length===0){
                            is_ch = 0;
                            luck.open({
                                content: $('.swiper-slide_mfd').eq(i).attr('data-name') + ' 没有选择',
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });
                            return false;
                        }
                        if($(this).attr('s-type') === '1'){
                            var erj='';
                            var erjk='';
                            $(this).find('.on').not(".noselect").each(function(){
                                erj += $(this).attr('sid') + '|';
                                erjk=$(this).attr('pid');
                            });
                            erj=erj.substring(0,erj.length-1);
                            postObj[erjk] = JSON.stringify(erj);
                        }else{
                            postObj[$(this).find('.on').attr('pid')] = $(this).find('.on').attr('sid');
                        }

                    });

                    if(!is_ch)
                    {
                        return false;
                    }
                    var token = GetQueryString("token");
                    token = token === null ? '' : token ;
                    var goods_num = $(".goods_num").val();
                    var data=[];
                    var dog_name = $("input[name=dog_name]").val();
                    var dog_date = $("input[name=dog_date]").val();
                    var dog_desc = $("textarea[name=dog_desc]").val();
                    //计算饲喂量
                    var w = $("input[name='weight']").val();
                    // var dog_nums = $("input[name='dog_nums']").val();
                    var dog_nums = $("select[name='dog_nums']").val();
                    if (w <= 0)
                    {
                        //信息框
                        luck.open({
                            type:1,
                            content: '<div style="padding:20px;">为准确计算您爱宠的饲喂量，请在包装规格处填写爱宠体重!<p style="text-align:center;margin-top:20px;"><input type="button" class="button button-small button-primary button-rounded" value="我知道了" onclick="luck.close()"></p></div>'
                        });
                        return false;
                    }
                    if(!($(".dog_nums").hasClass("noselects")))
                    {
                        if (dog_nums <= 0)
                        {
                            luck.open({
                                content: "请填写哺乳小狗数目",
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });
                            return false;
                        }
                    }

                    if (dog_name)
                    {
                        var len = _diy.strlen(dog_name);
                        if(len > 10)
                        {

                            luck.open({
                                content: "狗狗名称过长,建议10个字符以内",
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });
                            return false;
                        }
                    }


                    if (dog_desc)
                    {
                        var len = _diy.strlen(dog_desc);
                        if(len > 50)
                        {
                            luck.open({
                                content: "主人寄语过长,建议25个汉字以内",
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });

                            return false;
                        }
                    }

                    //var time_id = $('.swl_dl option:selected').val()*1;
                    // var time_id = $('.swl_dl input[name=time_idaa]').val()*1;
                    if ( $(".swl_dl").is('.noselects'))
                    {
                        var time_id = 0;
                    }
                    else
                    {
                        //var time_id = $('.swl_dl option:selected').val()*1;
                        var time_id = $('.swl_dl input:checked').val()*1;
                    }

                    var body_id = $(".dogs").not('.noselects').find("dd.on").data("id")*1;
                    var run = $(".slider-status").not('.noselects').text()*1;
                    var run_id = 0;

                    if( $(".slider-status").is('.noselects'))
                    {
                    }
                    else
                    {
                        if (run >= 0 && run <= 0.5)
                        {
                            run_id = 1;
                        }
                        else if(run > 0.5 && run <= 1)
                        {
                            run_id = 2;
                        }
                        else if(run > 1 && run < 3)
                        {
                            run_id = 3;
                        }
                        else if(run >= 3)
                        {
                            run_id = 4;
                        }
                    }



                    $("#jiesuan").text("结算中...");

                    $.ajax({
                        url:'/cart-add.html?ajax',
                        type:'POST',
                        dataType:"json",
                        data:{
                            type:'fdiy',
                            num:goods_num,
                            dog_name:dog_name,
                            dog_date:dog_date,
                            dog_desc:dog_desc,
                            body_condition:body_id,
                            run_time:run_id,
                            time_id:time_id,
                            weight : w,
                            mfd_cart_is_check : 1,
                            is_try:_diy.is_try,
                            dog_nums:dog_nums,
                            params:JSON.stringify(postObj),//配置
                            fpic:$('.file_img').attr('src') === '/diy/images/sytu.png' ? '' : $('.file_img').attr('src'),//个性化标签设置
                            cid:_diy.cid+','+_diy.sid+','+qid,
                            mfd_reload_mid:_diy.mid

                        },
                        success:function(data){
                            //$("p").unbind("click");
                            if(data.done){
                                window.location.href='/cart-checkout.html?mfd_cart_is_check=1';
                            }else{
                                $("#jiesuan").text("去结算");
                                // $('#sizeSelect').modal('hide');
                                //_diy.alert(data.msg);
                                luck.open({
                                    content:  data.msg,
                                    style:'background:rgba(0,0,0,0.8); color:#fff;',
                                    time:2000
                                });
                            }
                        },
                        error:function(){
                            // $("#jiesuan").text("去结算");
                            // layer.open({
                            //     content: '网络异常，请稍后再试！'
                            //     ,skin: 'msg'
                            //     ,time: 2 //2秒后自动关闭
                            // });
                            luck.open({
                                content:  "加入购物车失败!请联系客服",
                                style:'background:rgba(0,0,0,0.8); color:#fff;',
                                time:2000
                            });
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

        _diy.fprice();
       // alert('jiage');
       //  _diy.feedtme();
    },

    //根据体重 计算饲喂量$('#test option:selected').val();
    feedw:function(){

        $(".fffeed_w").text(0);
        $(".ffnums").text(0);

        var w = $("input[name='weight']").val();
        if(w <= 0)
        {
            return ;
        }

        //var dog_nums = $("input[name='dog_nums']").val();
        var dog_nums = $("select[name='dog_nums']").val();
        if ( $(".swl_dl").is('.noselects'))
        {
            var time_id = 0;
        }
        else
        {
            //var time_id = $('.swl_dl option:selected').val()*1;
            var time_id = $('.swl_dl input:checked').val()*1;
        }
        var body_id = $(".dogs").not('.noselects').find("dd.on").data("id")*1;
        var run = $(".slider-status").not('.noselects').text()*1;
        var run_id = 0;
        if( $(".slider-status").is('.noselects'))
        {

        }
        else
        {
            if (run >= 0 && run <= 0.5)
            {
                run_id = 1;
            }
            else if(run > 0.5 && run <= 1)
            {
                run_id = 2;
            }
            else if(run > 1 && run < 3)
            {
                run_id = 3;
            }
            else if(run >= 3)
            {
                run_id = 4;
            }
        }

        //计算饲喂量
        var gongxiao = "";
        $('.xuan1').find('li.on').not(".noselect").each(function(){ // 功效
            //gxall += ($(this).attr('data-uprice')/gx);
            gongxiao += $(this).attr('sid')+",";
        });

        //计算饲喂量
        var qq = parseInt($('.xuan34').find('li.on').not(".noselect").attr('sid')); // 犬期
        var qzs = this.quanzhong_son;
        $.ajax({
            type: "POST",
            url: "fdiy-formFeed.html?ajax",
            dataType:'json',
            data: "mfd_reload_mid="+_diy.mid+"&is_feed=0&cat_id="+qzs+"&age_id="+qq+"&body_condition="+body_id+"&run_time="+run_id+"&weight="+w+"&time_id="+time_id+"&dog_nums="+dog_nums+"&gongxiao="+gongxiao,
            success: function(res){

                if(res.done)
                {
                    var feed_w = res.retval.feed_w;
                    var feed = res.retval.feed;
                    var nums = res.retval.nums;
                    if (feed == 1)
                    {
                        $(".swl_text").empty();
                        $(".swl_text").text("饲喂量: 自由采食");
                    }
                    else
                    {
                        $(".fffeed_w").text(feed_w);
                        $(".ffnums").text(nums);
                    }
                }

            },
        });


    },

    // 根据犬期和犬种计算  时间轴和体重上下线
    feedtme:function(){
        var qq = parseInt($('.xuan34').find('li.on').not(".noselect").attr('sid')); // 犬期
        //哺乳期特殊处理
        $(".dog_nums").css("display","none").addClass('noselects');
        $(".fffeed_w").text(0);
        $(".ffnums").text(0);
        $("input[name='weight']").val("");
        _diy.addweightl(0,0);
        if(qq == 36)
        {
            $(".dog_nums").css("display","").removeClass('noselects');
        }

        var qzs = this.quanzhong_son;
        $.ajax({
            type: "POST",
            url: "fdiy-formFeed.html?ajax",
            dataType:'json',
            data: "mfd_reload_mid="+_diy.mid+"&is_feed=1&cat_id="+qzs+"&age_id="+qq,
            success: function(res){
                // var tstr = "<option value='0' data-wmax='0' data-wmin='0'>请选择阶段</option>";
                var tstr = "";

                var is_body = 0;
                var is_run = 0;
                var is_time = 0;
                var default_weight = 0;
                //遍历时间阶段
                $.each(res.retval.time_list, function(i, n){
                    var cls = "";
                    var clsact = "jj_rad"
                    if((i == 0))
                    {
                        _diy.addweightl(n.wt_min,n.wt_max);
                        // cls = "selected";
                        cls = "checked";
                        var clsact = "jj_rad act";
                    }
                    // alert(i);
                    if(n.time_id > 0)
                    {
                        // tstr += "<option "+cls+" value="+n.time_id+" data-wmax="+n.wt_max+" data-wmin="+n.wt_min+"  data-default="+n.default_weight+">"+n.time_name+"</option>";
                       // tstr += n.time_name +":<input name='time_idaa' type='radio'  "+cls+" value="+n.time_id+" data-wmax="+n.wt_max+" data-wmin="+n.wt_min+"  data-default="+n.default_weight+">";
                        tstr += "<label class='"+clsact+"'> <span>"+n.time_name +"</span><input name='time_idaa' type='radio'  "+cls+" value="+n.time_id+" data-wmax="+n.wt_max+" data-wmin="+n.wt_min+"  data-default="+n.default_weight+"></label>";
                    }
                });
                $(".swl_dl").empty();
                $(".swl_dl").append(tstr);


                //遍历参数
                $.each(res.retval.feed_list, function(i, n){
                    //检查是否有
                    if (n.time_id > 0)
                    {
                        is_time = 1;
                    }
                    if (n.body_condition > 0)
                    {
                        is_body = 1;
                    }
                    if (n.run_time > 0)
                    {
                        is_run = 1;
                    }
                });

                //取消隐藏参数
                if(!is_body)
                {
                    // alert("add")
                    $(".dogs").css("display",'none').addClass("noselects");
                    // $(".dogs").addClass("noselects");
                }
                else
                {
                    // alert("del")
                    $(".dogs").css("display",'').removeClass("noselects");
                    // $(".dogs").removeClass("noselects");
                }
                //alert(is_run);
                if(!is_run)
                {
                    $(".ydlxz").css("display",'none')
                    $(".wrap").css("display",'none');
                    $(".slider-status").addClass("noselects");
                }
                else
                {
                    $(".ydlxz").css("display",'')
                    $(".wrap").css("display",'');
                    $(".slider-status").removeClass("noselects");
                }
                if(!is_time)
                {
                    $(".swl_dl").css("display",'none').addClass("noselects");
                    $(".jieduandiv").css("display",'none').addClass("noselects");
                    // $(".swl_dl").addClass("noselects");
                }
                else
                {
                    $(".swl_dl").css("display",'').removeClass("noselects");
                    $(".jieduandiv").css("display",'').removeClass("noselects");
                }
                _diy.feedw();

                //阶段点击时间

                $('.swl_dl input').click(function(){
                    //兼容前段act属性
                    if ($(this).parent(".jj_rad").hasClass("act"))
                    {
                        return ;
                    }
                    $(".jj_rad").each(function(){
                        $(this).removeClass("act");
                    })
                    $(this).parent(".jj_rad").addClass("act");

                    // if ($(this).is(":checked")) {
                    //     return;
                    // }
                    _diy.addweightl($(".swl_dl").find("input:checked").data("wmin"),$(".swl_dl").find("input:checked").data("wmax"));
                    $("input[name='weight']").val("");
                    $(".fffeed_w").text(0);
                    $(".ffnums").text(0);
                    //$(this).addClass('on').siblings().removeClass('on');
                    //_diy.fweight();
                    //_diy.feedw();
                });

            },
            error:function(){
                //信息框
                luck.open({
                    type:1,
                    content: '<div style="padding:20px;">为准确计算您爱宠的饲喂量，请在包装规格处填写爱宠体重!<p style="text-align:center;margin-top:20px;"><input type="button" class="button button-small button-primary button-rounded" value="我知道了" onclick="luck.close()"></p></div>'
                });
            }
        });
    },

    //格式化weitht iput框的 范围值
    addweightl:function (wmin,wmax) {
        $(".w_min").text(wmin);
        $(".w_max").text(wmax);
        // $("input[name=weight]").attr("data-min",wmin);
        // $("input[name=weight]").attr("data-max",wmax);
        $("input[name=weight]").attr("min",wmin);
        $("input[name=weight]").attr("max",wmax);

    },

    //weitht 值发生变化的时候 调用
    fweight:function()
    {
        return true;
        // var min = $("input[name='weight']").data("min")*1;
        // var max = $("input[name='weight']").data("max")*1;
        var min = $(".w_min").text()*1;
        var max = $(".w_max").text()*1;
        var val = $("input[name='weight']").val()*1;
        if(!val)
        {
            $("input[name='weight']").val("");
            return;
        }
        // alert(min+'--'+max+'------'+val);
        if(val < min)
        {
            val = min
        }
        if(val > max)
        {
            val = max;
            // alert('dayu')
        }
        $("input[name='weight']").val(val);
    },


    //处理价格
    fprice:function()
    {
        $(".tishi").text("选择三种功效是为保证最大功效，如果您选择更多的功效，将降低功效的有效性，如您希望更多功效，建议您重新下单");
        var qq = parseInt($('.xuan34').find('li.on').attr('sid')); // 犬期
        var gg = parseInt($('.xuan11').find('li.on').attr('sid')); // 规格
        var bz = parseInt($('.xuan16').find('li.on').attr('sid')); // 包装
        var gongxiao = "";
        $('.xuan1').find('li.on').each(function(){ // 功效
            //gxall += ($(this).attr('data-uprice')/gx);
            gongxiao += $(this).attr('sid')+"|";
        });
        $('.cdiy_price').text(0.00);
        if(!gongxiao)
        {
            return;
        }
        var jiliao_price = 0;
        var guige_price = 0;
        var is_alone = 0;
        //alert(qq);
        //计算基料的价格
        var ress = $.ajax({
            url:'/fdiy-formPrice.html?ajax',
            type:'POST',
            dataType:"json",
            //async:false,
            data:{
                cate_id:_diy.quanzhong_son,
                is_try:_diy.is_try,
                age_id:qq,
                baozhuang:bz,
                guige:gg,
                gongxiao:gongxiao,
                mfd_reload_mid:_diy.mid,
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
//alert('(规格重量 '+gg+' * 口味价格'+kw+' * (1 - 功效选择个数'+gxnum+' * 0.25)) + (规格重量'+gg+' * 0.25 * 功效选择个数'+gxnum+' * 功效均价'+(_diy.effectprice)+' * (选择功效总和'+gxall+') / 功效数量'+gxnum+' ) + 包装价格'+bz);
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
                                $(".tishi").text("由于减肥瘦身与其他功效产品在蛋白、脂肪含量中存在很大差异，建议先帮助您的爱犬恢复到理想体重，再选择其他功效");
                                var price = ((gg * 1 * (_diy.effectprice) * (gxall) / gxnum ) +  bz*1)*parseInt(num);
                            }
                            else
                            {
                                var price = (((gg * kw * (1 - gxnum * 0.25)) + (gg * 0.25 * gxnum * (_diy.effectprice) * (gxall) / gxnum )) +  bz*1)*parseInt(num);
                            }
                        }
                        else
                        {
                            var price = (((gg * kw * (1 - gxnum * 0.25))) +  bz*1)*parseInt(num);
                        }
                    }


                    //alert(price)
                    price = (price*(res.retval.is_youhui));
                    price = price.toFixed(2);
                    $('.cdiy_price').text( price );
                    _diy.xiaoguo();
                }
                else
                {
                   // alert(res.msg);
                    $('.cdiy_price').text("0.00");
                    alert(res.msg);
                }
            },
            error:function(){
                // layer.open({
                //     content: '服务器网络异常'
                //     ,skin: 'msg'
                //     ,time: 2 //2秒后自动关闭
                // });

                // alert('error004');
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
         if(gongxiao)
         {
        
         	$(".sxuan1").text(gongxiao);
         	///$(".axuan1").addClass("on");
         }
         else
     	 {
        	// $(".axuan1").removeClass("on");
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
         if(kw)
         {
        	// $(".axuan6").addClass("on");
         	$(".sxuan6").text(kw);
         }
         else
     	{
         	$(".sxuan6").text("未设置");
         //	$(".axuan6").removeClass("on");
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
        	//	$(".axuan34").addClass("on"); 
         	$(".sxuan34").text(qq);
         }
         else
     	{
        	// $(".axuan34").removeClass("on");
         	$(".sxuan34").text("未设置");
     	}
     	//处理包装图url(../images/dzjs.png) left top no-repeat
        var bz_id = $('.xuan16').find('li.on').attr("sid");
        var url = '/diy/images/dzjs_'+bz_id+'.png';
    },
    
    run:function(){
        var diyconsolehtml=''; // 子级导航数据
        var diycall='';
        _diy.xiaoguo();
        
        for (var i=0;i<$('.swiper-slide_mfd').length;i++)
        {
            if(i>0){
                $('.swiper-slide_mfd').eq(i).each(function(i){
                        _diy.runrule();
                      //  $(this).find('li:not(.noselect)').eq(0).addClass('on');
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
    rule: $rule,//冲突规则
    mid: $mid,//会员
    cid: $cid,//一级
    sid: $sid,//二级
    quanzhong: $quanzhong,//二级
    quanzhong_son: $quanzhong_son,//二级
    is_try: $is_try,//二级
    gxmaxnum: $gxmaxnum,//功效选择上限
    effectprice: $effectprice,//功效均价
});
diy.init();

$('.plus').click(function(){
	//alert($(this).data("id"))
	var num = $(".goods_num").val();
	$(".goods_num").val(parseInt(num) + 1);
	diy.js();
});

$('.reduce').click(function(){
	//alert($(this).data("id"))
	var num = $(".goods_num").val();
	if(num == 1)
	{
		return;
	}
	$(".goods_num").val(parseInt(num) - 1);
	diy.js();

});



$("input[name='weight']").bind('input propertychange', function() {
    //if (!($(".swl_dl").hasClass('noselects')) && ($('.swl_dl option:selected').val()*1 <= 0))
    // var fff = $('.swl_dl input:checked').val()-0;
   // alert($(".swl_dl input[name=time_idaa][checked]").val());
    if (!($(".swl_dl").hasClass('noselects')) && ($(".swl_dl input:checked").val() == undefined))
    {

    }
    var min = $(".w_min").text()*1;
    var max = $(".w_max").text()*1;
    var val = $("input[name='weight']").val()*1;
    if (val < min || val > max)
    {
        //return false;
    }


    _diy.feedw();
});

$("select[name='dog_nums']").change(function(){
    var val = $(this).val()*1;
    if (val > 20)
    {
        $(this).val(20);
    }
    // _diy.fweight();
    _diy.feedw();
});


$("input[name='weight']").blur(function(){

    // _diy.fweight();
   _diy.feedw();
// alert(min+"--"+val+"--"+max);
});


function shuliang() {
    _diy.feedw();
}



//时间轴点击事件
$('.subfeed').click(function(){
    _diy.feedw();
});

//体况点击事件
$('.dogs dd').click(function(){
    $(this).addClass('on').siblings().removeClass('on');
    _diy.feedw();
});

function GetQueryString(name){
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!==null)return  unescape(r[2]); return null;
}

function readFile(){
    window.location.href="https://www.diyupload.com";
}
function uploadimg(obj){

}

