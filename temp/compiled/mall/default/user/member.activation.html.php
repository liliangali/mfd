<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<div class="user_box">
<?php echo $this->fetch('member.menu.html'); ?>
       <div class="user_right user_rights fr">
		<h4>激活优惠券</h4>
       
        <div class="recently recentlyfs jihuo_box">
			<form >
			  <span class="error"></span>
			  <input name="code" class="jihuo_ipt" placeholder="输入10位激活码">
			  <input type="button" value="激活" class="grbut">
				<div class="flip fr">
				<?php echo $this->fetch('page.bottom.html'); ?>
				</div>
			</form>
        </div>

    </div>
</div>

<div id="window004" style="display:none;">
	<div class="wdktbword">
	<?php echo $this->_var['article_info']; ?>
	</div>
</div>

<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/global/jquery.swipe.js"></script> 
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/static/pc/js/usercenter.js"></script>
<script src="/public/global/luck/pc/luck.js"></script> 
<link rel="stylesheet" type="text/css" href="/public/static/layer_mobile/need/layer.css">
<script type="text/javascript" src="/public/static/layer_mobile/layer.js"></script>
<script>
 $('.grbut').click(function(){
	 var code=$("input[name='code']").val()
	                $.ajax({
                        url:'/member-activation.html',
                        type:'POST',
                        data:"code="+code,
                        dataType: "json",
                        async: false,
                    success:function(res){
                    	console.log(res)
                    if(!res.done)
                    {
                    	$('.error').html(res.msg);
                    }
                    else
                   {
                    	
                    	layer.open({
        					  anim: 'up',
        					  content:'激活成功<br><span style="font-size:12px;color:#767676;">您可以到我的麦券栏目里查看&nbsp;&nbsp;相关优惠券信息</span>',
        					  btn: ['去看看', '取消'],
        					  yes:function(){
        							window.location.href="/member-debit.html";
        						},
        					 
        					});
                    			}	
                    		},
                    })
  })


cotteFn.customer004()
//cotteFn.index()
//分享
window._bd_share_config = {
	common : {
		bdText : document.title,	
		bdDesc : '',	
		bdUrl : window.location.href, 	
		bdPic : ''
	},
	share : [{
		"bdSize" : 16
	}]
}
with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];
</script>
<?php echo $this->fetch('footer.html'); ?>