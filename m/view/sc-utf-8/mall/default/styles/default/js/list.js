$(document).ready(function(){
						   
	//mouseover效果
	function liMouseOn(){
		$(".scrolltab .ulBigPic dd").attr("class","");
		for(var i=0; i<$(".scrolltab .ulBigPic dd").length;i++) {
			(function(j){
				$(".scrolltab .ulBigPic dd:eq("+j+")").mouseover(function(){
					if($(".scrolltab").attr("class") == "dSmallList" || $(".scrolltab").attr("class") == "dMiddleList") {
						if($(this).attr("class") != "liSelected") {
							$(this).attr("class","liSelected");
						}
					}
				});
				$(".scrolltab .ulBigPic dd:eq("+j+")").mouseout(function(){
					if($(".scrolltab").attr("class") == "dSmallList" || $(".scrolltab").attr("class") == "dMiddleList") {
						if($(this).attr("class") == "liSelected") {
							$(this).attr("class","");
						}
					}
				});
			}) (i);
		}
	}
	
	//定义当前、之前、之后要显示图片的排位
	var curPic=0,nextPic=-1,prePic=-1;preShowPic=-1;
	var allPic=$(".scrolltab .ulBigPic dd").length;
	//初始化当前、之前、之后要显示图片的排位
	
	function numInit(num){
		if(num=="init"){
			if(allPic > 1) {
				nextPic=curPic+1;
				prePic=allPic-1;
			}else if(allPic == 1){
				nextPic=0;
				prePic=0;
			}
			$(".scrolltab .ulBigPic dd:eq("+curPic+")").attr("class","liSelected");
		}else if(num == "plus"){
			preShowPic=curPic;
			prePic=curPic;
			curPic=nextPic;
			if(curPic < (allPic-1)) {
				nextPic=curPic+1;
			}else if(curPic == (allPic-1)) {
				nextPic=0;
			}
			$(".scrolltab .ulBigPic dd:eq("+curPic+")").attr("class","liSelected");
			if(preShowPic != curPic) {
				$(".scrolltab .ulBigPic dd:eq("+preShowPic+")").attr("class","");
			}
		}else if(num == "minus") {
			preShowPic=curPic;
			nextPic=curPic;
			curPic=prePic;
			if(curPic > 0) {
				prePic=curPic-1;
			}else if(curPic == 0 && allPic > 1) {
				prePic=allPic-1;
			}
			$(".scrolltab .ulBigPic dd:eq("+curPic+")").attr("class","liSelected");
			if(preShowPic != curPic) {
				$(".scrolltab .ulBigPic dd:eq("+preShowPic+")").attr("class","");
			}
		}else{
			preShowPic=curPic;
			curPic=num;
			if(curPic == (allPic-1)) {
				nextPic=0;
				if(allPic > 1) {
					prePic=curPic-1;
				}else if(allPic == 1) {
					prePic=0;
				}
			}else if(curPic == 0) {
				prePic=allPic-1;
				if(allPic > 1) {
					nextPic=1;
				}else if(allPic == 1) {
					nextPic=0;
				}
			}else{
				nextPic=curPic+1;
				prePic=curPic-1;
			}
			$(".scrolltab .ulBigPic dd:eq("+curPic+")").attr("class","liSelected");
			if(preShowPic != curPic) {
				$(".scrolltab .ulBigPic dd:eq("+preShowPic+")").attr("class","");
			}
		}
		//alert("curPic="+curPic+"/nextPic="+nextPic+"/prePic="+prePic+"/preShowPic="+preShowPic);
	}
	
	//大图左右按钮初始化
	function btnAInit(){
		if(allPic < 2) {
			$("#sLeftBtnA").attr("class","sLeftBtnABan");
			$("#sRightBtnA").attr("class","sRightBtnABan");
		}else{
			if(curPic == 0) {
				$("#sLeftBtnA").attr("class","sLeftBtnABan");
				$("#sRightBtnA").attr("class","sRightBtnA");
			}else if(curPic == (allPic-1)) {
				$("#sLeftBtnA").attr("class","sLeftBtnA");
				$("#sRightBtnA").attr("class","sRightBtnABan");
			}else{
				$("#sLeftBtnA").attr("class","sLeftBtnA");
				$("#sRightBtnA").attr("class","sRightBtnA");
			}
		}
	}
	
	//小图左右按钮初始化
	function btnBInitA(){
		if(allPic > 6) {
			$("#sRightBtnB").attr("class","sRightBtnB");
		}
	}
	
	function btnBInitB(){
		if(curPic > 2 && (allPic-curPic) > 4) {
			if($("#sLeftBtnB").attr("class") != "sLeftBtnB") {$("#sLeftBtnB").attr("class","sLeftBtnB");}
			if($("#sRightBtnB").attr("class") != "sRightBtnB") {$("#sRightBtnB").attr("class","sRightBtnB");}
		}else if(curPic < 3){
			if($("#sLeftBtnB").attr("class") != "sLeftBtnBBan") {$("#sLeftBtnB").attr("class","sLeftBtnBBan");}
			if(allPic > 6) {
				if($("#sRightBtnB").attr("class") != "sRightBtnB") {$("#sRightBtnB").attr("class","sRightBtnB");}
			}else{
				if($("#sRightBtnB").attr("class") != "sRightBtnBBan") {$("#sRightBtnB").attr("class","sRightBtnBBan");}
			}
		}else if(curPic > (allPic-4)) {
			if($("#sRightBtnB").attr("class") != "sRightBtnBBan") {$("#sRightBtnB").attr("class","sRightBtnBBan");}
			if(allPic > 6) {
				if($("#sLeftBtnB").attr("class") != "sLeftBtnB") {$("#sLeftBtnB").attr("class","sLeftBtnB");}
			}else{
				if($("#sLeftBtnB").attr("class") != "sLeftBtnBBan") {$("#sLeftBtnB").attr("class","sleftBtnBBan");}
			}
		}
	}
	
	//小图标签selected函数
	function smallPicSelected(){
		if(!$(".scrolltab .ulSmallPic li:eq("+curPic+")").hasClass("liSelected")) {$(".scrolltab .ulSmallPic li:eq("+curPic+")").addClass("liSelected");}
		if(preShowPic!=(-1)) {
			if($(".scrolltab .ulSmallPic li:eq("+preShowPic+")").hasClass("liSelected")) {
				$(".scrolltab .ulSmallPic li:eq("+preShowPic+")").removeClass("liSelected");
			}
		}
	}
	
	//小图滚动函数
	function smallPicScroll(){
		if(curPic != preShowPic) {
			var leftPosition=0;
			if(curPic>2 && curPic<($(".scrolltab .ulSmallPic li").length-3)) {
				leftPosition=-(curPic-2)*202;
			}else if(curPic > ($(".scrolltab .ulSmallPic li").length-4) && $(".scrolltab .ulSmallPic li").length>6) {
				leftPosition=-($(".scrolltab .ulSmallPic li").length-6)*202;
			}
			leftPosition+="px";
			$(".scrolltab .ulSmallPic").attr("rel","moving");
			$(".scrolltab .ulSmallPic").animate({left:leftPosition},200,function(){$(".scrolltab .ulSmallPic").attr("rel","stop");});
		}
	}
	
	//大图按钮效果
	$("#sLeftBtnA").mouseover(function(){
		if($(this).attr("class")=="sLeftBtnA") {
			$(this).attr("class","sLeftBtnASel");
		}
	});
	
	$("#sLeftBtnA").mouseout(function(){
		if($(this).attr("class")=="sLeftBtnASel") {
			$(this).attr("class","sLeftBtnA");
		}
	});
	
	$("#sLeftBtnA").click(function(){
		if($(this).attr("class")=="sLeftBtnASel") {
			numInit("minus");
			btnBInitB();
			smallPicSelected();
			smallPicScroll();
			if(curPic == 0) {$("#sLeftBtnA").attr("class","sLeftBtnABan");}
			if(curPic < (allPic-1) && $("#sRightBtnA").attr("class")=="sRightBtnABan") {$("#sRightBtnA").attr("class","sRightBtnA");}
		}
	});
	
	$("#sRightBtnA").mouseover(function(){
		if($(this).attr("class")=="sRightBtnA") {
			$(this).attr("class","sRightBtnASel");
		}
	});
	
	$("#sRightBtnA").mouseout(function(){
		if($(this).attr("class")=="sRightBtnASel") {
			$(this).attr("class","sRightBtnA");
		}
	});
	
	$("#sRightBtnA").click(function(){
		if($(this).attr("class")=="sRightBtnASel") {
			numInit("plus");
			btnBInitB();
			smallPicSelected();
			smallPicScroll();
			if(curPic == (allPic-1)) {$("#sRightBtnA").attr("class","sRightBtnABan");}
			if(curPic > 0 && $("#sLeftBtnA").attr("class")=="sLeftBtnABan") {$("#sLeftBtnA").attr("class","sLeftBtnA");}
		}
	});
	
	//小图li按键效果
	for (var i=0;i<$(".scrolltab .ulSmallPic li").length;i++) {
		(function(j) {
			$(".scrolltab .ulSmallPic li:eq("+j+")").click(function() {
				if($(this).attr("class") != "liSelected") {
					numInit(j);
					btnAInit();
					smallPicSelected();
				}
			})
		}) (i);
	}
	
	//小图左右按键效果
	$("#sLeftBtnB").mouseover(function(){
		if($(this).attr("class")=="sLeftBtnB") {
			$(this).attr("class","sLeftBtnBSel");
		}
	});
	
	$("#sLeftBtnB").mouseout(function(){
		if($(this).attr("class")=="sLeftBtnBSel") {
			$(this).attr("class","sLeftBtnB");
		}
	});
	
	$("#sLeftBtnB").click(function(){
		if($(this).attr("class")=="sLeftBtnBSel") {
			var leftPosition=$(".scrolltab .ulSmallPic").css("left");
			var leftPositionNum=Number(leftPosition.substring(0,(leftPosition.length-2)));
			leftPosition=leftPositionNum+202+"px";
			if(leftPosition=="0px") {if($(this).attr("class") != "sLeftBtnBBan") {$(this).attr("class","sLeftBtnBBan");}}
			var bestLeftNum=-($(".scrolltab .ulSmallPic li").length-6)*202;
			if((leftPositionNum+202) > bestLeftNum && $("sRightBtnB").attr("class") != "sRightBtnB") {$("#sRightBtnB").attr("class","sRightBtnB")}
			if($(".scrolltab .ulSmallPic").attr("rel")=="stop"){
				$(".scrolltab .ulSmallPic").attr("rel","moving");
				$(".scrolltab .ulSmallPic").stop();
				$(".scrolltab .ulSmallPic").animate({left:leftPosition},200,function(){$(".scrolltab .ulSmallPic").attr("rel","stop");});
			}
		}
	});
	
	$("#sRightBtnB").mouseover(function(){
		if($(this).attr("class")=="sRightBtnB") {
			$(this).attr("class","sRightBtnBSel");
		}
	});
	
	$("#sRightBtnB").mouseout(function(){
		if($(this).attr("class")=="sRightBtnBSel") {
			$(this).attr("class","sRightBtnB");
		}
	});
	
	$("#sRightBtnB").click(function(){
		if($(this).attr("class")=="sRightBtnBSel"){
			var leftPosition=$(".scrolltab .ulSmallPic").css("left");
			var leftPositionNum=Number(leftPosition.substring(0,(leftPosition.length-2)));
			leftPosition=leftPositionNum-202+"px";
			var bestLeftNum=-($(".scrolltab .ulSmallPic li").length-6)*202;
			if((leftPositionNum-202)==bestLeftNum) {$(this).attr("class","sRightBtnBBan");}
			if(leftPositionNum==0 && $("#sLeftBtnB").attr("class")=="sLeftBtnBBan") {$("#sLeftBtnB").attr("class","sLeftBtnB");}
			if($(".scrolltab .ulSmallPic").attr("rel")=="stop") {
				$(".scrolltab .ulSmallPic").attr("rel","moving");
				$(".scrolltab .ulSmallPic").stop();
				$(".scrolltab .ulSmallPic").animate({left:leftPosition},200,function(){$(".scrolltab .ulSmallPic").attr("rel","stop");});
			}
		}
	});
	
	liMouseOn();
	numInit("init");
	btnAInit();
	btnBInitA();
	smallPicSelected();	

});