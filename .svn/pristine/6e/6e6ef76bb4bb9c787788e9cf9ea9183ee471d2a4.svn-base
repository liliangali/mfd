
/*高度自适应*/
window.onload=window.onresize=function()
{
 var wh=$(window).height();
 $('.spdtu').css({width:(wh-243),height:(wh-243)})
 $('.cus_right').height(wh-185)
 $('.cpxq_tab').height(wh-113)
 $('.img_list').height(wh-250)
 $('.text_list').height(wh-250)
 $('.cpxq_left').height(wh-113)
}


$(function() {
	
	$(".tab_tit li").click(function() {
		$(this).eq($(this).index(this)).addClass("on").siblings().removeClass("on");
		var ss = $(".show1").eq($(".tab_tit li").index(this));
		ss.show().siblings().hide();
	})

	$(".tab_tit2 li").click(function() {
		$(this).eq($(this).index(this)).addClass("on").siblings().removeClass("on");
		var ss = $(".show1_1").eq($(".tab_tit2 li").index(this));
		ss.show().siblings("div").hide();
	})
	
	$('.img_list li').click(function(){
	   $(this).addClass('on').siblings().removeClass('on');	
	})
	
	$('.text_list li').click(function(){
	   $(this).addClass('on').siblings().removeClass('on');	
	})	
	
})