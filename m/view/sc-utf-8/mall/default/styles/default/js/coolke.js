$(function() {
	
  $("#CoolKe ul li").each(function(){
	  
	   var t = $(this),
	       c = t.find('.hot_tu'),
	       u = t.find('.user_box'),
	       Time = null;
	   c.mousemove(function(){
		   clearTimeout(Time);
		   t.css("z-index","200")
		    $(this).parent().parent().find(".user_box").show();
	   });
	   c.mouseout(function(){
		   var Othis = $(this);
		    Time = setTimeout(function(){
			  
				   Othis.parent().parent().find(".user_box").hide();
				   t.removeAttr("style");

		   },50);
		   
	   });
	   u.mousemove(function(){
		   clearTimeout(Time); 
	   });
	   u.mouseout(function(){
		   var Othis = $(this);
		   Time = setTimeout(function(){
			  
				   Othis.hide();
				   t.removeAttr("style");

		   },50);
	   });
  });
  
  $("#CoolKe1 ul li").each(function(){
	  
	   var t = $(this),
	       c = t.find('.hot_tu'),
	       u = t.find('.user_box'),
	       Time = null;
	   c.mousemove(function(){
		   clearTimeout(Time);
		   t.css("z-index","200")
		    $(this).parent().find(".user_box").show();
	   });
	   c.mouseout(function(){
		   var Othis = $(this);
		    Time = setTimeout(function(){
			  
				   Othis.parent().find(".user_box").hide();
				   t.removeAttr("style");

		   },50);
		   
	   });
	   u.mousemove(function(){
		   clearTimeout(Time); 
	   });
	   u.mouseout(function(){
		   var Othis = $(this);
		   Time = setTimeout(function(){
			  
				   Othis.hide();
				   t.removeAttr("style");

		   },50);
	   });
 });
  
	
  $(".cool_tab").find("span").each(function(){
	  $(this).click(function(){
		  if($(this).text() == "街拍"){
			  $(this).addClass("jiepai_on");
			  $(this).prev("span").removeClass("geren_on");
			  $("#cooler_more").attr("href",$(this).attr("data-url"));
		  }else{
			  $(this).addClass("geren_on");
			  $(this).next("span").removeClass("jiepai_on");
			  $("#cooler_more").attr("href",$(this).attr("data-url"));
		  }
		  
		  $(".cool_hot_list").each(function(){
			    $(this).hide();
		  });
		   $(".cool_hot_list").eq($(this).index()).show();

	  });
  });
  
  
});