//通用-开始------------------------------------------------------------------------------------------------------------------------
$(function(){
	$('.error').hide();	   
	$(".ht_number").focus(function(){
								   
	   $(this).addClass('focus_input');
	   
	});	
	
	$(".ht_number").blur(function(){
								   
	   $(this).removeClass('focus_input');
	   
	});
	
	$('.ht_button').live('click',function(){
										  
		if($('.ht_number').val() == ''){
			alert('请输入合同编号');
		}else{
			$.ajax({
						type:"post",
						url:"index.php?app=apply&act=chick_hetong",
						data:$('.ht_number').val(),
						dataType:"json",
						success:function(data)
						{
						  if(data.retval.error == false)
						  {
							  $('.ht_tip').find('.error').prev().hide();
							  $('.ht_tip').find('.error').show().html(data.retval.error_content).addClass('error_no');
						  }
						}
			});
		}
		
	});
	
	$('#from1').find('input:[type=text],textarea').each(function(){
											
	    $(this).focus(function(){
			$(this).addClass('focus_input')
	    }),$(this).blur(function(){
		    $(this).removeClass('focus_input');
		})
		
	});
	
	$('#from1').submit(function(){
								
	    var form1_name  =  $('#name');
		var form1_phone =  $('#phone');
		
		var pattern = /^([a-zA-Z]?[\u4e00-\u9fa5]?){2,12}$/;
		var reg = /^[0-9]{5,11}$/;
		
	   if(form1_name.val() == '' || form1_name.val().length < 2){
	       form1_name.nextAll('.error1').show().addClass('error_no');
		   form1_name.nextAll('.result_yes').hide();
		   form1_name.nextAll('.error2').hide();
		   return false;
	   }else if(!pattern.test(form1_name.val())){
		  
		  form1_name.nextAll('.result_yes').hide();
		  form1_name.nextAll('.error1').hide();
		  form1_name.nextAll('.error2').show().addClass('error_no');
		  return false;

	  }else if(pattern.test(form1_name.val())){
		  
		  form1_name.nextAll('.result_yes').show();
		  form1_name.nextAll('.error1').hide();
	      form1_name.nextAll('.error2').hide();
		  
	 }
	 
	 if(form1_phone.val() == ''){
			
		   form1_phone.nextAll('.result_yes').hide();
		   form1_phone.nextAll('.error2').hide();
		   form1_phone.nextAll('.error1').show().addClass('error_no');
		   return false;
		   
		}else if(!reg.test(form1_phone.val())){
			
		   form1_phone.nextAll('.error2').show().addClass('error_no');
		   form1_phone.nextAll('.error1').hide();
		   form1_phone.nextAll('.result_yes').hide();
		   return false;
		   
		}else{

		   form1_phone.nextAll('.error2').hide();
		   form1_phone.nextAll('.error1').hide();
		   form1_phone.nextAll('.result_yes').show();
		
		}
	 
	    return true;
	 
	});
	
	$('#name').blur(function(){
	  
	  var pattern = /^([a-zA-Z]?[\u4e00-\u9fa5]?){2,12}$/;
	  
	  if($(this).val() == '' || $(this).val().length < 2){
		  
	   $(this).nextAll('.error1').show().addClass('error_no');
	   $(this).nextAll('.result_yes').hide();
	   $(this).nextAll('.error2').hide();
	   return false;
	   
	  }
	  if(!pattern.test($(this).val())){
		  
		  $(this).nextAll('.result_yes').hide();
		  $(this).nextAll('.error1').hide();
		  $(this).nextAll('.error2').show().addClass('error_no');

	  }else if(pattern.test($(this).val())){
		  
		  $(this).nextAll('.result_yes').show();
		  $(this).nextAll('.error1').hide();
	      $(this).nextAll('.error2').hide();
	 }
	});
	
	$('#phone').blur(function(){
	    var reg = /^[0-9]{5,11}$/;
		
		if($(this).val() == ''){
			
		   $(this).nextAll('.result_yes').hide();
		   $(this).nextAll('.error2').hide();
		   $(this).nextAll('.error1').show().addClass('error_no');
		   
		}else if(!reg.test($(this).val())){
			
		   $(this).nextAll('.error2').show().addClass('error_no');
		   $(this).nextAll('.error1').hide();
		   $(this).nextAll('.result_yes').hide();
		   
		}else{

		   $(this).nextAll('.error2').hide();
		   $(this).nextAll('.error1').hide();
		   $(this).nextAll('.result_yes').show();
		
		}
		
	});
	
	

})

