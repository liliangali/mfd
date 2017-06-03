<!DOCTYPE html>
<html lang="zh_cn">

	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="pragma" content="no-cache">
		<title>个性化定制方案</title>

		<link href="/diy/css/stylenew.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="/diy/js/jquery-1.8.3.min.js"></script>
		<script src="https://cdn.ravenjs.com/3.15.0/raven.min.js" crossorigin="anonymous"></script>
		<script>Raven.config('https://9e1ad3acb3e84cedbedd9121dfb954a6@sentry.io/170500').install();</script>
		<script type="text/javascript" src="/diy/js/luck/luck.js"></script>
		<script src="/diy/js/time.js" type="text/javascript"></script>
		<script src="/diy/js/photoClip.js?22"></script>

	</head>

	<body style="background:url(/diy/images/dzbg_2.png) left top repeat;">
		<?php if (! $this->_var['is_weixin']): ?>
		
		<header class="topBar" id="header">
			<div class="wrapss">
				<span class="back" onClick="window.location.href='fdiy-1-3.html' "></span>
				<h1><?php echo $this->_var['title']; ?></h1>
			</div>
		</header>
		<?php endif; ?>

		<section class="main">
			<section class="dzfa_1">
				<p class="dzjs_1 animated fadeIn"><img src="<?php echo $this->_var['quanzhong_info']['small_img']; ?>"></p>
				<section class="xlrq">
					<h1><?php echo $this->_var['quanzhong_info']['cate_name']; ?></h1>
					<p>
						犬期：<i class="sxuan34">待选择</i><br/> 功效：
						<i class="sxuan1">待选择</i><br/> 口味：
						<i class="sxuan6">待选择</i><br/> 规格：
						<i class="sxuan11">待选择</i><br/> 包装：
						<i class="sxuan16">待选择</i>
					</p>

					<div class="tag_img"><img src="/diy/images/dzjs_2.png" class="zrtx">未传标签</div>

				</section>
			</section>

			<section class="dzfa_2">
				<?php $_from = $this->_var['clist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['test'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['test']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['test']['iteration']++;
?>
				<div class="xzfadyb  step<?php echo $this->_foreach['test']['iteration']; ?> addCart  " data-id="<?php echo $this->_foreach['test']['iteration']; ?>">
					<p class="p1 diy_icon bg<?php echo $this->_foreach['test']['iteration']; ?>"></p>
					<p class="p2"><?php echo $this->_var['item']['cate_name']; ?></p>
					<p class="p3 diy_icon"><?php echo $this->_foreach['test']['iteration']; ?></p>
				</div>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				<div class="xzfadyb step4 addCart " data-id="4">
					<p class="p1 diy_icon bg4"></p>
					<p class="p2">包装规格</p>
					<p class="p3 diy_icon">4</p>
				</div>
				<div class="xzfadyb step5 addCart " data-id="5">
					<p class="p1 diy_icon bg5"></p>
					<p class="p2">个性设计</p>
					<p class="p3 diy_icon">5</p>
				</div>
				<div class="qsdzmc"><i class="diy_icon"></i>亲手为您的爱宠做一顿美餐吧</div>
			</section>

			<div class="blank_ft"></div>

			<section class="dzj_qrgm dzj_qrgm1">
				<div class="foot zsykj_box foot1">
					<div class="dzjg c_db">
						<!--<a href="###" class="reduce"><img src="static/images/add1.png"/></a>-->
						<p class="c_bf1"><input type="text" value="1" disabled class="goods_num"></p>
						<!--<a href="###" class="plus"><img src="static/images/add2.png"/></a>-->
					</div>
					<div class="dzjg">
						<span>
    		<i class="cdiy_price">计算中...</i>
    		<i>元</i>
    	</span>
					</div>
					<!--<p class="zsykj_1 qrgm addCart1"><i id="jiesuan">去结算</i> </p>-->
					<p class="zsykj_1 qrgm addCart1">
						<i class="js_shop addCart2">加入购物车</i>
						<i id="" class="clearfix addCart3">立即购买</i>
					</p>
				</div>
			</section>

		</section>
		<input type="hidden" id="base_price" name="base_price" value="0">
		
		<?php $_from = $this->_var['clist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['test'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['test']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['test']['iteration']++;
?>
		<section class="mfdtc tjltdz" id="customSize<?php echo $this->_foreach['test']['iteration']; ?>">
			<section class="jrgwc_xx">

				<div style="padding:10px 15px;" class="swiper-slide_mfd xuan<?php echo $this->_var['item']['cate_id']; ?>" s-type="<?php echo $this->_var['item']['is_box']; ?>" data-id="<?php echo $this->_var['item']['cate_id']; ?>" data-name="<?php echo $this->_var['item']['cate_name']; ?>">
					<h1 class="tcbt" data-id="<?php echo $this->_foreach['test']['iteration']; ?>" style="margin:5px 15px 0 15px;"><?php echo $this->_var['item']['cate_name']; ?><a href="#" class="diy_icon"></a></h1>
					<div class="bzxs">
						<ul>
							<?php $_from = $this->_var['item']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item2');$this->_foreach['t2'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['t2']['total'] > 0):
    foreach ($_from AS $this->_var['item2']):
        $this->_foreach['t2']['iteration']++;
?>
							<li class="rule<?php echo $this->_var['item2']['cate_id']; ?> <?php if ($this->_var['item2']['is_def'] == 1): ?>on<?php endif; ?>" data-fprice="<?php echo $this->_var['item2']['fprice']; ?>" data-uprice="<?php echo $this->_var['item2']['uprice']; ?>" data-content="<?php echo $this->_var['item2']['gongxiao_content']; ?>" ve="<?php echo $this->_var['item2']['ve']; ?>" sid="<?php echo $this->_var['item2']['cate_id']; ?>" pid="<?php echo $this->_var['item']['cate_id']; ?>"><span><?php echo $this->_var['item2']['cate_name']; ?><img src="/diy/images/dog.png"></span></li>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</ul>

						<?php if ($this->_var['item']['cate_id'] == 1): ?>
						<div class="tijs">
							<i>温馨提示: </i>
							<p style="padding-bottom:10px;" class="gongxiao_content"></p>
							<p class="tishi"></p>
						</div>
						<?php endif; ?>

					</div>
					<input type="submit" value="确认" class="qrtj tcbt" data-id="<?php echo $this->_foreach['test']['iteration']; ?>">
				</div>
			</section>
		</section>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

		
		<section class="mfdtc tjltdz" id="customSize4">
			<section class="jrgwc_xx">

				<div style="padding:10px 15px 0 15px;" class="swiper-slide_mfd xuan<?php echo $this->_var['baozhuang_list']['cate_id']; ?>" s-type="<?php echo $this->_var['baozhuang_list']['is_box']; ?>" data-id="<?php echo $this->_var['baozhuang_list']['cate_id']; ?>" data-name="包装规格">

					<h1 class="tcbt" data-id="4" style="margin:5px 15px 0 15px;">包装规格<a href="#" class="diy_icon"></a></h1>

					<!-- <h1 class="tcbt" data-id="4" style="margin:15px 15px 0 15px;"><?php echo $this->_var['baozhuang_list']['cate_name']; ?><a href="#"></a></h1> -->
					<div class="bzxs">
						<ul style="padding-bottom:0">
							<?php $_from = $this->_var['baozhuang_list']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item2');$this->_foreach['t2'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['t2']['total'] > 0):
    foreach ($_from AS $this->_var['item2']):
        $this->_foreach['t2']['iteration']++;
?>
							<li class="rule<?php echo $this->_var['item2']['cate_id']; ?> <?php if ($this->_var['item2']['is_def'] == 1): ?>on<?php endif; ?>" data-fprice="<?php echo $this->_var['item2']['fprice']; ?>" data-uprice="<?php echo $this->_var['item2']['uprice']; ?>" ve="<?php echo $this->_var['item2']['ve']; ?>" sid="<?php echo $this->_var['item2']['cate_id']; ?>" pid="<?php echo $this->_var['baozhuang_list']['cate_id']; ?>"><span><?php echo $this->_var['item2']['cate_name']; ?><img src="/diy/images/dog.png"></span></li>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</ul>
					</div>
				</div>

				<div style="padding:0px 15px 10px 15px;" class="swiper-slide_mfd xuan<?php echo $this->_var['guige_list']['cate_id']; ?>" s-type="<?php echo $this->_var['guige_list']['is_box']; ?>" data-id="<?php echo $this->_var['guige_list']['cate_id']; ?>" data-name="包装规格">
					<!--   <h1 class="tcbt" data-id="4" style="margin:15px 15px 0 15px;"><?php echo $this->_var['guige_list']['cate_name']; ?></h1> -->
					<div class="bzxs">
						<ul style="padding:0 0 15px 0;">
							<?php $_from = $this->_var['guige_list']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item2');$this->_foreach['t2'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['t2']['total'] > 0):
    foreach ($_from AS $this->_var['item2']):
        $this->_foreach['t2']['iteration']++;
?>
							<li class="rule<?php echo $this->_var['item2']['cate_id']; ?> <?php if ($this->_var['item2']['is_def'] == 1): ?>on<?php endif; ?>" data-fprice="<?php echo $this->_var['item2']['fprice']; ?>" data-uprice="<?php echo $this->_var['item2']['uprice']; ?>" ve="<?php echo $this->_var['item2']['ve']; ?>" sid="<?php echo $this->_var['item2']['cate_id']; ?>" pid="<?php echo $this->_var['guige_list']['cate_id']; ?>"><span><?php echo $this->_var['item2']['cate_name']; ?><img src="/diy/images/dog.png"></span></li>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</ul>
					</div>

					<p class="swlxs swl_text">喂食量: <i class="fffeed_w">0</i>g/天，1天<i class="ffnums">0</i>次</p>

					<dl class="dogs_dl jj_box dogs">
						<h1 class="txxzbt">体型选择</h1> <?php $_from = $this->_var['body_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
        $this->_foreach['name']['iteration']++;
?>
						<dd class="jj_1 <?php if ($this->_foreach['name']['iteration'] == 3): ?>on<?php endif; ?>" data-id="<?php echo $this->_var['key']; ?>">
							<p class="dog<?php echo $this->_foreach['name']['iteration']; ?>"></p><?php echo $this->_var['item']; ?></dd>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</dl>

					<div class="jieduandiv" style=" display: none; position:relative;">
						<p class="jj_xzjd"> 阶段选择</p>
						<div class="jdxz1 swl_dl"></div>
					</div>
					<div class="jj_box jj_tz">
						<p class="jj_1"><input type="number" name="weight" value="" placeholder="请输入体重数字，单位kg " class="srtz" /></p>
						<p class="tzsxx"><i class="w_min">0</i>-<i class="w_max">0</i>kg</p>
					</div>

					<div class="dog_nums">
						<select class="" name="dog_nums">
							<option value="0">请选择小狗数目</option>
							<?php $_from = $this->_var['dog_num_arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
							<option value="<?php echo $this->_var['item']; ?>"><?php echo $this->_var['item']; ?></option>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</select>
					</div>

					<h1 class="ydlxz">运动量<span>（<i style="padding-right:15px; font-style: normal;">单位:小时</i>请拖动按钮选择）</span></h1>

					<div class="wrap">
						<input id="example-4" name="example-4" placeholder="0 - 3" min="0" max="3" step="0.1" value="1" data-decimals="1" data-color="#B59824" class="srs">
					</div>

					<!--<p class="hdswl subfeed">确认获得饲喂量</p>-->

					<input type="submit" value="确认" class="qrtj tcbt" data-id="4">

				</div>

			</section>
		</section>

		
		<section class="mfdtc tjltdz" id="customSize5">

			<section class="jrgwc_xx">

				<div class="qianm">

					<h1 class="tcbt" data-id="5" style="margin:15px 15px 10px 15px;">个性化标签设计<a href="#" class="diy_icon"></a></h1>

					<input type="text" name="dog_name" class="qm_ipt" value="" placeholder="爱宠名称" style="margin-top:15px;" />
					<input readonly id="appDate" name="dog_date" class="qm_ipt" value="" placeholder="爱宠生日" style="-webkit-appearance:none;" />
					<textarea name="dog_desc" placeholder="" class="qm_ipt" style="height:72px; line-height:18px; resize: none; padding:6px 10px;" value="<?php echo $this->_var['word']; ?>" maxlength="25"><?php echo $this->_var['word']; ?></textarea>

					<div id="clipArea" style="display:none"><button id="clipBtn">截取</button></div>

					<div class="jieqtu">
						<div class="jqdiv">
							<input type="file" id="file">
							<img src="/diy/images/sctu.png" class="sctu">
						</div>
						<span>亲，选择一张狗狗与你相伴的照片，做一个独一无二的包装设计吧！</span>

					</div>

					<div class="jq_img"><img src="" class="file_img"></div>

					<input type="submit" value="确认" class="qrtj tcbt" data-id="5">

				</div>

			</section>
		</section>

		

		<style>
			#clipArea {
				margin: 15px 0;
				height: 240px;
				position: relative;
				border-radius: 5px;
				z-index: 100;
			}
			
			#clipArea button {
				position: absolute;
				bottom: 10px;
				border-radius: 4px;
				left: 50%;
				z-index: 9999;
				background: #fff;
				width: 80px;
				height: 30px;
				line-height: 30px;
				margin-left: -40px;
				font-size: 14px;
			}
			
			#view {
				margin: 0 auto;
			}
			
			.jieqtu {
				width: 100%;
				overflow: hidden;
				margin: 10px 0 20px 0;
				height: 57px;
				line-height: 18px;
				color: #888;
			}
			
			.jieqtu span {
				padding: 5px 0 0 140px;
				font-size: 12px;
				display: block;
			}
			
			.jieqtu .jqdiv {
				width: 124px;
				height: 46px;
				position: absolute;
			}
			
			.jieqtu .jqdiv img {
				width: 100%;
				height: 100%;
			}
			
			.jieqtu .jqdiv input {
				width: 100%;
				height: 100%;
				opacity: 0;
				position: absolute;
				left: 0;
				top: 0;
			}
			
			.jieqtu button {
				width: 80px;
				height: 34px;
				background: #e66800;
				float: left;
				border-radius: 5px;
				color: #fff;
				font-size: 14px;
				margin: 34px 0 0 30px;
			}
			
			.qianm {
				padding: 10px 15px;
			}
			
			.qm_ipt {
				padding: 0 10px;
				border: 1px solid #ddd;
				border-radius: 5px !important;
				height: 38px;
				line-height: 38px;
				background: #fff;
				font-size: 14px;
				box-sizing: border-box;
				width: 100%;
				margin-bottom: 12px;
			}
			
			.jq_img {
				width: 160px;
				margin-bottom: 6px;
			}
			
			.jq_img img {
				width: 100%;
				border-radius: 5px;
			}
			
			.tijs {
				color: #717171;
				line-height: 16px;
				font-size: 12px;
				padding-bottom: 8px;
			}
			
			.tijs i {
				font-style: normal;
				color: #ff7700;
				display: block;
				padding-bottom: 5px;
			}
		</style>
		<script type="text/javascript" src="/diy/js/huadong.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				var currYear = (new Date()).getFullYear();
				var opt = {};
				opt.date = {preset: 'date'};
				opt.datetime = {preset: 'datetime'};
				opt.time = {preset: 'time'};
				opt.default = {
					theme: 'android-ics light', //皮肤样式
					display: 'modal', //显示方式
					mode: 'scroller', //日期选择模式
					dateFormat: 'yyyy-mm-dd',
					lang: 'zh',
					showNow: true,
					nowText: "今天",
					startYear: currYear - 40, //开始年份
					endYear: currYear + 0 //结束年份
				};

				$("#appDate").mobiscroll($.extend(opt['date'], opt['default']));
				var optDateTime = $.extend(opt['datetime'], opt['default']);
				var optTime = $.extend(opt['time'], opt['default']);
				$("#appDateTime").mobiscroll(optDateTime).datetime(optDateTime);
				$("#appTime").mobiscroll(optTime).time(optTime);

				$(".srs-thumb").css("left", 86);

				//    $('.single-slider').jRange({
				//        from: 10,
				//        to: 60,
				//        step: 10,
				//        scale: [10,20,30,60],
				//        format: '%s days',
				//        width: 300,
				//        showLabels: true,
				//        showScale: true
				//    });

			});
		</script>

		<script type="text/javascript">
			$(window).load(function() {
				clipArea = new bjj.PhotoClip("#clipArea", {
					size: [130, 130],
					outputSize: [640, 640],
					file: "#file",
					view: "#view",
					ok: "#clipBtn",
					loadStart: function() {
						$("#clipArea").css("display",'');
						console.log("照片读取中");
						$('.load_div').show();
						$('.jieqtu').hide();
					},
					loadComplete: function() {
						console.log("照片读取完成");
						$('.load_div').hide();
						$('.jieqtu').addClass('is_img');

					},
					clipFinish: function(dataURL) {
						$("#clipArea").css("display",'none');
						$('.jieqtu').show();
						$('.load_div').hide();
						$('.file_img').attr('src',dataURL);
						$(".zrtx").attr('src',dataURL);
						$(".sctu").attr("src","/diy/images/xsctu.png");
						$('.jieqtu').addClass('has_img');
						//判断类型是不是图片
					}
				});





			});


			//选择当前属性后添加下一个属性动画提示

			$(document).ready(function() {

				attrClik(0);
			});

			$(document).ready(function() {

				$('.step1').addClass("animated pulse cur")

				$('.tcbt').click(function() {
					var id = parseInt($(this).data("id"));
					if(id == 5){
						//判断剪裁图片是否点击
						if ($('.jieqtu').hasClass('is_img') && !($('.jieqtu').hasClass('has_img')))
						{
							alert('亲，请点击截取按钮，才可以完成标签图片的设计哦！')
							return true;
						}
					}

					$('#customSize' + id).fadeOut(500);
					var oDivs = $(".dzfa_2>div[data-id]");
					if(!oDivs.eq(id).attr("select")) {
						oDivs.eq(id).attr("select", "select");
						//alert(id)
						oDivs.eq(id - 1).addClass("on");
						oDivs.eq(id - 1).removeClass("cur");
						if(id == 5) {
							oDivs.eq(id - 1).removeClass("animated pulse cur");
						} else {
							oDivs.eq(id).addClass("animated pulse cur").siblings().removeClass("animated pulse");
						}
					}
					attrClik(id);
				});



			});

			//滑动对象
			try{
				$rule = <?php echo $this->_var['rule']; ?>; //冲突配置
				$mid = '<?php echo $this->_var['mid']; ?>';
				$cid = '<?php echo $this->_var['cid']; ?>';
				$sid = '<?php echo $this->_var['sid']; ?>';
				$gxmaxnum = '<?php echo $this->_var['maxnum']; ?>';
				$effectprice = '<?php echo $this->_var['aprice']; ?>';
				$quanzhong = "<?php echo $this->_var['quanzhong']; ?>";
				$quanzhong_son = "<?php echo $this->_var['quanzhong_son']; ?>";
				$is_try = "<?php echo $this->_var['is_try']; ?>";
			}catch(e){};



			//按id绑定属性点击事件；
			function attrClik(id) {
				var id = id;
				var obj = $('.addCart');
				obj.eq(id).click(function() {
					var id = $(this).data("id");
					$('#customSize' + id).fadeIn(500);
				});
				return false;
			}

			/*$('.addCart').click(function(){
				//alert($(this).data("id"))
				var id = $(this).data("id");
				$('#customSize'+id).fadeIn(500);
				//$(".tjltdz").eq(id-1).click(function(event){
					//$('#customSize'+id).fadeOut(500);
				//});
				return false
			})
			*/

			/*时间插件*/
			(function($) {
				$.mobiscroll.i18n.zh = $.extend($.mobiscroll.i18n.zh, {
					dateFormat: 'yyyy-mm-dd',
					dateOrder: 'yymmdd',
					dayNames: ['周日', '周一;', '周二;', '周三', '周四', '周五', '周六'],
					dayNamesShort: ['日', '一', '二', '三', '四', '五', '六'],
					dayText: '日',
					hourText: '时',
					minuteText: '分',
					monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
					monthNamesShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
					monthText: '月',
					secText: '秒',
					timeFormat: 'HH:ii',
					timeWheels: 'HHii',
					yearText: '年'
				});
			})(jQuery);

			(function($) {
				$.mobiscroll.i18n.zh = $.extend($.mobiscroll.i18n.zh, {
					setText: '确定',
					cancelText: '取消'
				});
			})(jQuery);

			(function($) {
				var theme = {
					defaults: {
						dateOrder: 'Mddyy',
						mode: 'mixed',
						rows: 5,
						width: 70,
						height: 36,
						showLabel: true,
						useShortLabels: true
					}
				}

				$.mobiscroll.themes['android-ics'] = theme;
				$.mobiscroll.themes['android-ics light'] = theme;

			})(jQuery);

			/*选中飞入的效果*/
			!
			function(a) {
				a.fly = function(b, c) {
						var d = {
								version: "1.0.0",
								autoPlay: !0,
								vertex_Rtop: 20,
								speed: 1.2,
								start: {},
								end: {},
								onEnd: a.noop
							},
							e = this,
							f = a(b);
						e.init = function(a) {
								this.setOptions(a), !!this.settings.autoPlay && this.play()
							},
							e.setOptions = function(b) {
								this.settings = a.extend(!0, {},
									d, b);
								var c = this.settings,
									e = c.start,
									g = c.end;
								f.css({
										marginTop: "0px",
										marginLeft: "0px",
										position: "fixed"
									}).appendTo("body"),
									null != g.width && null != g.height && a.extend(!0, e, {
										width: f.width(),
										height: f.height()
									});
								var h = Math.min(e.top, g.top) - Math.abs(e.left - g.left) / 3;
								h < c.vertex_Rtop && (h = Math.min(c.vertex_Rtop, Math.min(e.top, g.top)));
								var i = Math.sqrt(Math.pow(e.top - g.top, 2) + Math.pow(e.left - g.left, 2)),
									j = Math.ceil(Math.min(Math.max(Math.log(i) / .05 - 75, 30), 100) / c.speed),
									k = e.top == h ? 0 : -Math.sqrt((g.top - h) / (e.top - h)),
									l = (k * e.left - g.left) / (k - 1),
									m = g.left == l ? 0 : (g.top - h) / Math.pow(g.left - l, 2);
								a.extend(!0, c, {
									count: -1,
									steps: j,
									vertex_left: l,
									vertex_top: h,
									curvature: m
								})
							},
							e.play = function() {
								this.move()
							},
							e.move = function() {
								var b = this.settings,
									c = b.start,
									d = b.count,
									e = b.steps,
									g = b.end,
									h = c.left + (g.left - c.left) * d / e,
									i = 0 == b.curvature ? c.top + (g.top - c.top) * d / e : b.curvature * Math.pow(h - b.vertex_left, 2) + b.vertex_top;
								if(null != g.width && null != g.height) {
									var j = e / 2,
										k = g.width - (g.width - c.width) * Math.cos(j > d ? 0 : (d - j) / (e - j) * Math.PI / 2),
										l = g.height - (g.height - c.height) * Math.cos(j > d ? 0 : (d - j) / (e - j) * Math.PI / 2);
									f.css({
										width: k + "px",
										height: l + "px",
										"font-size": Math.min(k, l) + "px"
									})
								}
								f.css({
										left: h + "px",
										top: i + "px"
									}),
									b.count++;
								var m = window.requestAnimationFrame(a.proxy(this.move, this));
								d == e && (window.cancelAnimationFrame(m), b.onEnd.apply(this))
							},
							e.destory = function() {
								f.remove()
							},
							e.init(c)
					},
					a.fn.fly = function(b) {
						return this.each(function() {
							void 0 == a(this).data("fly") && a(this).data("fly", new a.fly(this, b))
						})
					}
			}(jQuery);

			/*隐藏显示晒单*/
			$(function () {
				$('.comm_fixed').click(function () {
					$(".diy_comm").animate({left:'0'});
					$(".diy_comm").append("<iframe class='comm_iframe' scrolling='auto' frameborder='0' src='/comment-comments.html?&quan_id=<?php echo $this->_var['quan_id']; ?>&type=0&s_id=0' style='height:100%; width:100%;'></iframe>");
					$("body").animate({left:'-100%'});
					$('.comm_fixed2').show();
					$('#zhichiBtnBox').hide();
					$('.diy_fcart').hide();
					$('.comm_fixed').hide();
				});

				$('.comm_fixed2').click(function () {
					$('.comm_fixed').show();
					$(".diy_comm").animate({left:'100%'});
					$("body").animate({left:'0'},function(){
						$(".diy_comm").empty();
					});

					$('.comm_fixed2').hide();
					$('.diy_fcart').show();
					$('#zhichiBtnBox').show();

				});

				$(".srs-thumb").on("mousemove touchmove", function(){
					$('.srtz').blur();
				});

			});

		</script>
		<script src="/diy/js/diy2.js?33"></script>

		<style>
			.load_div {
				width: 80px;
				height: 100px;
				background: rgba(0, 0, 0, 0.8);
				border-radius: 10px;
				color: #fff;
				position: absolute;
				left: 50%;
				top: 50%;
				margin: -50px 0 0 -40px;
				z-index: 999999;
				text-align: center;
			}
			
			.load_div img {
				width: 32px;
				height: 32px;
				display: block;
				margin: 20px auto 8px auto;
			}

		</style>

		<div class="load_div" style="display:none;"><img src="/diy/images/load.gif">加载中...</div>


		<div class="comm_fixed"><span>>></span>宠友晒单</div>
		<div class="comm_fixed2"><span><<</span>继续定制</div>
		<div class="diy_comm"></div>


		<div class="diy_fcart">
			<a href="<?php echo $this->build_url(array('app'=>'cart')); ?>">购物车<br><i class="cart_icon"><?php echo $this->_var['cart_goods_num']; ?></i></a>
		</div>


		
		<script src="<?php echo $this->_var['kefu_url']; ?>" class="zhiCustomBtn"  id="zhichiScript" data-args="<?php echo $this->_var['kefu_uinfo']; ?>">
		</script>

		<script>
			document.body.addEventListener('touchstart', function () {});
		</script>
		



		
		<div class="fixed_main">
			<span class="close">关闭</span>


			<div class="swiper-container swiper-container-horizontal" id="swiper-container1">
				<div class="swiper-wrapper">

					<div class="swiper-slide blue-slide swiper-slide-active" style="width: 800px;">

					</div>

				</div>
				<div class="swiper-pagination swiper-pagination-bullets" id="swiper-pagination1"><span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet"></span></div>
			</div>
		</div>
		<script src="https://cdn.bootcss.com/Swiper/3.4.1/js/swiper.jquery.js"></script>
		<script type="text/javascript">
			function commentbig(obj){

				// var comment_id=$(obj).attr('data-commentid');
				var comment_id=$('.comm_iframe').contents().find(obj).attr('data-commentid');

				$.ajax({
					url: '/comment-getimage.html',
					type: 'POST',
					timeout: 5000,
					dataType: "json",
					data: {
						cid: comment_id,
					},
					success: function(data) {
						// var data = eval("("+data+")");

						if(data.retval){
							var html='';
							for(var i=0;i<data.retval.length;i++){
								html +="<div class='swiper-slide blue-slide swiper-slide-active' style='width: 800px;'><img src="+data.retval[i]+" /></div>";
							}
						}

						$('.swiper-wrapper div').remove();
						$('.swiper-wrapper').append(html)
						$(".fixed_main").css("display", "block");
						var mySwiper2 = new Swiper('#swiper-container1', {
							pagination: '#swiper-pagination1',
							paginationType: 'fraction',
						});
					},
					error: function() {
						alert('获取图片失败');

					}
				});

				$('.close').click(function(){
					$('.fixed_main').hide();
				})

			}
			/*	$(".comment_img li").click(function() {
			 $(".fixed_main").css("display", "block");
			 var mySwiper2 = new Swiper('#swiper-container1', {
			 pagination: '#swiper-pagination1',
			 paginationType: 'fraction',
			 });
			 });*/
		</script>
		


	</body>

</html>