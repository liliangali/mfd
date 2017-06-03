$.fn.extend({
	sc: function(options) {
		return this.each(function() {
			new $.sc(options);
		});
	}
});

$.sc = function(opt){
	opt.count = opt.max = 0;
    var url_sign = '&id='+opt.id;
	var _class ='.main';;

    function loadComment(){
		$.get(opt.loadUrl+"?max="+opt.max+url_sign+"&cate="+opt.type, function(res){
            var $res = eval("("+res+")");
            if(!opt.count){
				opt.count = $res.retval.count;
			}
			$(".a_2").html(opt.count);
			$("#just_id").replaceWith("");
			$("#getMore").replaceWith("");
			$(_class).append($res.retval.content);
			if($res.retval.next > 0){
				$(_class).append('<div id="getMore" align="center">查看更多评论</div><p  id="just_id" style="padding-top:60px;"></p>');
				$("#getMore").unbind().bind('click', function(){
					opt.max = $res.retval.max;
					loadComment();
				})
			}else{
				$(_class).append('<p  id="just_id" style="padding-top:60px;"></p>');
;
			}
		})
	}


	$(".input_2").click(function(){
		var _c = $(".input_1").val();

		if(_c == "你也可以随便说点什么"){
			 alert('请输入要评论的内容！');
			 return false;
		}

		if(_c.length < 5 || _c.length > 200){
			alert("评论的内容只能在5-200个字之间！",330,165);
			return false;
		}

		$.post(opt.postUrl, {cate:opt.type,content:_c}, function(res){

			var $res = eval("("+res+")");
			if($res.done == false){
				alert($res.msg)
				return false;
			}else{
                var num = parseInt($(".a_2").html())+1;
                $(".a_2").html(num);

				$(".input_1").val('');
				$('.sjs_lc.jj_box').after($res.retval)

				alert("发表评论成功！");

			}
		})
	})

	loadComment();
}
