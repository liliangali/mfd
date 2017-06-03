<?php echo $this->fetch('header.html'); ?>

<script type="text/javascript">
//<!CDATA[
$(function(){
    $(".show_image").mouseover(function(){
        $(this).next("div").show();
    });
    $(".show_image").mouseout(function(){
        $(this).next("div").hide();
    });
});
//]]>
</script>

<div id="rightTop">
  <ul class="subnav">
    <li><a class="btn1" href="index.php?app=setting&amp;act=base_setting">系统设置</a></li>
    <li><span>基本信息</span></li>
    <li><a class="btn1" href="index.php?app=setting&amp;act=email_setting">Email</a></li>
<!--     <li><a class="btn1" href="index.php?app=setting&amp;act=captcha_setting">验证码</a></li> -->
    <li><a class="btn1" href="index.php?app=setting&amp;act=store_setting">开店设置</a></li>
    <li><a class="btn1" href="index.php?app=setting&amp;act=credit_setting">信用评价</a></li>
    <li><a class="btn1" href="index.php?app=setting&amp;act=subdomain_setting">二级域名</a></li>
<!-- 	<li><a class="btn1" href="index.php?app=setting&amp;act=appupdate_setting">APP相关设置</a></li>
	<li><a class="btn1" href="index.php?app=setting&amp;act=appupdate_linking">APP下载链接</a></li> -->
    <!-- <li><a class="btn1" href="index.php?app=setting&amp;act=creditscore_setting">订单交易额设定</a></li> -->
	<!-- <li><a class="btn1" href="index.php?app=setting&amp;act=profit_setting">抵用券红包设定</a></li> -->
  </ul>
</div>
<div class="info">
  <form method="post" enctype="multipart/form-data">
    <table class="infoTable">
      <tr>
        <th class="paddingT15"> <label for="site_name">网站名称 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="site_name" type="text" name="site_name" value="<?php echo $this->_var['setting']['site_name']; ?>" class="infoTableInput"/>        </td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_title">网站标题 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="site_title" type="text" name="site_title" value="<?php echo $this->_var['setting']['site_title']; ?>" class="infoTableInput"/>        </td>
      </tr>
      <tr>
        <th class="paddingT15" valign="top"> <label for="site_description">网站描述 :</label></th>
        <td class="paddingT15 wordSpacing5"><textarea name="site_description" id="site_description"><?php echo $this->_var['setting']['site_description']; ?></textarea>        </td>
      </tr>
      <tr>
        <th class="paddingT15">网站关键字 :</th>
        <td class="paddingT15 wordSpacing5"><input id="site_keywords" type="text" name="site_keywords" value="<?php echo $this->_var['setting']['site_keywords']; ?>" class="infoTableInput"/></td>
      </tr>
	<tr>
        <th class="paddingT15"><label for="hot_search">服务电话:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search" type="text" name="service_phone" value="<?php echo $this->_var['setting']['service_phone']; ?>" class="infoTableInput"/>
        <label class="field_notice"></label></td>
      </tr>
<!--      <tr>
        <th class="paddingT15"> <label for="copyright">版权信息:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="copyright" type="text" name="copyright" value="<?php echo $this->_var['setting']['copyright']; ?>" class="infoTableInput"/>        </td>
      </tr> -->
      <tr>
        <th class="paddingT15"> <label for="site_logo">网站Logo :</label></th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableFile" id="site_logo" type="file" name="site_logo" />
          <?php if ($this->_var['setting']['site_logo']): ?>
          <img class="show_image" src="<?php echo $this->res_base . "/" . 'style/images/right.gif'; ?>" />
          <div style="position:absolute; display:none"><img src="<?php echo $this->_var['site_url']; ?>/<?php echo $this->_var['setting']['site_logo']; ?>?<?php echo $this->_var['random_number']; ?>" /></div>
          <?php endif; ?></td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="icp_number">ICP证书号 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="icp_number" type="text" name="icp_number" value="<?php echo $this->_var['setting']['icp_number']; ?>" class="infoTableInput"/>        </td>
      </tr>
<!--      <tr>
        <th class="paddingT15"> <label for="site_region">网站所在地:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="site_region" type="text" name="site_region" value="<?php echo $this->_var['setting']['site_region']; ?>" class="infoTableInput"/>        </td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_address">网站详细地址:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="site_address" type="text" name="site_address" value="<?php echo $this->_var['setting']['site_address']; ?>" class="infoTableInput"/>        </td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_postcode">邮编:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="site_postcode" type="text" name="site_postcode" value="<?php echo $this->_var['setting']['site_postcode']; ?>" class="infoTableInput"/>        </td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_phone_tel">电话:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="site_phone_tel" type="text" name="site_phone_tel" value="<?php echo $this->_var['setting']['site_phone_tel']; ?>" class="infoTableInput"/>        </td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_email">Email:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="site_email" type="text" name="site_email" value="<?php echo $this->_var['setting']['site_email']; ?>" class="infoTableInput"/>        </td>
      </tr>-->
      <tr>
        <th class="paddingT15">网站状态 :</th>
        <td class="paddingT15"><input id="site_status0" type="radio" name="site_status" <?php if ($this->_var['setting']['site_status'] == 0): ?>checked<?php endif; ?> value="0" />
          <label for="site_status0">关闭</label>
          <input id="site_status1" type="radio" name="site_status" <?php if ($this->_var['setting']['site_status'] == 1): ?>checked<?php endif; ?> value="1" />
          <label for="site_status1">开启</label>        </td>
      </tr>
      <tr>
        <th class="paddingT15" valign="top"> <label for="closed_reason">关闭原因 :</label></th>
        <td class="paddingT15 wordSpacing5"><textarea name="closed_reason" id="closed_reason"><?php echo $this->_var['setting']['closed_reason']; ?></textarea>        </td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="hot_search">热门搜索 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search" type="text" name="hot_search" value="<?php echo $this->_var['setting']['hot_search']; ?>" class="infoTableInput"/>
        <label class="field_notice">多个关键词之间请用逗号分隔</label></td>
      </tr>

      <tr>
        <th class="paddingT15">是否开启图片验证 :</th>
        <td class="paddingT15"><input id="if_photo_disabled" type="radio" name="if_photo" <?php if (! $this->_var['setting']['if_photo']): ?>checked<?php endif; ?> value="0" />
          <label for="if_photo_disabled">是</label>
          <input type="radio" id="if_photo_enable" name="if_photo" <?php if ($this->_var['setting']['if_photo']): ?>checked<?php endif; ?> value="1" />
          <label for="if_photo_enable">否</label>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
      </tr>
         <tr>
        <th class="paddingT15"> <label for="hot_search">App推荐安装-奖励金额 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search" type="text" name="bonus" value="<?php echo $this->_var['setting']['bonus']; ?>" class="infoTableInput"/>
        <label class="field_notice"></label></td>
      </tr>
         <tr>
        <th class="paddingT15"><label for="hot_search">App推荐安装-下单提成比例:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search" type="text" name="commission" value="<?php echo $this->_var['setting']['commission']; ?>" class="infoTableInput"/>
        <label class="field_notice"></label></td>
      </tr>
	  <!-- <tr>
        <th class="paddingT15"><label for="hot_search">成为创业者后，初始信用积分:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search" type="text" name="initial" value="<?php echo $this->_var['setting']['initial']; ?>" class="infoTableInput"/>
        <label class="field_notice"></label></td>
      </tr>
         <tr>
        <th class="paddingT15"> <label for="hot_search">量体费用 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search" type="text" name="bodycost" value="<?php echo $this->_var['setting']['bodycost']; ?>" class="infoTableInput"/>
        <label class="field_notice"></label></td>
      </tr>-->

	  		<tr>
        <th class="paddingT15"> <label for="hot_search">延期收货操作时间 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search" type="text" name="ship_days" value="<?php echo $this->_var['setting']['ship_days']; ?>" class="infoTableInput"/>
        <label class="field_notice">系统计算在已发货后的第几天，可以出现 延期收货 按钮</label></td>
      </tr>
	  
	<tr>
        <th class="paddingT15"> <label for="hot_search">延长收获时间 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search" type="text" name="delay_days" value="<?php echo $this->_var['setting']['delay_days']; ?>" class="infoTableInput"/>
        <label class="field_notice">每点击1次 延期收货，自动延期天数</label></td>
      </tr>
      <tr>
        <th></th>
        <td class="ptb20"><input class="tijia" type="submit" name="Submit" value="提交" />
          <input class="congzi" type="reset" name="Submit2" value="重置" />        </td>
      </tr>
    </table>
  </form>
</div>
<?php echo $this->fetch('footer.html'); ?>