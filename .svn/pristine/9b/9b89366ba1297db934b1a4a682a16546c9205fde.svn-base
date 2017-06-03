//表单样式
$(document).on('blur','.input-text',function(){
	if(this.value==''){
		$(this).parents('.form-section').removeClass('form-section-active').removeClass('form-section-focus');	
	}else{
		$(this).parents('.form-section').removeClass('form-section-focus')	
	}
})
$(document).on('focus','.input-text',function(){
	$(this).parents('.form-section').addClass('form-section-active').addClass('form-section-focus');
})

//保存
$('#J_save').click(function(){
	var b=$('.address-edit-box').validate({
			acticle:true,
			error:function(obj,error){
				obj.next('.msg-error').remove()
				$(obj).parent().append('<p class="msg msg-error">'+error+'</p>')
				obj.focus();
				obj.one('blur',function(){
					$(this).next('.msg-error').remove();
				})
			}
		});
});
//关闭层
function closeLayer(){
	$('#popShadow',window.parent.document).click()
}
//取消
$('#J_cancel').click(closeLayer)