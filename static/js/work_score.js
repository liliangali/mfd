$.fn.extend({
	sc: function(options) {
		return this.each(function() {
			new $.sc(options);
		});
	}
});

$.sc = function(opt){
	opt.count =opt.max = 0;
    var url_sign = '&id='+opt.id;

	$("textarea").focus(function(){
		if(hasLogin() == 0){
            login();
			return false;
		}
	})

	$(".submit").click(function(){
		var _c = $("textarea").val();

		if(_c == "你也可以随便说点什么"){
			 msg('请输入要评论的内容！');
			 return false;
		}

		if(_c.length < 5 || _c.length > 200){
			msg("评论的内容只能在5-200个字之间！",330,165);
			return false;
		}

		$.post(opt.postUrl, {content:_c,vote:$('#amount').html()}, function(res){
			var res = eval("("+res+")");

			if(res.done == false){
                msg(res.msg)
				return false;
			}else{
				if(res.retval == "login"){
					login();
				}else{
					$("textarea").val('');
                    msg("发表评论成功！");
                    var gourl=setTimeout(function(){
                        if(res.retval.e_sign ==2){
                            self.location.href ="/work_score-info.html?id="+opt.id+"&e_sign=2";
                        }else{
                            self.location.href ="/work_score-info.html?id="+opt.id+"&e_sign=1";
                        }
                    },200)
				}
			}
		})
	})
}


/*--分享/S--*/
$(document).on('click','.fxdsgt.fl>a',function(){
    var cla=this.className,url=window.location.href,page='',img=$('.pxly_left.fl>p img:eq(0)').attr('src');
    if (cla == "a_1") {
        var html = '<div style="text-align:center"><img src="http://qr.liantu.com/api.php?text=' + url + '" width="260" height="260"></div><p style="text-align:center;padding:10px 20px;line-height:17px">使用微信“扫一扫”即可将网页分享到我的朋友圈</p>';

//            use('/static/expand/layer/layer.min.js',function(){
        $.layer({
            type: 1,
            title:'分享到微信朋友圈',
            shade: [0.3, '#000'],
            area: ['310px','340px'],
            moveType: 1,
            page: {html:html}
        })
//            })
        return
    }
    if(cla=='qqwb'){cla='qblog'}
    switch(cla){
        case 'a_4'://腾讯微博
            page = "http://share.v.t.qq.com/index.php?c=share&a=index&url=" + url + "&title=" + title + " @阿里裁缝&appkey=801cf76d3cfc44ada52ec13114e84a96&pic=" + img;
            break;
        case 'a_3'://QQ空间
            page = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?title=" + title + " @阿里裁缝&url=" + url + "&summary=来自阿里裁缝&pics=" + img;
            break;
        case 'a_2'://新浪微博
            page = "http://v.t.sina.com.cn/share/share.php?appkey=691463583&title=" + title + " @阿里裁缝&url=" + url + "&pic=" + img;
            break;
    }
    window.open(page, "mfd官网分享", "height=600, width=700");

})
/*--分享/E--*/

var expert,e_sign;
function _page(page){
    offerPage(page);
}


function offerPage(_pg){
    if($('.now_hover').attr('id')=="ti_1" ){
        expert = 1; //z
        e_sign = '&e_sign=1';
    }else{
        expert = 2; //n
        e_sign = '&e_sign=2';
    }

    $.get(_pg+"?id="+_id+e_sign, function(res){
        var res = eval("("+res+")");
        if(res.done==true){
            //$('.cc_page').remove();
            $("#tj_"+expert+" .mdpjlb").html(res.retval);
        }
    })
}


//下一页
function wnext_enter(e_nid){
    if(e_nid == ''){
        msg('已经是最后一个作品')
        return false;
    }
    window.location ='/work_score-info.html?id='+e_nid;
}


if(window.location.href.split('=')[2]==2){
    ceck_pic(2)
}else{
    ceck_pic(1)
}


function ceck_pic(ix)
{
    if(ix==1){
        $('.zjsjspx.clearfix li').eq(0).attr('class','now_hover')
        $('.zjsjspx.clearfix li').eq(1).attr('class','old_hover')
        $('#tj_2').hide()
        $('#tj_1').show()

    }else{
        $('.zjsjspx.clearfix li').eq(1).attr('class','now_hover')
        $('.zjsjspx.clearfix li').eq(0).attr('class','old_hover')
        $('#tj_1').hide()
        $('#tj_2').show()
    }
}





//评分
$( "#slider-range-min" ).slider({
    range: "min",
    value: 5,
    step:.1,
    min: .1,
    max: 10,
    slide: function( event, ui ) {
        $( "#amount" ).text(ui.value );
    }
});
$( "#amount" ).text($( "#slider-range-min" ).slider( "value" ));
//字数限制
function textNum(options){
    var setttings = {
        defaultTxt:"请输入",
        ShowWordNumArae: "#wnum",
        WordNum: 100
    };
    if (options) {
        $.extend(setttings, options);
    };
    var content, length, Rnum;
    var $this=$(setttings.textArea)
    $this.val(setttings.defaultTxt)



    $this.focus(function(){
        if($(this).val()==setttings.defaultTxt){
            $(this).val('');
        }
    });
    $this.blur(function(){
        if($(this).val()==''){
            $(this).val(setttings.defaultTxt);
        }
    });


    $(setttings.ShowWordNumArae).html(setttings.WordNum);
    $this.keyup(function (e) {
        length = $this.val().length;
        Rnum = setttings.WordNum - length;
        if (e.keyCode != 8) {
            if (Rnum <= 0) {
                content = $this.val();
                content = content.substring(0, setttings.WordNum);
                $this.val(content);
                $(setttings.ShowWordNumArae).html(0);
            } else {
                content = $this.val();
                $this.val(content);
                $(setttings.ShowWordNumArae).html(Rnum);
            }
        } else {
            $(setttings.ShowWordNumArae).html(Rnum);
        }
    });
}
textNum({ShowWordNumArae:'#numtxt',textArea:'#textarea',WordNum:100})



