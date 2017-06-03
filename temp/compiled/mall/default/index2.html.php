

<?php echo $this->fetch('header-new.html'); ?>

<style>
    /*用户评分*/
    .grade_warp{width:840px;margin:10px auto 15px auto;}
    .User_grade{margin: 10px auto 10px auto;overflow:hidden;float:left;}
    .User_ratings .ratings_title{width:815px;height:78px;font-size:24px;color:#333;}
    .User_ratings .ratings_title01{width:62px;height:auto;font-size:14px;color:#333; float:left;}
    .User_ratings .ratings_title p{float:left;}
    .User_ratings .ratings_title p span{font-size:48px;}
    .User_ratings .ratings_title p i{color:#7dc234;font-style:normal;}
    .User_ratings .ratings_title01 p span{font-size:18px;}
    .User_ratings .ratings_title01 p i{color:#7dc234;font-style:normal;}
    .User_ratings .ratings_title01 input{width:120px;height:48px;border:0;margin:15px auto auto 45px;float:left;background:url(images/batton_01.png) -202px -2441px no-repeat;}
    .User_ratings .ratings_title01 input:hover{background:url(/diy/images/batton_01.png) -202px -2489px no-repeat;}
    .User_ratings .ratings_title01 input01{background:url(/diy/images/batton_01.png) -202px -2537px no-repeat;}
    .User_ratings .ratings_title input{width:120px;height:48px;border:0;margin:15px auto auto 45px;float:left;background:url(/diy/images/batton_01.png) -202px -2441px no-repeat;}
    .User_ratings .ratings_title input:hover{background:url(/diy/images/batton_01.png) -202px -2489px no-repeat;}
    .User_ratings .ratings_title input01{background:url(/diy/images/batton_01.png) -202px -2537px no-repeat;}
    .User_ratings .ratings_bars{width:407px;height:27px;float:left;}
    .User_ratings .ratings_bars #title0{height:25px; line-height:25px;font-size:18px;float:left;color:#e66800;background:#fff; display: block;}
    .User_ratings .ratings_bars font {font-size:14px; position: relative; top:0px; left:2px; height:25px; line-height:25px; float:left;}
    .User_ratings .ratings_bars .title0{width:25px;height:25px;text-align:center;line-height:25px;font-size:18px;float:left;color:#e66800;background:#fff;}
    .User_ratings .ratings_bars #title1{width:25px;height:25px;text-align:center;border:1px solid #bfbebe;line-height:25px;font-family:Georgia, "Times New Roman", Times, serif;font-size:14px;float:left;color:#a0a0a0;margin-right:10px;background:#fff;}
    .User_ratings .ratings_bars #title2{width:25px;height:25px;text-align:center;border:1px solid #bfbebe;line-height:25px;font-family:Georgia, "Times New Roman", Times, serif;font-size:14px;float:left;color:#a0a0a0;margin-right:10px;background:#fff;}
    .User_ratings .ratings_bars #title3{width:25px;height:25px;text-align:center;border:1px solid #bfbebe;line-height:25px;font-family:Georgia, "Times New Roman", Times, serif;font-size:14px;float:left;color:#a0a0a0;margin-right:10px;background:#fff;}
    .User_ratings .ratings_bars #title4{width:25px;height:25px;text-align:center;border:1px solid #bfbebe;line-height:25px;font-family:Georgia, "Times New Roman", Times, serif;font-size:14px;float:left;color:#a0a0a0;margin-right:10px;background:#fff;}
    .User_ratings .ratings_bars .bars_10{font-family:Georgia, "Times New Roman", Times, serif;font-size:18px;line-height:25px;float:left;color:#a0a0a0;}
    .User_ratings .ratings_bars .scale{width:220px;height:10px;float:left;margin:7px 10px auto 10px;position:relative;background:#eee; border-radius:15px;}
    .User_ratings .ratings_bars .scale div{width:0px;position:absolute;width:0;left:0;height:10px;bottom:0;background:#e66800; border-radius:15px 0 0 15px;}
    .User_ratings .ratings_bars .scale span{width:15px;height:15px;position:absolute;left:-2px;top:-4px;cursor:pointer;background:#fff; border:1px solid #e66800; border-radius:50%;}

    .mCSB_scrollTools .mCSB_draggerRail {background:none !important;}
    .mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar {background-color: rgba(91, 84, 77, 0.5) !important;}
    .mCSB_scrollTools .mCSB_dragger:hover .mCSB_dragger_bar {background-color: rgba(91, 84, 77, 0.5) !important;}
    .mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar, .mCSB_scrollTools .mCSB_dragger:active .mCSB_dragger_bar {background-color: rgba(91, 84, 77, 0.5) !important;}
</style>

<link href="/diy/css/diy.css" type="text/css" rel="stylesheet" />
<link href="/diy/css/animate.min.css" type="text/css" rel="stylesheet" />

<script src="/diy/js/jquery-1.8.3.min.js"></script>
<script src="/public/static/pc/js/public.js"></script>
<script src="/diy/js/jquery.time.js"></script>
<link rel="stylesheet" href="/diy/css/mCustomScrollbar.css">

<!-- Custom styles for this template -->
<link href="/diy/assets/css/main.css" rel="stylesheet">
<link href="/diy/assets/css/croppic.css" rel="stylesheet">

<script src="/diy/js/mCustomScrollbar.js"></script>
<script>
    (function($) {
        $(window).load(function() {
            $("#content-1").mCustomScrollbar({
                theme: "minimal"
            });
        });
    })(jQuery);
</script>





<style>
	#clipArea {width:520px; z-index:100; height: 240px; position:absolute; top:56px; left:0;}
	#clipBtn {display:none; width:72px; background:#e66800; height:32px; line-height:32px; border:0; color:#fff; position:absolute; top:250px; left:50%; margin-left:-30px; z-index:999; cursor:pointer;}
    .head {border:none;}
</style>

<?php echo $this->fetch('fdiy/fidy.header.html'); ?>

<div class="diy_box clearfix">
     <div class="cpxq_tab clearfix">
        <div class="cpxq_left clearfix">
            <ul class="tab_tit">
                <?php $_from = $this->_var['clist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['name']['iteration']++;
?>
                <li class="<?php if ($this->_foreach['name']['iteration'] == 1): ?>on<?php endif; ?> pxuan<?php echo $this->_foreach['name']['iteration']; ?>" data-pid="<?php echo $this->_foreach['name']['iteration']; ?>"><i class="bg<?php echo $this->_foreach['name']['iteration']; ?> axuan<?php echo $this->_var['item']['cate_id']; ?>"></i><?php echo $this->_var['item']['cate_name']; ?><b class="xjt"></b><p class="yjt"></p></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <li class="pxuan4" data-pid="4"><i class="bg4"></i>包装<b></b><p class="yjt"></p></li>
                <li class="pxuan5" data-pid="5"><i class="bg5"></i>标签<p class="yjt"></p></li>
            </ul>
        </div>
        <div class="cus_right">

            <!--生长阶段开始-->
            <?php $_from = $this->_var['clist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['name']['iteration']++;
?>
            <div class="show1 text_list clearfix"  <?php if ($this->_foreach['name']['iteration'] == 1): ?> style="display:block;" <?php endif; ?>>
              <h1><?php echo $this->_var['item']['cate_name']; ?></h1>
              <ul class="clearfix ul_list xuan<?php echo $this->_var['item']['cate_id']; ?>" s-type="<?php echo $this->_var['item']['is_box']; ?>"  data-name="<?php echo $this->_var['item']['cate_name']; ?>">
                  <?php $_from = $this->_var['item']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item2');$this->_foreach['name1'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name1']['total'] > 0):
    foreach ($_from AS $this->_var['item2']):
        $this->_foreach['name1']['iteration']++;
?>
                <li class="rule<?php echo $this->_var['item2']['cate_id']; ?>  <?php if ($this->_var['item2']['is_def'] == 1): ?>on<?php endif; ?> " data-fprice="<?php echo $this->_var['item2']['fprice']; ?>" data-uprice="<?php echo $this->_var['item2']['uprice']; ?>" data-content="<?php echo $this->_var['item2']['gongxiao_content']; ?>"  ve="<?php echo $this->_var['item2']['ve']; ?>" sid="<?php echo $this->_var['item2']['cate_id']; ?>" pid="<?php echo $this->_var['item']['cate_id']; ?>"><?php echo $this->_var['item2']['cate_name']; ?></li>
                  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

              </ul>
            <?php if ($this->_var['item']['cate_id'] == 1): ?>
            <p class="tis">
                <span>温馨提示:</span><i class="tishi"></i>
            </p>
            <?php endif; ?>

        </div>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <!--生长阶段结束-->

            <!--规格开始-->
            <div class="show1 clearfix" style="display:none;">
              <h1>包装规格</h1>

             <div class="text_list" style="display:block;">

                <h3>包装</h3>
                <ul class="clearfix ul_list xuan16 " s-type="0"  data-name="包装">
                    <?php $_from = $this->_var['baozhuang_list']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item2');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['item2']):
        $this->_foreach['name']['iteration']++;
?>
                  <li class="rule<?php echo $this->_var['item2']['cate_id']; ?> <?php if ($this->_var['item2']['is_def'] == 1): ?>on<?php endif; ?>" data-fprice="<?php echo $this->_var['item2']['fprice']; ?>" data-uprice="<?php echo $this->_var['item2']['uprice']; ?>" data-content="<?php echo $this->_var['item2']['gongxiao_content']; ?>"  ve="<?php echo $this->_var['item2']['ve']; ?>" sid="<?php echo $this->_var['item2']['cate_id']; ?>" pid="<?php echo $this->_var['baozhuang_list']['cate_id']; ?>"><?php echo $this->_var['item2']['cate_name']; ?></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

                </ul>


                <h3>规格</h3>
                <ul class="clearfix ul_list xuan11"  s-type="0" data-name="规格">
                    <?php $_from = $this->_var['guige_list']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item2');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['item2']):
        $this->_foreach['name']['iteration']++;
?>
                  <li class="rule<?php echo $this->_var['item2']['cate_id']; ?> <?php if ($this->_var['item2']['is_def'] == 1): ?>on<?php endif; ?>" data-fprice="<?php echo $this->_var['item2']['fprice']; ?>" data-uprice="<?php echo $this->_var['item2']['uprice']; ?>" data-content="<?php echo $this->_var['item2']['gongxiao_content']; ?>"  ve="<?php echo $this->_var['item2']['ve']; ?>" sid="<?php echo $this->_var['item2']['cate_id']; ?>" pid="<?php echo $this->_var['guige_list']['cate_id']; ?>"><?php echo $this->_var['item2']['cate_name']; ?></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

                </ul>

              </div>

              <h3 style="margin-top:10px;"><?php echo $this->_var['quanzhong_info']['cate_name']; ?>的喂食量建议</h3>
              <div class="hdswl">
                 <p class="swl_text">喂食量: <i class="fffeed_w">0</i>g/天，1天<i class="ffnums">0</i>次</p>
               </div>


              <dl class="dogs clearfix">
                <dt>体型：</dt>
                  <?php $_from = $this->_var['body_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
        $this->_foreach['name']['iteration']++;
?>
                <dd <?php if ($this->_foreach['name']['iteration'] == 3): ?>class="on"<?php endif; ?> data-id="<?php echo $this->_var['key']; ?>"><i class="dog<?php echo $this->_foreach['name']['iteration']; ?>"></i><?php echo $this->_var['item']; ?></dd>
                  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

              </dl>

              <dl class="swl_dl clearfix">
                <dt>阶段：</dt>
                  <!--<dd>1周</dd>-->
                  <!--<dd>2周 </dd>-->
                  <!--<dd>3周</dd>-->
                  <!--<dd>1个月</dd>-->
                  <!--<dd>3个月</dd>-->
              </dl>




                <dl class="swl_dlw clearfix">
                    <dt><i style="color: red;">*</i>体重：</dt>
                    <dd><input type="text" name="weight" maxlength="4" value="" class="tzfh" id="idweight" />(范围:<i class="w_min"></i>--<i class="w_max"></i> <i class="default_weight" style="display:none"></i>kg)</dd>
                </dl>

                <dl class="swl_we dog_nums clearfix" >
                    <dt>哺乳小狗数目（只）：</dt>
                    <dd style="padding-left:0;"><input type="number" name="dog_nums" value=" " class="prxgzs" /></dd>
                </dl>


                <div class="User_ratings User_grade" id="div_fraction0">
                    <div class="ratings_title01"><span>运动量：</span></div>
                    <div class="ratings_bars">
                        <!-- 不要改title0这个id 和slider-status这个class -->
                        <div class="scale" id="bar0">
                            <div style="width: 66px;"></div>
                            <span id="btn0" style="left: 66px;"></span>
                        </div>
                        <span id="title0" class="slider-status">1</span><font>小时</font>
                    </div>
                </div>

            </div>
            <!--规格结束-->


            <!--个性设计开始-->
            <div class="show1 text_list clearfix">

              <h1>个性设计</h1>

              <div class="tag_box">

                <div id="keydown">

                  <div class="tag_ipt">
                   <label><input type="text" name="dog_name" class="input_txt dog_ipt" placeholder="狗狗名称"  /></label>
                   <label><input placeholder="狗狗生日" type="text" name="dog_date"  id="hello" class="input_txt dog_date laydate-icon" /></label>
                  </div>

                  <div class="jiyu">
                   <label><textarea name="dog_desc" class="input_txt" placeholder="" maxlength="50"><?php echo $this->_var['word']; ?></textarea></label>
                  </div>
                </div>
                <p class="tag_text">亲，选择一张狗狗与你相伴的照片，亲手做一个独一无二的包装设计吧！</p>


                <div class="tag_img">
                  <!--<p class="file_box">-->

                   <!--&lt;!&ndash;<input name="" type="file" class="file">a&ndash;&gt;-->
                  <!--</p>-->


                    <div id="croppic"></div>
                    <span class="btn" id="cropContainerHeaderButton">上传标签图</span>


                </div>

              </div>

            </div>
            <!--个性设计结束-->



        </div>


    </div>


    <div class="dog_info">
      <img src="<?php echo $this->_var['quanzhong_info']['small_img']; ?>" class="dog_img">
      <h1><?php echo $this->_var['quanzhong_info']['cate_name']; ?></h1>
      <h2>主人寄语：<i class="zword dog_desc"><?php echo $this->_var['word']; ?></i></h2>

        <?php $_from = $this->_var['clist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['name']['iteration']++;
?>
      <p><?php echo $this->_var['item']['cate_name']; ?>: <i class="sxuan<?php echo $this->_var['item']['cate_id']; ?>"></i></p>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

      <p><?php echo $this->_var['baozhuang_list']['cate_name']; ?>: <i class="sxuan<?php echo $this->_var['baozhuang_list']['cate_id']; ?>"></i></p>
      <p><?php echo $this->_var['guige_list']['cate_name']; ?>: <i class="sxuan<?php echo $this->_var['guige_list']['cate_id']; ?>"></i></p>


      <p>喂食量: <i><i class="siweiliang">0</i>克/每天</i></p>
      <p>爱宠名称: <i class="dog_name">未设置</i></p>
      <p>生日: <i class="dog_dates">未设置</i></p>
      <p>标签图: <span class="bgimg">
          <img src="diy/images/no_img.png" class="file_img">
      </span></p>




    </div>




</div>


<!--底部浮层-->

<!--上一步、下一步按钮-->

<div class="diy_foot">

    <div class="fixed_foot clearfix">

        <button class="div_prev">上一步</button>
        <button class="div_next">下一步</button>

        <!--购买信息-->
        <div class="buy_info">
            <i class="jian"></i>
            <input type="text" class="bnum goods_num" value="1" disabled>
            <i class="jia"></i>
        </div>

        <span class="diy_price">￥<span id="price">4220</span></span>
        <button class="addCart">加入购物车</button>


    </div>

</div>

<!--定制模块结束-->



<script src="/diy/js/iscroll-zoom.js"></script>
<script src="/diy/js/hammer.js"></script>
<script src="/diy/js/lrz.all.bundle.js"></script>
<script src="/diy/js/jquery.photoClip.js"></script>
<script src="/diy/js/laydate.js"></script>
<script src="/diy/js/layer/layer.js"></script>
<script src="/diy/js/jquery-ui-1.8.4.custom.min.js"></script>
<script src="/diy/js/jQuery.peSlider.js"></script>
<script src="/diy/js/croppic/croppic.min.js"></script>

<script>
    var croppicHeaderOptions = {
        //uploadUrl:'img_save_to_file.php',
        cropData:{
            "dummyData":1,
            "dummyData2":"asdas"
        },
        cropUrl:'fdiy-upfile.html',
        customUploadButtonId:'cropContainerHeaderButton',
        modal:false,
        processInline:true,
        loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
        onBeforeImgUpload: function(){
            //alert(1)
            console.log('onBeforeImgUpload')
        },
        onAfterImgUpload: function(){
           // alert('aa')

        },
        onImgDrag: function(){console.log('onImgDrag')},
        onImgZoom: function(){console.log('onImgZoom')},
        onBeforeImgCrop: function(){console.log('onBeforeImgCrop')},
        onAfterImgCrop:function(){
            $('.file_img').attr('src',$('.croppedImg').attr('src'));
        },
        onReset:function(){
            console.log('onReset')
        },
        onError:function(errormessage){console.log('onError:'+errormessage)}
    }
    var croppic = new Croppic('croppic', croppicHeaderOptions);



</script>



<script type="text/javascript">
    scale = function (btn, bar, title) {
        this.btn = document.getElementById(btn);
        this.bar = document.getElementById(bar);
        this.title = document.getElementById(title);
        this.step = this.bar.getElementsByTagName("DIV")[0];
        this.init();
    };
    scale.prototype = {
        init: function () {
            var f = this, g = document, b = window, m = Math;
            f.btn.onmousedown = function (e) {
                var x = (e || b.event).clientX;
                var l = this.offsetLeft;
                var max = f.bar.offsetWidth - this.offsetWidth;

                g.onmousemove = function (e) {

                    var thisX = (e || b.event).clientX;
                    var to = m.min(max, m.max(-2, l + (thisX - x)));
                    f.btn.style.left = to + 'px';
                    f.ondrag(m.round(m.max(0, to / max) * 30), to);
                    b.getSelection ? b.getSelection().removeAllRanges() : g.selection.empty();

                };
                g.onmouseup = new Function('this.onmousemove=null');
            };
        },
        ondrag: function (pos, x) {
            this.step.style.width = Math.max(0, x) + 'px';
            this.title.innerHTML = pos / 10;
        }
    }
    new scale('btn0', 'bar0', 'title0');


</script>



<script type="text/javascript">
    $(function(){


        //创建输入滑杆
        $('input#price').peSlider({range: 'min'});
        $('input#bedrooms,input#baths').peSlider({range: 'min'});

        //创建选择滑块
        $('select').attr('aria-hidden','true').after('<div class="slider-status" aria-hidden="true"></div>').peSlider({

            slide:function(e,ui){

                $(this).next().next().text($(this).find('a:eq(0)').attr('aria-valuetext'));
            }
        }).each(function(){

            $(this).next().text($(this).prev().find('a:eq(0)').attr('aria-valuetext'));
        });

        //体况点击事件
//        $('.ui-slider').click(function(){
//           alert('aa')
//
//        });

        //添加选择的标签
       // $('<div class="sliders-labels" aria-hidden="true"><span class="label-first">'+ $('#subway option:first').text() +'</span><span class="label-last">'+ $('#subway option:last').text() +'</span></div>').insertAfter('#amenities legend');

    });


</script>

<script>
!function () {

//laydate.skin('molv');
    var d = new Date()
    var vYear = d.getFullYear();
    var vMon = d.getMonth() + 1;
    var vDay = d.getDate();
   var v = vYear + "-" + vMon + "-" + vDay;

    laydate({
        elem: '#hello', //目标元素。由于laydate.js封装了一个轻量级的选择器引擎，因此elem还允许你传入class、tag但必须按照这种方式 '#id .class'
        event: 'focus', //响应事件。如果没有传入event，则按照默认的click
        choose: function(datas){ //选择日期完毕的回调
            $(".dog_dates").text(datas)
        }
    });

//
//laydate({
//   elem: '#demo',
//    max: v,
//    choose: function(dates){ //选择好日期的回调
//        var name = $("input[name='dog_date']").val();
//        $('.dog_dates').text(name);
//    }
//})

}();
</script>


<!--弹层样式-->
<script>
$(function() {

	$(".tab_tit li").click(function() {
		$(this).eq($(this).index(this)).addClass("on").siblings().removeClass("on");
		var ss = $(".show1").eq($(".tab_tit li").index(this));
		ss.show().siblings().hide();

		$('.button_box').show();

        if($(this).index()==3){
            $("#idweight").focus();
        }
	})




})

try{
    $rule =<?php echo $this->_var['rule']; ?>;//冲突配置
    $mid='<?php echo $this->_var['mid']; ?>';
    $cid='<?php echo $this->_var['cid']; ?>';
    $sid='<?php echo $this->_var['sid']; ?>';
    $gxmaxnum='<?php echo $this->_var['maxnum']; ?>';
    $effectprice='<?php echo $this->_var['aprice']; ?>';
    $quanzhong = "<?php echo $this->_var['quanzhong']; ?>";
    $quanzhong_son = "<?php echo $this->_var['quanzhong_son']; ?>";
}catch(e){}

</script>
<script src="/diy/js/diy2.js"></script>
<script src="/public/global/luck/pc/luck.js"></script>
</body>

</html>
