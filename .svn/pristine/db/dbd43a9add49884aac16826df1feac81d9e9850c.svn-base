//头部分类菜单
$(document).on('click','.cp-menu',function(e){
	var left=parseInt($('#headerMenu').css('left'));
	if(left==0){
		$('#headerMenu').css('left','-100%');
		$('.MenuLayer').remove();
		$('html,body').removeClass('overhide');
	}else{
		$('#headerMenu').css('left','0');
		$('body').append('<div class="MenuLayer" style="position:fixed; width:100%; height:100%;left:0;top:100px;background:#000; opacity:0.5; z-index:10;"></div>')
		$('html,body').addClass('overhide');
		$('.MenuLayer').one('click',function(){
			$('#headerMenu').css('left','-100%');
			$(this).remove();
		});
	}
})
$(document).on('click','#headerMenu dt',function(){
	var obj=$(this).next('dd');
	if($(this).next('dd').is(':visible')){
		obj.slideUp('fast');
		$(this).removeClass('cur')
	}else{
		obj.siblings('dd').slideUp('fast').end().slideDown('fast');
		$(this).siblings('dt').removeClass('cur').end().addClass('cur')
	}
})