/*--幻灯/S--*/
(function(){
	var n=0;
	var d=null;
	function ShowImg(n){
		$(".banner .slide-pic>li").eq(n).siblings("li").fadeOut(1000).end().fadeIn(1000);
		$(".banner .btn>span").eq(n).siblings("span").removeClass("cur").end().addClass("cur");
	}
	function auto(){
		var len=$(".banner .slide-pic>li").length;
		n++;
		if(n>=len){n=0}
		ShowImg(n)
	}
	$(".banner .btn>span").mouseover(function(){
		clearInterval(d);							 
		var index=$(".banner .btn>span").index(this);
		ShowImg(index);
	}).mouseout(function(){d=setInterval(auto,4000);
	});
	d=setInterval(auto,4000)
})();
/*--幻灯/E--*/

twoMenu();//顶部二级菜单
anav();//地区导航
































