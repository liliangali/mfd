$(function(){
    
    var ajaxLoginHtml = $.ajax({ url: "/index.php/member-ajaxlogin.html", async: false }).responseText;

    $('body').append(ajaxLoginHtml);

    // 登录弹窗处理
    function poplogin(){
        $.layer({
            type: 1,
            title: '',
            border : [5, 0.5, '#666'],
            area: ['auto','auto'],
            shadeClose: true,
            page: {dom: '#poplogin-box'}
        }); 
    }
    // 登录弹窗处理
    
    poplogin();

    // 提交登录
    function verifyinput(pos){
        var inputname = pos.attr('name');
        var inputval = pos.val();
        var tip = pos.parents('.poplogin').find('.popp');
        switch(inputname){
            case 'username':
                    if( (inputval.length < 4 || inputval.length > 30 ) || inputval == '请输入您的手机号/邮箱/昵称等登录名'){
                        tip.show().html('<i></i>10-30个字符');
                        return false;
                    }else{
                        tip.hide().html('<i></i>');
                    }
                break;
            case 'password':
                    if(inputval.length < 4 || inputval.length > 30){
                        tip.show().html('<i></i>6-30个字符');
                        return false;
                    }else{
                        tip.hide().html('<i></i>');
                    }
                break;
        }
    }
    $('.relg-submit').click(function() {
            var f = $(this).parents('.poplogin');
            var username = f.find("input[name='username']");
            var password = f.find("input[name='password']");
        
            if( verifyinput(username) === false ){ return false; }
            if( verifyinput(password) === false ){ return false; }
            
            var msg = $.ajax({
                            type:'POST',
                            url: "/index.php/member-entrance.html",
                            async: false,
                            data:{
                                'login_username':username.val(),
                                'login_password':password.val(),
                                't':'login'
                            }
                        }).responseText;
            if(msg == 1){
                f.find('.popp').show().html('<i></i>用户名或者密码错误');
            }else if(msg == 2) {
                window.location.reload();
            }
        return false;
    });
    $('.relg-register').click(function() {
    	window.location.replace('/index.php/member-entrance.html');
    });
    // 提交登录
    
});