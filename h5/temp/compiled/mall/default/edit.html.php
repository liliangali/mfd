<!DOCTYPE html>
<html lang="zh_cn">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<title>收货地址</title>
<link rel="stylesheet" type="text/css" href="static/css/style.css" media="screen">
<link type="text/css" href="public/static/wap/css/productlist.css" rel="stylesheet" />
</head>
<body>
<script>window.onunload=function(){};</script>
<div class="main"> 
  
    <header class="hdtop">
        <div class="edit fl">
            <p class="p1"><a href="javascript:history.go(-1)"><img src="public/static/wap/images/tw_03.png" /></a></p>
            <p class="p2">收货地址</p>
        </div>
    </header>  
  
  
  <div class="tjdz w">
  <form id="addressForm">
    <h1>收件人</h1>
    <p>
      <input type="text" value="<?php echo $this->_var['data']['consignee']; ?>" name="name" />
    </p>
    <h1>手机号码</h1>
    <p>
      <input type="text" value="<?php echo $this->_var['data']['phone_mob']; ?>"  maxlength="11" name="phone" />
    </p>
    <h1>所在地区</h1>
    <div class="sgxlk">
      <p class="szdq" id="region">
        <select>
          <option  value="2"  >中国</option>
        </select>
        <input type="hidden" name="region_list" id="region_list" value="<?php echo $this->_var['data']['region_id']; ?>">
        <input type="hidden" name="region_name" id="region_name" value="<?php echo $this->_var['data']['region_name']; ?>">
      </p>
    </div>
    <h1>详细地址</h1>
    <p><input type="text" value="<?php echo $this->_var['data']['address']; ?>" name="address" /></p>    
    <h1>邮政编码</h1>
    <p>
      <input type="text" value="<?php echo $this->_var['data']['zipcode']; ?>" name="zipcode" />
    </p>
    <div class="bc_sc">
     <input type="hidden" name="edit" value="<?php echo $this->_var['edit']; ?>">
     <input type="hidden" name="id" value="<?php echo $this->_var['data']['addr_id']; ?>" />
     <input type="button" value="保存" class="input_3">
    </div>
    </form>
  </div>
</div>

<script type="text/javascript" src="static/js/jquery-1.7.2.min.js"></script> 
<script type="text/javascript" src="/includes/libraries/javascript/jquery.plugins/jquery.validate.js"></script>
<script type="text/javascript" src="static/js/jquery.form.js"></script>
<script type=text/javascript src="static/js/layer.m.js"></script><link rel="stylesheet" type="text/css" href="static/css/layer.css" media="screen">
<script type="text/javascript">
function _alert(msg){
    layer.open({
        content: '<p style="text-align:center">'+msg+'</p>',
        btn: ['确定'],
    }); 
}

$('.input_3').unbind().bind('click',addressSave);


function addressSave(){
	$('.input_3').unbind();
    $('#addressForm').ajaxSubmit({
        type:"post",
        url:"<?php echo $this->build_url(array('app'=>'cart','act'=>'addressSave')); ?>?time="+ (new Date()).getTime(),
        //beforeSubmit:function(formData,jqForm,options){return _oj.valid();},
        success:function (res,statusText) {
            var res = $.parseJSON(res);
            if(res.done == false){
                  _alert(res.msg);
                  $('.input_3').unbind().bind('click',addressSave);
            }else{
                history.go(-1);
                return false;
                //location.replace(document.referrer);
                
                $('#addressForm').find('input').val('');
                location.href="<?php echo $this->build_url(array('app'=>'cart','act'=>'address')); ?>";
            }
        }
    });
}




//地址.//乱七八糟 稍后封装
var url = "<?php echo $this->build_url(array('app'=>'mlselection')); ?>";

$(function(){
	var ri = $("#region_list").val();
	var regions = ri.split(",");
	$("#region select:first").find("option").each(function(){
	    if($(this).val() == regions[0]){
	        $(this).attr("selected", "true");
	    }
	})
	$("#region select:first").change();
})

$("#region > select").change(regionChange);

function regionChange(){
	var ri = $("#region_list").val();
    var regions = ri.split(",");
    
    $(this).nextAll("select").remove();
    var selects = $(this).siblings("select").andSelf();
    
    var id = 0;
    var names = new Array();
    var ids =new Array();
    for (i = 0; i < selects.length; i++){
        sel = selects[i];
        if (sel.value > 0)
        {
        	ids.push(sel.value);
            id = sel.value;
            name = sel.options[sel.selectedIndex].text;
            names.push(name);
        }
    }
    if (this.value > 0){
        var _self = this;
        $.getJSON(url, {'pid':this.value,'type':'region'}, function(data){
            if (data.done){
                if (data.retval.length > 0){
                    ns = $(_self).next("select");
                    if(ns.is("select")  == false){
                         $("<select><option>请选择</option></select>").insertAfter(_self);
                    }
                   
                    $(_self).next("select").unbind().bind("change",regionChange)
                    var data  = data.retval;
                    for (i = 0; i < data.length; i++){
                    	var s = "";
                        try{
                            if(regions){
                                for(r=0;r<regions.length;r++){
                                    if(data[i].region_id == regions[r]){
                                        s = " selected";
                                    }
                                }
                            }
                        }catch(e){}
                        $(_self).next("select").append("<option value='" + data[i].region_id + "'"+s+">" + data[i].region_name + "</option>");
                    }
                    if(regions){
                        var select = $(_self).next("select");
                        if(select.val()>0){
                            $(select).change();
                        }
                    }
                }else{
                    regions = "";
                    $("#region_list").val(ids.join(","));
                    $("#region_name").val(names.join("\t"));
                    $(_self).nextAll("select").remove()
                }
            }
        });
    }
}

</script>
<script>
	//检测是否是微信浏览器
	function is_weixin() {
		var ua = navigator.userAgent.toLowerCase();
		if (ua.match(/MicroMessenger/i) == "micromessenger") {
			return true;
		} else {
			return false;
		}
	}
	if (is_weixin()) {
		document.getElementById("header").style.display = 'none';
	}
</script>


</body>
</html>

