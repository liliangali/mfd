<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<div class="user_box">
<?php echo $this->fetch('member.menu.html'); ?>
    <div class="user_right fr">
    	<div class="yhname">
        	<div class="namexx">
            	<p id="" class="portrait personal fl">
				<img src="<?php echo $this->_var['avatar']; ?>" width="94" height="94">
				<!--<img width="45" height="45" class="edit animated zoomIn" src="/public/static/pc/images/bjpic.png">-->
				</p>
                <div class="member fl">
                	<p class="display"><?php echo $this->_var['user']['nickname']; ?></p>
                	<p class="etc">会员等级</p>
                	<!--<p class="etc"><?php echo $this->_var['lv_info']['name']; ?></p>-->
                    <p class="yhxg"><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'profile')); ?>">修改个人信息&nbsp;></a></p>
                </div>

				<div class="yhaqxx fl">
                    <div class="zhaqc">
                        <p class="hpjw fl">账户安全：</p>
                        <div class="hptia fl" style="margin-top: 11px;">
                        <p style="width:<?php echo $this->_var['percent']; ?>%;background-color:<?php echo $this->_var['color']; ?>"></p></div>&nbsp;&nbsp;<?php if ($this->_var['percent'] < 100): ?> <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'safe')); ?>">提高&nbsp;></a><?php endif; ?>
                        <p class="jiaog fl"><?php echo $this->_var['safe_level']; ?></p>
                    </div>
                    <p>手机号码：<?php echo $this->_var['user']['phone_mob']; ?>&nbsp;&nbsp;&nbsp;<!-- <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'phone_bind_1')); ?>">修改&nbsp;></a> --></p>
                    <p>邮箱地址：<?php if ($this->_var['user']['email']): ?><?php echo $this->_var['user']['email']; ?><?php else: ?><!-- <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'email_bind_1')); ?>">未绑定&nbsp;></a> --><?php endif; ?></p>
                </div>

                <div class="mjjf fr">
                	<p>麦券：<span><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'debit')); ?>"><?php echo $this->_var['debitnum']; ?>张</a></span></p>
                	<!--<p>积分：<span><?php echo $this->_var['user']['point']; ?>分</span></p>-->
                	<a href=<?php echo $this->build_url(array('app'=>'member','act'=>'logout')); ?> class="yhtcbut fr">退出登录</a>
                </div>
                
            </div>
        </div>
     
        <div class="info">
        	<div class="yhwzfdd fl">
            	<p class="yhwzfqk">待支付<span>（<?php echo $this->_var['order_about']['npaid']; ?>）</span></p>
                <a href="/my_order-index-1-unpay.html">查看待支付订单&nbsp;></a>
            </div>
            <div class="yhwzfdd yhwsh fl">
                <p class="yhwzfqk">待发货<span>（<?php echo $this->_var['order_about']['df']; ?>）</span></p>
                <a href="/my_order-index-1-payed.html">查看待发货订单&nbsp;></a>
            </div>
            <div class="yhwzfdd yvhdid fl">
            	<p class="yhwzfqk">已发货<span>（<?php echo $this->_var['order_about']['nreceipt']; ?>）</span></p>
                <a href="/my_order-index-1-shipped.html">查看已发货订单&nbsp;></a>
            </div>
            <div class="yhwzfdd yhdpj fr">
            	<p class="yhwzfqk">待评价<span>（<?php echo $this->_var['fx_info']['count']; ?>）</span></p>
                <a href="/fx-fx_list.html">查看待评价订单&nbsp;></a>
            </div>
        </div>
		<div class="track">
        	<h4><p class="guess fl">为你推荐</p><p class="change fr"><a href="javascript:void(0)" id="GoodsList">换一组</a></p></h4>
            <ul id="goodslist">
                <?php if ($this->_var['goods_list']): ?>
                <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
            	<li><a href="<?php echo $this->build_url(array('app'=>'goods','arg0'=>$this->_var['goods']['goods_id'])); ?>" target="_blank"><img src="<?php echo $this->_var['goods']['thumbnail_pic']; ?>"></a><p class="yhword"><a href="<?php echo $this->build_url(array('app'=>'goods','arg0'=>$this->_var['goods']['goods_id'])); ?>" target="_blank"><?php echo $this->_var['goods']['name']; ?></a></p><p class="yhword"><span>¥<?php echo $this->_var['goods']['price']; ?></span></p><p class="pinjia"><?php echo $this->_var['goods']['comment_num']; ?>人评价</p></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/global/jquery.form.js"></script> 
<script src="/public/global/luck/pc/luck.js"></script> 
<link rel="stylesheet" href="/public/global/jcrop/css/jquery.Jcrop.css">
<script src="/public/global/jcrop/js/jquery.Jcrop.js"></script>
<script src="/public/static/pc/js/public.js"></script> 
<script src="/public/static/pc/js/Xslider.js"></script>
<script type="text/javascript">
//头像上传
$('#headEdit').click(function(){
	luck.open({
		title:'修改头像',
		content:'<div class="head-edit"><div class="step1"><form id="uploadForm"><div class="btn">上传头像<input type="file" name="avatar" accept="image/jpeg, image/png" onChange="uploadHead(this)"></div><p>jpg 或 png，大小不超过2M</p></form></div><div class="step2" style="display:none"><div class="picBox" id="picBox"></div><p class="btnBox"><button class="ok">确定</button><button class="reset">重新上传</button></p></div><div class="loading" style="display:none"><img src="/public/static/pc/images/loading.gif" width="90" height="90"><p class="tip">上传中...</p></div></div>',
		width:'400px',
		height:'auto',
		class:'mfd-luck'
	})	
});
function uploadHead(e){
	$('#uploadForm').ajaxSubmit({
		url:'/member-getImg.html',
		type:'post',
		dataType:'json',
		beforeSubmit:function(a,b,c){
			$('.head-edit .step1').hide();
			$('.head-edit .loading').show();
		},
		success:function(d){
			if(!d.done){
				alert(d.msg);
				return
			}
			var data=null;
			var img=new Image;
			img.src=d.retval+'?'+Math.random();
			img.className="img";
			img.onload=function(){
				//切换状态
				$('.head-edit .loading').hide();
				$('.head-edit .step2').show();
				//追加图片
				var oDiv=$('<div/>');
				oDiv.html(this);
				$('#picBox').html(oDiv);
				oDiv.css('margin-top',(300-oDiv.height())/2>0?(300-oDiv.height())/2:0);
				//调用裁剪工具
				$(this).Jcrop({
					//完成回调
					onSelect:function(c){
						data=c;
					},
					//移动回调
					onChange:function(c){
						data=c
					},
					//背景颜色
					bgColor:'fff',
					//背景透明度
					bgOpacity:'.5',	
					//坐标
					setSelect: [ 0, 0, 100, 100],//x,y,w,h
					//最小
					minSize:[100,100],
					//最大
					maxSize:[200,200],
					//比例
					aspectRatio:1/1
				});
				//绑定按钮事件
				$('.head-edit .ok').unbind('click').click(function(){
					$.ajax({
						url:'/member-editAvatar.html',
						type:'POST',
						dataType:"json",
						data:{
							data:'{"x":'+data.x+',"y":'+data.y+',"w":'+data.w+',"h":'+data.h+',"imgw":300,"imgh":'+(300/img.width*img.height)+'}',
							avatar:d.retval
						},
						beforeSend: function(){
							$('.head-edit .step2').hide();
							$('.head-edit .loading').show();
						},
						success: function(d){
							if(d.done){
								$('#headEdit img:eq(0)').attr('src',d.retval.avatar+'?'+Math.random());
							}else{
								alert(d.msg)	
							}
							luck.close();	
						},
						error:function(){
							alert('errorasdfasdf')	
						}
					})
				});
				$('.head-edit .reset').unbind('click').click(function(){
					$('.head-edit .step1').show();
					$('.head-edit .step2').hide();
				});
			}
		},
		error:function(){
			alert('error')	
		}	
	})	
}




 $('#GoodsList').click(function(){

    $.get("/member-getGoodsList.html",{num:4}, function(res){
        var r=eval("("+res+")");
        if(r.done){
            console.log(r.retval);
            var str="";
            $.each(r.retval,function(n,d){
                str+='<li><a href="/goods-'+d.goods_id+'.html" target="_blank"><img src="'+d.small_pic+'" width="202" height="303"></a><p class="yhword"><a href="/goods-'+d.goods_id+'.html" target="_blank">'+d.name+'</a></p><p class="yhword">¥'+d.price+'</p></li>'
            });
            $('#goodslist').html(str)
        }else{
            alert(r.msg);
            return;
        }
    })
})  

 $('#is_auth').click(function(){
	var status = $(this).attr('value');
	if(status == ''){
	 	  luck.confirm('系统提示','尚未实名认证，暂不能提现。',function(obj){
					if(obj == false)
					{
					//window.open('/member-cyz_auth_1.html');
					window.location.href="/member-cyz_auth_1.html";
					}
		},['取消','去认证']);
	}else if(status == 0){
	 	  luck.confirm('系统提示','实名认证审核中，暂不能提现。',function(obj){
				if(obj == false)
					{
					
					 //window.open('/member-cyz_auth_1.html');
					window.location.href="/member-cyz_auth_1.html";
					}
	},['取消','查看']);
	  
	}else if(status ==2){
	 	  luck.confirm('系统提示','实名认证失败，暂不能提现。',function(obj){
				if(obj == false)
					{
					
					//window.open('/member-cyz_auth_1.html');
					window.location.href="/member-cyz_auth_1.html";
					}
	},['取消','查看']);
		
	}
  
})  


</script>
<script type="text/javascript">
$(function(){
	//左右切换分页效果的实现；
	var totalnum=$(".productshow li").length,
		numperpage=6,
		pages=Math.ceil(totalnum/numperpage),
		temp=$(".scrollwraper ul");
	
	function app(){
		temp=$("<ul />").append(temp.find("li:gt("+(numperpage-1)+")")).appendTo($(".scrollwraper"));
		if(temp.find("li").length>numperpage){
			app();
		}
	}
	app();
	
	$(".productshow").Xslider({
		scrollobj:".scrollwraper",
		unitdisplayed:1,
		numtoMove:1,
		viewedSize:900,
		unitlen:900,
		scrollobjSize:900*pages
	});
	
	$("a").focus(function(){this.blur();});
})
</script>
<?php echo $this->fetch('footer.html'); ?>
