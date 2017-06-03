//通过锚点定位导航
function setnav(n){
	$('.m_head a').eq(n).siblings('.cur').removeClass('cur').end().addClass('cur');
}
if(location.hash=='#1'){
	setnav(2)
}else if(location.hash=='#2'){
	setnav(3)
}
$('.m_head a').click(function(){
  setnav($(this).index())
})
// 滚动图
;(function(){
	var elem = document.getElementById('swipe1');
	var mySwipe = Swipe(elem, {
		callback: function(index, element) {
			$('.box1 .swipe-btn>span').eq(index).siblings('span').removeClass('cur').end().addClass('cur');     
		}
	});
	$('.prev').click(function(){
		mySwipe.prev()	
	});
	$('.next').click(function(){
		mySwipe.next()	
	});
})();

//晒单图片大图显示
$('.listItem .sun li').click(function(){
	var $this=$(this);
	use('../../static/expand/layer/layer.min.js',function(){
		use('../../static/expand/layer/extend/layer.ext.js',function(){
			layer.photos({
				//html: '裁缝的作品展示',    //自定义的html，显示在层右侧
				//tab:function(obj){}, //图片切换操作的回调，obj返回了图片pid和name
				page: { 
					parent:$this.parent('ul'),
					start:$this.parent('ul').children('li').index($this), //初始显示的图片序号，默认0
					title:$this.parent().attr('data-name')
				}
			})	
		})
	})
});

//固定定位
function boxFixed(obj) {
    var fixedDiv = obj.fixedDiv,
        fixedDivH = fixedDiv.outerHeight(),
	    offset = fixedDiv.offset().top,
	    parentDiv = obj.parentDiv,
		mt = Number(fixedDiv.css('margin-top').replace('px', ''));
    fixedDiv.after('<div style="width:100px;height:' + fixedDivH + 'px;display:none" class="perch"></div>')
	$(window).load(fexed);
    $(window).scroll(fexed);
    $(window).resize(fexed);
    function fexed() {
	    var parentDivH = parentDiv.height(),
            scrTop = $(document).scrollTop();
        if (scrTop >= offset) {
            fixedDiv.next(".perch").show();
            if ($.browser.msie && $.browser.version < 7) {
                fixedDiv.css({ position: "absolute", top: scrTop, zIndex: "3" });
            } else {
                if (scrTop >= parentDiv.offset().top + parentDivH - fixedDivH) {
                    fixedDiv.css({ position: "absolute", marginTop: parentDivH - fixedDivH,top:0});
                } else {
                    fixedDiv.css({ position: "fixed", top: 0, marginTop: 0, zIndex: "3" });
                }
            }
        } else {
            fixedDiv.next(".perch").hide();
            fixedDiv.css({ position: "static", marginTop: mt })
        }
    }
};
//boxFixed({fixedDiv:$('.tailorInfo'),parentDiv:$('#fixedBox')})	




//顶部二级菜单
$('.head .btn-angle').hover(function(){
	var $this=$(this);
	delay=setTimeout(function(){
		var leftNum=($this.offset().left-60+$this.width()/2);
		$('.head>.bd-menu').children('.'+$this.attr('data-class')).show().end().css({left:leftNum}).slideDown(200);
		$this.addClass('btn-angle-hover')
	},100)
},function(){
	if(delay){
		clearTimeout(delay)
	}
	var $this=$(this);
	_menu=setTimeout(function(){
		$('.head>.bd-menu').children('.'+$this.attr('data-class')).hide().end().hide();
		$this.removeClass('btn-angle-hover');
	},100)
});
$('.head>.bd-menu').hover(function(){
	clearTimeout(_menu);
},function(){
	$(this).children('div:visible').hide().end().hide();
	$('.head').find('.btn-angle').removeClass('btn-angle-hover')
	_menu=null
});

//地区切换
$('.anav .hd').click(function(){
	var obj=$(this).next('.bd');
	if(obj.is(':visible')){
		obj.hide('fast')		
	}else{
		obj.show('fast')
	}
	return false
});
$(document).click(function(){$('.anav .bd').hide()})


















