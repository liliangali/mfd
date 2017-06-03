//顶部城市切换
   $(function(){
       $('.top_city').click(function(e){
	        $(this).find('i').show();
			$('.city_menu').slideDown();
			if(jQuery('#city_list').height()>160){
				jQuery('.serve_his_list').height(160).jScrollPane({
					showArrows: true,
					animateScroll: true
				});
	        }
	 });
	 
	 $('.city_menu').find('li').each(function(){
	   $(this).click(function(){
		   var code =$(this).data("code");
		   if(code){
			   $.removeCookie("cityShop");
			   $.cookie("cityCode", code, {expires:30});
			   location.reload();
		   }
		   // 
	   });
	 });
	 
	 $('.top_qjd').click(function(e){
	        if($('.qjd_menu').find('li').length > 0){
				$(this).find('i').show();
				$('.qjd_menu').slideDown();
				if(jQuery('#qjdlist').height()>160){
					jQuery('.serve_his').height(160).jScrollPane({
						showArrows: true,
						animateScroll: true
					});
				}
		  }
	 });
	 
	 
	 $('.qjd_menu').find('li').each(function(){
	   $(this).click(function(){
		   var shop =$(this).data("shop");
		   if(shop){
			   $.cookie("cityShop", shop, {expires:30});
			   location.reload();
		   }
	   });
       
	 });
		
	 
   })