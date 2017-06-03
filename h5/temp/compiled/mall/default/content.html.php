<!DOCTYPE html>
<html lang="zh_cn">

	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="pragma" content="no-cache">
		<title><?php echo $this->_var['goods']['goods']['name']; ?></title>
		<link rel="stylesheet" type="text/css" href="static/css/mobile_style.css" media="screen">
		<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'jquery-1.8.3.min.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'jquery.swipe.js'; ?>"></script>
		<script type="text/javascript" src="/diy/js/layer_mobile/layer.js"></script>
	</head>

	<body>


	<!--<div href="#" class="spxq_fg">
		<?php $_from = $this->_var['banner']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
		<?php $_from = $this->_var['item']['rc']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item2');if (count($_from)):
    foreach ($_from AS $this->_var['item2']):
?>
		<a href="<?php echo $this->_var['item2']['link_url']; ?>"><img src="<?php echo $this->_var['item2']['img']; ?>" width="100" height="100"/></a>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		<div class="close">
			<span class="icon_close">x</span>
		</div>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>-->

		<div class="main xq_section">
			<div class="xiangc">
				<div id="slider" class="swipe">
					<div class="swipe-wrap">
						<?php $_from = $this->_var['goods']['goods']['gallery_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'gallery');if (count($_from)):
    foreach ($_from AS $this->_var['gallery']):
?>
						<div>
							<a href="###"><img src="<?php echo $this->_var['gallery']['img_url']; ?>" /></a>
						</div>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</div>
					<div class="swipe-btn"> <span class="cur"></span> <span></span> <span></span> <span></span><span></span> </div>
				</div>
			</div>

			<div class="spxq_bt" data-id="<?php echo $this->_var['goods']['goods']['goods_id']; ?>">
				<h4><a href="#"><?php echo $this->_var['goods']['goods']['name']; ?></a></h4>

				<h5><i id="singprice"><?php echo $this->_var['goods']['goods']['price']; ?></i> <b>元</b></h5>

				<div class="scj"><span>市场价 <del id="mktprice"><?php echo $this->_var['goods']['goods']['mktprice']; ?>元</del></span><span>已售<?php echo $this->_var['goods']['goods']['buy_count']; ?>件</span></div>
				<?php if ($this->_var['is_pro']): ?>
				<div class="cx"><span>促销</span><?php echo $this->_var['goods_con']['content']; ?></div>
				<?php endif; ?>
				<div class="yx cx addCarts">
					<a href="#" style="display:block;" id="clects"><span>已选</span> <?php $_from = $this->_var['goods_con']['spec_value']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'specvalue');if (count($_from)):
    foreach ($_from AS $this->_var['specvalue']):
?>
						<font><?php echo $this->_var['specvalue']; ?></font>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						<font id="ffnum">1个</font>
					</a>
				</div>
			</div>

			<h1 class="xgcp">商品详情</h1>
			<div class="spxqtu" style="text-align: center;"><?php echo $this->_var['goods']['goods']['intro']; ?></div>
			<?php if ($this->_var['goods']['goods']['attr_list']): ?>
			<h1 class="xgcp">产品参数</h1>
			<ul class="cpcs">
				<?php $_from = $this->_var['goods']['goods']['attr_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goodsattr');if (count($_from)):
    foreach ($_from AS $this->_var['goodsattr']):
?>
				<li>
					<h1><?php echo $this->_var['goodsattr']['p_name']; ?></h1>
					<p><?php echo $this->_var['goodsattr']['s_name']; ?></p>
				</li>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			</ul>
			<?php endif; ?>

			<h1 class="xgcp">相关产品</h1>
			<div class="lb_list" style="margin: 0;">
				<ul>
					<?php $_from = $this->_var['goods_con']['cat_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat_goods');if (count($_from)):
    foreach ($_from AS $this->_var['cat_goods']):
?>
					<li>
						<a href="<?php echo $this->build_url(array('app'=>'product','act'=>'content')); ?>?id=<?php echo $this->_var['cat_goods']['goods_id']; ?>" style="display:block">
							<p class="p1"><img src="<?php echo $this->_var['cat_goods']['thumbnail_pic']; ?>"></p>
							<p class="p2"><?php echo $this->_var['cat_goods']['name']; ?></p>
							<p class="p3"><?php echo $this->_var['cat_goods']['price']; ?>元</p>
						</a>
					</li>
					<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</ul>
			</div>

			
			<div class="tjltdz" id="customSize">
				<div class="jrgwc_xx">
					<div class="gwcjzc">
						<h1><?php echo $this->_var['goods']['goods']['name']; ?><span class="gb">x</span></h1> <?php $_from = $this->_var['goods']['products']['spe']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'spe');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['spe']):
?>
						<h2><?php echo $this->_var['spe']['name']; ?></h2>
						<ul class="spec2_<?php echo $this->_var['key']; ?>" data-id="111">
							<?php $_from = $this->_var['spe']['value']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'spv');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['spv']):
?>
							<li <?php if ($this->_var['spv']['is_default'] == 1): ?>class="on" <?php endif; ?> data-id=<?php echo $this->_var['key']; ?> value="<?php echo $this->_var['key1']; ?>"><?php echo $this->_var['spv']['value_name']; ?></li>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</ul>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

						<h2>数量</h2>
						<div class="gwcsl clearfix">
							<div class="gwcjj">
								<p class="p1" onclick="cartNum(1)"></p>
								<input type="text" class="nums" name="nums"  value="1" disabled>
								<p class="p2" onclick="cartNum(2)"></p>
							</div>
							<p class="kc">库存<span id="numbers"><?php echo $this->_var['goods']['goods']['store']; ?></span>件</p>
						</div>

					</div>
					
					<p class="xq_tc_qr gb">确认</p>
					<!--<div class="sc_jrgwc">
						<?php if ($this->_var['goods_con']['is_collect'] == 1): ?>
						<p class="ysc">已收藏</p>
						<?php else: ?>
						<p class="sc">收藏</p>
						<?php endif; ?>
						<p class="jrgwc">加入购物车<span id="prices"><?php echo $this->_var['goods']['goods']['price']; ?>元</span></p>
						
					</div>-->

				</div>
			</div>
			

			<p style="height: 70px;"></p>
			<div class="zyjr_box sc_jrgwc">
				<!--<?php if ($this->_var['goods_con']['is_collect'] == 1): ?>
				<p class="ysc">已收藏</p>
				<?php else: ?>
				<p class="sc">收藏</p>
				<?php endif; ?>
				<p <?php if ($this->_var['goods_con']['spec_value']): ?>class="jrgwc" <?php else: ?>class="jrgwc addCart" <?php endif; ?>>加入购物车<span id="addcarts"><?php echo $this->_var['goods']['goods']['price']; ?>元</span></p>-->
				<div class="zyjr_box_left">
					<a href="<?php echo $this->_var['kefu_url']; ?>&visitTitle=商品详情">
						<span>
							<img src="public/static/wap/images/xq_huihua_1.jpg"/>
						</span>
						<span>客服</span>
					</a>
					<a href="<?php echo $this->build_url(array('app'=>'cart')); ?>">
						<span>
							<i class="cart_num"><?php echo $this->_var['cart_goods_num']; ?></i>
							<img src="public/static/wap/images/xq_huihua_2.jpg"/>
						</span>
						<span>购物车</span>
					</a>
					<div class="shouc">
					<?php if ($this->_var['goods_con']['is_collect'] == 1): ?>
					<a onclick="yscs()">
						<span>
							<img src="public/static/wap/images/xq_huihua_3.jpg"/>
						</span>
						<span >已收藏</span>
					</a>
					<?php else: ?>
					<a onclick="scs()">
						<span>
							<img src="public/static/wap/images/xq_huihua_3.jpg"/>
						</span>
						<span >收藏</span>
					</a>
					<?php endif; ?>
					 </div>
				</div>
				<!--<p <?php if ($this->_var['goods_con']['spec_value']): ?>class="jrgwc" <?php else: ?>class="jrgwc addCart" <?php endif; ?>>加入购物车<span id="addcarts"><?php echo $this->_var['goods']['goods']['price']; ?>元</span></p>-->
				<div class="zyjr_box_right">
					<a href="###" <?php if ($this->_var['goods_con']['spec_value']): ?>class="jrgwcs" <?php else: ?>class="jrgwcs addCarts" <?php endif; ?>>加入购物车</a>
					<a href="###" <?php if ($this->_var['goods_con']['spec_value']): ?>class="jrgwcss" <?php else: ?>class="jrgwcs addCarts" <?php endif; ?>>立即购买</a>
				</div>

			</div>

			<style type="text/css">

			</style>

		</div>
		<div id="product_id" style="display:none;"><?php echo $this->_var['goods']['products']['default_product_id']; ?></div>
		<script>
			//页面滚动效果
			Swipe(document.getElementById('slider'), {
				continuous: false,
				//auto:1000,
				callback: function(index, element) {
					$('.swipe-btn span').eq(index).addClass('cur').siblings().removeClass();
				}
			});
			//收藏
			//$('.scs').click(function() {
				function scs(){
				var name = "hasLogin";
				var goods_id = $('.spxq_bt').attr("data-id");
				var haslogin = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
				if(haslogin) {
					if(unescape(haslogin[2]) == 0) {
						location.href = "/member-login.html";
						return
					}
				}
				$.ajax({
					url: '/my_favorite-add.html',
					type: 'POST',
					timeout: 5000,
					dataType: "json",
					data: {
						type: 'goods',
						gid: goods_id,
					},
					success: function(data) {
					
						layer.open({
								content: "收藏成功"
								,skin: 'msg'
								,time: 2 //2秒后自动关闭
						});
						//window.location.reload();
						$('.shouc a').remove();
						$('.shouc').append("<a onclick='yscs()'><span><img src='public/static/wap/images/xq_huihua_3.jpg'/></span><span >已收藏</span></a>")
					},
					error: function() {
						layer.open({
								content: "收藏失败"
								,skin: 'msg'
								,time: 2 //2秒后自动关闭
						});
					
					}
				});
			};
			//取消收藏
		//	$('.yscs').click(function() {
        function yscs(){
				var name = "hasLogin";
				var goods_id = $('.spxq_bt').attr("data-id");
				var haslogin = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
				if(haslogin) {
					if(unescape(haslogin[2]) == 0) {
						location.href = "/member-login.html";
						return
					}
				}
				$.ajax({
					url: '/product-dropCollect.html',
					type: 'POST',
					timeout: 5000,
					dataType: "json",
					data: {
						type: 'goods',
						gid: goods_id,
					},
					success: function(data) {
						layer.open({
								content: "取消收藏成功"
								,skin: 'msg'
								,time: 2 //2秒后自动关闭
						});
						//window.location.reload();
						$('.shouc a').remove();
						$('.shouc').append("<a onclick='scs()'><span><img src='public/static/wap/images/xq_huihua_3.jpg'/></span><span >收藏</span></a>")
					},
					error: function() {
							layer.open({
								content: "取消收藏失败"
								,skin: 'msg'
								,time: 2 //2秒后自动关闭
						});
					}
				});
			};

			function cartNum(type) {
				var num = parseInt($("input[name=nums]").val());
				if(type == 1) //减
				{
					if(num <= 1) {
						return false;
					}
					num = num - 1;
				} else {
					num = num + 1;
				}

				$("input[name=nums]").val(num);
				fprice();
			}

			//加入购物车
			$('.gb').click(function() {
				$('#customSize').fadeOut(500);
			});
			$('.addCarts').click(function() {
				$('#customSize').fadeIn(500);
				return false
			});
			$(".gwcjzc li").click(function() {
				var spec_id = $(this).attr("data-id");
				var goods_id = $('.spxq_bt').attr("data-id");
				var num = $('.nums').val();
				$(".spec2_" + spec_id + " li").removeClass("on");
				$(this).addClass("on");
				var spec_value_ids = '';
				$('.gwcjzc li.on').each(function() {
					spec_value_ids += $(this).data("id") + "-" + $(this).val() + ",";
				});
				$.post("<?php echo $this->build_url(array('app'=>'product','act'=>'checkp')); ?>", {
					hid: spec_value_ids,
					goodsId: goods_id,
					num: num
				}, function(res) {
					var res = $.parseJSON(res);
					if(res.done == true) {
						$("#prices").html(res.retval.productLists.price * num);
						$("#singprice").html(res.retval.productLists.price);
						$("#mktprice").html(res.retval.productLists.mktprice);
						$("#numbers").html(res.retval.productLists.store);
						$("#addcarts").html(res.retval.productLists.price * num);
						$("#product_id").html(res.retval.productLists.product_id);
						var html = '<span>已选</span>';
						for(var i = 0; i < res.retval.productLists.spec_value_count; i++) {
							html += " <font>" + res.retval.productLists.spec_value[i] + "</font>";
						}
						html += "<font>" + res.retval.productLists.numbers + "个</font>";
						$('#clects').html('');
						$('#clects').append(html);
					}
				})
			});
			$(".nums").keyup(function() {

				fprice();
			});

			//处理价格
			function fprice() {
				var goods_id = $('.spxq_bt').attr("data-id");
				var num = $('.nums').val();
				if(num <= 0) {
					$('.nums').val(1);
					return;
				}
				$("#ffnum").text(num + "个");
				var spec_value_ids = '';
				$('.gwcjzc li.on').each(function() {
					spec_value_ids += $(this).data("id") + "-" + $(this).val() + ",";
				});
				$.post("<?php echo $this->build_url(array('app'=>'product','act'=>'checkp')); ?>", {
					hid: spec_value_ids,
					goodsId: goods_id,
					num: num
				}, function(res) {
					var res = $.parseJSON(res);
					if(res.done == true) {
						$("#prices").html(res.retval.productLists.price * num);
						$("#singprice").html(res.retval.productLists.price);
						$("#mktprice").html(res.retval.productLists.mktprice);
						$("#numbers").html(res.retval.productLists.store);
						$("#addcarts").html(res.retval.productLists.price * num);
						$("#product_id").html(res.retval.productLists.product_id)
					}
				})
			}

			$(".jrgwcs").click(function() {
				var goods_id = $('.spxq_bt').attr("data-id");
				var num = $('.nums').val();
				var product_id = $("#product_id").html();
				var name = "hasLogin";
				var haslogin = "<?php echo $this->_var['visitor']['user_id']; ?>";
				//var haslogin=document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
				//    alert(haslogin)
//				if(haslogin == 0) {
//					location.href = "/member-login.html";
//					return
//				}
				$.ajax({
					url: '/cart-add.html?ajax',
					type: 'POST',
					timeout: 5000,
					dataType: "json",
					data: {
						pid: product_id,
						gid: goods_id,
						num: num,
						mfd_reload_mid:"<?php echo $_SESSION['user_info']['user_id']; ?>"
					},
					success: function(data) {
						if(data.done) {
							layer.open({
								content: "购物车加入成功"
								,skin: 'msg'
								,time: 2 //2秒后自动关闭
							});
							//cart_num
							$(".cart_num").text(data.retval.goods_num);



						} else {
							alert(data.msg);
						}

					},
					error: function() {
						alert('加入购物车请求出错')
					}
				});

				/*  $.post("/cart-add.html",{pid: product_id,gid:goods_id,num:num}, function(res){
					 var res = $.parseJSON(res);
				        if(res.done==true){
				        	
				        }
				    }) */
			})


			$(".jrgwcss").click(function() {
				var goods_id = $('.spxq_bt').attr("data-id");
				var num = $('.nums').val();
				var product_id = $("#product_id").html();
				var name = "hasLogin";
				var haslogin = "<?php echo $this->_var['visitor']['user_id']; ?>";
				//var haslogin=document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
				//    alert(haslogin)
				if(haslogin == 0) {
					location.href = "/member-login.html";
					return
				}
				$.ajax({
					url: '/cart-add.html?ajax',
					type: 'POST',
					timeout: 5000,
					dataType: "json",
					data: {
						pid: product_id,
						gid: goods_id,
						num: num,
						mfd_cart_is_check:1,
						mfd_reload_mid:"<?php echo $_SESSION['user_info']['user_id']; ?>"
					},
					success: function(data) {
						if(data.done) {
							window.location.href = '/cart-checkout.html?mfd_cart_is_check=1'
						} else {
							alert(data.msg);
						}

					},
					error: function() {
						alert('加入购物车请求出错')
					}
				});

				/*  $.post("/cart-add.html",{pid: product_id,gid:goods_id,num:num}, function(res){
				 var res = $.parseJSON(res);
				 if(res.done==true){

				 }
				 }) */
			})

//			$(".xq_section").css("top", "90px");
//			//悬浮广告关闭
//			$(".close").click(function() {
//				$(this).parent().next().css("top", "0");
//				$(this).parent().remove();
//			})
		</script>
		<script>
			//检测是否是微信浏览器
			function is_weixin() {
				var ua = navigator.userAgent.toLowerCase();
				if(ua.match(/MicroMessenger/i) == "micromessenger") {
					return true;
				} else {
					return false;
				}
			}
			if(is_weixin()) {
				document.getElementById("header").style.display = 'none';
			}
		</script>
		<?php echo $this->fetch('footer.html'); ?>
		<?php echo $this->fetch('fudong1.html'); ?>
	</body>

</html>