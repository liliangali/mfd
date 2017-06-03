
$(function(){
    //goodsspec.init();

    //放大镜效果/
    if ($(".jqzoom img").attr('jqimg'))
    {
        $(".jqzoom").jqueryzoom({ xzoom: 410, yzoom: 410 });
    }

    // 图片替换效果
    $('.ware_box li').mouseover(function(){
        $('.ware_box li').removeClass();
        $(this).addClass('ware_pic_hover');
        $('.big_pic img').attr('src', $(this).children('img:first').attr('src'));
        $('.big_pic img').attr('jqimg', $(this).attr('bigimg'));
    });

    //点击后移动的距离
    var left_num = -85;

    //整个ul超出显示区域的尺寸
    var li_length = ($('.ware_box li').height() + 19) * $('.ware_box li').length - 434;

    $('.right_btn').click(function(){
        var posleft_num = $('.ware_box ul').position().top;
        if($('.ware_box ul').position().top > -li_length){
            $('.ware_box ul').css({'top': posleft_num + left_num});
        }
    });

    $('.left_btn').click(function(){
        var posleft_num = $('.ware_box ul').position().top;
        if($('.ware_box ul').position().top < 0){
            $('.ware_box ul').css({'top': posleft_num - left_num});
        }
    });

});