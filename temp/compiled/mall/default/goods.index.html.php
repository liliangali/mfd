<?php echo $this->fetch('header-new.html'); ?>
<script>
if('<?php echo $this->_var['p_source']; ?>'=='mobile'){
	window.location.href=('http://h5.myfoodiepet.com/product-content.html?id='+window.location.href.split('goods-')[1].split('.')[0])
}
</script>
<style>
@charset "utf-8";
.tab li, .tab a, .tab-item{float:left;text-align:center;}
.tab li, .tab a, .tab-item{cursor:pointer;float:left;text-align:center;}
#summary-stock .dt{margin-top:4px;}
#list1 .dt{float:left;height:18px;text-align:right;width:72px;}
#list1 .dd{width:324px;float:left;}

#store-selector{position:relative;float:left;z-index:2;height:26px;margin-right:6px}
#store-selector dl,#store-selector dt,#store-selector dd{float:none;color:#999}
#store-selector dl{position:absolute;top:23px;z-index:1;width:358px;width:358px;padding:5px;border:1px solid #ddd;background:#fff;display:none;-moz-box-shadow:0 0 5px #ddd;-webkit-box-shadow:0 0 5px #ddd;box-shadow:0 0 5px #ddd}
#store-selector dt{padding:6px 0 10px;color:#999}
#store-selector dd{padding-bottom:5px;line-height:18px}
#store-selector a:link,#store-selector a:visited{color:#005aa0}
#store-selector a:hover,#store-selector a:active{background:#005aa0;color:#fff}
#store-selector .text{float:left;+float:none;_float:left;position:relative;top:0;z-index:2;height:23px;background:#fff;border:1px solid #CECBCE;padding:0 20px 0 4px;line-height:23px;overflow:hidden}
#store-selector .text b{display:block;position:absolute;top:0;right:0;overflow:hidden;width:17px;height:24px;background:url(img/btn20121210.png) 0 0 no-repeat}
#store-selector .close{display:none;position:absolute;z-index:2;top:19px;left:366px;cursor:pointer;width:17px;height:17px;background:url(img/20120418.png) no-repeat -40px 0}
#store-selector a:hover{background:#B79567;color:#fff;cursor:pointer}
#store-selector.hover .text{border-bottom:0}
#store-selector.hover .i-storeinfo{position:relative;z-index:2;background:url(img/bg_store.gif) no-repeat right -50px}
#store-selector.hover .i-storeinfo div{background:url(img/bg_store.gif) no-repeat -81px -75px}
#store-selector.hover dl{display:block}
#store-selector.hover .close{display:block}

#store-prompt{clear:left;line-height:25px}
#store-prompt strong{font-size:14px}
#store-prompt a{color:#005EAA}
*html #store-selector .i-storeinfo,*html #store-selector .i-storeinfo div{float:left}
*html #store-selector dl{left:0}
#store-selector .content{display:none;position:absolute;top:23px;left:-45px;border:1px solid #cecbce;width:390px;padding:15px;background:#fff;-moz-box-shadow:0 0 5px #ddd;-webkit-box-shadow:0 0 5px #ddd;box-shadow:0 0 5px #ddd}
#store-selector .content select{float:left;width:120px;border:1px solid #cecbce;margin-right:15px}
#store-selector .content .select3{margin-right:0}
#store-selector.hover .content,#store-selector.hover .close{display:block}
#JD-stock{position:relative;margin-bottom:0}
#JD-stock .tab{width:100%;height:25px;float:left;border-bottom:2px solid #edd28b;overflow:visible;*overflow:hidden}
#JD-stock .tab li{float:left;clear:none;height:23px;padding:1px 1px 0;border:1px solid #ddd;border-bottom:0;margin-right:3px;background-color:#fff;line-height:22px;text-decoration:none}
#JD-stock .tab .curr{*position:relative;height:25px;padding:0;border:2px solid #edd28b;border-bottom:0}
#JD-stock .tab a{position:relative;float:left;height:23px;padding:0 20px 1px 10px;line-height:23px;text-align:center;text-decoration:none;cursor:pointer;color:#005AA0;outline:0;*blr:expression(this.onFocus=this.blur())}
#JD-stock .tab a:hover{background:0;color:#005AA0}
#JD-stock .tab a i{position:absolute;right:5px;top:10px;*top:9px;display:block;width:7px;height:5px;overflow:hidden;background:url(img/20130606B.png) no-repeat -76px -34px;opacity:.5;filter:alpha(opacity=50)}
#JD-stock .tab .curr i,#JD-stock .tab a:hover i{opacity:1;filter:alpha(opacity=100)}
#JD-stock .area-list{padding-top:5px}
#JD-stock .area-list li{float:left;width:80px;padding:2px 0 2px 15px;clear:none}
#JD-stock .area-list li a{float:left;padding:2px 4px;*padding:0 4px;color:#005aa0}
#JD-stock .area-list li a:hover{color:#fff}
#JD-stock .area-list .longer-area{width:370px}
#JD-stock .area-list .long-area{width:170px}
</style>
<link rel="stylesheet" href="/public/global/scroll/jquery.mCustomScrollbar.css">
<link href="/public/static/pc/css/details.css" rel="stylesheet">
<script src="/public/global/jquery-1.9.1.min.js"></script>
<div class="mbx"><p class="container">
<?php $_from = $this->_var['_curlocal']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cur');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['cur']):
        $this->_foreach['loop']['iteration']++;
?>
<a href="<?php echo $this->_var['cur']['url']; ?>"><?php echo $this->_var['cur']['text']; ?></a><?php if (! ($this->_foreach['loop']['iteration'] == $this->_foreach['loop']['total'])): ?><span>/</span><?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</p>
</div>

<div class="detailed_top">
	<div class="xq_center container clearfix">
		<div class="AreaL fl">
			<div class="xqtop_left">
			
			<div id="slider" class="swipe">
			  <div class="swipe-wrap">
			  <?php $_from = $this->_var['goodsItem']['goods']['gallery_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['img']):
        $this->_foreach['loop']['iteration']++;
?>
				<div><img <?php if ($this->_foreach['loop']['iteration'] == 1): ?>src="<?php echo $this->_var['img']['img_url']; ?>"<?php else: ?>lazy-src="<?php echo $this->_var['img']['img_url']; ?>"<?php endif; ?>></div>
			  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			  </div>
			  <ul class="swipe-btn">
			  <?php $_from = $this->_var['goodsItem']['goods']['gallery_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['img']):
        $this->_foreach['loop']['iteration']++;
?>
				<li <?php if ($this->_foreach['loop']['iteration'] == 1): ?>class="cur"<?php endif; ?>></li>
			  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			  </ul>
			</div>
			</div>
			<div class="xqtop_right">
				<ul class="popup-gallery">
				<?php $_from = $this->_var['goodsItem']['goods']['gallery_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['img']):
?>
					<li><a href="<?php echo $this->_var['img']['img_url']; ?>"><img src="<?php echo $this->_var['img']['thumb_url']; ?>"></a></li>
			    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</ul>
			</div>
		</div>
		<div class="AreaR xqjj_jgwc fr">
			<div class="kuadu">
				<h1><?php echo $this->_var['goodsItem']['goods']['name']; ?></h1>
				<?php $_from = $this->_var['goodsItem']['products']['pd']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
				<?php if ($this->_var['item']['is_default']): ?>
				<div class="jgjs"><font class="s_price"><?php echo $this->_var['item']['price']; ?>元</font>
					<?php if ($this->_var['end_time']): ?>
					<span id="fnTimeCountDown">
						距离结束时间还有：<i class="year"></i>年<i class="month"></i>月<i class="day"></i>天<i class="hour"></i>小时<i class="mini"></i>分<i class="sec"></i>秒
					</span>
					<?php endif; ?>
				</div>
                <h2>市场价<del class='sy_price'><?php echo $this->_var['item']['mktprice']; ?>元</del>已售<?php echo $this->_var['goodsItem']['goods']['buy_count']; ?></h2> 
			    <?php endif; ?>
			    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				<!--<div id="fnTimeCountDown" data-end="2018/07/08 18:45:13">-->
					<!--<span class="year">00</span>年-->
					<!--<span class="month">00</span>月-->
					<!--<span class="day">00</span>天-->
					<!--<span class="hour">00</span>时-->
					<!--<span class="mini">00</span>分-->
					<!--<span class="sec">00</span>秒-->
					<!--<span class="hm">000</span>-->
				<!--</div>-->


					<script type="text/javascript">
							$.extend($.fn,{
								//懒人建站 http://www.51xuediannao.com/
								fnTimeCountDown:function(d){
									this.each(function(){
										var $this = $(this);
										var o = {
											hm: $this.find(".hm"),
											sec: $this.find(".sec"),
											mini: $this.find(".mini"),
											hour: $this.find(".hour"),
											day: $this.find(".day"),
											month:$this.find(".month"),
											year: $this.find(".year")
										};
										var f = {
											haomiao: function(n){
												if(n < 10)return "00" + n.toString();
												if(n < 100)return "0" + n.toString();
												return n.toString();
											},
											zero: function(n){
												var _n = parseInt(n, 10);//解析字符串,返回整数
												if(_n > 0){
													if(_n <= 9){
														_n = "0" + _n
													}
													return String(_n);
												}else{
													return "00";
												}
											},
											dv: function(){
												//d = d || Date.UTC(2050, 0, 1); //如果未定义时间，则我们设定倒计时日期是2050年1月1日
												var _d = $this.data("end") || d;
												var now = new Date(),
														endDate = new Date(_d);
												//现在将来秒差值
												//alert(future.getTimezoneOffset());
												var dur = (endDate - now.getTime()) / 1000 , mss = endDate - now.getTime() ,pms = {
													hm:"000",
													sec: "00",
													mini: "00",
													hour: "00",
													day: "00",
													month: "00",
													year: "0"
												};
												if(mss > 0){
													pms.hm = f.haomiao(mss % 1000);
													pms.sec = f.zero(dur % 60);
													pms.mini = Math.floor((dur / 60)) > 0? f.zero(Math.floor((dur / 60)) % 60) : "00";
													pms.hour = Math.floor((dur / 3600)) > 0? f.zero(Math.floor((dur / 3600)) % 24) : "00";
													pms.day = Math.floor((dur / 86400)) > 0? f.zero(Math.floor((dur / 86400)) % 30) : "00";
													//月份，以实际平均每月秒数计算
													pms.month = Math.floor((dur / 2629744)) > 0? f.zero(Math.floor((dur / 2629744)) % 12) : "00";
													//年份，按按回归年365天5时48分46秒算
													pms.year = Math.floor((dur / 31556926)) > 0? Math.floor((dur / 31556926)) : "0";
												}else{
													pms.year=pms.month=pms.day=pms.hour=pms.mini=pms.sec="00";
													pms.hm = "000";
													//alert('结束了');
													return;
												}
												return pms;
											},
											ui: function(){
												if(o.hm){
													o.hm.html(f.dv().hm);
												}
												if(o.sec){
													o.sec.html(f.dv().sec);
												}
												if(o.mini){
													o.mini.html(f.dv().mini);
												}
												if(o.hour){
													o.hour.html(f.dv().hour);
												}
												if(o.day){
													o.day.html(f.dv().day);
												}
												if(o.month){
													o.month.html(f.dv().month);
												}
												if(o.year){
													o.year.html(f.dv().year);
												}
												setTimeout(f.ui, 1);
											}
										};
										f.ui();
									});
								}
							});
				</script>

				<script type="text/javascript">
					<?php if ($this->_var['end_time']): ?>
					$("#fnTimeCountDown").fnTimeCountDown("<?php echo $this->_var['end_time']; ?>");
					<?php endif; ?>
				</script>


<script>

<?php if ($this->_var['end_time']): ?>
var prtime = 1483203661;
//prtime = parseInt(prtime);
//show_date_time();
<?php endif; ?>
var prtime = 1478624461;
function show_date_time(){
//	alert(prtime);
	    window.setTimeout("show_date_time()", 1000);

	    target=new Date(prtime);  //注意：表示月份的参数介于 0 到 11 之间。也就是说，如果希望把月设置为8月，则参数应该是7。
	    today=new Date();
	    timeold=(target.getTime()-today.getTime());
	    sectimeold=timeold/1000;
	    secondsold=Math.floor(sectimeold);
	    msPerDay=24*60*60*1000;
	    e_daysold=timeold/msPerDay;
	    daysold=Math.floor(e_daysold);
	    e_hrsold=(e_daysold-daysold)*24;
	    hrsold=Math.floor(e_hrsold);
	    e_minsold=(e_hrsold-hrsold)*60;
	    minsold=Math.floor((e_hrsold-hrsold)*60);
	    seconds=Math.floor((e_minsold-minsold)*60);
	    if (daysold<0) {
//	    	alert('aa')
	    document.getElementById("time").innerHTML="逾期,倒计时已经失效";
	    }
	    else{
	    if (daysold<10) {daysold="0"+daysold}
	    if (hrsold<10) {hrsold="0"+hrsold}
	    if (minsold<10) {minsold="0"+minsold}
	    if (seconds<10) {seconds="0"+seconds}
	    document.getElementById("time").innerHTML="(<b>"+daysold+"</b>天<b>"+hrsold+"</b>小时<b>"+minsold+"</b>分<b>"+seconds+"</b>秒)";
	    return;
	    if (daysold>0)
	    {
	    	
	    document.getElementById("time").innerHTML="距离结束时间还有："+daysold+"天"+hrsold+"小时"+minsold+"分"+seconds+"秒";
	    }
	    else
	    document.getElementById("time").innerHTML="距离结束时间还有："+daysold+"天"+hrsold+"小时"+minsold+"分"+seconds+"秒";  //结束时间小于1天，字体呈红色提醒
	    }
    }
 
</script>
			
               <input type="hidden"  name="product_id" value="<?php echo $this->_var['goodsItem']['products']['default_product_id']; ?>" />
               
               <div class="spzljs">
              
               	 <?php $_from = $this->_var['goodsItem']['products']['spe']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'spec');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['spec']):
?>
               	<ul>
               	<li class="li_1"><?php echo $this->_var['spec']['name']; ?></li>
               	 <?php $_from = $this->_var['spec']['value']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('item_key1', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item_key1'] => $this->_var['item']):
?>
               		<li class="<?php if ($this->_var['item']['is_default'] == 1): ?>on<?php endif; ?>"  data-id=<?php echo $this->_var['key']; ?>-<?php echo $this->_var['item_key1']; ?> onclick="selep(this)"> 
	               		<?php if ($this->_var['spec']['spec_type'] == 'image'): ?>
	               		<img src="<?php echo $this->_var['item']['iamge']; ?>" title="<?php echo $this->_var['item']['value_name']; ?>"> 
	               		<?php else: ?>
					    <?php echo $this->_var['item']['value_name']; ?>
					    <?php endif; ?>
				   </li>
               	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
               	</ul>
               	 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
               </div>

               <div class="clearfix">
                <div class="jajia_box fl">
                  <a href="#" class="a_2"></a>
                  <input type="text" value="1" disabled>
                  <a href="#" class="a_1"></a>
                </div>
                
                <div class="fl kuzue">库存<i id="store"><?php echo $this->_var['store']; ?></i>件</div>
               </div>
               
               <!--<div class="psdz" >-->
               	 	<!--<p class="p1">配送：<a href="#">山东青岛</a><span>至</span></p>-->
               		<!--<div class="dropdown">-->
	                    <!--<label for="J_province" class="iconfont"></label>-->
	                    <!--<p class="qxz">-->
		                    <!--<select id="J_province" name="province" validate="required" onchange="selcpr(this)">-->
		                        <!--<option value="0">请选择</option>-->
		                    <!--</select>	-->
		                    <!--<span></span>-->
	                    <!--</p>-->
	                    <!--<p class="qxz">-->
		                    <!--<select id="J_city" name="city" validate="required" onchange="selcs(this)">-->
		                    	<!--<option value="0">请选择</option>-->
		                    	<?php $_from = $this->_var['sregion_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
		                    	<!--<option value="<?php echo $this->_var['item']['region_id']; ?>" <?php if ($this->_var['item']['citycode'] == $this->_var['city_code']): ?>selected<?php endif; ?>><?php echo $this->_var['item']['region_name']; ?></option>-->
		                    	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		                    <!--</select>-->
		                    <!--<span></span>-->
	                    <!--</p>-->
                    <!--</div>          -->
                    <!--<p class="p1">邮费<?php echo $this->_var['ship_price']; ?>元</p>              	-->
               <!--</div> -->

             
                        
				<div class="gwcjj">                
					<div class="jrgwcal" id="JaddCar">加入购物车</div>
					<div class="like">收藏</div>					
				</div>
				
				<div class="scfxfp">
				 <div class="sjgm_box fl">
                      <div class="sjgm">手机购买<img src="/public/static/pc/images/xrwm.jpg" class="img1"><img src="/public/static/pc/images/jt.png" class="img2"></div>
                      <div class="sjgmtc animated zoomIn">
                       <p class="drwm"><img src="<?php echo $this->_var['erweima']; ?>"></p>
                       <p class="sjxdgfb">手机下单更方便</p>
                      </div>
                </div>
				<div class="fenxia"><div class="bdsharebuttonbox share-area fr" data-tag="share_1"> <a class="a2" href="#" style="width:42px; background:none; line-height:32px;">分享：</a><a href="javascript:" class="tsina" data-cmd="tsina"></a> <a href="javascript:" class="tqq" data-cmd="tqq"></a> <a href="javascript:" class="weixin" data-cmd="weixin"></a> <a href="javascript:" class="qzone" data-cmd="qzone"></a> </div></div>
								</div>
			</div>				
				
			</div>
		</div>

	</div>
</div>

<div class="gmgk_box">
	<div class="deTailTab" id="JdeTailTab">
		<ul class="gmhgk clearfix">
			<li class="now_hover"><a href="#p1">商品详情<i></i></a></li>
			<li><a href="#p2">顾客评价<span>(<?php echo ($this->_var['comment_count'] == '') ? '0' : $this->_var['comment_count']; ?>)</span><i></i></a></li>
            <li><a href="#p3">相关推荐</a></li>
			<li class="addCart">加入购物车</li>
		</ul>
	</div>
	
	<a id="p1" name="p1"></a>
	<div class="gyxx container">
		<?php if ($this->_var['goodsItem']['goods']['attr_list']): ?>
			<ul>
		<?php $_from = $this->_var['goodsItem']['goods']['attr_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
		<li>
		<?php $_from = $this->_var['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item1');if (count($_from)):
    foreach ($_from AS $this->_var['item1']):
?>
			<?php if ($this->_var['item1']['s_name']): ?>
		<h2><?php echo $this->_var['item1']['p_name']; ?></h2><h3><?php echo $this->_var['item1']['s_name']; ?> </h3>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
		<?php endif; ?>
	</div>
	
</div>
<div class="container">
    <div class="fabric"><?php echo $this->_var['goodsItem']['goods']['intro']; ?></div>
	
	<!--顾客评价开始-->
	<a id="p2" name="p2"></a>
	<div class="tjdp">
		<h1>顾客评价</h1>
	</div>
	<div class="hplhz w">
		<div class="div_1 fl">
				<p class="p1 fl">&nbsp;<?php echo $this->_var['feedback']; ?><span>%</span></p>
                <p class="p2 fl"><span>好评率</span><br>共<?php echo ($this->_var['comment_count'] == '') ? '0' : $this->_var['comment_count']; ?>人评论</p>
		</div>

		<div class="div_3 fl">
			<h1>买家印象</h1>
			<ul>
			<li></li>
			<?php $_from = $this->_var['tags']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'tag');if (count($_from)):
    foreach ($_from AS $this->_var['tag']):
?>
				<li><?php echo $this->_var['tag']['impression']; ?></li>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			</ul>
		</div>
	</div>
	<ul class="pjlist w commentLayer"></ul>
	<!--顾客评价结束--> 
	
	<a id="p3" name="p3"></a>
	<div class="tjdp">
		<h1>相关推荐</h1>
	</div>

<!--相册轮播开始-->
<div class="productshow">
	<div class="scrollcontainer" id="moveid">
		<ul>
		<?php $_from = $this->_var['goods_link']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'suit');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['suit']):
        $this->_foreach['name']['iteration']++;
?>
			<li class="picimglink" <?php if (($this->_foreach['name']['iteration'] <= 1)): ?>  style="margin-left:0px;"<?php endif; ?>>
             <div>
				<p class="p1"><a href="<?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['suit']['goods_id'])); ?>"><img src="<?php echo $this->_var['suit']['thumbnail_pic']; ?>"></a></p>
				<p class="p2"><a href="<?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['suit']['goods_id'])); ?>"><?php echo $this->_var['suit']['name']; ?></a></p>
				<p class="p3"><?php echo $this->_var['suit']['price']; ?>元</p>
                <p class="p4">33389人评价</p>
             </div>
			</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
		<p class="clear"></p>
	</div>
	<a class="abtn aleft animated zoomIn" href="#left">左移</a> <a class="abtn aright animated zoomIn" href="#right">右移</a> </div>
<!--相册轮播结束--> 
</div>

<!--底部/S-->
<?php echo $this->fetch('navicate.html'); ?>

<?php echo $this->fetch('footer-new.html'); ?>
<div id="banner" class="swipe"> <span class="swipe-prev"></span> <span class="swipe-next"></span> </div>

<!--底部/E--> 

<script src="/public/global/luck/pc/luck.js"></script> 
<script src="/public/global/jquery.nav.js"></script> 
<script src="/public/static/pc/js/public.js"></script> 
<script src="/public/static/pc/js/orderinfo.js"></script> 
<script src="/public/static/pc/js/category.js"></script> 
<script src="/public/static/pc/js/Xslider.js"></script>
<link rel="stylesheet" href="/public/global/magnific/magnific-popup.css">
<script src="/public/global/magnific/jquery.magnific-popup.min.js"></script>
<script src="/public/global/my97date/wdatepicker.js"></script> 
<script src="/public/global/scroll/jquery.mCustomScrollbar.concat.min.js"></script> 
<script src="/public/global/jquery.swipe.js"></script>
<script src="/diy/js/jquery.countdown.min.js"></script>

<script>
var p_id = <?php echo $this->_var['p_id']; ?>;
var user_id = "<?php echo $this->_var['visitor']['user_id']; ?>";
$.get("mlselection.html", {pid:"2",type:"region"},
		   function(data)
		   {
		     var str = "";
		     $.each(data.retval,function(key,val){ 
		    	 selct = "";
		    	 if(val.region_id == p_id)
		    	  {
		    		 selct = "selected";
		    	  }
		    	 
		    	   str = str + "<option value="+val.region_id+" "+selct+">"+val.region_name+"</option>";
		     });
		     $("#J_province").append(str);
		   },
	'json');
	

	function  selcpr(obj)
	{
		//  $(".selector").val();
		var pid = $(obj).val();
		$.get("mlselection.html", {pid:pid,type:"region"},
				   function(data)
				   {
				     var str = "";
				     $.each(data.retval,function(key,val){ 
				    	   str = str + "<option value="+val.region_id+" >"+val.region_name+"</option>";
				     });
				     $("#J_city").empty();
				     $("#J_city").append(str);
				   },
			'json');
		
	}
	
	function  selcs(obj)
	{
		return false;
		//  $(".selector").val();
		var region_id = $(obj).val();
		var p_id = $("input[name=product_id]").val();
		 $.get("goods-getShip.html", {region_id:region_id,p_id:p_id},
				   function(data)
				   {
				    
				   },
			'json'); 
		
	}
           
             
</script>
             
<script>
Swipe(document.getElementById('slider'), {
	continuous: false,
	callback: function(i, e) {
		$('#slider .swipe-btn li').eq(i).addClass('cur').siblings().removeClass();	
		var img=$(e).children('img');
		img.attr('src',img.attr('lazy-src'))
	}	
});
</script>


<script>


var goodsId = "<?php echo $this->_var['goodsItem']['goods']['goods_id']; ?>";
	function selep(obj)
	{
		//alert(goodsId)
		var sid = $(obj).attr("data-id");
		$(obj).siblings().removeClass("on");
		$(obj).addClass("on");
		//alert(sid);
		var pid = $(".on");
		var hid = "";
		//alert(sid);
		$.each(pid, function(name, value) {
			 hid = hid + $(this).attr("data-id") + ",";
		});

		/*  $(".on").each(function(){
				alert( $(this).attr("data-id"))
			}); */

		$.post("goods-checkp.html",{hid:hid,goodsId:goodsId},function(res){
			if(res.done)
			{
				$("input[name=product_id]").val(res.retval.product_id);
				$(".s_price").empty();
				$(".sy_price").empty();
				$(".s_price").text(res.retval.price+"元");
				$(".sy_price").text(res.retval.mktprice+"元");
				$("#store").text(res.retval.store);
			}
			else
			{
				alert(res.msg);
			}
		},'json')

	}


//预约时间限制
function my97(obj){
	var date=new Date();
	if(date.getHours()>11||$(obj).parents('form').find('.shiduan').val()=='am'){
		WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'%y-%M-{%d+1}'})		
	}else{
		WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'%y-%M-%d'})
	}
}
$('select.shiduan').focus(function(){
	var date=new Date(),
		date2=new Date($(this).parent().parent().find('.Wdate').val()),
		arr=[[],[],[]];
		arr[0].push(date.getMonth());
		arr[0].push(date.getDate());
		arr[1].push(date2.getMonth());
		arr[1].push(date2.getDate());
		arr[2].push(date.getHours());
		if(arr[0].join()==arr[1].join()){
			this.options[1].disabled=true;
			if(arr[2]>=12){
				this.options[2].disabled=true
			}
		}else{
			this.options[1].disabled=false;
			this.options[2].disabled=false;	
		}
});

$('#goodsAttr').mCustomScrollbar({scrollInertia:500});
var id = '<?php echo $this->_var['sData']['id']; ?>';
$(document).ready(function(){
    $(".questionButton").click(function(){
        var v = $(".questionInput").val();
        if(v.length == 0){
        	//msg("请填写要咨询的问题！",330,150);
        	luck.alert('',"请填写要咨询的问题！",1);
            return false;
        }
        $.post("<?php echo $this->build_url(array('app'=>'goods','act'=>'commit')); ?>", {id:id,content:v}, function(res){
            var res = eval("("+res+")");
            if(res.done == true){
                //msg(res.retval,330,150);
				luck.alert('',res.retval,6);
                $(".questionInput").val('');
            }else{
				luck.alert('',res.msg,1);
            }
        })
    });
    
    var $t = new Date().getTime();
    $.get("<?php echo $this->build_url(array('app'=>'goods','act'=>'ask','arg'=>'1')); ?>?"+$t,{id:id}, function(res){
        var html = eval("("+res+")");
        var count = html.retval.recordCount ? html.retval.recordCount : 0;
        $("#askRecord").html(count);
        $(".questionLayer").html(html.retval.content);
    });

    $.get("goods-comment.html?"+$t,{id:goodsId}, function(res){
        var html = eval("("+res+")");
        $(".commentLayer").html(html.retval.content);
    });

    $("#Jdata-btn").click(function(){
       figure.param = {};
       figure.loadFigure();
    });

    $("#JexistingData .search .btn").click(function(){
    	var value = $(this).parents(".search").find("input[name=keyword]").val();
        if(value.length == 0) return false;
        figure.param.keyword = value;
        figure.loadFigure();
    });

    $("#Jappoint2 .search .btn").click(function(){
    	var value = $(this).parents(".search").find("input[name=keyword]").val();
    	if(value.length == 0) return false;
        assign.param.keyword = value;
        assign.loadFigure();
    });
    
    $(".region").change(function(){
        var v = $(this).val();
        if(v == 0) return false;
        $.get("<?php echo $this->build_url(array('app'=>'goods','act'=>'loadServer')); ?>", {region:v},function(res){
        	var res  = eval("("+res+")");
        	var html='';
      		 for(var i=0;i<res.retval.length;i++){
      			if(res.retval[i].is_free)
      			{
      				 var _str = "<i>收费 ¥100</i>";
      			}
      			else
      			{
      				var _str = "<span>免费</span>";
      			}
       			html += '<li data-id='+res.retval[i].idserve+'>'+_str+'<p class="p1">'+res.retval[i].serve_name+' '+res.retval[i].mobile+'</p><p class="p2">'+res.retval[i].serve_address+'</p></li>';
      	     }
      		 $("#serverlist").html(html);
        })
    });

    $(".like").click(function(){
    	var state = getCookie('hasLogin');
    	if(state != 1){
    		loginIn.show(function(res){
        		if(res.status != "-1"){
    			  favorite();
        		}
 			})
        }else{
        	favorite();
        }
    })
});
function favorite(){
	$.post("<?php echo $this->build_url(array('app'=>'my_favorite','act'=>'add')); ?>",{id:goodsId, type:"goods"}, function(res){
        var res = eval("("+res+")");
		alert(res.msg);
        return false;
    })
}
var figure = {};
figure.param = {};
figure.loadFigure = function(){
	 $.get("<?php echo $this->build_url(array('app'=>'goods','act'=>'loadFigure')); ?>",this.param, function(res){
		 var res = eval("("+res+")");
		 var html='';
		 for(var i=0;i<res.retval.length;i++){
			 if(res.retval[i].is_free)
   			{
   				 var _str = "<i>收费 ¥100</i>";
   			}
   			else
   			{
   				var _str = "<span>免费</span>";
   			}
			    html += '<li data-id='+res.retval[i].figure_sn+'><p class="p1">姓名：'+res.retval[i].customer_name+'</p>'+_str+'<p class="p2">电话：'+res.retval[i].customer_mobile+'</p><p class="p3">地区：'+res.retval[i].address+'</p></li>';
	     }
		 $("#figurelist").html(html);
     })
};

var assign = {};
assign.param = {};
assign.loadFigure = function(){
	 $.get("<?php echo $this->build_url(array('app'=>'goods','act'=>'searchFigurer')); ?>",this.param, function(res){
		 var res  = eval("("+res+")");
			//var tit  = "已选择："+res.retval.title
			var content = res.retval.content;
		    var html='';
    		 for(var i=0;i<content.length;i++){
    			 if(content[i].is_free)
    	   			{
    	   				 var _str = "<i>收费 ¥100</i>";
    	   			}
    	   			else
    	   			{
    	   				var _str = "<span>免费</span>";
    	   			}
     			html += '<li data-id='+content[i].user_id+'>'+_str+'<p class="p1">姓名：'+content[i].real_name+'</p><p class="p2">电话：'+content[i].phone_mob+'</p><p class="p3">地区：'+content[i].address+'</p></li>';
    	     }
		    $("#assignlist").html(html);
     })
};

function goToPage(obj){
	var url = $(obj).data("url");
    $.get(url,{id:goodsId}, function(res){
        var html = eval("("+res+")");
        $("."+html.retval.layer).html(html.retval.content);
    })
}

cotteFn.detail();
//分享
window._bd_share_config = {
	common : {
		bdText : document.title,
		bdDesc : '',	
		bdUrl :window.location.href, 	
		bdPic : ''
	},
	share : [{
		"bdSize" : 16
	}]
};
with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];
</script>


<script>
!function(a) {
    "use strict";
    var c = document;
    //主程序
    a.lucks = {
        view:function(d) {
			var t=d.content?d.content:'';
            //主框架
			var luck = c.createElement("div");
            luck.className = "luck";
            luck.id = "luck";
			//内容容器
            var con = c.createElement("div");
            con.className = "luck-con";
			con.id = "luck-con";
			if(d.width){
				con.style.width=d.width;
			}
			if(d.height){
				con.style.height=d.height;
			}
			con.innerHTML=t;
			//关闭按钮
			var clo=c.createElement("div");
			clo.className='luck-closes';
			clo.onclick=a.lucks.close;
			//遮罩层
            var oShade = c.createElement("div");
            oShade.className = "luck-shade";
		    //组合框架
			if(d.closeBtn){
				con.appendChild(clo);	
			}
			luck.appendChild(con);
			luck.appendChild(oShade);
			return luck;
        },
		resize:function(){
			var t=$(window).scrollTop()+$(window).height()/2-$('#luck-con').height()/2;
			$('#luck-con').css('top',t>$(window).scrollTop()?t:$(window).scrollTop())
		},
        open:function(t) {
            c.body.appendChild(lucks.view(t));
			lucks.resize();
			if(typeof t.callback=='function'){
				t.callback();	
			}
        },
        close:function() {
            var obj = document.getElementById("luck");
			if(obj){
            	c.body.removeChild(obj);
			}
        },
    };
	//基础样式
	var style=document.createElement('style');
	style.type="text/css";
	style.innerHTML=".luck{position:absolute;left:0;top:0;right:0;bottom:0;z-index:1000;}.luck-shade{position:fixed;width:100%;height:100%;left:0;top:0;background:#000;opacity:.8;filter:alpha(opacity=50);z-index:0}.luck-con{position:relative;margin:0 auto;z-index:1;min-width:150px;min-height:680px;max-width:980px;animation:bouncedelay ease .3s;-webkit-animation:bouncedelay ease .3s}.luck-closes{width:30px;height:30px;background:#fff;color:#000;position:absolute;right:0;top:12px;text-align:center;line-height:30px;border-radius: 15px;font-size:24px;cursor:pointer}.luck-closes:after{content:'×'}@-webkit-keyframes bouncedelay{0%{-webkit-transform:scale(0)}100%{-webkit-transform:scale(1)}}@keyframes bouncedelay{0%{transform:scale(0);-webkit-transform:scale(0)}100%{transform:scale(1);-webkit-transform:scale(1)}}";
	document.body.appendChild(style);
}(window);
</script>
</body>
</html>