<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=setting&amp;act=base_setting">系统设置</a></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=base_information">基本信息</a></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=email_setting">Email</a></li>
<!--         <li><a class="btn1" href="index.php?app=setting&amp;act=captcha_setting">验证码</a></li> -->
        <li><a class="btn1" href="index.php?app=setting&amp;act=store_setting">开店设置</a></li>
        <li><span>信用评价</span></li>
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
                    升一级所需信用积分:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="upgrade_required" type="text" name="upgrade_required" value="<?php echo $this->_var['setting']['upgrade_required']; ?>"/></td>
            </tr>
            <tr>
            <th></th>
            <td class="ptb20">
            </td>
        </tr>
        </table>
    </form>
</div>
<?php echo $this->fetch('footer.html'); ?>
