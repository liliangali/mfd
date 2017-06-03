
/*高度自适应*/
window.onload=window.onresize=function()
{
 var wh=$(window).height();
 $('.diy_datu').height(wh-280)
}


//$(function(){
 //$(".tab_tit li").click(function(){
	 //$(this).eq($(this).index(this)).addClass("on").siblings().removeClass("on");
	 //$(".tab_div .show_d").eq($(".tab_tit li").index(this)).show().siblings().hide();
   //})		   		   
//})