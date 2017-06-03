/**
 * @name 瀑布流效果
 */
;(function(){
    $.rc.wall = {
        settings : {
            container: '#J_waterfall', //容器
            item_unit: '.J_item', //商品单元
            loading_bar: '#J_wall_loading', //加载条
            page_bar: '#J_wall_page',
            ajax_url: null, //请求地址
            distance: 50, //高度微调
            spage: 1,
            max_spage: 5
        },
        init: function(options){
            options && $.extend($.rc.wall.settings, options);
            var s = $.rc.wall.settings;
            s.ajax_url = $(s.container).attr('data-uri');
            var distance = $(s.container).attr('data-distance');
            if(distance != void(0)){
                s.distance = distance;
            }
            //使用masonry插件
            $(s.container)[0] && $(s.container).imagesLoaded( function(){
                $(s.container).masonry({
                    itemSelector: s.item_unit
                });
                $(s.item_unit).animate({opacity: 1});
            });
            $.rc.wall.is_loading = !1;
            $(window).bind('scroll', $.rc.wall.lazy_load);
        },
        //加载
        lazy_load: function(){
            var s = $.rc.wall.settings,
                st = $(document).height() - $(window).scrollTop() - $(window).height();
            if (!$.rc.wall.is_loading && $(s.loading_bar)[0] && st <= s.distance) {
                $.rc.wall.is_loading = !0;
                $.rc.wall.loader();
            }
        },
        //加载状态
        is_loading: !0,
        //执行加载
        loader: function(){
            var s = $.rc.wall.settings;
            $(s.loading_bar).show();
            $.ajax({
                url: s.ajax_url,
                data: {sp: s.spage},
                type: 'GET',
                dataType: 'json',
                success: function(result){
                    if(result.status == 1){
                        var html = $(result.data.html).css({opacity: 0});
                        $.rc.ui.decode_img(html);
                        html.find('.J_img').imagesLoaded(function(){
                            html.animate({opacity: 1});
                            $(s.container).append(html).masonry('appended', html, true, function(){
                                $(s.loading_bar).hide(); //隐藏加载条
                                $.rc.wall.is_loading = !1; //可以继续加载
                                s.spage += 1; //页码加1
                                if(s.spage >= s.max_spage || !result.data.isfull){
                                    $(s.page_bar).show(); //子页加载完毕
                                    $(window).unbind('scroll', $.rc.wall.lazy_load);
                                }
                                !result.data.isfull && $(s.loading_bar).remove();
                            });
                        });
                    }else{
                        $.rc.tip({content:result.msg, icon:'error'});
                    }
                }
            });
        }
    }
    $.rc.wall.init({distance:PINER.config.wall_distance, max_spage:PINER.config.wall_spage_max});
})(jQuery);