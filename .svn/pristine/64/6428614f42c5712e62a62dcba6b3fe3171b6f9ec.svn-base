/* author yhao.bai 2014.6.10; */
var comment = {};

comment.init = function(opt){
	

		if(!opt.loadUrl || !opt.postUrl || !opt.disid){
			return;
		}
	
		$("#saytext").focus(function(){
			$.rc.dialog.islogin();
			return false;
		})
		
		$("#saytext").keyup(function(){
			var _c = $("#saytext").val();
			var num = 185-_c.length;
			$(".shuru").html("还可以输入<font>"+num+"</font>个字符");
		})
	
		$(".sub_btn_comm").click(function(){
		var _c = $("#saytext").val();
		//var _match  = /(回复@.*:)/i;
		//var r = content.replace(_match, '');
		
		if(_c == "你也可以随便说点什么"){
			$.rc.tip({content:"请输入要评论的内容！", icon:"error"});
		}
		
		if(_c.length < 5 || _c.length > 185){
			$(".shuru").html("<font>评论的内容只能在5-185个字符之间！</font>");
			return false;
		}
		
		$.post(opt.postUrl, {content:_c}, function(res){
			var res = $.evalJSON(res);
			if(res.done == false){
				$(".shuru").html("<font>"+res.msg+"</font>");
				return false;
			}else{
				if(res.retval == "login"){
					location.reload();
				}else{
					$("#saytext").val('');
					$.rc.tip({content:"发表评论成功！"});
					loadComment();
				}
			}
		})
	})
	
	function loadComment(){
			$.get(opt.loadUrl,{id:opt.disid}, function(res){
				var res = $.evalJSON(res);
				$('.comment_list').html(res.retval);
				$("#page a").click(function(){
					opt.loadUrl = $(this).data("url");
					loadComment();
				})
				$.rc.user.init();
			})
	}
	
	loadComment();
}