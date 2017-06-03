/*--幻灯/S--*/
(function(){
	var n=0;
	var d=null;
	function ShowImg(n){
		$(".banner .slide-pic>li").eq(n).siblings("li").fadeOut(1000).end().fadeIn(1000);
		$(".banner .btn>span").eq(n).siblings("span").removeClass("cur").end().addClass("cur");
		$(".banner .slide-txt li").eq(n).siblings().removeClass('show').end().addClass('show');
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

/*--裁缝滚动/E--*/
(function(){	
	var elem = document.getElementById('swipe1');
	var mySwipe = Swipe(elem, {
		callback: function(index, element) {
			$('.box1 .swipe-btn>span').eq(index).siblings('span').removeClass('cur').end().addClass('cur');     
		}
	});
	$('.box1 .swipe-btn>span').click(function(){
		var index=$('.box1 .swipe-btn>span').index(this);
		mySwipe.slide(index,400)	
	})
	$('.box1 .prev').click(function(){
		mySwipe.prev()	
	});
	$('.box1 .next').click(function(){
		mySwipe.next()	
	});
})();
/*--裁缝滚动/E--*/

/*--Show定制/E--*/
(function(){
	var elem = document.getElementById('swipe2');
	var mySwipe = Swipe(elem, {
	   callback: function(index, element) {
			$('.box2 .swipe-btn>span').eq(index).siblings('span').removeClass('cur').end().addClass('cur');     
	   }
	});
	$('.box2 .swipe-btn>span').click(function(){
		var index=$('.box2 .swipe-btn>span').index(this);
		mySwipe.slide(index,400)	
	})
	$('.box2 .prev').click(function(){
		mySwipe.prev()	
	});
	$('.box2 .next').click(function(){
		mySwipe.next()	
	});
})()
/*--Show定制/E--*/

twoMenu();//顶部二级菜单
anav();//地区导航
































