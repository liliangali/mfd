<?php echo $this->fetch('member.header.html'); ?>
<script type="text/javascript" src="/public/static/pc/js/jquery-1.9.1.min.js"></script>

<style>
@charset "utf-8";
/*--全局/S--*/
body {margin: 0; padding: 0; background: #fff; color: #666; font-size: 12px; line-height: none; font-family: 微软雅黑, 'Arial, Helvetica, sans-serif'; outline:0}
div, form, ul, ol, li, span, p, dl, dt, dd, img, i, em, input, textarea {margin: 0; padding: 0; outline: 0;border:0;}
ul, ol, li {list-style: none}
img {border: 0;}
em, i {font-style: normal;}
a {color: #535353; text-decoration: none; outline: 0}
a:hover {color: #e66800; text-decoration: none;}
.fl {float: left;}
.fr {float: right;}
/*--全局/E--*/

/*--颜色/S--*/
.cRed{color:#e66800;}
/*--颜色/E--*/

/*--布局/S--*/
.w {width: 960px; margin: 0 auto; clear: both;}
.main {min-height:720px;}
.AreaL{width:580px; margin: 0 auto;}
/*--布局/E--*/

/*--带尖角号的连接按钮/S--*/
.btn-angle .link {float: left;}
.btn-angle .angle {float: left; width: 9px; height: 5px; background: url(static/img/bg.png) no-repeat -162px -158px; margin: 5px 0 0 5px; transition: all ease .3s; -moz-transition: all ease .3s; -ms-transition: all ease .3s; -o-transition: all ease .3s; -webkit-transition: all ease .3s;}
.btn-angle:hover .angle, .btn-angle-hover .angle {transform: rotate(180deg); -moz-transform: rotate(180deg); -ms-transform: rotate(180deg); -o-transform: rotate(180deg); -webkit-transform: rotate(180deg);}
/*--带尖角号的连接按钮/E--*/

/*--模拟select/S--*/
.select {display: none; position: absolute; z-index: 2; width: 75px; background: #fff; border: solid 1px #929292; text-align: center; line-height: 30px;}
.select .list {width: 100px; max-height: 150px; overflow-y: auto; overflow-x: hidden; position: absolute; left: 0; top: 28px; text-align: center; z-index: 2; background: #fff; border: solid 1px #000;}
.select span {display: block; cursor: pointer;}
.select span:hover {background: #f5f5f5;}
.select .angle {position: absolute; width: 12px; height: 6px; left: 50%; top: -6px; margin-left: -6px;}
.select .angle i {width: 0px; height: 0px; overflow: hidden; font-size: 0; border-style: solid; border-color: transparent transparent #000; border-width: 0 6px 6px; position: absolute; left: 0; top: 0;}
.select .angle em {width: 0px; height: 0px; overflow: hidden; font-size: 0; border-style: solid; border-color: transparent transparent #fff; border-width: 0 6px 6px; position: absolute; left: 0; top: 1px;}
/*--模拟select/E--*/

/*--二级菜单/S--*/
.bd-menu {display: none; width: 100px; padding:5px; background: #fff; border: solid 1px #ddd; position: absolute; left: 50%; top: 41px;}
.bd-menu div {display: none;}
.bd-menu a {display: block; line-height: 28px; border-bottom: dotted 1px #f5f5f5; text-align: center;}
.bd-menu a:last-child {border: 0;}
.bd-menu .angle {position: absolute; left: 50%; top: -5px; margin: 0 0 0 -5px; width: 9px; height: 6px; overflow: hidden; background: url(static/img/bg.png) no-repeat -147px -157px;}
/*--二级菜单/E--*/



.dlzc_nav {float:left; padding-top:34px;}
.dlzc_nav  li {float:left; font-size:18px; line-height:38px; height:38px; overflow:hidden; padding-right:28px;}
.dlzc_nav li a {transition: all 1s ease 0s;}
.gwhwd {float:left; margin:45px 0 0 21px;}
.gwhwd .p1 {float:left; width:20px; height:18px;}
.gwhwd .p1 a {display:block; width:20px; height:18px; background:url(static/img/gwc_slx.png) left top no-repeat;}
.gwhwd .p2 {float:left; width:18px; height:18px; margin-left:18px;}
.gwhwd .p2 a {display:block; width:18px; height:18px; background:url(static/img/gwc_slx.png) -38px top no-repeat;}
/*--头部/E--*/

/*--当前步骤/S--*/
.pwd-step {width: 900px; height: 47px; background: url(static/img/findpw.png) no-repeat; margin: 50px auto;}
.pwd-1 {background-position: 0 0;}
.pwd-2 {background-position: 0 -47px;}
.pwd-3 {background-position: 0 -94px;}
.reg-step {width: 660px; height: 20px; margin: 50px auto; background: #eee; border-radius: 15px; text-align: center; font-size: 12px; color: #333;}
.reg-step p {float: left; width: 220px;}
.reg-step .on {color: #fff; background: #e66800; border-radius: 15px;}
/*--当前步骤/E--*/

/*--基本表单/S--*/
.myForm {width: 400px; margin: 0 auto;}
.myForm .item {position: relative; z-index: 0; width: 400px; height: 52px; margin-bottom: 20px;}
.myForm .txt {width: 298px; height: 50px; padding-left: 99px; line-height:50px9; position: absolute; left: 0; top: 0; background: #fff; border: solid 1px #ddd; z-index: 1; color: #a9a9a9;}
.myForm .txt.cur {color: #333;}
.myForm .txt:focus {border-color: #e66800; color:#333;}
.myForm .item .tit {position: absolute; left: 15px; top: 17px; z-index: 2; font-size: 14px; color: #333;}
.myForm .item .btn-angle {display: block; height: 20px; line-height: 20px;}
.myForm .item .btn-angle .link {color: #333;}
.myForm .item .angle {margin-top: 8px;}
.myForm .sendValidate {position: absolute; right: 9px; top: 9px; width: 129px; height: 32px; line-height: 32px; text-align: center; z-index: 2; color: #333; border: solid 1px #ddd; border-radius: 2px; cursor: pointer; background-color: #f5f5f5; background-image: linear-gradient(#fff, #ebebeb); background-image: -webkit-gradient(#fff, #ebebeb); background-image: -moz-linear-gradient(#fff, #ebebeb); -moz-user-select: none; user-select: none;}
.myForm .sendValidate:hover {background-image: linear-gradient(#ebebeb, #fff); background-image: -webkit-gradient(#ebebeb, #fff); background-image: -moz-linear-gradient(#ebebeb, #fff);}
.myForm .sendValidate.disabled, .myForm .sendValidate.disabled:hover {background: #f8f8f8; cursor: default;}
.myForm .validateCode {width: 148px; padding-right: 150px;}
.myForm .next {width: 400px; height: 46px; background:#e66800; font-size: 16px; color: #fff; cursor: pointer; font-weight: bold; transition: all 1s ease 0s;}
.myForm .next:hover {background:#ce5e02;}
.myForm .next:active {opacity: 0.8;}
.myForm .validateImg {position: absolute; right: 50px; top: 13px; z-index: 2;}
.myForm .refreshCode {position: absolute; right: 14px; top: 14px; z-index: 2; width: 24px; height: 24px; border: solid 1px #ddd; background: url(static/img/bg2.png) no-repeat -106px -56px; border-radius: 2px; cursor: pointer;}
.myForm .refreshCode:hover {border-color: #ccc; box-shadow: 0 0 3px #ddd;}
.myForm .gotoReg{line-height:50px; text-align:center;}
.myForm .gotoReg a{text-decoration:underline;}
.emallBox {display: none;}
.myForm .p {overflow: hidden; padding-bottom: 20px; font-size: 14px; color: #4d4d4d;}
.AreaL .myForm {}
.AreaR .qrcode {margin: 89px 100px 0 0; text-align: center;}
.AreaR .qrcode dt {font-size: 20px; color: #777; line-height: 24px; margin-bottom: 10px;}
.AreaR .qrcode dd {width: 250px; height: 160px; padding-top: 275px; background: url(static/img/qrcode2.png) no-repeat;}
.AreaR .qrcode .android, .AreaR .qrcode .ios {width: 207px; height: 41px; display: block; background: url(static/img/btn2.png) no-repeat 0 -104px; margin: 0 0 10px 21px;}
.AreaR .qrcode .ios:hover{background-position: 0 -186px;}
.AreaR .qrcode .android {background-position: 0 -145px;}
.AreaR .qrcode .android:hover {background-position: 0 -227px;}
.AreaL .regForm {padding-top: 0; margin-top:-10px;}
.AreaR .RegR {width: 350px; height: 475px; overflow: hidden;}
/*--基本表单/E--*/

/*--错误信息/S--*/
#error {height: 60px; line-height: 60px; color: #e66800; font-size: 14px;}
#error .ico {float: left; width: 24px; height: 24px; background: url(static/img/bg2.png) no-repeat -79px -54px; margin: 17px 5px 0 0;}
.myForm .error-css, .myForm .error-css:focus {background-color: #fdf6f1; border-color: #eec6a5; color: #e66800;}
/*--错误信息/E--*/

/*--邮箱手机验证成功、新密码设置成功/S--*/
.verifyOk {padding-top: 50px;}
.verifyOk .txt {padding-left: 240px; font-size: 24px; line-height: 28px; margin-bottom: 20px; color: #000; overflow: hidden;}
.verifyOk .txt2 {padding-left: 375px;}
.verifyOk .txt .ico {width: 28px; height: 28px; vertical-align: middle; background: url(static/img/bg2.png) no-repeat -44px -51px; margin-right: 8px; position: relative;}
.verifyOk .txt p {clear: both; padding: 15px 0 0 40px; font-size: 14px; color: #666;}
.verifyOk .btn {padding: 15px 0 0 280px; overflow: hidden;}
.verifyOk .btn .vCode {width: 200px; height: 34px; line-height: 34px; padding: 0 3px; border: solid 1px #ddd; color: #999; margin-right: 8px;}
.verifyOk .btn .vCode.cur {color: #2b2b2b;}
.verifyOk .sendValidate {width: 129px; height: 34px; line-height: 32px; text-align: center; z-index: 2; color: #333; border: solid 1px #ddd; border-radius: 2px; cursor: pointer; background-color: #f5f5f5; background-image: linear-gradient(#fff, #ebebeb); background-image: -webkit-gradient(#fff, #ebebeb); background-image: -moz-linear-gradient(#fff, #ebebeb); -moz-user-select: none; user-select: none;}
.verifyOk .sendValidate:hover {background-image: linear-gradient(#ebebeb, #fff); background-image: -webkit-gradient(#ebebeb, #fff); background-image: -moz-linear-gradient(#ebebeb, #fff);}
.verifyOk .sendValidate.disabled, .verifyOk .sendValidate.disabled:hover {background: #f8f8f8; cursor: default;}
.verifyOk .gotoEmall {margin: 8px 0 0 15px; font-size: 14px;}
.verifyOk .gotoEmall:hover {text-decoration: underline;}
.verifyOk .btn2 {padding: 15px 0 0 406px;}
.verifyOk .btn-red {display: block; width: 154px; height: 40px; background: #e66800; text-align: center; line-height: 40px; color: #fff; padding-left: 3px;}
.verifyOk .btn-red span {display: block; height: 40px; background: #e66800; transition: all 1s ease 0s;}
.verifyOk .btn-red:hover {background: #ce5e02;}
.verifyOk .btn-red:hover span {background: #ce5e02;}
.verifyOk .btn-red:active {opacity: 0.8;}
.verifyOk .btn-grey {margin-left: 10px; display: block; width: 154px; height: 40px; background: url(static/img/btn.jpg) no-repeat 0 -173px; text-align: center; line-height: 40px; color: #454545; padding-left: 3px;}
.verifyOk .btn-grey span {display: block; height: 40px; background: url(static/img/btn.jpg) no-repeat right -173px}
.verifyOk .btn-grey:hover {background-position: 0 -213px;}
.verifyOk .btn-grey:hover span {background-position: right -213px;}
.verifyOk .btn-grey:active {opacity: 0.8;}
.verifyOk .nextBox {clear: both; padding-top: 20px;}
.verifyOk .nextBox a {width: 206px;}
/*--邮箱手机验证成功、新密码设置成功/E--*/


</style>
<div class="main w">
  <div class="reg-step reg-1"><p class="on">1、填写账号信息</p><p>2、验证手机/邮箱</p><p>3、注册成功</p></div>
  <div class="AreaL">
    <div class="myForm regForm">
      <div id="error"></div>
        <div class="item">
          <div class="tit"> 帐号： </div>
          <input type="text" class="txt" data-type="phone|emall" data-placeholder="手机/邮箱" id="userName">
        </div>
        <div class="item">
          <div class="tit"> 密码： </div>
          <input type="password" data-type="pwd" class="txt" id="userPwd">
        </div>
        <div class="item">
          <div class="tit"> 确认密码： </div>
          <input type="password" data-type="pwd" class="txt">
        </div>
        <div class="item">
          <div class="tit"> 验证码： </div>
          <input type="text" data-type="vcode" data-placeholder="请输入右侧验证码" class="txt validateCode" id="vCode" onKeyUp="enter(event,reg,4,this)" maxlength="4">
          <img src="ajax-getAuthCode-regcode.html" width="65" height="25" class="validateImg" onClick="upCode('regcode')"> <span class="refreshCode" onClick="upCode('regcode')"></span>
         </div>
         
             <?php if ($this->_var['code'] == '' || $this->_var['type'] == ''): ?>
         <!--<div class="item">-->
          <!--<div class="tit"> 邀请码： </div>-->
          <!--<input type="text" data-type="invite" id="invite" class="txt">-->
        <!--</div>-->
            <?php endif; ?>
        <p class="p">
          <label class="fl">
            <input type="checkbox" value="1" id="protocol">
            我已阅读并同意 <a href="#" id="protocolmg">《麦富迪用户注册协议》</a></label>
          </p>
          <input type="hidden" value="1" name="step1" id="step1">
        <input type="button" value="下一步" class="next" onClick="reg()">
    </div>
  </div>
</div>
<!--底部开始-->
<?php echo $this->fetch('footer.html'); ?>
<!--底部结束-->
<script type="text/javascript" src="/public/static/pc/js/findpw.js"></script>
<script>
$(document).on('click','#protocolmg',function(){
	
	use('/public/static/expand/layer/layer.min.js',function(){
		$.layer({
			type: 2,
			shadeClose: true,
			title: '用户注册协议',
			shade: [0.3, '#000'],
			area: ['800px','600px'],
			iframe: {src: '/ajax-provision.html'}
		}); 	
	});
	return false
})
</script>
</body>
</html>
