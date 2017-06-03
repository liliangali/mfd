var online_province=new Array();
var store_province=new Array();
var online_city=new Array();
var online_area=new Array();
var store_city=new Array();
var municipalities = {};
//var municipalities = {"11":"北京","12":"天津","31":"上海","50":"重庆"};
var autonomousregion = {"54":"西藏","65":"新疆","15":"内蒙古"};
var receipt_modify=true;

var init=function(){
	var size=0;
	for(i=2;i<=3;i++){
		for(j=1;j<=2;j++){ 
			var v = i+''+j;
			var product_data=product[0][v];
			$('#version_'+size+' .price').html('&yen;'+product_data['Price']);
			size++;
		 } 
	}
	
	$('#hidden_product').val(0);
	$('#hidden_booking_type').val(0);
	$('#hidden_province').val(0);
	$('#hidden_city').val(0);
	$('#hidden_area').val(0);
	$('#hidden_store').val('');
	$('#recipient').val('');
	$('#mobile_phone').val('');
	$('#address_detail').val('');
	$('#zip_code').val('');
	$('#receipt').val('');
	$('#hidden_version').val('2');
	$('#hidden_type').val('1');
	$('#hidden_address').val('');
	$('#hidden_product_name').val('');
	$('#hidden_product_price').val('');
};

$(document).ready(function(){
	init();
	$('.version li').click(function(){
		$('.version li').removeClass("selected");
		$(this).addClass("selected");
		$('#hidden_version').val($(this).attr('version'));
		$('#hidden_type').val($(this).attr('type'));
		load_product();
	});
	
	var load_product=function(){
		var booking_type=$('#hidden_booking_type').val();
		var version=$('#hidden_version').val();
		var type=$('#hidden_type').val();
		var v = version+''+type;
		
		if(typeof product[booking_type][v] !='undefined'){
			if(booking_type=='0'){
				var now_price=product[booking_type][v]['Price']+'.00';
				$('#product_price_text').html('商品价');	
			}else{
				var now_price='200.00';
				$('#product_price_text').html('预付款');
			}
			$('#product_price_text').show();
			$('#hidden_product').val(product[booking_type][v]['ID']);
			$('#hidden_product_name').val(product[booking_type][v]['VerName']);
			$('#hidden_product_price').val(now_price);
			$('#total_price').html('&yen; '+now_price);
			$('#version_text').html(product[booking_type][v]['VerName']);
		}
	};
	
	$('#booking_type li').click(function(){
		$('#booking_type li').removeClass("selected");
		$(this).addClass("selected");
		var booking_type=$(this).attr('values');
		$('#hidden_booking_type').val(booking_type);
		if(booking_type=='0'){
			$('#recipient_text').html('收件人');
			if(typeof online_province['id']!='undefined'){
				$('#province').html(online_province['name']);
				$('#hidden_province').val(online_province['id']);
			}else{
				$('#province').html('省份 / 直辖市');
				$('#hidden_province').val(0);
			}
			
			if(typeof online_city['id']!='undefined'){
				$('#city').html(online_city['name']);
				$('#hidden_city').val(online_city['id']);
			}else{
				$('#city').html('城市');
				$('#hidden_city').val(0);
			}
			
			if(typeof online_area['id']!='undefined'){
				$('#area').html(online_area['name']);
				$('#hidden_area').val(online_area['id']);
			}else{
				$('#area').html('区 / 县');
				$('#hidden_area').val(0);
			}
			
			$('#store_list').hide(); 
			$('#store_list_notice').hide();
			$('#address_detail_li').show();
			//$('#zip_code_li').show();
			$('#address_head').show();
			$('#area').show();
			$('#receipt_li').show();
		}else{
			$('#recipient_text').html('姓名');
			
			if(typeof store_province['id']!='undefined'){
				$('#province').html(store_province['name']);
				$('#hidden_province').val(store_province['id']);
			}else{
				$('#province').html('省份 / 直辖市');
				$('#hidden_province').val(0);
			}
			
			if(typeof store_city['id']!='undefined'){
				$('#city').html(store_city['name']);
				$('#hidden_city').val(store_city['id']);
			}else{
				$('#city').html('城市');
				$('#hidden_city').val(0);
			}
			
			if($('#store_list').html()!=''){
				$('#store_list_notice').show();
				$('#store_list').show();
			}
		
			$('#address_detail_li').hide();
			//$('#zip_code_li').hide();
			$('#address_head').hide();
			$('#area').hide();
			$('#receipt_li').hide();
		}
		load_product();
	});
	
	$('#province').click(function(e){
		var p='';
		for(i in province){
			p=p+"<li class='province_li' p_value='"+i+"'>"+province[i]+"</li>";
		}
		
		$('#order_city').hide();
		$('#order_area').hide();
		$('#order_province').html('');
		$('#order_province').append(p);
		$('#order_province').show();
		
		var foo = function(){
			$('#order_province').hide();
			$(document).unbind('click',foo);
		}
		$(document).bind('click', foo);
		
		if(e && e.stopPropagation ){
			e.stopPropagation(); 
		}else{ 
			window.event.cancelBubble = true;
			return false;
		}
	});
	
	$('#city').click(function(e){
		var c='';
		var p=$('#hidden_province').val();
		var booking_type=$('#hidden_booking_type').val();
		
		if(booking_type=='0' && typeof municipalities[p]!='undefined'){
			return true;
		}
		if(p!='0'){
			
			if(booking_type=='0'){
				for(i in city[p]){
					c=c+"<li class='city_li' c_value='"+i+"'>"+city[p][i]['city']+"</li>";
				}
			}else{
				for(i in city[p]){
					if(city[p][i]['store']=='1')
						c=c+"<li class='city_li' c_value='"+i+"'>"+city[p][i]['city']+"</li>";
				}
			}
			
		}else{
			c="<li c_value='0'>请先选择省份 / 直辖市</li>";
		}
		
		$('#order_province').hide();
		$('#order_area').hide();
		$('#order_city').html('');
		$('#order_city').append(c);
		$('#order_city').show();
		
		var foo = function(){
			$('#order_city').hide();
			$(document).unbind('click',foo);
		}
		$(document).bind('click', foo);
		
		
		if(e && e.stopPropagation ){
			e.stopPropagation(); 
		}else{ 
			window.event.cancelBubble = true;
			return false;
		}
		
	});
	
	$('#area').click(function(e){

		var p=$('#hidden_province').val();
		var hidden_city=$('#hidden_city').val();
		var booking_type=$('#hidden_booking_type').val();
		
		$('#order_province').hide();
		$('#order_city').hide();
		
		if(booking_type==1) {
			return true;
		}
		var c='';
		if(typeof municipalities[p]!='undefined'){
			if(p!='0'){
				for(i in city[p]){
					c=c+"<li class='city_li' c_value='"+i+"'>"+city[p][i]['city']+"</li>";
				}
			}else{
				c="<li c_value='0'>请先选择省份 / 直辖市</li>";
			}
		}else{
			if(hidden_city!='0'){
				for(i in area[hidden_city]){
					c=c+"<li class='area_li' a_value='"+i+"'>"+area[hidden_city][i]['area']+"</li>";
				}
			}else{
				c="<li a_value='0'>请先选择城市</li>";
			}
		}
		
		$('#order_area').html('');
		$('#order_area').append(c);
		$('#order_area').show();
		
		var foo = function(){
			$('#order_area').hide();
			$(document).unbind('click',foo);
		}
		$(document).bind('click', foo);
		
		if(e && e.stopPropagation){
			e.stopPropagation(); 
		}else{ 
			window.event.cancelBubble = true;
			return false;
		}
		
	});
	
	$("#order_province li").live({ mouseenter: function () {
			$(this).addClass("hover");
		}, mouseleave: function () {
			$(this).removeClass("hover"); 
		} , click: function(){
			var p_value=$(this).attr('p_value');
			$('#hidden_province').val(p_value);
			$('#province').html($(this).html());
			$('#order_province').hide();
			if($('#hidden_booking_type').val()=='0'){
				if(typeof municipalities[p_value]!='undefined'){
					$('#hidden_city').val(p_value);	
					if(p_value!=online_province['id']){
						online_city['id']=p_value;
						online_city['name']=$(this).html()+'市';
						online_area['id']=0;
						online_area['name']='';
						$('#hidden_city').val(p_value);
						$('#hidden_area').val(0);
					}
					online_province['id']=p_value;
					online_province['name']=$(this).html();
					
					$('#city').html(municipalities[p_value]+'市');
					$('#area').html('区 / 县');
					$('#order_area').html('');
					$('#area').click();
				}else{
					$('#city').html('城市');
					if(p_value!=online_province['id']){
						online_city['id']='';
						online_city['name']='';
						online_area['id']=0;
						online_area['name']='';
						$('#area').html('区 / 县');
						$('#hidden_city').val(0);
						$('#hidden_area').val(0);
					}
					
					online_province['id']=p_value;
					online_province['name']=$(this).html();
						
					$('#city').click();
				}
			}else{
				if(typeof municipalities[p_value]!='undefined'){
				
					if(p_value!=store_province['id']){
						store_city['id']=p_value;
						store_city['name']=$(this).html()+'市';
						
						$('#city').html(municipalities[p_value]+'市');
						
						$('#hidden_city').val(p_value);
						$('#hidden_area').val(0);
						
						var c='';
						for(i in store[p_value]){
							c=c+"<li s_value='"+i+"'>"+store[p_value][i]+"<a target='_blank' href='http://retail.meizu.com/details/"+i+".htm'>查看地址</a></li>";
						}
						$('#store_list').html('');
						$('#store_list').append(c);
						var w=$('#store_list').width()+1;
						$('#store_list').width(w);
						$('#store_list').show();
						$('#store_list_notice').show();
					}
					
					store_province['id']=p_value;
					store_province['name']=$(this).html();
					
					$('#order_city').html('');
				
				}else{
					store_city['id']=0;
					store_city['name']='';
					$('#hidden_city').val(0);
					
					store_province['id']=p_value;
					store_province['name']=$(this).html();
					var c='';
					if(p_value!='0'){
						for(i in city[p_value]){
							if(city[p_value][i]['store']=='0') continue;
							c=c+"<li class='city_li' c_value='"+i+"'>"+city[p_value][i]['city']+"</li>";
						}
					}else{
						c="<li c_value='0'>请先选择省份 / 直辖市</li>";
					}
					
					$('#order_city').html('');
					$('#order_city').append(c);
					$('#order_city').show();
				}
				
				
			}
		}
	});
	
	$("#order_city li").live({ mouseenter: function () {
			$(this).addClass("hover");
		}, mouseleave: function () {
			$(this).removeClass("hover"); 
		} , click: function(){
			var c_value=$(this).attr('c_value');
			var p=$('#hidden_province').val();
			
			if(c_value=='0'){
				$('#order_city').hide();
				return false;
			}
			$('#hidden_city').val(c_value);
			$('#city').html($(this).html());
			$('#order_city').hide();
			$("#hidden_store").val('');
			
			var booking_type=$('#hidden_booking_type').val();
			if(booking_type=='0'){
				online_city['id']=c_value;
				online_city['name']=$(this).html();
				if(typeof municipalities[p]=='undefined'){
					$('#area').click();
				}
			}else{
				var c='';
				for(i in store[c_value]){
					c=c+"<li s_value='"+i+"'>"+store[c_value][i]+"<a target='_blank' href='http://retail.meizu.com/details/"+i+".htm'>查看地址</a></li>";
				}
				$('#store_list').html('');
				$('#store_list').append(c);
				var w=$('#store_list').width()+1;
				$('#store_list').width(w);
				$('#store_list').show();
				store_city['id']=c_value;
				store_city['name']=$(this).html();
				$('#store_list_notice').show();
				
			}
			
		}
	});
	
	$("#order_area li").live({ mouseenter: function () {
			$(this).addClass("hover");
		}, mouseleave: function () {
			$(this).removeClass("hover"); 
		} , click: function(){
			var p=$('#hidden_province').val();
			var c=$('#hidden_city').val();
			var area_text=$(this).html();
			$('#area').html(area_text);
			$('#order_area').hide();
			if(typeof municipalities[p]=='undefined'){
				var a_value=$(this).attr('a_value');
				if(a_value==0) return false;
				
				if(typeof autonomousregion[p]=='undefined'){
					var address=$('#province').html()+'省'+$('#city').html()+area_text;
				}else{
					var address=$('#province').html()+$('#city').html()+area_text;
				}
				
				if(typeof area[c][a_value]!='undefined' && typeof area[c][a_value]['code']!='undefined'){
					$('#zip_code').val(area[c][a_value]['code']);
				}
			}else{
				var a_value=$(this).attr('c_value');
				var address=$('#city').html()+area_text;
				
				if(typeof city[p][a_value]!='undefined' && typeof city[p][a_value]['code']!='undefined'){
					$('#zip_code').val(city[p][a_value]['code']);
				}
			}
			var address_details=$('#address_detail').val();
			var hidden_address=address+address_details;
			$('#hidden_area').val(a_value);
			$('#address_head').html(address);
			$('#hidden_address').val(hidden_address);
			
			online_area['id']=a_value;
			online_area['name']=area_text;
			
			var paddings=$('#address_head').width()+15;
			var address_detail_width=558-paddings;
			
			$('#address_detail').css({
				'paddingLeft':paddings+"px",
				'width':address_detail_width+'px'
			});
			$('#address_detail').val(address_details);
			$('#address_detail').focus();
		}
	});
	


	



	
	$('#address_detail').blur(function(){
		check_address(true);
		var hidden_booking_type=$('#hidden_booking_type').val();
		var p=$('#hidden_province').val();
		
		if(hidden_booking_type=='0'){
			if(typeof municipalities[p]!='undefined'){
				var address=$('#city').html()+$('#area').html()+$('#address_detail').val();
			}else{
				if(typeof autonomousregion[p]=='undefined'){
					var address=$('#province').html()+'省'+$('#city').html()+$('#area').html()+$('#address_detail').val();
				}else{

					var address=$('#province').html()+$('#city').html()+$('#area').html()+$('#address_detail').val();
				}
			}
			$('#hidden_address').val(address);	
		}
	});
	
	
	$("#address_detail").focus(function(){
		var id='#'+$(this).attr('id')+'_error';
		$(this).removeClass('errorClass').addClass('foucsClass');
		$(id).html('');
	});
	
	
	$('#address_detail').keydown(function(e){
		if(e.which==32) return false;
		if(e.which==13) return false;
	});
	
	$('#serve_time').click(function(e){
		$('#serve_time_order').show();
		
		var foo = function(){
			$('#serve_time_order').hide();
			$(document).unbind('click',foo);
		}
		$(document).bind('click', foo);
		
		if(e && e.stopPropagation ){
			e.stopPropagation(); 
		}else{ 
			window.event.cancelBubble = true;
			return false;
		}
	});
	
	$('#serve_time_order li').click(function(e){
		$('#serve_time').text($(this).text());
		$('#timeopt').val($(this).attr('p_value'));
	});
	
}); 