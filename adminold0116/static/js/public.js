// JavaScript Document

/*--顶部二级菜单/S--*/
function twoMenu(){
	$('.topBar .btn-angle').hover(function(){
		var $this=$(this);
		delay=setTimeout(function(){
			var leftNum=($this.offset().left-60+$this.width()/2);
			$('.topBar>.bd-menu').children('.'+$this.attr('data-class')).show().end().css({left:leftNum}).slideDown(200);
			$this.addClass('btn-angle-hover')
		},100)
	},function(){
		if(delay){
			clearTimeout(delay);
		}
		var $this=$(this);
		_menu=setTimeout(function(){
			$('.topBar>.bd-menu').children('.'+$this.attr('data-class')).hide().end().hide();
			$this.removeClass('btn-angle-hover');
		},100);
	});
	$('.topBar>.bd-menu').hover(function(){
		clearTimeout(_menu);
	},function(){
		$(this).children('div:visible').hide().end().hide();
		$('.topBar').find('.btn-angle').removeClass('btn-angle-hover')
		_menu=null;	
	})
}
/*--顶部二级菜单/E--*/

/*--地区导航/S--*/
function anav(){
	$('.anav').hover(function(){
		$(this).addClass('anav-hover');
		$(this).children('.anav .bd-menu').show(400);
		$(this).children('.anav .btn-angle').addClass('btn-angle-hover')
	},function(){
		var $this=$(this);
		anav=setTimeout(function(){
			$this.removeClass('anav-hover');
			$this.children('.anav .bd-menu').stop().hide(400);
			$this.children('.anav .btn-angle').removeClass('btn-angle-hover');
		},100);
	});
	$('.anav>.bd-menu').hover(function(){
		clearTimeout(anav);
	},function(){
		$('.anav').removeClass('anav-hover');
		$(this).stop().hide(400);
		$(this).prev('.anav .btn-angle').removeClass('btn-angle-hover');
		anav=null;	
	})
}
/*--地区导航/E--*/

/*--表单默认提示/S--*/
$(function(){
	$('input[data-placeholder]').each(function(i, e) {
		var t=e.getAttribute('data-placeholder');
		e.value=t;
		if(e.type=='password'){
			e.type='text';
			e.setAttribute('d',1);	
		}
		$(e).focus(function(){
			$(e).parents('.search').addClass('focus')
			if(e.value==t){
				e.value='';
				$(e).addClass('cur');
				if(e.getAttribute('d')==1){
					e.type='password';
				}
			}
		});
		$(e).blur(function(){
			$(e).parents('.search').removeClass('focus');
			if(e.value==''){
				e.value=t;
				$(e).removeClass('cur');
				if(e.getAttribute('d')==1){
					e.type='text';	
				}
			}
		});
	});
})
/*--表单默认提示/E--*/

/*--添加收藏/S--*/
$(document).on('click','.AddFavorite',function(){
	var e=window.location.href,t=document.title;
	try {
		window.external.addFavorite(e, t)
	}
	catch (n) {
		try {
			window.sidebar.addPanel(t, e, "")
		} catch (n) {
			alert("加入收藏失败，请使用Ctrl+D进行添加！")
		}
	}	
})
/*--添加收藏/E--*/
