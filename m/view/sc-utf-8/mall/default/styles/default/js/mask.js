$(document).ready(function() { 
$('.btn_mask').click(function() { 
$('.mask').css({'display': 'block'}); 
center($('.mess')); 
check($(this).parent()); 
}); 
// 居中 
function center(obj) { 
var screenWidth = $(window).width(), screenHeight = $(window).height(); //当前浏览器窗口的 宽高 
var scrolltop = $(document).scrollTop();//获取当前窗口距离页面顶部高度 
var objLeft = (screenWidth - obj.width())/2 ; 
var objTop = (screenHeight - obj.height())/2 + scrolltop; 
obj.css({left: objLeft + 'px', top: objTop + 'px','display': 'block'}); 
//浏览器窗口大小改变时 
$(window).resize(function() { 
screenWidth = $(window).width(); 
screenHeight = $(window).height(); 
scrolltop = $(document).scrollTop(); 
objLeft = (screenWidth - obj.width())/2 ; 
objTop = (screenHeight - obj.height())/2 + scrolltop; 
obj.css({left: objLeft + 'px', top: objTop + 'px','display': 'block'}); 
}); 
//浏览器有滚动条时的操作、 
$(window).scroll(function() { 
screenWidth = $(window).width(); 
screenHeight = $(widow).height(); 
scrolltop = $(document).scrollTop(); 
objLeft = (screenWidth - obj.width())/2 ; 
objTop = (screenHeight - obj.height())/2 + scrolltop; 
obj.css({left: objLeft + 'px', top: objTop + 'px','display': 'block'}); 
}); 
} 
//确定取消的操作 
$(".mess,.mask").click(function() { 
    $(".mess").fadeOut(400);$(".mask").fadeOut(400);
}); 


}); 