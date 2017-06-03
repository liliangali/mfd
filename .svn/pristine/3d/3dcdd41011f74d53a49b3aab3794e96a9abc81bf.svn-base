//顶部城市切换
   $(function(){
	 $('#topCity a').each(function(){
	   $(this).click(function(){
		   var code = $(this).data("code");
		   var name = $(this).data("name");
		   if(code && name){
			   $.removeCookie("cityShop");
			   $.cookie("cityCode", code, {expires:30});
			   $.cookie("cityName", name, {expires:30});
			   location.reload();
		   }
		   // 
	   });
	 });
	
 })