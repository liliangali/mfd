twoMenu();
anav();
fixedNav('.fixedNav');

function item_masonry(){ 
	$('.item img').load(function(){ 
		$('.infinite_scroll').masonry({ 
			itemSelector: '.masonry_brick',
			columnWidth:279,
			gutterWidth:28								
		});		
	});
		 //columnWidth 函数递增控制div左边距
	$('.infinite_scroll').masonry({ 
		itemSelector: '.masonry_brick',
		columnWidth:279,
		gutterWidth:28								
	});	
}
$(function(){
//滚动条下拉事件
	function item_callback(){
		item_masonry();	
	}
	item_callback();  
	$('.item').fadeIn();
	var sp = 1
	$(".infinite_scroll").infinitescroll({
		navSelector  	: "#more",
		nextSelector 	: "#more a",
		itemSelector 	: ".item",
		loading:{
			img: "other/masonry_loading_1.gif",
			msgText: '正在加载中....',
			finishedMsg: '木有了,看看下一页',
			finished: function(){
				sp++;
				if(sp>=10){ //到第10页结束事件
					$("#more").remove();
					$("#infscr-loading").hide();
					$("#pagebox").show();
					$(window).unbind('.infscr');
				}
			}	
		},errorCallback:function(){ 
			$("#pagebox").show();
		}
	},function(newElements){
		var $newElems = $(newElements);
		$('.infinite_scroll').masonry('appended', $newElems, false);
		$newElems.fadeIn();
		item_callback();
		return;
	});
});
