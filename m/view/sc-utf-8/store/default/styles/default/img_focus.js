// JavaScript Document
   //首页轮播图 begin
  var t = n = 0, count;
  $(document).ready(function() {
	  var top_ads_str=new Array("/Public/kindeditor-3.5.6-zh_CN/attached/20130730/20130730145027_66466.jpg",
		  "/Public/kindeditor-3.5.6-zh_CN/attached/20130730/20130730150041_55855.jpg",
		  "/Public/kindeditor-3.5.6-zh_CN/attached/20130730/20130730150553_18677.jpg"
		  );
	  var top_ads_imgurl=top_ads_str[parseInt(Math.random()*(top_ads_str.length))];
	  $("#js_ads_top_bigimg").attr("src",top_ads_imgurl);
	  if($("#js_ads_banner_top_slide").length){
		  var $slidebannertop = $("#js_ads_banner_top_slide"),$bannertop = $("#js_ads_banner_top");
		  setTimeout(function(){
			  $bannertop.slideUp(1000);
			  $slidebannertop.slideDown(1000);
		  },2500);
		  setTimeout(function(){
			  $slidebannertop.slideUp(1000,function(){
				  $bannertop.slideDown(1000);
			  });
		  },8500);
	  }
	  
	  
	  /*新轮播图*/
	  $(".diaporama1").jDiaporama({
		  animationSpeed: "slow",
		  delay: 5
	  });
	  count = $("#banner_list a").length;
	  $("#banner_list a:not(:first-child)").hide();
	  $("#banner_info").html($("#banner_list a:first-child").find("img").attr('alt'));
	  $("#banner_info").click(function() {
		  window.open($("#banner_list a:first-child").attr('href'), "_blank")
	  });
	  $("#banner li").click(function() {
		  var i = $(this).text() - 1;//获取Li元素内的值，即1，2，3，4 
		  n = i;
		  if (i >= count)
			  return;
		  $("#banner_info").html($("#banner_list a").eq(i).find("img").attr('alt'));
		  $("#banner_info").unbind().click(function() {
			  window.open($("#banner_list a").eq(i).attr('href'), "_blank")
		  })
		  $("#banner_list a").filter(":visible").fadeOut(500).parent().children().eq(i).fadeIn(1000);
		  $(this).css({"background": "", 'color': '#000'}).siblings().css({"background": "", 'color': '#fff'});
	  });
	  t = setInterval("showAuto()", 4000);
	  $("#banner").hover(function() {
		  clearInterval(t)
	  }, function() {
		  t = setInterval("showAuto()", 4000);
	  });
  });
  function showAuto() {
	  n = n >= (count - 1) ? 0 : ++n;
	  $("#banner li").eq(n).trigger('click');
  }
  function showHideUl(o, member) {
	  $('#baoguang dd').each(function() {
		  $(this).removeClass('focus');
	  });

	  $(o).addClass(' focus news-hover-1');
	  $('[id^="rig"]').hide();
	  $('#rig' + member).show();
  }
  //首页轮播图 end  