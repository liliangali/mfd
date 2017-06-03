/**
 * @name 专辑相关
 */
;(function($){
    $.rc.album = {
        settings: {
            create_btn: '.J_createalbum_btn',
            album_unit: '.J_album_item'
        },
        init: function(options){
            options && $.extend($.rc.album.settings, options);
            var s = $.rc.album.settings;
            //创建专辑
            $(s.create_btn).bind('click', function(){
                $.rc.album.create();
            });
            //专辑操作
            $(s.album_unit).bind('mouseover', function(){
                $(this).find('.J_control').show();
                $(this).find('.J_mask').hide();
            }).bind('mouseout', function(){
                $(this).find('.J_control').hide();
                $(this).find('.J_mask').show();
            });
            $(s.album_unit).find('.J_edit').bind('click', function(){
                var aid = $(this).parents(s.album_unit).attr('data-aid');
                $.rc.album.edit(aid);
            });
            $(s.album_unit).find('.J_del').bind('click', function(){
                var aid = $(this).parents(s.album_unit).attr('data-aid');
                $.rc.album.del(aid);
            });
            $.rc.album.follow();
            $.rc.album.unfollow();
        },
        form_init: function(form) {
            //封面图片上传
            form.find('#J_upload_banner').uploader({
                action_url: PINER.root + '/?m=album&a=album_upload_banner',
                input_id: 'J_banner',
                input_name: 'banner',
                onComplete: function(id, fileName, result){
                    if(result.status == '1'){
                        $('#J_banner').val(result.data.banner);
                        $('#J_album_form').find('.J_preview').html('<img src="'+result.data.src+'" class="fl" />');
                    }else{
                        $.rc.tip({content:result.msg, icon:'error'});
                    }
                }
            });
            //表单验证
            $.formValidator.initConfig({formid:form.attr('id'),autotip:true});
            form.find('#J_album_title').formValidator({onshow:' ', onfocus:lang.please_input+lang.title}).inputValidator({min:1,onerror:lang.please_input+lang.title});
            form.ajaxForm({
                success: function(result){
                    if(result.status == 1){
                        $.dialog.get('album').close();
                        $.rc.tip({content:result.msg});
                        window.location.reload();
                    } else {
                        $.rc.tip({content:result.msg, icon:'error'});
                    }
                },
                dataType: 'json'
            });
        },
        create: function(){
            if(!$.rc.dialog.islogin()) return !1;
            $.getJSON(PINER.root + '/?m=album&a=create_album', function(result){
                if(result.status == 1){
                    $.dialog({id:'album', title:lang.create_album, content:result.data, padding:'', fixed:true, lock:true});
                    $.rc.album.form_init($('#J_album_form'));
                }else{
                    $.rc.tip({content:result.msg, icon:'error'});
                }
            });
        },
        edit: function(aid){
            if(!$.rc.dialog.islogin()) return !1;
            $.dialog({id:'album', title:lang.edit_album, padding:'', fixed:true, lock:true});
            $.getJSON(PINER.root + '/?m=album&a=edit_album', {aid:aid}, function(result){
                if(result.status == 1){
                    $.dialog.get('album').content(result.data);
                    $.rc.album.form_init($('#J_album_form'));
                }else{
                    $.rc.tip({content:result.msg, icon:'error'});
                }
            });
        },
        del: function(aid){
            if(!$.rc.dialog.islogin()) return !1;
            if(!confirm(lang.confirm_del_album)) return !1;
            $.getJSON(PINER.root + '/?m=album&a=delete_album', {aid:aid}, function(result){
                if(result.status == 1){
                    $.rc.tip({content:result.msg});
                    window.location.reload();
                }else{
                    $.rc.tip({content:result.msg,  icon:'error'});
                }
            });
        },
        follow: function(){
            $('.J_follow_album').bind('click', function(){
                if(!$.rc.dialog.islogin()) return !1;
                var self = $(this),
                    aid = self.attr('data-aid'),
                    afn = parseInt($('#J_afn_'+aid).text());
                $.getJSON(PINER.root + '/?m=album&a=follow', {aid:aid}, function(result){
                    if(result.status == 1){
                        //更改按钮&添加数量
                        self.html(lang.unfollow_album);
                        self.removeClass('J_follow_album').removeClass('album_look_link').addClass('J_unfollow_album').addClass('album_unlook_link');
                        $('#J_afn_'+aid).text(afn+1);
                    }else{
                        $.rc.tip({content:result.msg,  icon:'error'});
                    }
                });
            });
        },
        unfollow: function(){
            $('.J_unfollow_album').bind('click', function(){
                if(!$.rc.dialog.islogin()) return !1;
                var self = $(this),
                    aid = self.attr('data-aid'),
                    afn = parseInt($('#J_afn_'+aid).text());
                $.getJSON(PINER.root + '/?m=album&a=unfollow', {aid:aid}, function(result){
                    if(result.status == 1){
                        //更改按钮&添加数量
                        self.html(lang.follow_album);
                        self.removeClass('J_unfollow_album').removeClass('album_unlook_link').addClass('J_follow_album').addClass('album_look_link');
                        $('#J_afn_'+aid).text(afn-1);
                    }else{
                        $.rc.tip({content:result.msg,  icon:'error'});
                    }
                });
            });
        }
    };
    $.rc.album.init();
})(jQuery);