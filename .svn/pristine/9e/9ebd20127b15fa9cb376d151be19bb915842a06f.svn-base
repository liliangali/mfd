/*
 @ Name：LUCK弹层精简版
 @ Author：前端老徐
 @ Date：2015/12/23
 @ E-mail：442413729@qq.com
 @ GitHub:https://github.com/waihaolaoxu/luck-s
 @ Address:http://www.loveqiao.com/
*/
!function(a) {
    "use strict";
    var c = document;
    //主程序
    a.luck = {
        view:function(d) {
			var t=d.content?d.content:'';
            //主框架
			var luck = c.createElement("div");
            luck.className = "luck";
            luck.id = "luck";
			//内容容器
            var con = c.createElement("div");
            con.className = "luck-con";
			con.id = "luck-con";
			if(d.width){
				con.style.width=d.width;
			}
			if(d.height){
				con.style.height=d.height;
			}
			con.innerHTML=t;
			//关闭按钮
			var clo=c.createElement("div");
			clo.className='luck-close';
			clo.onclick=a.luck.close;
			//遮罩层
            var oShade = c.createElement("div");
            oShade.className = "luck-shade";
		    //组合框架
			if(d.closeBtn){
				con.appendChild(clo);	
			}
			luck.appendChild(con);
			luck.appendChild(oShade);
			return luck;
        },
		resize:function(){
			var obj=document.getElementById('luck-con');
			var t=((document.documentElement.clientHeight||document.body.clientHeight)/2-obj.offsetHeight/2);
			obj.style.top=t>0?t:0+'px';
		},
        open:function(t) {
            c.body.appendChild(luck.view(t));
			luck.resize();
			if(typeof t.callback=='function'){
				t.callback();	
			}
        },
        close:function() {
            var obj = document.getElementById("luck");
			if(obj){
            	c.body.removeChild(obj);
			}
        },
    };
	//基础样式
	var style=document.createElement('style');
	style.type="text/css";
	style.innerHTML=".luck{position:absolute;left:0;top:0;right:0;bottom:0}.luck-shade{position:fixed;width:100%;height:100%;left:0;top:0;background:#000;opacity:.5;filter:alpha(opacity=50);z-index:0}.luck-con{position:relative;margin:0 auto;z-index:1;background:#fff;min-width:150px;min-height:100px;max-width:500px;animation:bouncedelay ease .3s;-webkit-animation:bouncedelay ease .3s}.luck-close{width:20px;height:20px;background:#000;color:#fff;position:absolute;right:0;top:0;text-align:center;line-height:20px;cursor:pointer}.luck-close:after{content:'×'}@-webkit-keyframes bouncedelay{0%{-webkit-transform:scale(0)}100%{-webkit-transform:scale(1)}}@keyframes bouncedelay{0%{transform:scale(0);-webkit-transform:scale(0)}100%{transform:scale(1);-webkit-transform:scale(1)}}"
	document.body.appendChild(style);
}(window);

