/**
 * @url http://www.rc.com
 */
;(function($){
    $.rc.item = {
        settings: {
            like_btn: '.addalbum_btn',
            item_unit: '.J_item'
        },
        
        init: function(options){
            options && $.extend($.rc.item.settings, options);
            var s = $.rc.item.settings;
            $.rc.item.like();
        },
        //喜欢
        like: function(){
            var s = $.rc.item.settings;
            $(s.like_btn).bind('click', function(){
                if(!$.rc.dialog.islogin()) return !1;
                var self = $(this),
                	nb = self.siblings('.sk_m_xh').find('a'),
                    id = self.attr('data-id'),
                    aid = self.attr('data-aid'),
                    ty = self.attr('data-ty'),
                    n = parseInt(nb.text());
                $.getJSON('/index.php/trends-ajax_like-'+id+'-'+ty+'.html', {}, function(result){
                    if(result.status == 1){
                        //喜欢成功
                    	num = parseInt($("#like_"+id).text());
                    	$("#like_"+id).text(num+1)
                    	self.removeClass('J_joinalbum');
                    	//self.unbind("click");
                    	//self.removeClass('addalbum_btn');
                    	//self.removeAttr("data-ty");
                    	//self.removeAttr("data-id");
                    	self.addClass('yxih');
                        nb.text(n+1).show();
                        $.rc.tip({content:result.msg});
                        return false;
                    }else{
                        $.rc.tip({content:result.msg, icon:'error'});
                        return false;
                    }
                });
                return false;
            });
        }

    };
    $.rc.item.init();
})(jQuery);