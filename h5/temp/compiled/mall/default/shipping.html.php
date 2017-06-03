<!DOCTYPE html>
<html lang="zh_cn">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<title>收货地址</title>
<link rel="stylesheet" type="text/css" href="static/css/style.css" media="screen">
<link rel="stylesheet" type="text/css" href="static/css/layer.css" media="screen">
<link type="text/css" href="public/static/wap/css/productlist.css" rel="stylesheet" />
<script type=text/javascript src="static/js/jquery-1.8.3.min.js"></script>
<script type=text/javascript src="static/js/layer.m.js"></script>
</head>
<body>
<script>window.onunload=function(){};</script>
<div class="main"> 
     <header id='header' class="hdtop">
        <div class="edit fl">
            <p class="p1"><a href="javascript:history.go(-1)"><img src="public/static/wap/images/tw_03.png" /></a></p>
            <p class="p2">收货地址</p>
        </div>
    </header>
    <p class="gwchst"></p>
    
       
    <div <?php if ($this->_var['type'] == 'address' || $this->_var['type'] == 'no_choice'): ?> style="display:block;" <?php else: ?> style="display:block;" <?php endif; ?>>
    <form data-type="address">
     <div class="shrxx">        
        <ul>
          <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
          <li id="list_<?php echo $this->_var['item']['addr_id']; ?>" class="<?php if ($this->_var['item']['addr_id'] == $this->_var['address']['addr_id']): ?> on <?php endif; ?> addr_list" data-id="<?php echo $this->_var['item']['addr_id']; ?>" >
            <div class="check">
            <h1 class="bgww"><?php echo $this->_var['item']['consignee']; ?><span><?php if ($this->_var['item']['phone_mob']): ?><?php echo $this->_var['item']['phone_mob']; ?><?php else: ?><?php echo $this->_var['item']['phone_tel']; ?><?php endif; ?></span></h1>
            <p class="bgww"><?php echo $this->_var['item']['region_name']; ?> <?php echo $this->_var['item']['address']; ?></p>
            </div>
            <p class="p3 bgww"><a href="javascript:void(0)" class="del" data-id="<?php echo $this->_var['item']['addr_id']; ?>">删除</a><a href="<?php echo $this->build_url(array('app'=>'cart','act'=>'addressEdit','arg0'=>$this->_var['item']['addr_id'])); ?>">编辑</a></p>
          </li>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
        <div style="height:60px;"><p class="tjxdz"><a href="<?php echo $this->build_url(array('app'=>'cart','act'=>'addressEdit')); ?>">新增收货地址</a></p></div>
     </div>
     </form>
    </div>
        
</div>
<script type="text/javascript" src="static/js/jquery.form.js"></script>
<?php if ($this->_var['type'] == 'store'): ?>
<script>
$(function(){
    var region_id = "<?php echo $this->_var['store']['region_id']; ?>"; 
    var server_id = "<?php echo $this->_var['store']['server_id']; ?>";
    if(region_id && server_id)
    getServers(region_id,server_id);
})
</script>
<?php endif; ?>
<script>

$('#region_id').unbind().bind('change',function(){
    getServers($(this).val())
})
function getServers(region_id,server_id){
    $.post("<?php echo $this->build_url(array('app'=>'cart','act'=>'getServer')); ?>",{region_id:region_id,server_id:server_id,type:'ship'}, function(res){
        var res = $.parseJSON(res);
        if(res.done == true){
            $("#serverlist").html(res.retval.content);
            $('.server_li').unbind().bind('click',function(){
                $("#server_id").val($(this).data('id'));
                $("#server_name").val($(this).data('name'));
                $('.server_li').removeClass('on');
                $(this).addClass('on');
            })
        }
    })
}
$('.saveShip').unbind().bind('click',function(){
    var _oj = $(this).parents('form');
    shipSave(_oj)
})



$('.check').click(function(){
//    alert(11)
//	if($(this).parents('li').hasClass('on')) return;
	var _id = $(this).parents('li').data('id');
	$.post("<?php echo $this->build_url(array('app'=>'cart','act'=>'addressSet')); ?>",{id:_id}, function(res){
        var res = $.parseJSON(res);
        if(res.done == true){
        	$('.addr_list').removeClass('on');
        	$('#list_'+_id).addClass('on');
        	// add by xiao5 checkout
        	history.go(-1)
        	return false
        	//location.href="<?php echo $this->build_url(array('app'=>'cart','act'=>'checkout')); ?>";
        }
    })
})

$('.shrxx .del').click(function(){
    var $this=$(this);
    var msg=layer.open({
        content: '确认删除？',
        btn: ['确认', '取消'],
        shadeClose: false,
        yes: function(){
        	
        	$.post("<?php echo $this->build_url(array('app'=>'cart','act'=>'addressDel')); ?>",{id:$this.data('id')}, function(res){
                var res = $.parseJSON(res);
                if(res.done == true){
                	if(res.retval){
                		$('#list_'+res.retval).addClass('on')
                	}
                	$this.parents('li').fadeOut('slow');
                    layer.close(msg);
                }else{
                	
                }
            })
            
        }
    });     
})

function _alert(msg){
    layer.open({
        content: '<p style="text-align:center">'+msg+'</p>',
        btn: ['确定'],
    }); 
}
function shipSave(_oj){
	var _type = _oj.data('type');
    _oj.ajaxSubmit({
        type:"post",
        data:{type:_type},
        dataType:"json",
        url:"<?php echo $this->build_url(array('app'=>'cart','act'=>'shippingSave')); ?>?time="+ (new Date()).getTime(),
        //beforeSubmit:function(formData,jqForm,options){return _oj.valid();},
        success:function (res,statusText) {
            if(res.done == false){
                   _alert(res.msg);
            }else{
                history.go(-1)
                return false;
                //location.href="<?php echo $this->build_url(array('app'=>'cart','act'=>'checkout')); ?>";
            }
        }
    });
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
