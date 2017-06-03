/* author yhao.bai 2014.6.10; */
$.fn.extend({
	sc: function(options) {
		return this.each(function() {
			new $.sc(options);
		});
	}
});

$.sc = function(opt){
	opt.count = 0;
	opt.max = 0;

    //加一个页面扩展项  yusw
    if(typeof(opt.type) !== 'undefined' && opt.type == 'work_score_comment'){
        var url_sign = '&id='+opt.id;
    }else{
        var url_sign = ' ';
    }
    function loadComment(){
		$("#more").html('<div class="getMore">loading.....</div>');
//console.log(opt.loadUrl+"?max="+opt.max+url_sign)
//        console.log(opt)
		$.get(opt.loadUrl+"?max="+opt.max+url_sign, function(res){
            var $res = eval("("+res+")");
            if(!opt.count){
				opt.count = $res.retval.count;
			}
			$("#commentNum").html(opt.count);
			$(".comNum").html('<i></i>'+opt.count+'评论');

			$("#list").append($res.retval.content);
			if($res.retval.next > 0){
				$("#more").html('<div class="getMore">查看更多评论</div>');
				$(".getMore").unbind().bind('click', function(){
					opt.max = $res.retval.max;
					loadComment();
				})
			}else{
				$("#more").empty();
			}
		})
	}

	$("#editor").focus(function(){
		if(hasLogin() == 0){
			login();
			return false;
		}
	})

	$(".submit").click(function(){
		var _c = $("#editor").val();

		if(_c == "你也可以随便说点什么"){
			 msg('请输入要评论的内容！');
			 return false;
		}

		if(_c.length < 5 || _c.length > 200){
			msg("评论的内容只能在5-200个字之间！",330,165);
			return false;
		}

		$.post(opt.postUrl, {content:_c}, function(res){
			var $res = eval("("+res+")");
			if($res.done == false){
				msg($res.msg);
				return false;
			}else{
				if($res.retval == "login"){
					login();
				}else{
					var num = parseInt($("#commentNum").html())+1;
					$("#editor").val('');
					$(".comList").prepend($res.retval);
					$("#commentNum").html(num);
					$(".comNum").html('<i></i>'+num+'评论')
					msg("发表评论成功！");
				}
			}
		})
	})

	loadComment();
}
