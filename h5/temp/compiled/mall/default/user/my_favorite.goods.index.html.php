<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1, user-scalable=no, minimal-ui" >
<title>我的收藏</title>
<link rel="stylesheet" type="text/css" href="/static/css/mobile_style.css" media="screen">
<link type="text/css" href="public/static/wap/css/productlist.css" rel="stylesheet" />
<link type="text/css" href="public/static/wap/css/cpxq.css" rel="stylesheet" />
</head>
<body style="background:#fff;">
<div class="main">
  
    <header class="hdtop topBar">
        <div class="edit fl">
                <p class="p1"><a href="<?php echo $this->build_url(array('app'=>'member','act'=>'index')); ?>"><img src="public/static/wap/images/tw_03.png"></a></p>
                <p class="p2">我的收藏</p>
            </div>
    </header>
  
  <div id="tj_1" class="zplb">
   <div id="brick" class="brick">

   <?php if ($this->_var['collect_custom']): ?>
   <?php $_from = $this->_var['collect_custom']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'custom');if (count($_from)):
    foreach ($_from AS $this->_var['custom']):
?>
        <div class="item">
     	 <ul>
           <li>
              <p class="p1"><a href="<?php echo $this->build_url(array('app'=>'product','act'=>'content')); ?>?id=<?php echo $this->_var['custom']['ctm']['goods_id']; ?>"><img src="<?php echo $this->_var['custom']['ctm']['thumbnail_pic']; ?>"></a></p>
              <p class="p2"><a href="<?php echo $this->build_url(array('app'=>'product','act'=>'content')); ?>?id=<?php echo $this->_var['custom']['ctm']['goods_id']; ?>"><?php echo $this->_var['custom']['ctm']['name']; ?></a></p>
              <p class="p2">¥<?php echo $this->_var['custom']['ctm']['price']; ?></p>
              <p class="p3"><a href="javascript:;" onclick="dropCustom(<?php echo $this->_var['custom']['id']; ?>,'确认删除？')">删 除</a></p>
           </li>
          </ul>
        </div>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php endif; ?>

    </div>
  </div>
  </div>
<script type="text/javascript" src="/diy/js/jquery-1.8.3.min.js"></script>


<script src="public/global/luck/mobile/luck.js"></script>


<script>
(function(){
  var $container = $('#brick'),num=20,mnum=20;
  var html=''

  //初始化
  function masonry(){
    $container.imagesLoaded( function(){
      $container.masonry({
      columnWidth:0,
      itemSelector: '.item'
      });
    });
  }
  masonry()

  //响应式
  var wnum = parseInt($('.main').width()/160);
  $container.css('width',wnum*160)
  $(window).resize(function() {
    var numbers = parseInt($('.main').width()/160);
    $container.css('width',numbers*160);
  });

  //获取数据
  function getData(){
    var winh=$(window).height(),
      bodyh=$container.height();
    if(($(window).scrollTop()+winh)>=bodyh){
      $(window).off('scroll resize',getData);
      $.get("my_favorite-ajax_list.html?type=goods",{limit:num+','+(num+mnum)},function(res){
                var res = eval("("+res+")");
                if(!res.done){
                  luck.open({content:res.msg,time:1500})
                  return
                }else{
                  $.each(res.retval,function(index,value){
                      html+='<div class="item">';
                      html+='<ul><li>';
                      html+='<p class="p1"><a href="/goods-'+value.ctm.id+'.html"><img src="'+value.ctm.image+'"></a></p>';
                      html+='<p class="p2"><a href="/goods-'+value.ctm.id+'.html">'+value.ctm.suit_name+'</a></p>';
                      html+='<p class="p2">¥'+value.ctm.cost_price+'</p>';
                      html+='<p class="p3" style="margin-left:26px;margin-top:10px;width:50px;border:1px solid #dfdfdf;padding-left:24px;"><a href="javascript:;" onclick="dropCustom('+value.id+',确认删除？)">删 除</a></p>';
                      html+='</li></ul></div>';
                 });
                  var $newElems=$(html);
                 $container.append($newElems).masonry("appended",$newElems);
                 num+=mnum;
                 $(window).on('scroll resize',getData);
                }
        });

    }
  }
  $(window).on('scroll resize',getData);
})()

function dropCustom(id){
  luck.open({
  	  content: '确定删除吗？',//内容
  		btn:['确认','取消'],
  		yes:function(){
        $.post("/my_favorite-dropCollect.html",{item_id:id}, function(res){
            var res = $.parseJSON(res);
            if(res.done == true){
              //  alert(res.msg);
                luck.open({content:"删除成功",time:1500});
                setTimeout(function(){
                  location.href='/my_favorite.html';
                },1500)
                return;
            }else{
              luck.open({content:res.msg,time:1500})

                return;
            }
        });
  		},
  		no: function(){
  			luck.close()
  		}
  	});

}

</script>
</body>
</html>