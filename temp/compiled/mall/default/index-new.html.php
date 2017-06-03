<?php echo $this->fetch('header-new.html'); ?>

<!--<editmode></editmode>-->
<link href="/public/static/pc/css/index.css" rel="stylesheet">
<script src="/public/global/jquery-1.8.3.min.js"></script>
<script src="/public/global/idangerous.swiper.js"></script>
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/static/pc/js/category.js"></script>
<script src="/public/global/vivus/vivus.js"></script>


<div class="device">
  <a class="arrow-left" href="#"><i></i></a> 
  <a class="arrow-right" href="#"><i></i></a>
  <div class="swiper-container">
    <div class="swiper-wrapper">
      <?php $_from = $this->_var['indexAds']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ad');if (count($_from)):
    foreach ($_from AS $this->_var['ad']):
?>
       <div class="swiper-slide" style="background:url(<?php echo $this->_var['ad']['img']; ?>) no-repeat center center;"><a href="<?php echo $this->_var['ad']['link_url']; ?>" target="_blank"></a></div>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
    </div>
  </div>
  <div class="pagination"></div>
</div>


  <script>
  var mySwiper = new Swiper('.swiper-container',{
    pagination: '.pagination',
    loop:true,
	autoplay: 2500,
    autoplayDisableOnInteraction: false,
    grabCursor: true,
    paginationClickable: true
  })
  $('.arrow-left').on('click', function(e){
    e.preventDefault()
    mySwiper.swipePrev()
  })
  $('.arrow-right').on('click', function(e){
    e.preventDefault()
    mySwiper.swipeNext()
  })
  </script>



    
<div class="main">
	<ul class="btnList clearfix">
		<li>
			<a href="fdiy-1.html" target="_blank">
				<div class="svgbox svg1">
                    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="80px" height="80px" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 140 122.331"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" id="mysvg1">
                     <defs>
                      <style type="text/css">
                       <![CDATA[
                        .str2 {stroke:#E66800;stroke-width:0.802697}
                        .str1 {stroke:#E66800;stroke-width:0.802697;stroke-linecap:round;stroke-linejoin:round}
                        .str3 {stroke:#E66800;stroke-width:3.21079}
                        .str0 {stroke:#E66800;stroke-width:3.21079;stroke-linecap:round;stroke-linejoin:round}
                        .fil0 {fill:none}
                        .fil1 {fill:#E66800}
                       ]]>
                      </style>
                     </defs>
                     <g id="图层_x0020_1">
                      <metadata id="CorelCorpID_0Corel-Layer"/>
                      <path class="fil0 str0" d="M58.2012 118.867c0,0 3.81602,-34.6107 2.99807,-36.2466 -0.817146,-1.63509 -0.370043,-3.22443 -2.47793,-2.47793 -2.10708,0.746508 -21.0138,13.2606 -34.2077,7.95553 -13.1939,-5.30422 -21.2193,-21.0828 -21.2193,-21.0828l23.6675 -18.7711c0,0 -0.951999,-17.4105 0.816343,-20.9472 1.76834,-3.53668 14.9623,-22.8512 37.5413,-22.5791 22.5799,0.272114 35.6614,5.56991 48.039,31.55 12.3776,25.9793 -11.3196,39.0103 -19.7512,34.1427 -8.43153,-4.86836 -14.8218,-11.8285 -18.7663,-23.3906 -3.94445,-11.5612 -1.63188,-20.9472 -1.63188,-20.9472"/>
                      <path class="fil0 str0" d="M105.085 69.4975c0,0 3.62498,15.7441 11.2426,26.3533 7.61679,10.61 17.9098,20.5691 20.248,22.9026"/>
                      <circle class="fil1 str1" cx="43.0549" cy="41.1865" r="2.65687"/>
                      <circle class="fil1 str2" cx="27.1956" cy="104.099" r="1.31611"/>
                      <circle class="fil1 str2" cx="14.5333" cy="108.98" r="1.31611"/>
                      <circle class="fil1 str2" cx="4.3463" cy="115.323" r="1.31611"/>
                      <circle class="fil1 str2" cx="35.9219" cy="116.524" r="1.31611"/>
                      <circle class="fil1 str2" cx="20.8972" cy="118.926" r="1.31611"/>
                      <path class="fil0 str2" d="M19.642 86.1157c0,0 -0.143683,0.576336 -1.72901,5.90865 -1.58533,5.33151 2.44983,7.49318 5.76417,6.77316 3.31434,-0.720822 9.36667,-10.232 9.36667,-10.232"/>
                     </g>
                    </svg>
				</div>
				<strong>我要定制</strong> <span class="grey">每一只爱宠都是独一无二的，为每一只爱宠打造一对一专属美食，您只需轻点鼠标即可尽享麦富迪高端定制服务</span> </a>
		</li>
		<li>
			<a href="gallery.html" target="_blank">
				<div class="svgbox svg2">
                    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="80px" height="80px" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 140 122.331"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" id="mysvg2">
                     <defs>
                      <style type="text/css">
                       <![CDATA[
                        .str0 {stroke:#E66800;stroke-width:3.21079}
                        .str1 {stroke:#E66800;stroke-width:3.21079;stroke-linecap:round;stroke-linejoin:round}
                        .fil0 {fill:none}
                       ]]>
                      </style>
                     </defs>
                     <g id="图层_x0020_1">
                      <metadata id="CorelCorpID_0Corel-Layer"/>
                      <g id="_109071528">
                       <path id="_109072824" class="fil0 str0" d="M51.2578 69.1074c0.33633,3.10002 -1.27549,7.36876 -3.93643,9.97913 -4.09456,4.0167 -10.3757,4.40841 -14.0954,0.504094 -3.7205,-3.90352 -3.44277,-9.60748 0.519345,-13.7687 0.654198,-0.687109 1.50024,-1.53395 2.36796,-2.17772 -0.579547,-0.318671 -1.3983,-0.801894 -2.3206,-1.62787 -4.882,-4.37069 -2.86884,-12.5526 3.21239,-15.5362 5.04575,-2.47552 9.36266,-0.725638 12.1665,2.48515 1.20726,1.38305 2.93787,4.18366 2.37117,8.35287 5.23037,1.18799 26.8021,0.71119 31.062,-0.319473"/>
                       <path id="_109072752" class="fil0 str0" d="M80.2464 59.0336c-0.33633,-3.10002 1.27549,-7.36876 3.93562,-9.97913 4.09536,-4.0167 10.3765,-4.40841 14.0962,-0.504896 3.7205,3.90432 3.44277,9.60748 -0.519345,13.7695 -0.654198,0.687109 -1.50024,1.53395 -2.36796,2.17772 0.579547,0.318671 1.3983,0.801894 2.3206,1.62787 4.882,4.37069 2.86884,12.5526 -3.21239,15.5362 -5.04575,2.47552 -9.36266,0.725638 -12.1665,-2.48595 -1.20726,-1.38224 -2.93787,-4.18285 -2.37117,-8.35206 -5.23037,-1.18799 -26.8029,-0.71119 -31.062,0.319473"/>
                      </g>
                      <polygon class="fil0 str1" points="20.6213,5.61085 117.228,5.61085 112.891,117.651 14.121,117.651 "/>
                      <polyline class="fil0 str1" points="117.464,5.88778 127.672,100.405 113.126,117.651 "/>
                     </g>
                    </svg>
				</div>
				<strong>麦富迪尚品</strong> <span class="grey">懒癌晚期? 养宠新手? 不够专业？统统不必担心。凭借麦富迪多年的研发积累和品牌保障我们有足够多的产品供您选择</span> </a>
		</li>
		<li>
			<a href="article.html" target="_blank">
				<div class="svgbox svg3">
                    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="80px" height="80px" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
viewBox="0 0 333 299.702"
 xmlns:xlink="http://www.w3.org/1999/xlink" id="mysvg3">
 <defs>
  <style type="text/css">
   <![CDATA[

    .str000 {stroke:#E66800;stroke-width:8;stroke-linecap:round;stroke-linejoin:round}
   .fil0 {fill:none}
   ]]>
  </style>
 </defs>
 <g id="图层_x0020_1">
  <metadata id="CorelCorpID_0Corel-Layer"/>
  <path class="fil0 str000" d="M41.565 268.314c-2.32053,0 -4.64106,0 -6.95962,0 -14.7118,0 -21.7166,-10.7 -21.7166,-25.398 0,-68.9374 0,-133.718 0,-202.653 0,-15.5495 9.40995,-25.5022 26.161,-25.5022 90.3728,0 169.629,0 260.004,0 14.7845,0 21.7796,12.8337 21.8582,25.0558 0,64.5442 0,139.141 0,203.687 0,17.6301 -6.43259,24.81 -27.5278,24.81l-86.3748 0"/>
  <circle class="fil0 str000" cx="122.583" cy="104.576" r="34.7338"/>
  <path class="fil0 str000" d="M91.5193 154.655c3.22908,0 6.45815,0 9.68723,0 7.83671,14.1847 15.6734,28.3695 23.5101,42.5542 6.89669,-14.1847 13.7934,-28.3695 20.6901,-42.5542l11.5673 0c11.9999,0 21.8641,9.82489 21.8169,21.8189 -0.112093,28.1158 -0.226153,52.5757 -0.338247,80.6915 0,7.13268 -11.8976,14.8691 -21.2309,15.7835l-0.0039331 10.5446 -69.1852 0 0.00589965 -10.5859c-9.01664,-2.41689 -18.047,-9.86815 -18.0647,-15.5082l-0.271384 -80.9255c-0.039331,-11.9999 9.81702,-21.8189 21.8169,-21.8189z"/>
  <line class="fil0 str000" x1="178.903" y1="255.386" x2="247.133" y2= "169.003" />
  <line class="fil0 str000" x1="204.132" y1="77.1144" x2="281.813" y2= "77.1144" />
  <line class="fil0 str000" x1="217.721" y1="132.603" x2="279.069" y2= "132.603" />
 </g>
</svg>

				</div>
				<strong>麦富迪讲堂</strong> <span class="grey">麦富迪宠物营养师结合实践经验和大数据分析，定期发布专业知识，协助您解决养宠过程中遇到的烦恼，带给您更快乐的生活体验</span> </a>
		</li>
		<li>
			<a href="professor-online.html" target="_blank">
				<div class="svgbox svg4">
                    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="80px" height="80px" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
                    viewBox="0 0 300 259.616"
                     xmlns:xlink="http://www.w3.org/1999/xlink" id="mysvg4">
                     <defs>
                      <style type="text/css">
                       <![CDATA[
                        .stryml {stroke:#E66800;stroke-width:8}
                        .str02 {stroke:#E66800;stroke-width:8;stroke-linecap:round;stroke-linejoin:round}
                        .fil02 {fill:none}
                       ]]>
                      </style>
                     </defs>
                     <g id="图层_x0020_1">
                      <metadata id="CorelCorpID_0Corel-Layer"/>
                      <circle class="fil02 str02" cx="148.646" cy="109.629" r="62.9476"/>
                      <path class="fil02 str02" d="M270.27 251.627c-12.7883,-48.5333 -62.0149,-78.4232 -123.193,-78.5476m0 0c-54.1685,-0.0664373 -104.191,29.7997 -117.422,78.706"/>
                      <path class="fil02 str02" d="M85.9579 84.1845c0,0 -41.1502,-15.6059 -45.5266,-17.1885 1.21802,-0.73592 106.034,-58.8702 107.019,-58.8702 0.986338,0 109.943,51.3509 109.974,51.3645l-45.9201 24.8918"/>
                      <line class="fil02 stryml" x1="252.327" y1="60.6725" x2="252.327" y2= "128.37" />
                      <circle class="fil02 stryml" cx="252.075" cy="138.457" r="10.2688"/>
                     </g>
                    </svg>
				</div>
				<strong>专家在线</strong> <span class="grey">我们组建了一支来自中国农业大学、南京农业大学等国内一流学府的专家团队，24小时为您在线服务，给您的爱宠独一无二的关爱</span> </a>
		</li>
	</ul>
	<?php if ($this->_var['columns']): ?>
	<?php $_from = $this->_var['columns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'adsLocation');if (count($_from)):
    foreach ($_from AS $this->_var['adsLocation']):
?>
	<div class="sysdbt">
		<p></p>
		<?php if ($this->_var['adsLocation']['title_switch']): ?><h1><?php echo $this->_var['adsLocation']['title']; ?></h1><?php endif; ?>
		<?php if ($this->_var['adsLocation']['subhead_switch']): ?><a href="<?php echo $this->_var['adsLocation']['subhead_link']; ?>"><h2><?php echo $this->_var['adsLocation']['subhead']; ?></h2></a><?php endif; ?>
	</div>
	
			<div class="rmdzqz">
			<?php $_from = $this->_var['adsLocation']['rc']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'content');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['content']):
        $this->_foreach['foo']['iteration']++;
?>
			     <div class="div_<?php echo $this->_foreach['foo']['iteration']; ?>">
			     <?php if ($this->_var['content']['is_show'] && $this->_var['key'] < 6): ?>
                <a href="<?php echo $this->_var['content']['link_url']; ?>" <?php if ($this->_var['content']['is_blank']): ?>target="_blank"<?php endif; ?>>
                    <h1><?php echo $this->_var['content']['title']; ?></h1>
                    <h2><?php echo $this->_var['content']['intro']; ?></h2>
                    <img src="<?php echo $this->_var['content']['img']; ?>" class="dog_1" />
                </a>
                <?php else: ?>
                <a href="<?php echo $this->_var['content']['link_url']; ?>" <?php if ($this->_var['content']['is_blank']): ?>target="_blank"<?php endif; ?>>
                    <h3><?php echo $this->_var['content']['title']; ?></h3>
                </a>
                <?php endif; ?>
            </div>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</div>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      <?php endif; ?>
      
	<dl class="appTit">
		<dt>&bull; 麦富迪在线产品，给您全方位的体验 &bull;</dt>
		<dd> <span>Web平台</span> <span>iPhone APP</span> <span>Andoird APP</span> <span>iPad</span> <span>手机版</span> <span>麦富迪微信</span> </dd>
	</dl>
	<div class="appShow"><img src="/public/static/pc/images/b_pic_2.png" width="222" height="411" class="pic2"> <img src="/public/static/pc/images/b_pic_3.png" width="538" height="403" class="pic3"><img src="/public/static/pc/images/b_pic_4.png" width="222" height="411" class="pic4"><img src="/public/static/pc/images/b_pic_11.png" width="170" height="266" class="pic5">
		<!--<a href="down.html" target="_blank"><img src="/public/static/pc/images/b_pic_5.png" width="170" height="305" class="pic5"></a>-->
	</div>
</div>
<div class="appshadow"></div>
<ul class="toolbar">
	<li class="toolbar-cart"><a href="cart.html"><span class="tab-text">购物车</span><i class="tab-ico"></i></a></li>
	<li class="toolbar-follow"><a href="member.html"><span class="tab-text">账户中心</span><i class="tab-ico"></i></a></li>
	<li class="toolbar-app"><a href="down.html"><span class="tab-text">APP下载</span><i class="tab-ico"></i></a></li>
	<li class="toolbar-help" style="display:none;"><a href="help.html"><span class="tab-text">帮助中心</span><i class="tab-ico"></i></a></li>
</ul>
<ul class="toolbar toolbar-2">
    <li class="toolbar-kefu"><a href="javascript:void(0);"  ><span class="tab-text">在线客服</span><i class="tab-ico"></i></a></li>
	<li class="toolbar-fankui"><a href="javascript:void(0)" id="feedback"><span class="tab-text">意见反馈</span><i class="tab-ico"></i></a></li>
	<li class="toolbar-top"><a href="javascript:void(0)"><span class="tab-text">返回顶部</span><i class="tab-ico"></i></a></li>
</ul>

<?php echo $this->fetch('footer-new.html'); ?>

<script>
	cotteFn.index();
</script>

</body>
</html>