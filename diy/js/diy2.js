
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
        _diy.feedtme();

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
        },
        bind:function(){
            // 元素选择
            $('.ul_list li').live('click',function(){
                var stype = $(this).parents('.ul_list').attr('s-type'); // 是否多选 1多选 其他单选
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

                        if($(this).siblings('.on').not('.noselect').length < _diy.gxmaxnum ){
                            $(this).addClass('on');
                        }
                        else
                        {
                            luck.poptip({
                                con:'亲，最多选择3种功效哦！',
                            });
                            return;
                        }
                    }
                }else{
                    $(this).addClass('on').siblings().removeClass('on');

                }


                if(pid == 34)//犬期重新计算饲料
                {
                    _diy.feedtme();
                    //犬期把功效的选中状态去掉
                    $(".rule107").removeClass("on");

                }
                _diy.runrule();
                _diy.js();

            });

            //加入购物车
            $('.addCart').on({
                "click":function() {
                    _diy.checkLogin(function(){
                        //计算饲喂量
                        var w = $("input[name='weight']").val();
                        if (w <= 0)
                        {
                            luck.poptip({
                                con:"为准确计算饲喂量，请您输入爱宠体重！",
                                callback:function(){
                                    luck.close();
                                    document.getElementById("idweight").focus().val;
                                }
                            });
                            var next_pid = 4;
                            $(".pxuan"+next_pid).addClass("on").siblings().removeClass("on");
                            var ss = $(".show1").eq($(".tab_tit li").index($(".tab_tit").find("li.on")));
                            // alert(ss)
                            ss.show().siblings().hide();
                            $('.button_box').show();
                            return false;

                        }


                        var dog_name = $("input[name=dog_name]").val();
                        var dog_date = $("input[name=dog_date]").val();
                        if(!dog_name || !dog_date)
                        {
                            layer.confirm('亲，您真不打算给爱宠定制一个独一无二的个性化标签了？', {
                                btn: ['先不填写了','去设计一个个性化标贴去！'] //按钮
                            }, function(){
                                layer.closeAll('dialog');
                                _diy.addCart();
                            }, function(){
                                var next_pid = 5;
                                $(".pxuan"+next_pid).addClass("on").siblings().removeClass("on");
                                var ss = $(".show1").eq($(".tab_tit li").index($(".tab_tit").find("li.on")));
                                // alert(ss)
                                ss.show().siblings().hide();
                                $('.dog_name').show();

                            });
                        }
                        else
                        {
                            _diy.addCart();
                        }
                    });


                }
            });

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

        //加入购物车
        addCart:function () {
            $("#jiesuan").text("结算中...");

            _diy.checkLogin(function(){

                var qid=$quanzhong_son;
                var postObj = {};
                //犬种选择
                postObj[_diy.quanzhong] = _diy.quanzhong_son;
                var is_ch = 1;
                $('.ul_list').each(function(i){
                    if($(this).find('.on').not(".noselect").length===0){
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
                        $(this).find('.on').not(".noselect").each(function(){
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
                if (dog_name)
                {
                    var len = _diy.strlen(dog_name);
                    if(len > 10)
                    {
                        luck.poptip({
                            con:"狗狗名称过长,建议10个字符以内",
                        });
                        return false;
                    }
                }

                if (dog_desc)
                {
                    var len = _diy.strlen(dog_desc);
                    if(len > 50)
                    {
                        luck.poptip({
                            con:"主人寄语过长,建议25个汉字以内",
                        });
                        return false;
                    }
                }






                //计算饲喂量
                var w = $("input[name='weight']").val();
                var dog_nums = $("input[name='dog_nums']").val();
                if (w <= 0)
                {
                    luck.poptip({
                        con:"为给您的爱宠准确计算喂食量，请如实填写爱宠体重！",
                        callback:function(){
                            luck.close();
                            document.getElementById("idweight").focus().val;
                        }
                    });
                    var next_pid = 4;
                    $(".pxuan"+next_pid).addClass("on").siblings().removeClass("on");
                    var ss = $(".show1").eq($(".tab_tit li").index($(".tab_tit").find("li.on")));
                    // alert(ss)
                    ss.show().siblings().hide();
                    $('.button_box').show();
                    return false;
                }

//询问框





                if(!($(".dog_nums").hasClass("noselects")))
                {
                    if (dog_nums <= 0)
                    {
                        luck.poptip({
                            con:"请填写哺乳小狗数目",
                        });
                        return false;
                    }
                }


                var time_id = $(".swl_dl").not('.noselects').find("dd.on").data("id")*1;
                var body_id = $(".dogs").not('.noselects').find("dd.on").data("id")*1;
                var run = $(".slider-status").not('.noselects').text();
                run = run.replace(/h/, "")*1;
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
                        body_condition:body_id,
                        run_time:run_id,
                        time_id:time_id,
                        weight : w,
                        dog_nums : dog_nums,
                        params:JSON.stringify(postObj),//配置
                        // fpic:$('.file_img').attr('src') === '/diy/images/sytu.png' ? '' : $('.file_img').attr('src'),//个性化标签设置
                        img_dir:$('.croppedImg').attr('src') === '/diy/images/sytu.png' ? '' : $('.croppedImg').attr('src'),//个性化标签设置
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
                    // error:function(){
                    //     $("#jiesuan").text("去结算");
                    //     luck.poptip({
                    //         con:'网络异常，请稍后再试！'
                    //     })
                    // }
                });

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
            console.log($('.noselect'));
            $('.noselect').removeClass('noselect');
            // alert(11)

            $.each(_diy.rule,function(i,d){//循环冲突规则数据
                var collection=d.collection.split(','); // rules

                if(collection.length===1){//单一互斥处理
                    if($('.rule'+collection).is('.on')){
                        $.each(d.rules.split(','),function(i,d){
                            // $('.rule'+d).removeClass('on').addClass('noselect');
                            $('.rule'+d).addClass('noselect');
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
            _diy.xiaoguo();
           // _diy.feedtme();
        },


        //根据体重 计算饲喂量
        feedw:function(){

            $(".fffeed_w").text(0);
            $(".ffnums").text(0);
            $(".siweiliang").text(0);

            var w = $("input[name='weight']").val();
            if(w <= 0)
            {
                return ;
            }

            var dog_nums = $("input[name='dog_nums']").val();
            var time_id = $(".swl_dl").not('.noselects').find("dd.on").data("id")*1;
            var body_id = $(".dogs").not('.noselects').find("dd.on").data("id")*1;
            var run = $(".slider-status").not('.noselects').text();
            run = run.replace(/h/, "")*1;

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
            var qq = parseInt($('.xuan34').find('li.on').not(".noselect").attr('sid')); // 犬期
            var qzs = this.quanzhong_son;
            $.ajax({
                type: "POST",
                url: "fdiy-formFeed.html",
                dataType:'json',
                data: "is_feed=0&cat_id="+qzs+"&age_id="+qq+"&body_condition="+body_id+"&run_time="+run_id+"&weight="+w+"&time_id="+time_id+"&dog_nums="+dog_nums+"&gongxiao="+gongxiao,
                success: function(res){
                  if(res.done)
                  {
                      var feed_w = res.retval.feed_w;
                      var feed = res.retval.feed;
                      var nums = res.retval.nums;
                      if (feed == 1)
                      {

                        $(".swl_text").empty();
                          $(".swl_text").text("喂食量: 自由采食");
                      }
                      else
                      {
                          $(".fffeed_w").text(feed_w);
                          $(".ffnums").text(nums);
                          $(".siweiliang").text(feed_w);
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
                url: "fdiy-formFeed.html",
                dataType:'json',
                data: "is_feed=1&cat_id="+qzs+"&age_id="+qq,
                success: function(res){
                    var tstr = "<dt>阶段：</dt>";
                    var is_body = 0;
                    var is_run = 0;
                    var is_time = 0;
                    var default_weight = 0;
                    //遍历时间阶段

                    $.each(res.retval.time_list, function(i, n){
                        var cls = "non";
                        if((i == 0)  && (res.retval.time_num == 1))
                        {
                            default_weight = n.default_weight;
                            cls = "on";
                            _diy.addweightl(n.wt_min,n.wt_max);
                        }
                        if(n.time_id > 0)
                        {
                            tstr += "<dd class="+cls+"   data-id="+n.time_id+" data-wmax="+n.wt_max+" data-wmin="+n.wt_min+" data-default="+n.default_weight+">"+n.time_name+"</dd>"
                        }
                    });
                    $(".swl_dl").empty();
                    $(".swl_dl").append(tstr);

                    //遍历参数
                    $.each(res.retval.feed_list, function(i, n){
                        if (default_weight == 0)
                        {
                            default_weight = n.default_weight;
                        }
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

                    //默认体重

                    // if(default_weight > 0)
                    // {
                    //     $("input[name='weight']").val(default_weight);
                    // }



                    //取消隐藏参数
                    if(!is_body)
                    {
                        $(".dogs").css("display",'none').addClass("noselects");
                        // $(".dogs").addClass("noselects");
                    }
                    else
                    {
                        $(".dogs").css("display",'').removeClass("noselects");
                        // $(".dogs").removeClass("noselects");
                    }
                    //alert(is_run);
                    if(!is_run)
                    {
                        $(".User_grade").css("display",'none');
                        $(".slider-status").addClass("noselects");
                    }
                    else
                    {
                        $(".User_grade").css("display",'');
                        $(".slider-status").removeClass("noselects");
                    }
                    if(!is_time)
                    {
                        $(".swl_dl").css("display",'none').addClass("noselects");
                        // $(".swl_dl").addClass("noselects");
                    }
                    else
                    {
                        $(".swl_dl").css("display",'').removeClass("noselects");
                    }

                  //  _diy.feedw();


                    //阶段点击时间
                    $('.swl_dl dd').click(function(){
                        _diy.addweightl($(this).data("wmin"),$(this).data("wmax"),$(this).data("default"));
                        $(this).addClass('on').siblings().removeClass('on');
                        $("input[name='weight']").val("");
                        $(".fffeed_w").text(0);
                        $(".ffnums").text(0);
                       // _diy.fweight();
                        //_diy.feedw();
                    });


                },
            });
        },

     //格式化weitht iput框的 范围值
       addweightl:function (wmin,wmax,default_weight) {
           $(".w_min").text(wmin);
           $(".w_max").text(wmax);
           //$(".default_weight").text(default_weight);
           $("input[name=weight]").attr("data-min",wmin);
           $("input[name=weight]").attr("data-max",wmax);
           // alert('1')
       },

     //weitht 值发生变化的时候 调用
      fweight:function()
      {

          // var min = $("input[name='weight']").data("min");
          // var max = $("input[name='weight']").data("max");
          var min = $(".w_min").text()*1;
          var max = $(".w_max").text()*1;
          var val = $("input[name='weight']").val();
          var default_weight = $("input[name='weight']").attr("data-default");
// alert(default_weight)
          //默认体重
          // if(!($("input[name='weight']").val()))
          // {
          //     $("input[name='weight']").val(default_weight);
          // }
          // console.log(val)
          if(val == '')
          {
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
            var qq = parseInt($('.xuan34').find('li.on').not(".noselect").attr('sid')); // 犬期
            var gg = parseInt($('.xuan11').find('li.on').not(".noselect").attr('sid')); // 规格
            var bz = parseInt($('.xuan16').find('li.on').not(".noselect").attr('sid')); // 包装
            var gongxiao = "";
            $('.xuan1').find('li.on').not(".noselect").each(function(){ // 功效
                //gxall += ($(this).attr('data-uprice')/gx);
                gongxiao += $(this).attr('sid')+"|";
            });
            $('#price').text(0.00);
            if(!gongxiao)
            {
                return;
            }



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
                        var gxnum = parseInt( $('.xuan1 ').find('li.on').not(".noselect").length ); // 功效
                        //alert(gxnum);
                        var gxall=0;//功效选中个数的钱数和
                        //var is_jianfei = 0;
                        $('.xuan1').find('li.on').not(".noselect").each(function(){ // 功效
                            //gxall += ($(this).attr('data-uprice')/gx);
                            var sid = $(this).attr('sid');
                            //alert(gp_price[sid]);
                            gxall += (gp_price[sid]/gx);
                        });
                        gxall = gxall.toFixed(2);
                        //alert(gxall)
                        // var kw = ( $('.xuan6').find('li.on').attr('data-uprice') ); // 口味
                        var kw = jiliao_price;
                        var gg = ( $('.xuan11').find('li.on').not(".noselect").attr('ve') ); // 规格
                        //var bz = ( $('.xuan16').find('li.on').attr('data-fprice') ); // 包装形式
                        var bz = guige_price;
                        //alert((gg * kw * (1 - gxnum * 0.25)) );
                        //alert((gg * 0.25 * gxnum * (_diy.effectprice) * (gxall) / gxnum ));
                        //alert((gg * kw * (1 - gxnum * 0.25))  + (gg * 0.25 * gxnum * (_diy.effectprice) * (gxall) / gxnum ) + bz*1);
    // alert('(规格重量 '+gg+' * 口味价格'+kw+' * (1 - 功效选择个数'+gxnum+' * 0.25)) + (规格重量'+gg+' * 0.25 * 功效选择个数'+gxnum+' * 功效均价'+(_diy.effectprice)+' * (选择功效总和'+gxall+') / 功效数量'+gxnum+' ) + 包装价格'+bz);
                        var num = $(".goods_num").val();
    //                     var num = 1;
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
                                    // alert(_diy.effectprice)
                                    var price = (((gg * kw * (1 - gxnum * 0.25)) + (gg * 0.25 * gxnum * (_diy.effectprice) * (gxall) / gxnum )) +  bz*1)*parseInt(num);
                                }
                            }
                            else
                            {
                                var price = (((gg * kw * (1 - gxnum * 0.25))) +  bz*1)*parseInt(num);
                            }
                        }

                        price = (price*(res.retval.is_youhui));
                        price = price.toFixed(2);
                        $('#price').text(price);

                    }
                    else
                    {
                        $('#price').text(0.00);
                         alert(res.msg);
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
            $('.xuan1').find('li.on').not(".noselect").each(function(){ // 功效
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

            //处理生长阶段
            // var gongxiao_cotent = $('.xuan34').find('li.on').not(".noselect").attr("data-content");
            // $(".gongxiao_content").text(gongxiao_cotent);

            //alert(gongxiao);
            var qq = $('.xuan34').find('li.on').not(".noselect").text(); // 犬期
            var kw = $('.xuan6').find('li.on').not(".noselect").text(); // 口味
            var gg = $('.xuan11').find('li.on').not(".noselect").text(); // 规格
            var bz = $('.xuan16').find('li.on').not(".noselect").text(); // 包装形式
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
            // var dog_name = $("input[name=dog_name]").val();
            // var dog_date = $("input[name=dog_date]").val();
            // var dog_desc = $("textarea[name=dog_desc]").val();
            // var dog_src = $(".file_img").attr("src");
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

$("input[name='dog_name']").keyup(function(){
   var name = $(this).val();
    $('.dog_name').text(name);
    
});


$("textarea[name='dog_desc']").keyup(function(){
    var name = $(this).val();
    $('.dog_desc').text(name);

});


$("input[name='weight']").blur(function(){

    if (!($(".swl_dl").hasClass('noselects')) && !($(".swl_dl dd").hasClass('on')))
    {
        layer.msg('请选择阶段!');
    }
   // var time_id = $(".swl_dl").not('.noselects').find("dd.on").data("id")*1;
    var w = $(this).val();
    _diy.fweight();
    _diy.feedw();
// alert(min+"--"+val+"--"+max);
});

$("input[name='dog_nums']").blur(function(){
    var val = $(this).val()*1;
    if (val > 20)
    {
        $(this).val(20);
    }
    
    _diy.feedw();
});

$("#btn0").mouseleave (function () {

    // var run = $(".slider-status").text();
    // run = run.replace(/h/, "")*1;
    // if (run > 3)
    // {
    //     $(".slider-status").text("大于3h");
    // }

    _diy.feedw();
})

//时间轴点击事件
$('.subfeed').click(function(){
    _diy.feedw();
});


//体况点击事件
$('.dogs dd').click(function(){
    //alert($(this).data("id"))
    $(this).addClass('on').siblings().removeClass('on');
    _diy.feedw();
});

$('.div_prev').click(function(){
    var pid = $(".tab_tit").find("li.on").data("pid")*1;
    if(pid == 1)
    {
        window.location.href = "fdiy.html";
        return false;
    }
    var next_pid = pid - 1;
    $('.div_next').css("display","");
    if (next_pid == 1)
    {
      //  $('.prev').css("display","none");
    }

    $(".pxuan"+next_pid).addClass("on").siblings().removeClass("on");
    var ss = $(".show1").eq($(".tab_tit li").index($(".tab_tit").find("li.on")));
    // alert(ss)
    ss.show().siblings().hide();

    $('.button_box').show();
});


$('.div_next').click(function(){
   var pid = $(".tab_tit").find("li.on").data("pid")*1;
    var next_pid = pid + 1;
    $('.div_prev').css("display","");
    if (next_pid >= 6)
    {

        var w = $("input[name='weight']").val();
        if (w <= 0)
        {
            luck.poptip({
                con:"为给您的爱宠准确计算饲喂量，请如实填写爱宠体重!",
                callback:function(){
                    luck.close();
                    document.getElementById("idweight").focus().val;
                }
            });
            var next_pid = 4;
            $(".pxuan"+next_pid).addClass("on").siblings().removeClass("on");
            var ss = $(".show1").eq($(".tab_tit li").index($(".tab_tit").find("li.on")));
            // alert(ss)
            ss.show().siblings().hide();
            $('.button_box').show();
            return false;

        }

        var dog_name = $("input[name=dog_name]").val();
        var dog_date = $("input[name=dog_date]").val();
        if(!dog_name || !dog_date)
        {
            layer.confirm('亲，给您的爱宠设计一个独一无二的包装吧!', {
                btn: ['先不设计了','开始设计'] //按钮
            }, function(){
                // layer.msg('的确很重要', {icon: 1});
                _diy.addCart();
            }, function(){
                var next_pid = 5;
                $(".pxuan"+next_pid).addClass("on").siblings().removeClass("on");
                var ss = $(".show1").eq($(".tab_tit li").index($(".tab_tit").find("li.on")));
                // alert(ss)
                ss.show().siblings().hide();
                $('.dog_name').show();

            });
        }
        else
        {
            _diy.addCart();
        }



        // $('.div_next').css("display","none");

    }

    $(".pxuan"+next_pid).addClass("on").siblings().removeClass("on");
    var ss = $(".show1").eq($(".tab_tit li").index($(".tab_tit").find("li.on")));
    // alert(ss)
    ss.show().siblings().hide();

    $('.button_box').show();
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
