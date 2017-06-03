//页面滚动效果
Swipe(document.getElementById('slider'), {
	continuous: false,
	callback: function(index, element) {$('.xcfy em').text(index+1)} 
});

$('.spread').toggle(function(){
	$(this).children('div').slideDown('fast')	
},function(){
	$(this).children('div').slideUp('fast')	
})
