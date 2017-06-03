var scrolltotop = {
    setting: {
        startline: 100,
        //起始行
        scrollto: 0,
        //滚动到指定位置
        scrollduration: 400,
        //滚动过渡时间
        fadeduration: [500, 100] //淡出淡现消失
    },
    controlHTML:"",
	controldiv: $("#topcontrol"),
    //返回顶部按钮
    controlattrs: {
        offsetx: ($(window).width() - $("#topcontrol").width()) / 2,
        offsety: 0
    },
    //返回按钮固定位置
    anchorkeyword: "#top",
    state: {
        isvisible: false,
        shouldvisible: false
    },
    /*scrollup: function() {
        if (!this.cssfixedsupport) {
            this.controldiv.css({
                opacity: 0
            });
        }
        var dest = isNaN(this.setting.scrollto) ? this.setting.scrollto: parseInt(this.setting.scrollto);
        if (typeof dest == "string" && jQuery("#" + dest).length == 1) {
            dest = jQuery("#" + dest).offset().top;
        } else {
            dest = 0;
        }
        this.$body.animate({
            scrollTop: dest
        },
        this.setting.scrollduration);
    },*/
    /*keepfixed: function() {
				
        var $window = jQuery(window);
        var controlx = $window.scrollLeft() + $window.width() - this.controldiv.width() - this.controlattrs.offsetx;
		
        var controly = $window.height() + $window.scrollTop() - this.controldiv.height()-147;
        this.controldiv.css({
            left:0,
            top: controly
        });
    },*/
    /*togglecontrol: function() {
        var scrolltop = jQuery(window).scrollTop();
        if (!this.cssfixedsupport) {
            this.keepfixed();
        }

        this.state.shouldvisible = (scrolltop >= this.setting.startline) ? true: false;
        if (this.state.shouldvisible && !this.state.isvisible) {
        		this.controldiv.stop().animate({
                    opacity: 1
                },
                this.setting.fadeduration[0]).show();
                this.state.isvisible = true;
        	
        } else {
            if (this.state.shouldvisible == false && this.state.isvisible) {
                this.controldiv.stop().animate({
                    opacity: 0
                },
                this.setting.fadeduration[1]);
                this.state.isvisible = false;
            }
        }
    },*/
    init: function() {
        jQuery(document).ready(function($) {
			var mainobj = scrolltotop;
            var iebrws = document.all;
            mainobj.cssfixedsupport = !iebrws || iebrws && document.compatMode == "CSS1Compat" && window.XMLHttpRequest;
            mainobj.$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $("html") : $("body")) : $("html,body");
	            mainobj.controldiv.css({
	                position: mainobj.cssfixedsupport ? "fixed": "absolute",
	                bottom: mainobj.controlattrs.offsety,
	                left: mainobj.controlattrs.offsetx,
	                opacity: 0,
	                cursor: "pointer"
	            }).attr({
	                title: "购买此组合"
	            })
            if (document.all && !window.XMLHttpRequest && mainobj.controldiv.text() != "") {
                mainobj.controldiv.css({
                    width: mainobj.controldiv.width()
                });
            }
			

            //mainobj.togglecontrol();

            $('a[href="' + mainobj.anchorkeyword + '"]').click(function() {
                mainobj.scrollup();
                return false;
            });
			
            $(window).bind("scroll resize",
            function(e) {
            	if(($(document).height() - $(window).scrollTop()) <= 1330 || $(window).scrollTop() <=1037){
            		mainobj.controldiv.stop().animate({
                        opacity: 0
                    },mainobj.setting.fadeduration[0]).hide();
            	}else if(($(document).height() - $(window).scrollTop()) >= 1037 && $(window).scrollTop() >=1037){
            		//alert($(window).scrollTop());
            		mainobj.controldiv.stop().animate({
                        opacity: 1
                    },mainobj.setting.fadeduration[1]).show()
            	}
            });
			 
        });
										
    }
};

scrolltotop.init();
