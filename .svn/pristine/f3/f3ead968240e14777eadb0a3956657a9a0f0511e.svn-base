
/*高度自适应*/

/*
$(document).ready(window.onresize=function()
{
 var wh=$(window).height();
 $('.spdtu').css({width:(wh-243),height:(wh-243)})
 $('.cus_right').height(wh-185)
 $('.cpxq_tab').height(wh-113)
 $('.img_list').height(wh-250)
 $('.text_list').height(wh-250)
 $('.cpxq_left').height(wh-113)
}
)
*/

$(function() {

  //一、左右切换：每次移动固定距离;
	$(".fabric").Xslider({
		unitdisplayed:5,
		numtoMove:5,
		scrollobjSize:Math.ceil($(".fabric:eq(0)").find("li").length/5)*450
	});
	
	$("a").focus(function(){this.blur();});
	
	
	//选中状态
	
	$('.fabric_con li').click(function(){
	   $(this).addClass('on').siblings().removeClass('on');	
	});;
	
	

	$(".size_tab li").click(function(){
		$(this).eq($(this).index(this)).addClass("on").siblings().removeClass("on");	 
		$(".size_show div").eq($(".size_tab li").index(this)).show().siblings().hide();
	})	

	
	
});;