// twoMenu();
// anav();
// 滚动图
(function(){
	var elem = document.getElementById('swipe1');
	var mySwipe = Swipe(elem, {
		callback: function(index, element) {
			$('.box1 .swipe-btn>span').eq(index).siblings('span').removeClass('cur').end().addClass('cur');     
		}
	});
	$('.slideBox .prev').click(function(){
		mySwipe.prev()	
	});
	$('.slideBox .next').click(function(){
		mySwipe.next()	
	});
})();

/*--报价/S--*/
$('.baojia,.priceList h2 .btn').click(function(){
	var date=new Date();
	var html='<div class="bjLayer"><div class="myForm"><div id="error"></div><div class="item"><div class="tit">报价</div><input type="text" class="txt" name="price"> 元</div><div class="item"><div class="tit">周期</div><input type="text" class="txt" name="date"> 天</div><div class="item"><div class="tit">备注</div><textarea maxLength="30" name="remark" onfocus="if($(this).val()==\'300字以内\'){$(this).val(\'\');style.color=\'#000\'}" onblur="if($(this).val()==\'\'){$(this).val(\'300字以内\');style.color=\'#666\'}">300字以内</textarea></div><a href="javascript:void(0)" class="btn" onclick="baojia()"><span>提交</span></a></div></div>';
	use('../../static/expand/layer/layer.min.js',function(){
		use('../../static/expand/my97date/wdatepicker.js',function(){
			$.layer({
				type: 1,
				title:'参与报价',
				shade: [0.3, '#000'],
				area: ['600px','400px'],
				moveType: 1,
				page: {html:html}
			})	
		})
	});
	return false;
})
function baojia(){
	var error=function(e,t){
		$('#error').html('<i class="ico fl"></i><span class="fl">'+t+'</span>');
		e.addClass('error-css').focus(function(){
			$(this).removeClass('error-css');
			$('#error').html('');	
		});
	}
	
	var _jq   = $('input[name=date]').val();
	
	if(!/^\d+$/.test($('input[name=price]').val())){
        error($('input[name=price]'),'报价必须为正整数数字');
        return
    }
	
	if($('input[name=date]').val()==""){
        error($('input[name=date]'),'定制周期不能为空');
        return
    }
	
	if(!/^\d+$/.test($('input[name=date]').val())){
        error($('input[name=date]'),'定制周期必须为正整数数字');
        return
    }
	if($('input[name=date]').val()<=0){
        error($('input[name=date]'),'定制周期必须为正整数数字');
        return
    }
	
	

    var _bj   = $('input[name=price]').val();
    var _jq   = $('input[name=date]').val();
    var _bz   = $("textarea[name=remark]").val();
    var _md   = $("#md_id").val();
    
    if(_bz == '300字以内'){
        _bz ='';
    }
    $.ajax({
        url  : "demand-offer.html",
        data : '&bj='+_bj+'&jq='+_jq+'&bz='+_bz+'&md='+_md,
        type : 'post',
        success : function(res){
            var res = $.parseJSON(res);
            if(res.done==true){
                $('em[id=takeInNum]').html(res.retval.takein);
                $('#offerList').html(res.retval.content);
                $('a[class=baojia]').remove();
                layer.closeAll();
                layer.msg('已报价', 2, 1);
            }
        }
    })
	
}
/*--报价/E--*/

/*--中标/S--*/
$('.priceList table .btn').click(function(){
    var _Id   = $(this).data('id');
    var _cfId   = $(this).data('cfid');
    var _cfName = $(this).data('cfname');
	var html='<div class="confirm"><p>选择后'+_cfName+'将为您服务，是否确认？</p><a href="javascript:void(0)" class="ok" onclick="adopt(\''+_cfName+'\',\''+_Id+'\',\''+_cfId+'\')"><span>确认</span></a><a href="javascript:void(0)" class="no" onclick="layer.closeAll()"><span>取消</span></a></div>';
		$.layer({
			type: 1,
			title:'系统提示',
			shade: [0.3, '#000'],
			area: ['600px','335px'],
			moveType: 1,
			page: {html:html}
	});
	return false;
})
function adopt(t,i,ci){
    $.ajax({
        url  : "/demand-ajaxoffer.html",
        data : '&id='+i+'&cfid='+ci,
        type : 'post',
        success : function(res){
            var res = $.parseJSON(res);
            if(res.done==true){
                $('#offerList').html(res.retval.content);
                layer.closeAll();
                layer.msg('已采纳'+t+'的定制', 2, 1);
            }else{
                msg(res.msg);
            }
        }
    });
	
}
/*--中标/E--*/

/*--附件预览/S--*/
$('.annex a').click(function(){
	var src=this.href;
	use('../../static/expand/layer/layer.min.js',function(){
		use('../../static/expand/layer/extend/layer.ext.js',function(){
			layer.photos({
				 json: { 
					"status": 1,
					"start":0,
					"data":[{src:src}]
					}
			})
		})
	})
	return false
})
/*--附件预览/E--*/
