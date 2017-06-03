twoMenu();
anav();
// 滚动图
(function(){
	var elem = document.getElementById('swipe1');
	var mySwipe = Swipe(elem, {
		callback: function(index, element) {
			$('.box1 .swipe-btn>span').eq(index).siblings('span').removeClass('cur').end().addClass('cur');     
		}
	});
	$('.prev').click(function(){
		mySwipe.prev()	
	});
	$('.next').click(function(){
		mySwipe.next()	
	});
})();



















