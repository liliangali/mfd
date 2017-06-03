<a class="left_jt icon" href="javascript:void(0);" rel=nofollow></a>
<a class="right_jt icon" href="javascript:void(0);" rel=nofollow></a>
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


<script>

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

    $(".acate li").on("click", function(){
        $(".dog_list").removeClass("on");
        $(this).addClass("on");
    });
</script>