// JavaScript Document
/*$(function()
{
	var Otime = null;
    $(".select_js ul li").hover(
        function()
        {
            $(this).addClass('search_nonce');
        },
        function()
        {
            $(this).removeClass();
        }
    );

    $(".select_js").mousemove(function(){
		clearTimeout(Otime);
        $(".select_js ul").show();
		if($(".select_js p").text() == '商品'){
			
		  $(".select_js ul li").html('店铺');
		  $(".select_js ul li").attr('ectype','store');
		  
		}else{
		  $(".select_js ul li").html('商品');
		  $(".select_js ul li").attr('ectype','index');
		}
	}
	);
	$(".select_js").mouseout(function(){
		      Otime = setTimeout(function(){
				$(".select_js ul").hide();
				},300);
		});

    $(".select_js ul li").click(function(){
        var text = $(this).text();
        $(".select_js p").text(text);

        var act  = $(this).attr("ectype");
        $(".select_js input").val(act);
    });

    $('body').click(mouseLocation);
    
});

function block_fn()
{
	
}
function none_fn()
{
	
}


function mouseLocation(e)
{
    if (e.pageX < $('.select_js').position().left ||
        e.pageX > $('.select_js').position().left + $('.select_js').outerWidth() ||
        e.pageY < $('.select_js').position().top ||
        e.pageY > $('.select_js').position().top + $('.select_js').outerHeight())
    {
        $('.select_js ul').hide();
    }
}*/