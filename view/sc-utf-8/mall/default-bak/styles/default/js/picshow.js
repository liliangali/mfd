/**
 * 大视野图库 add by yubin 这版由于需求改动比较快。。写的有点乱
 */
var _gaq = _gaq || [];
//var bdShare = {};

function _trackPageview(url)
{
	_gaq.push(['gqTracker._trackPageview', url.split("com.cn")[1]]);
	_trackDfpAd();
}

function _trackDfpAd(){
	var img = new Image();
	var regexp = /google_ads_div_([\w|\d|_]*)_ad_wrapper/g;
	regexp.exec($(".AD300-250").html());
	var adCode = RegExp.$1;
	if( !adCode ){
		return ;
	}
	var strs = adCode.split("_");
	var adSize = strs[ strs.length-1 ];
	img.src = "http://pubads.g.doubleclick.net/gampad/ad?iu=/30096625/"+ adCode +"&sz="+ adSize +"&c=" + new Date().getTime();
}

var $_CNC_PHOTO = 
{
    v_p : '.cnc_player',
    v_o : '.cnc_photo',
    v_s : '.cnc_stop',
    v_t : '.cnc_start',
    v_n : '.cnc_num',
    v_u : '.cnc_up',
    v_d : '.cnc_down',
    v_a : '.cnc_total',
    v_l : '.cnc_left',
    v_r : '.cnc_right',

    type : 1,//1为纵向，2为横向
    width : 120,
    height : 100,//每个小图片的高度
    object : '',//
    count : 0,//计数器
    inter : false,
    total : 0,
    time : 5000,
    cache : '',
    position : 7,//当多余这个数量时，才进行滚动
    cur : 1,//当前页数
    page : 1,//页数
    num : 0,//用户点击图片的计数，用于广告显示
    size :7,//每个图片间隔

    ad_div : '#cnc_pic_ad .cnc_pic_ad',//新增的广告功能
    ad_list : '#cnc_pic_ad .cnc_pic_adlist',//广告列表（直接读广告有问题。。只能这样了）
    ad_num : 6, //滚动到第几张时出现广告
    ad_inter : false,//广告倒计时
    ad_show : 5,//广告显示时长
    ad_txt : '.cnc_player .cnc_pic_ad_txt',//广告显示文字，一般为倒计时文字
    ad_button : '#cnc_pic_ad .cnc_pic_ad_button',//跳过广告的按钮
    ad_state : true, //是否开启广告

    link_up : '',//上一页地址
    link_down : '',//下一页地址
    
    

    init : function(option)
    {
        var t = this;
        t.type      = option.type || t.type;
        t.link_up   = option.link_up || t.link_up;
        t.link_down = option.link_down || t.link_down;
        t.position = option.position || t.position;
        t.size = option.size || t.size;
        this.object = $(this.v_o + ' li');
        t.titles = $(".filetitle");
        t.descs = $(".filedesc");

        t.total = this.object.length;

        t.page = Math.ceil(t.page+((t.total-t.position)/t.position));

        $(t.v_a).html(t.total);

        t.bind(t);
        
        if(location.href.indexOf('#id-') != -1)
        {
            var array = location.href.split('#');
            for(var a in array)
            {
                if(array[a].indexOf('id-') != -1)
                {
                    var url = array[a].split('id-');
                    var id = url[url.length-1];
                    reg = /^[0-9]{1,20}$/;
                    t.object.eq(reg.exec(id)).click();
                }
            }
        }
        else
        {
            t.object.eq(0).click();
        }
        t.set();
        $(t.v_o).fadeIn();
    },
    set : function()
    {
        if($(this.v_s).length && $(this.v_s).get(0).style.display == 'none')
        {
            return;
        }
        var t = this;
        this.clear();

        this.inter = setInterval(function()
        {
            if(t.count >= (t.total-1))
            {
				if(t.link_down)
                {
                    location.href=t.link_down;
                }
                //$(t.v_s).click();
                //最后一张停止滚动。
                t.clear();
                //t.count = 0;
                return;
            }
            else
            {
                t.count++;
            }
            t.object.eq(t.count).click();
        }, t.time);
    },
    clear : function()
    {
        if(this.inter) clearInterval(this.inter);
    },
    ad : function(t)
    {
        t.unbind(t);
        var n = t.ad_show;
        t.ad_inter = setInterval(function()
        {
            if(n <= 0)
            {
                t.ad_hide();
            }
            else
            {
                $(t.ad_txt).html(n).show(100);
            }
            n--;
        }, 1000);
    },
    ad_clear : function(t)
    {
        if(this.ad_inter) clearInterval(this.ad_inter);
        t.bind(t);
        t.object.eq(t.count).click();
        t.set();
    },
    ad_hide : function()
    {
        $(this.ad_txt).hide(100);
        this.ad_clear(this);
        if($("#left-btn-area").length)
        {
            $("#left-btn-area").show();
            $("#right-btn-area").show();
        }
    },
    click : function(i,t,e)
    {
        if(t.type == 2)
        {
            t.width = e.width()+10;
        }
        if(t.type == 1)
        {
            t.height = e.height();
        }
        t.count = i;
        t.num++;
        var j = 0;
        if(t.ad_state == true)
        {
            var r = /^\+?[1-9][0-9]*$/;
            var n = t.num/t.ad_num;
            if(r.test(n))
            {
                var m = $(t.ad_div).eq(n-1);
                var k = $(t.ad_list).eq(n-1);
                if(!m.length || !k.length)
                {
                    n = 1;
                    m = $(t.ad_div).eq(n-1);
                    k = $(t.ad_list).eq(n-1);
                }
                if(m.length && k.length)
                {
                    t.clear();
                    var frame = k.find('.cnc_pic_adlist_frame').contents().find(".ad");
                    /*
					if(frame.find("#google_ads_iframe_" + k.attr('rel')).length)
                    {
                       var g = frame.find("#google_ads_iframe_" + k.attr('rel')).contents();
                    }
					*/
					if(frame.find("iframe").length)
                    {
                       var g = frame.find("iframe:eq(0)").contents();
                    }
                    else
                    {
                        var g = frame.find("#google_ads_div_"+k.attr('rel')+"_ad_container");
                    }
                    if($(".cnc_pic_ad_a").length)
                    {
                        $(".cnc_pic_ad_a").hide();
                    }
                    if(g.find('img').attr('src') || g.length)
                    {
                        if(g.find('img').attr('src'))
                        {
                            m.find('.cnc_pic_ad_div').html('<a href="'+g.find('a').attr('href')+'" target="_blank"><img src="'+g.find('img').attr('src')+'"/></a>');
                        }
                        else
                        {
                            m.find('.cnc_pic_ad_div').html(frame.html());
                        }

                        $(t.v_p).html(m.html());

                        k.find('.cnc_pic_adlist_frame').attr('src', k.find('.cnc_pic_adlist_frame').attr('src'));
                        $("#left-btn-area").hide();
                        $("#right-btn-area").hide();
                        $(t.v_p).find('.cnc_pic_ad_button').unbind('click').click(function()
                        {
                            t.ad_hide();
                        });
                        t.ad(t);
                        return;
                    }else{
                        t.set();
                    }
                }
            }
        }
        
        //var src = e.find('img').attr('src').split('_');
        var src = e.find('img').attr('bigsrc');
		var imgtitle = e.find('img').attr('alt');
		
        if(!src)
        {
            src = e.find('img').attr('src').split('_') + '.jpg';
            src= src[0];
        }

        if(t.titles.length > 1){
            t.titles.hide();
            t.titles.eq(i).show();
        }
        if(t.descs.length > 1){
            t.descs.hide();
            t.descs.eq(i).show();
        }

        t.cache = '<a hrefs="'+e.find('img').attr('href')+'" class="binda" href="javascript:;"><img src="' + src + '" alt="'+imgtitle+'"/></a>';

        if($("#left-btn-area").length)
        {
            t.cache = $(t.cache);
            t.cache.find("img").bind("load", function(){
                $("#left-btn-area").height($(".gqview-photo").height());
                $("#right-btn-area").height($(".gqview-photo").height());
            });
        }
        $(t.v_p).html(t.cache).css({opacity: 0}).stop().animate({opacity: 1}, 1500).show();

        if(!$(t.v_p).find('.binda').attr('click'))
        {
            /*
            $(t.v_p).find('.binda').click(function(){$(t.v_r).mouseup()}).mouseover(function()
            {
                t.clear();
            }).mouseout(function()
            {
                t.set();
            });
            */
        }

        if(location.href.indexOf('#id-') != -1)
        {
            var href = '';
            var array = location.href.split('#');
            for(var a in array)
            {
                if(a == 0)
                {
                    href = array[a];
                }
                else if(array[a].indexOf('id-') == -1)
                {
                    href += '#' + array[a];
                }
                else
                {
                    href += '#id-' + i;
                }
            }

            location.href = href;
        }
        else
        {
            location.href=location.href + "#id-" + i;
        }
        _trackPageview(location.href);
		
        //CNC.Share.sPic = src;
		//CNC.Share.sUrl = location.href;
        CNC.Share.prototype.resetShareData({sPic : src , sUrl : location.href });
        
		

        //$.get(location.href);
        /*
        j = i;
        if(i < t.total && t.total > t.position)
        {
            if((t.total - i) < t.position)
            {
                j = t.total - t.position;
            }
            var top = 0 - (j*t.height+t.size*j);
            $(this.v_o).animate({top: top}, 500);
        }
        */
        if(i > 0)
        {
            var a = Math.ceil((((i+1)-t.position)/t.position))+1;
            if(a != t.cur)
            {
                t.turn(t, a);
            }
        }
        $(t.v_n).html(i+1);
        t.object.removeClass('now');
        e.addClass('now');
    },
    bind : function(t)
    {
        t.object.each(function(i)
        {
            $(this).click(function()
            {
                t.click(i,t,$(this));
            }).mouseover(function()
            {
                t.clear();
            }).mouseout(function()
            {
                t.set();
            });
        });

        if($(this.v_s).length)
        {
            $(this.v_s).click(function()
            {
                t.clear();
                $(this).hide();
                $(t.v_t).fadeIn();
            });
        }

        $(this.v_t).click(function()
        {
            $(this).hide();
            $(t.v_s).fadeIn();
            t.set();
        });

        $(this.v_l).mouseup(function()
        {
            var i = t.count-1;
            if(i>=0)
            {
                t.object.eq(i).click();
            }
            else
            {
                if(t.link_up)
                {
                    location.href=t.link_up;
                    return;
                }
                i = t.count = 0;
                t.object.eq(i).click();
            }
        }).mouseover(function()
        {
            if($("#left-btn-area").length)
            {
                $(".gqview-bigLbtn").show();
            }
            t.clear();
        }).mouseout(function()
        {
            if($("#left-btn-area").length)
            {
                $(".gqview-bigLbtn").hide();
            }
            t.set();
        });

        $(this.v_r).mouseup(function()
        {
            var i = t.count+1;
            if(i<t.total)
            {
                t.object.eq(i).click();
            }
            else
            {
                t.clear();
                //最后一张
                i = t.count = t.total - 1;
                //跳转下一页
                if(t.link_down)
                {
                    location.href=t.link_down;
                }
                //i = t.count = 0;
                //t.object.eq(i).click();
            }
        }).mouseover(function()
        {
            if($("#right-btn-area").length)
            {
                $(".gqview-bigRbtn").show();
            }
            t.clear();
        }).mouseout(function()
        {
            if($("#right-btn-area").length)
            {
                $(".gqview-bigRbtn").hide();
            }
            t.set();
        });

        $(this.v_u).mouseup(function()
        {
            if(t.cur == 1)
            {
                if(t.link_up)
                {
                    location.href=t.link_up;
                }
                return;
            }
            t.cur--;
            t.turn(t, t.cur);
            t.set();
        }).mouseover(function()
        {
            t.clear();
        }).mouseout(function()
        {
            t.set();
        });

        $(this.v_d).mouseup(function()
        {
            if(t.cur == t.page)
            {
                if(t.link_down)
                {
                    location.href=t.link_down;
                }
                return;
            }
            t.cur++;
            t.turn(t, t.cur);
            t.set();
            
        }).mouseover(function()
        {
            t.clear();
        }).mouseout(function()
        {
            t.set();
        });
    },
    unbind : function(t)
    {
        $(t.v_p).find('.binda').unbind('click').unbind('mouseover').unbind('mouseout');
        $(t.v_s).unbind('click');
        $(t.v_t).unbind('click');
        $(t.v_l).unbind('mouseup').unbind('mouseover').unbind('mouseout');
        $(t.v_r).unbind('mouseup').unbind('mouseover').unbind('mouseout');
        $(t.v_u).unbind('mouseup').unbind('mouseover').unbind('mouseout');
        $(t.v_d).unbind('mouseup').unbind('mouseover').unbind('mouseout');

        t.object.each(function(i)
        {
            $(this).unbind('click');
        });
    },
    turn : function(t, i)
    {
        t.cur = i;
        if(i <= 0)
        {
            return;
        }
        var send = {};
        //翻页
        if(t.type == 1)
        {
            i--;
            send.top = 0 - (i*t.height+t.size*i)*t.position;
        }
        if(t.type == 2)
        {
            i--;
            //t.size = 9;
            send.left = 0 - (i*t.width+t.size*i)*t.position;
        }

        $(t.v_o).animate(send, 500);
    },
    scoll : function(option)
    {
        var t = this;
        t.type      = option.type || t.type;
        var w = $(t.v_o).width();
        //$(t.v_o).stop().animate({"left":-1*w}, 500).show();

        $(t.v_u).click(function()
        {
            t.count--;
            $(t.v_o).stop().animate({"left":-1*t.count*500}, 500).show();
        });

        $(t.v_d).click(function()
        {
            t.count++;
            $(t.v_o).stop().animate({"left":-1*t.count*500}, 500).show();
            
        });
    }
}


