/**
 * @name 弹窗
 */
;(function($){
    $.rc.dialog = {
        //登陆
        islogin: function(){
        	
            return "" == PINER.uid ? ($.rc.dialog.login(), !1) : !0;
        },
        login: function(){
            $.getJSON('/index.php/club-ajax_login.html', function(result){
                if(result.status == 0){
                    $.rc.tip({content:result.msg, icon:'error'});
                }else{
                    //$.dialog({id:'login', title:lang.login_title, content:result.data, padding:'', fixed:true, lock:true});
                	$.dialog({id:'login', title:'用户登陆', content:result.data, padding:'', fixed:true, lock:true});
                    $.rc.dialog.dlogin_form($('#J_dlogin_form'));
                }
            });
        },
        dlogin_form: function(form){
            form.ajaxForm({
                beforeSubmit: function(){
                    var username = form.find('.J_username').val(),
                        password = form.find('.J_password').val();
                    if(username == ''){
                        form.find('#J_login_fail').html('请输入用户名！').css("visibility", 'visible');
                        return !1;
                    }
                    if(password == ''){
                        form.find('#J_login_fail').html('请输入密码！').css("visibility", 'visible');
                        return !1;
                    }
                },
                success: function(result){
                    if(result.status == 1){
                    	form.find('#J_login_fail').html(result.msg).css("visibility", 'visible');
                        //$.dialog.get('login').title(false).content('<div class="d_loading">'+result.msg+'</div>').time(2000);
                        $.dialog.get('login').time(2000);
                        window.location.reload();
                    } else {
                        form.find('#J_login_fail').html(result.msg).css("visibility", 'visible');
                    }
                },
                dataType: 'json'
            });
        }
    };
})(jQuery);