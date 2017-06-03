/**
 * @name 个人空间
 */
;(function($){
    $.rc.space = {
        settings: {
            pl_unit: '.J_pl' //关注列表
        },
        init: function(options){
            options && $.extend($.rc.space.settings, options);
            $.rc.space.people_list();
            //列表的取消关注
            $.rc.space.people_unfollow();
            //列表的移除粉丝
            $.rc.space.people_delfans();
        },
        people_list: function(){
            var s = $.rc.space.settings;
            $(s.pl_unit).live({
                mouseover: function(){
                    if($(this).attr('data-ptype') == 'follow'){
                        $(this).find('.J_fo_u_ok').show();
                    }else{
                        $(this).find('.J_fans_op').show();
                    }
                },
                mouseout: function(){
                    if($(this).attr('data-ptype') == 'follow'){
                        $(this).find('.J_fo_u_ok').hide();
                    }else{
                        $(this).find('.J_fans_op').hide();
                    }
                }
            });
        },
        //取消关注
        people_unfollow: function(){
            var s = $.rc.space.settings;
            $(s.pl_unit).find('.J_pl_tool').find('.J_unfo_u').live('click', function(){
                if(!$.rc.dialog.islogin()) return !1;
                if(!confirm(lang.confirm_unfollow)) return !1;
                var self = $(this),
                    pl = self.parents(s.pl_unit),
                    uid = pl.attr('data-uid');
                $.getJSON(PINER.root + '/?m=user&a=unfollow', {uid:uid}, function(result){
                    if(result.status == 1){
                        pl.remove();
                    }else{
                        $.rc.tip({content:result.msg,  icon:'error'});
                    }
                });
            });
        },
        //删除粉丝
        people_delfans: function(){
            var s = $.rc.space.settings;
            $(s.pl_unit).find('.J_delfans').live('click', function(){
                if(!$.rc.dialog.islogin()) return !1;
                if(!confirm(lang.confirm_delfans)) return !1;
                var self = $(this),
                    uid = self.attr('data-uid');
                $.getJSON(PINER.root + '/?m=user&a=delfans', {uid:uid}, function(result){
                    if(result.status == 1){
                        self.parents(s.pl_unit).remove();
                    }else{
                        $.rc.tip({content:result.msg,  icon:'error'});
                    }
                });
            });
        }
    };
    $.rc.space.init();
})(jQuery);