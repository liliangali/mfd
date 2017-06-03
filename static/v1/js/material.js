
twoMenu()// 顶部二级菜单
anav()//地区导航
//商品划过效果
$('.picList>li').hover(function(){
	$(this).addClass('item-hover')	
},function(){
	$(this).removeClass('item-hover')
});

//$('.btnFavorite').click(function(){
//	if(!true){
//		//登陆处理	
//	}else{
//		//未登录处理
//		login()	
//	}
//})