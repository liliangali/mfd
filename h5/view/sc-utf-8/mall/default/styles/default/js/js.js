function b(){
	h = $(window).height();
	t = $(document).scrollTop();
	if(t > h){
		$('#gotop1').show();
	}else{
		$('#gotop1').show();
	}
}
$(document).ready(function(e) {
	b();
	$('#gotop1').click(function(){
		$(document).scrollTop(0);	
	})	
	
});

$(window).scroll(function(e){
	b();		
})
