<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=setting&amp;act=base_setting">系统设置</a></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=base_information">基本信息</a></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=email_setting">Email</a></li>
<!--         <li><a class="btn1" href="index.php?app=setting&amp;act=captcha_setting">验证码</a></li> -->
        <li><span>开店设置</span></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=credit_setting">信用评价</a></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=subdomain_setting">二级域名</a></li>
<!-- 		<li><a class="btn1" href="index.php?app=setting&amp;act=appupdate_setting">APP相关设置</a></li>
		<li><a class="btn1" href="index.php?app=setting&amp;act=appupdate_linking">APP下载链接</a></li> -->
        <!-- <li><a class="btn1" href="index.php?app=setting&amp;act=creditscore_setting">订单交易额设定</a></li> -->
         <!-- <li><a class="btn1" href="index.php?app=setting&amp;act=profit_setting">抵用券红包设定</a></li> -->
        </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    允许申请开店:</th>
                <td class="paddingT15 wordSpacing5">
                    <input id="store_allow0" type="radio" name="store_allow" <?php if ($this->_var['setting']['store_allow'] == 0): ?>checked<?php endif; ?> value="0" /> <label for="store_allow0">关闭</label>
                    <input id="store_allow1" type="radio" name="store_allow" <?php if ($this->_var['setting']['store_allow'] == 1): ?>checked<?php endif; ?> value="1" /> <label for="store_allow1">开启</label>
                </td>
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
