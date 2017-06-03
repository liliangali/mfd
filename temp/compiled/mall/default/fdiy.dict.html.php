
<?php echo $this->fetch('header-new.html'); ?>

  <link href="/diy/css/diy.css" type="text/css" rel="stylesheet" />
  <script src="/diy/js/jquery-1.8.3.min.js"></script>
  <script src="/diy/js/layer/layer.js"></script>
  <script src="/diy/js/diy.js"></script>
  <script src="/diy/js/Xslider.js"></script>
  <script src="/public/static/pc/js/public.js"></script>
  
  <style>
  .head {border:none;}
  </style>
<?php echo $this->fetch('fdiy/fidy.header.html'); ?>

<div class="cate_box">

    <div  class="ss_box">
        <span class="icon"></span>
        <input type="text" name="search" value="<?php echo $this->_var['s']; ?>" class="ss_ipt" placeholder="搜索狗狗" max="30">
    </div>
    
    <div class="dog_cat">
        <span><a href="###" <?php if ($this->_var['pid'] == 0): ?> class="on"<?php endif; ?> data-id=0>全部</a></span>
        <?php $_from = $this->_var['plist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');$this->_foreach['last'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['last']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
        $this->_foreach['last']['iteration']++;
?>
        <span <?php if (($this->_foreach['last']['iteration'] == $this->_foreach['last']['total'])): ?>style="border:0;"<?php endif; ?>><a href="###" <?php if ($this->_var['item']['cate_id'] == $this->_var['pid']): ?> class="on" <?php endif; ?> data-id=<?php echo $this->_var['item']['cate_id']; ?>><?php echo $this->_var['item']['cate_name']; ?></a></span>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </div>

</div>


<!--图片滚动开始-->
<div id="shop_list">
    <a class="left_jt" href="javascript:void(0);" rel=nofollow></a>
    <a class="right_jt" href="javascript:void(0);" rel=nofollow></a>
    <div class="container" rel="1">
        <div class="inner clearfix">
            <?php $_from = $this->_var['flists']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
            <ul class="acate">
                <?php $_from = $this->_var['item']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item1');if (count($_from)):
    foreach ($_from AS $this->_var['item1']):
?>
                <li class="dog_list acate<?php echo $this->_var['item1']['parent_id']; ?>" data-id="<?php echo $this->_var['item1']['cate_id']; ?>">
                    <div><a href="<?php echo $this->build_url(array('app'=>'fdiy','act'=>'index2','arg0'=>$this->_var['item1']['cate_id'])); ?>"><img src="<?php echo $this->_var['item1']['small_img']; ?>" /></a></div>
                    <span><a href="<?php echo $this->build_url(array('app'=>'fdiy','act'=>'index2','arg0'=>$this->_var['item1']['cate_id'])); ?>"><?php echo $this->_var['item1']['cate_name']; ?></a></span>
                    <i></i>
                </li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>


            <p class="clear"></p>
        </div>
    </div>
</div>
<!--图片滚动结束-->

<?php echo $this->fetch('footer-new.html'); ?>


<!--列表滚动-->
<SCRIPT type=text/javascript>
    var shop_list;
    var brand_rec_cur = 0;
    $(function(){
        shop_list = $("#shop_list");
        shop_list_container = shop_list.find(".container");
        shop_list_count = shop_list_container.find("ul").length;

        shop_list.find("a.left_jt").click(function(){
            var cur = parseInt(shop_list_container.attr("rel"));
            if(cur - 1 > 0){
                cur -= 1;
                shop_list_container.attr("rel", cur);
                var v = "-" + (parseInt(shop_list_container.width())*(cur-1)) + "px";
                shop_list_container.find(".inner").animate({marginLeft: v}, 996);
            }
        });

        shop_list.find("a.right_jt").click(function(){
            var cur = parseInt(shop_list_container.attr("rel"));
            if(shop_list_count - cur > 0){
                var v = "-" + (parseInt(shop_list_container.width())*cur) + "px";
                cur += 1;
                shop_list_container.attr("rel", cur);
                shop_list_container.find(".inner").animate({marginLeft: v}, 996);
            }
        });

    });
    
    $(".dog_cat span").click(function () {
        $(".dog_cat span").find("a").removeClass("on");
        $(this).find("a").addClass("on");

        searchdog();
    });
    
    function searchdog() {
        var p_id =  $(".dog_cat span a.on").attr("data-id");
        var s = $("input[name=search]").val();
        $.post("fdiy-indexs-1.html", {pid: p_id, s: s},
                function(data){
                    $("#shop_list").empty();
                    $("#shop_list").append(data);
                });
    }
    $("#search").on("click", function(){
       var search = $('input[name="search"]').val();
       var pid = $(".dog_cat a.on").data('id');
       var url = "fdiy-1.html?pid="+pid+"&s="+search;
       window.location.href = url;
    });
    $("input[name=search]").keyup(function () {
        searchdog();
    });

//    $(".dog_list").on("click", function(){
//        alert('aa')
//            $(".dog_list").removeClass("on");
//            $(this).addClass("on");
//    });

    $(".acate li").on("click", function(){
        $(".dog_list").removeClass("on");
        $(this).addClass("on");
    });

    $(".button").on("click", function()
    {
        var cate_id = $(".inner li.on").data('id');

        if (typeof(cate_id) == "undefined")
        {
//            layer.msg('hello');
//            layer.open({
//                title: '在线调试'
//                ,content: '可以填写任意的layer代码'
//            });
            alert("请选择狗狗")
        }
        else
        {
            var url = "fdiy-index2-"+cate_id+".html";
            window.location.href = url;
        }

		
    });

</SCRIPT>




</body>

</html>