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
    <?php if ($this->_var['setting']['sitemap_enabled']): ?>
    $('#sitemap_frequency_setting').show();
    <?php endif; ?>
    $('#sitemap_disabled').click(function(){
        $('#sitemap_frequency_setting').hide();
    });
    $('#sitemap_enabled').click(function(){
        $('#sitemap_frequency_setting').show();
    });

    <?php if ($this->_var['setting']['session_type'] == 'memcached'): ?>
    $('#session_memcached').show();
    <?php endif; ?>
    $('#mysql').click(function(){
        $('#session_memcached').hide();
    });
    $('#memcached').click(function(){
        $('#session_memcached').show();
    });

    <?php if ($this->_var['setting']['cache_server'] == 'memcached'): ?>
    $('#cache_memcached').show();
    <?php endif; ?>
    $('#c_default').click(function(){
        $('#cache_memcached').hide();
    });
    $('#c_memcached').click(function(){
        $('#cache_memcached').show();
    });
});
//]]>
</script>

<div id="rightTop">
  <ul class="subnav">
    <li><span>系统设置</span></li>
    <li><a class="btn1" href="index.php?app=setting&amp;act=base_information">基本信息</a></li>
    <li><a class="btn1" href="index.php?app=setting&amp;act=email_setting">Email</a></li>
<!--     <li><a class="btn1" href="index.php?app=setting&amp;act=captcha_setting">验证码</a></li> -->
    <li><a class="btn1" href="index.php?app=setting&amp;act=store_setting">开店设置</a></li>
    <li><a class="btn1" href="index.php?app=setting&amp;act=credit_setting">信用评价</a></li>
    <li><a class="btn1" href="index.php?app=setting&amp;act=subdomain_setting">二级域名</a></li>
<!-- 	<li><a class="btn1" href="index.php?app=setting&amp;act=appupdate_setting">APP相关设置</a></li>
	<li><a class="btn1" href="index.php?app=setting&amp;act=appupdate_linking">APP下载链接</a></li> -->
  <!--   <li><a class="btn1" href="index.php?app=setting&amp;act=creditscore_setting">订单交易额设定</a></li> -->
	<!-- <li><a class="btn1" href="index.php?app=setting&amp;act=profit_setting">抵用券红包设定</a></li> -->
  </ul>
</div>
<div class="info">
  <form method="post" enctype="multipart/form-data">
    <table class="infoTable">
      <tr>
        <th class="paddingT15"><label for="time_zone"> 时区设置 :</label></th>
        <td class="paddingT15 wordSpacing5"><select id="time_zone" name="time_zone">
                <?php echo $this->html_options(array('options'=>$this->_var['time_zone'],'selected'=>$this->_var['setting']['time_zone'])); ?>
          </select>
          <span class="grey">设置系统使用的时区，中国为+8</span>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"><label for="time_format_simple"> 时间格式（简单） :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="time_format_simple" type="text" name="time_format_simple" value="<?php echo $this->_var['setting']['time_format_simple']; ?>" class="infoTableInput"/></td>
      </tr>
      <tr>
        <th class="paddingT15"><label for="time_format_complete"> 时间格式（完整） :</label></th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableInput" id="time_format_complete" type="text" name="time_format_complete" value="<?php echo $this->_var['setting']['time_format_complete']; ?>"/></td>
      </tr>
      <tr>
        <th class="paddingT15"><label for="price_format"> 金额格式 :</label></th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableInput" id="price_format" type="text" name="price_format" value="<?php echo $this->_var['setting']['price_format']; ?>"/></td>
      </tr>
<!--      <tr>
        <th class="paddingT15"> URL重写:</th>
        <td class="paddingT15 wordSpacing5"><input id="url_rewrite0" type="radio" name="url_rewrite" <?php if ($this->_var['setting']['url_rewrite'] == 0): ?>checked<?php endif; ?> value="0"/>
          <label for="url_rewrite0">关闭</label>
          <input id="url_rewrite1" type="radio" name="url_rewrite" <?php if ($this->_var['setting']['url_rewrite'] == 1): ?>checked<?php endif; ?> value="1" />
          <label for="url_rewrite1">开启</label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"><label for="cache_life"> 缓存存活时间（秒）:</label></th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableInput" id="cache_life" type="text" name="cache_life" value="<?php echo $this->_var['setting']['cache_life']; ?>"/></td>
      </tr>-->
      <tr>
        <th class="paddingT15"> <label for="default_goods_image">默认商品图片 :</label></th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableFile" id="default_goods_image" type="file" name="default_goods_image" />
          <?php if ($this->_var['setting']['default_goods_image']): ?>
          <img class="show_image" src="<?php echo $this->res_base . "/" . 'style/images/right.gif'; ?>" />
          <div style="position:absolute; display:none"><img src="<?php echo $this->_var['site_url']; ?>/<?php echo $this->_var['setting']['default_goods_image']; ?>?<?php echo $this->_var['random_number']; ?>" /></div>
          <?php endif; ?></td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="default_store_logo">默认供应商标志 :</label></th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableFile" id="default_store_logo" type="file" name="default_store_logo" />
          <?php if ($this->_var['setting']['default_store_logo']): ?>
          <img class="show_image" src="<?php echo $this->res_base . "/" . 'style/images/right.gif'; ?>" />
          <div style="position:absolute; display:none"><img src="<?php echo $this->_var['site_url']; ?>/<?php echo $this->_var['setting']['default_store_logo']; ?>?<?php echo $this->_var['random_number']; ?>" /></div>
          <?php endif; ?></td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="default_user_portrait">默认会员头像 :</label></th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableFile" id="default_user_portrait" type="file" name="default_user_portrait" />
          <?php if ($this->_var['setting']['default_user_portrait']): ?>
          <img class="show_image" src="<?php echo $this->res_base . "/" . 'style/images/right.gif'; ?>" />
          <div style="position:absolute; display:none"><img src="<?php echo $this->_var['site_url']; ?>/<?php echo $this->_var['setting']['default_user_portrait']; ?>?<?php echo $this->_var['random_number']; ?>" /></div>
          <?php endif; ?></td>
      </tr>
      <?php if ($this->_var['feed_enabled']): ?>
      <tr>
        <th class="paddingT15"> <label for="default_feed_config">默认个人动态设置 :</label></th>
        <td class="paddingT15 wordSpacing5">
            <?php $_from = $this->_var['feed_items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('_fi', '_fn');if (count($_from)):
    foreach ($_from AS $this->_var['_fi'] => $this->_var['_fn']):
?>
            <input type="checkbox" id="feed_<?php echo $this->_var['_fi']; ?>" name="default_feed_config[<?php echo $this->_var['_fi']; ?>]" value="1" <?php if ($this->_var['default_feed_config'][$this->_var['_fi']]): ?> checked="true"<?php endif; ?> /><label for="feed_<?php echo $this->_var['_fi']; ?>"><?php echo $this->_var['_fn']; ?></label>&nbsp;
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </td>
      </tr>
      <?php endif; ?>
      <tr>
        <th class="paddingT15"> <label for="statistics_code">流量统计代码 :</label></th>
        <td class="paddingT15 wordSpacing5"><textarea name="statistics_code" id="statistics_code"><?php echo htmlspecialchars($this->_var['setting']['statistics_code']); ?></textarea></td>
      </tr>
<!--      <tr>
        <th class="paddingT15">显示商品销售量:</th>
        <td><input id="disaplay_sales_volume0" type="radio" name="disaplay_sales_volume" <?php if ($this->_var['setting']['disaplay_sales_volume'] == 0): ?>checked<?php endif; ?> value="0" />
          <label for="disaplay_sales_volume0">关闭</label>
          <input id="disaplay_sales_volume1" type="radio" name="disaplay_sales_volume" <?php if ($this->_var['setting']['disaplay_sales_volume'] == 1): ?>checked<?php endif; ?> value="1" />
          <label for="disaplay_sales_volume1">开启</label>
        </td>
      </tr>-->
      <tr>
        <th class="paddingT15">允许游客咨询 :</th>
        <td class="paddingT15"><input id="guest_comment_disabled" type="radio" name="guest_comment" <?php if (! $this->_var['setting']['guest_comment']): ?>checked<?php endif; ?> value="0" />
          <label for="guest_comment_disabled">否</label>
          <input type="radio" id="guest_comment_enable" name="guest_comment" <?php if ($this->_var['setting']['guest_comment']): ?>checked<?php endif; ?> value="1" />
          <label for="guest_comment_enable">是</label>&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="grey"></span>
        </td>
      </tr>
            <tr>
        <th class="paddingT15">是否开启评论审核 :</th>
        <td class="paddingT15"><input id="check_comment_disabled" type="radio" name="check_comment" <?php if (! $this->_var['setting']['check_comment']): ?>checked<?php endif; ?> value="0" />
          <label for="guest_comment_disabled">否</label>
          <input type="radio" id="check_comment_enable" name="check_comment" <?php if ($this->_var['setting']['check_comment']): ?>checked<?php endif; ?> value="1" />
          <label for="check_comment_enable">是</label>&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="grey">开启后所有评论只有审核通过后，才能显示</span>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">启用伪静态 :</th>
        <td class="paddingT15"><input id="rewrite_disabled" type="radio" name="rewrite_enabled" <?php if (! $this->_var['setting']['rewrite_enabled']): ?>checked<?php endif; ?> value="0" />
          <label for="rewrite_disabled">否</label>
          <input type="radio" id="rewrite_enabled" name="rewrite_enabled" <?php if ($this->_var['setting']['rewrite_enabled']): ?>checked<?php endif; ?> value="1" />
          <label for="rewrite_enabled">是</label>&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="grey">启用伪静态将有助于SEO，及增加URL的可读性</span>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">启用Sitemap :</th>
        <td class="paddingT15"><input id="sitemap_disabled" type="radio" name="sitemap_enabled" <?php if (! $this->_var['setting']['sitemap_enabled']): ?>checked<?php endif; ?> value="0" />
          <label for="sitemap_disabled">否</label>
          <input type="radio" id="sitemap_enabled" name="sitemap_enabled" <?php if ($this->_var['setting']['sitemap_enabled']): ?>checked<?php endif; ?> value="1" />
          <label for="sitemap_enabled">是</label>&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="grey">采用sitemaps.org的协议，支持的搜索引擎有Google, Yahoo!, MSN, 提交地址为:您的网站访问地址/index.php?app=sitemap</span>
        </td>
      </tr>
      <tr id="sitemap_frequency_setting" style="display:none">
        <th class="paddingT15">
            更新周期(小时) :
        </th>
        <td class="paddingT15">
            <input type="text" name="sitemap_frequency" value="<?php echo $this->_var['setting']['sitemap_frequency']; ?>" />
        </td>
      </tr>
      <tr>
        <th class="paddingT15">Session服务 :</th>
        <td class="paddingT15"><input id="mysql" type="radio" name="session_type" <?php if ($this->_var['setting']['session_type'] == 'mysql'): ?>checked<?php endif; ?> value="mysql" />
          <label for="mysql">Mysql</label>
          <input type="radio" id="memcached" name="session_type" <?php if ($this->_var['setting']['session_type'] == 'memcached'): ?>checked<?php endif; ?> value="memcached" />
          <label for="memcached">Memcahced</label>&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="grey">Session服务类型，如果选择Memcached，则您的系统需安装配置好Memcached服务，且您的PHP环境需要支持Memcahce扩展</span>
        </td>
      </tr>
      <tr id="session_memcached" style="display:none">
        <th class="paddingT15">
            Memcached服务(for session) :
        </th>
        <td class="paddingT15">
            <input type="text" name="session_memcached" value="<?php echo $this->_var['setting']['session_memcached']; ?>" />
            <span class="grey">存储Session数据的Memcached服务器(如127.0.0.1:11211)</span>
        </td>
      </tr>


      <tr>
        <th class="paddingT15">缓存服务 :</th>
        <td class="paddingT15"><input id="c_default" type="radio" name="cache_server" <?php if ($this->_var['setting']['cache_server'] == 'default'): ?>checked<?php endif; ?> value="default" />
          <label for="c_default">File</label>
          <input type="radio" id="c_memcached" name="cache_server" <?php if ($this->_var['setting']['cache_server'] == 'memcached'): ?>checked<?php endif; ?> value="memcached" />
          <label for="c_memcached">Memcahce</label>&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="grey">缓存服务类型，如果选择Memcached，则您的系统需安装配置好Memcached服务，且您的PHP环境需要支持Memcahce扩展</span>
        </td>
      </tr>
      <tr id="cache_memcached" style="display:none">
        <th class="paddingT15">
            Memcached服务(for cache) :
        </th>
        <td class="paddingT15">
            <input type="text" name="cache_memcached" value="<?php echo $this->_var['setting']['cache_memcached']; ?>" />
            <span class="grey">存储缓存数据的Memcached服务器(如127.0.0.1:11212)</span>
        </td>
      </tr>
      
      
      <tr>
        <th class="paddingT15">订单是否需要审核 :</th>
        <td class="paddingT15"><input id="order_if_review" type="radio" name="order_if_review" <?php if ($this->_var['setting']['order_if_review'] == 'yes'): ?>checked<?php endif; ?> value="yes" />
          <label for="yes">是</label>
          <input type="radio" id="order_if_review" name="order_if_review" <?php if ($this->_var['setting']['order_if_review'] == 'no'): ?>checked<?php endif; ?> value="no" />
          <label for="no">否</label>&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="grey">订单是否需要审核</span>
        </td>
      </tr>
      
      
     <tr>
        <th></th>
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交" />
          <input class="formbtn" type="reset" name="Submit2" value="重置" />
        </td>
      </tr>
    </table>
  </form>
</div>
<?php echo $this->fetch('footer.html'); ?>
