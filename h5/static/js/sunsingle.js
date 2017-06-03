twoMenu()// 顶部二级菜单
anav()//地区导航

//字数限制
function textNum(options){
	var setttings = {
		defaultTxt:"请输入",
		ShowWordNumArae: "#wnum",
		WordNum: 100
	};
	if (options) {
		$.extend(setttings, options);
	};
	var content, length, Rnum;
	var $this=$(setttings.textArea)
	$this.val(setttings.defaultTxt)
	$(setttings.ShowWordNumArae).html(setttings.WordNum);
	$this.keyup(function (e) {
		length = $this.val().length;
		Rnum = setttings.WordNum - length;
		if (e.keyCode != 8) {
			if (Rnum <= 0) {
				content = $this.val();
				content = content.substring(0, setttings.WordNum);
				$this.val(content);
				$(setttings.ShowWordNumArae).html(0);
			} else {
				content = $this.val();
				$this.val(content);
				$(setttings.ShowWordNumArae).html(Rnum);
			}
		} else {
			$(setttings.ShowWordNumArae).html(Rnum);
		}
	});
}

/*--编辑器/S--*/
textNum({
    defaultTxt: "你也可以随便说点什么",
    textArea:".editor",
    ShowWordNumArae:".numLen",
    WordNum: 200	
});
//表情
$('.editorBtn').qqFace({
	id : 'facebox', 
	assign:'editor',
	path:'../../static/expand/qqface/arclist/'
});
/*--编辑器/E--*/

//获取更多评论(Ajax获取数据)
//$('.comList>.getMore').click(function(){
//	var html='<div class="item"><a href="javascript:void(0)" class="headPic"><img src="other/pic8.jpg" width="50" height="50" /></a><div class="tbar"><span class="name">李红卫</span><span class="time">30分钟前</span></div><div class="con">这套组合穿起来默认就像是为我量身定制的一样，而且价格还比市场便宜不少！</div><p class="menu"><a href="#" class="del">删除</a></p></div><div class="item"><a href="javascript:void(0)" class="headPic"><img src="other/pic8.jpg" width="50" height="50" /></a><div class="tbar"><span class="name">李红卫</span><span class="time">30分钟前</span></div><div class="con">这套组合穿起来默认就像是为我量身定制的一样，而且价格还比市场便宜不少！</div><p class="menu"><a href="#" class="del">删除</a></p></div><div class="item"><a href="javascript:void(0)" class="headPic"><img src="other/pic8.jpg" width="50" height="50" /></a><div class="tbar"><span class="name">李红卫</span><span class="time">30分钟前</span></div><div class="con">这套组合穿起来默认就像是为我量身定制的一样，而且价格还比市场便宜不少！</div><p class="menu"><a href="#" class="del">删除</a></p></div>';
//	$(this).before(html);
//});


//右侧分享
$('.operate .share').hover(function(){
	var l=$(this).offset().left-$('.shareBox').width()+$(this).width()+4,t=$(this).offset().top+68;
	delay = setTimeout(function(){  
		$('.shareBox').css({left:l,top:t}).fadeIn('fast');
	},100)
},function(){
	if(delay){
		clearTimeout(delay);
	}
	_menu = setTimeout(function(){  
		$('.shareBox').fadeOut('fast');
	},100)
});
$('.shareBox').hover(function(){
	clearTimeout(_menu);
},function(){
	$(this).hide();
	_menu=null;
});
