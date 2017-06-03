<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<div class="user_box">
	<?php echo $this->fetch('member.menu.html'); ?>
    <div class="user_right user_rights fr">
		<div class="lntegral">
        	<p class="mlntegral fl">我的消息</p>
        </div>
        <?php if ($this->_var['user_messages']): ?>
        <ul class="wdxxul">
        
        <?php $_from = $this->_var['user_messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'message');if (count($_from)):
    foreach ($_from AS $this->_var['message']):
?>
        	<li>
        		<div class="fl" style="padding-left:15px;"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['message']['id']; ?>"/></div>
                <?php if (! $this->_var['message']['is_read']): ?>
                	<div class="mrcur">
                    <?php else: ?><div class="mrcur mrcurs"><?php endif; ?>
                    <p class="wdwzword fl"><?php echo $this->_var['message']['title']; ?></p>
                    <?php if (! $this->_var['message']['is_read']): ?><p class="mrxxyuand fl">圆点</p>
                    <?php else: ?><p class="xxyuand fl">圆点</p><?php endif; ?>
                    <div class="fr">
                    	<p class="wdxxtada fl"><?php echo local_date("Y年m月d日",$this->_var['message']['add_time']); ?></p>
                   		<p class="wdxxsj fr">山角</p>
                    </div>
                </div>
                <div class="xlchak hide"><?php echo $this->_var['message']['content']; ?></div>
            </li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
             
        </ul>
        <?php else: ?>
        <div class="empty">亲，没有你的消息哦！</div>
        <?php endif; ?>
        <?php if ($this->_var['user_messages']): ?>
        <p class="fl" style="margin:0 10px 0 10px;">
            <input type="checkbox" class="checkall" />&nbsp;&nbsp;全选&nbsp;&nbsp;&nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="删除" name="id" uri="/my_messages-ajax_drop.html" presubmit="confirm('您确定要删除它吗？');" />
            <?php echo $this->fetch('page.bottom.html'); ?>
        </p>
        <?php endif; ?>
    </div>
</div>
<?php echo $this->fetch('footer.html'); ?>
<script src="/public/global/jquery-1.8.3.min.js"></script> 
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/static/pc/js/usercenter.js"></script>
<script src="/public/global/luck/pc/luck.js"></script>
<script type="text/javascript">	
$(".wdxxul li").click(function(e){
	if(e.target.nodeName=='INPUT'){
		return	
	}
	var _self=$(this);
	if(_self.hasClass('show')){
		_self.removeClass('show');
		var id=_self.children().children().val();
		//console.log(_self.find('input').val());
		//console.log($(this).find("div:eq(1)"));
		if($(this).find("p:eq(1)").hasClass('mrxxyuand')){			
			$.get('/my_messages-ajax_change_read.html',{id:id},function(res){
				if(res.done){
// 					$(this).find("div:eq(1)").removeClass("mrcur");
					_self.find("p:eq(1)").removeClass("mrxxyuand");
					_self.find("div:eq(1)").addClass("mrcurs");
					_self.find("p:eq(1)").addClass("xxyuand");
				}
			},"json");
		}		
	}
	else{
		_self.addClass('show').siblings('li').removeClass('show');
	}
});
/* 全选 */
$('.checkall').click(function(){
    $('.checkitem').attr('checked', this.checked)
});

/* 批量操作按钮 */

    $('.batchButton').click(function(){
        /* 是否有选择 */
        if($('.checkitem:checked').length == 0){    //没有选择
            return false;
        }
        /* 运行presubmit */
        if($(this).attr('presubmit')){
            if(!eval($(this).attr('presubmit'))){
                return false;
            }
        }
        /* 获取选中的项 */
        var items = '';
        $('.checkitem:checked').each(function(){
            items += this.value + ',';
        });
        items = items.substr(0, (items.length - 1));
        /* 将选中的项通过POST方式提交给指定的URI */
        var uri = $(this).attr('uri');
        $.post(	uri,{ids:items},function(res){
        	if(res.done){
				$('.checkitem:checked').parents('li').hide(1000,deleteli);
        	}else{
        		alert(res.msg);
        	}
        },"json");
        //window.location = uri + '&' + $(this).attr('name') + '=' + items;
    });
function deleteli(){
	$('.checkitem:checked').parents('li').remove();
}
</script>
</body>
</html>
