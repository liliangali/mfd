$(function()
{
	function scrollAd(t,options)
	{
		
		if(!$(t))return;
		
		options=options || {};
		options.time1=options.time1 || 3500;  //设置轮播图每次换图时间
		options.time2=options.time2 || 1500;  //设置每张图片做动画所需要的时间
		
		//获取图片的长度 ul的宽度 ul的图片复制一份 
		var oU=$(t).children("ul");
		var oOl=$(t).children("ol");
		var aLi=oU.children();
		var length=aLi.length;
		
		//创建按钮
		for(var i=0;i<length;i++)
		{
			$(oOl).append('<a href="javascript:;"></a>');	
		}
		//默认显示第一个
		oOl.children()[0].className='selected';
		
		//声明变量
		var iNow=0;
		var timer=null;
		
		function tab()
		{
			var h=iNow%length;  //获取当前的索引值
			aLi.stop(true,true).css("zIndex","1");   //所有动画停止
			aLi.eq(h).css("zIndex","18").stop(true,true).animate({left:0},options.time2,function()
			{
				$(this).siblings("li").css("zIndex","1"); //降低其他li的层级
				if($(this).position().left==0)
				{
					$(this).siblings("li").css({left:$(this).width()});   //当前动画结束其他li才返回初始位置
				}
			})
			oOl.find("a").removeClass().eq(h).addClass("selected");	//为当前按钮添加样式
		}
		
		//运动公式
		function next()
		{	
			iNow++;
			tab();	
		}
		
		//自动滚动
		timer=setInterval(next,options.time1);
		
		//banner图移入动画停止
		$(t).mouseenter(function()
		{
			clearInterval(timer)
		})
		
		//按钮被移入事件
		oOl.find("a").mouseenter(function()
		{
			iNow=oOl.find("a").index($(this));	
			setTimeout(tab,300);
		})
		
		//banner图移出事件
		$(t).mouseleave(function()
		{
			clearInterval(timer)
			timer=setInterval(next,options.time1)
		})		
	}
	scrollAd(".box")
})