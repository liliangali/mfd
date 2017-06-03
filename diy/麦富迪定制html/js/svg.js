//page脚本
var cotteFn={
	index:function(){
		
		//svg动画
		function hasSVG(){
			SVG_NS = "http://www.w3.org/2000/svg";
			return !!document.createElementNS &&!!document.createElementNS(SVG_NS, "svg").createSVGRect;
		} 
		if(hasSVG()){
			var svg1 = new Vivus("mysvg1", {type: "scenario-sync", duration: 10, dashGap: 20, forceRender: false});
			$("#mysvg1").mouseover(function(){
				svg1.reset().play();	
			});
			var svg2 = new Vivus("mysvg2", {type: "scenario-sync", duration: 50, dashGap: 20, forceRender: false});
			$("#mysvg2").mouseover(function(){
				svg2.reset().play();	
			});
		}
		
	
	}
}