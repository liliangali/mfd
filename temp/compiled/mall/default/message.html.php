<?php echo $this->fetch('header-new.html'); ?>
<link href="/public/static/pc/css/slx_dlzc.css" type="text/css" rel="stylesheet">
<div class="cus_center_box" style="margin-top:30px;">
  <div class="cgzhmm" style=" width:1198px; margin:0 auto; text-align:center; height:400px;">
    <div class="warning">
        <h3 style="background:none; margin-top:200px; font-size:18px; font-weight:normal; height:40px; line-height:40px; overflow:hidden;"><?php echo $this->_var['message']; ?><br />&nbsp;</h3>
        <p class="wexitisi">
		<?php $_from = $this->_var['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
          <a style="color:#e66800; line-height:30px;" href="<?php echo $this->_var['item']['href']; ?>"><?php echo $this->_var['item']['text']; ?></a><br />
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </p>
    </div>
  </div>
</div>

<div class="content" style="display:none;">
    <div class="particular" style="width:1000px; margin:0 auto; padding:100px; border:3px solid #cccccc;">
        <div class="particular_wrap" style=" margin:0 auto;">
            <p class="<?php if ($this->_var['icon'] == "notice"): ?>success<?php else: ?>defeated<?php endif; ?>">
                <span></span>
                <b style="width:380px;"><?php echo $this->_var['message']; ?></b>
                <?php if ($this->_var['err_file']): ?>
                <b style="clear: both; font-size: 15px;">Error File: <strong><?php echo $this->_var['err_file']; ?></strong> at <strong><?php echo $this->_var['err_line']; ?></strong> line.</b>
                <?php endif; ?>
                <?php if ($this->_var['icon'] != "notice"): ?>
                <font style="clear: both; display:block; margin:0 0 0 50px;">
                <?php $_from = $this->_var['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
                        <a style="color:#aaa;" href="<?php echo $this->_var['item']['href']; ?>">>> <?php echo $this->_var['item']['text']; ?></a><br />
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </font>
                <?php endif; ?>
            </p>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<?php if (! $this->_var['redirects']): ?>
<script type="text/javascript">
//<!CDATA[
<?php if ($this->_var['redirect']): ?>
window.setTimeout("<?php echo $this->_var['redirect']; ?>", 3000);
<?php endif; ?>
//]]>
</script>
<?php endif; ?>

<!--底部开始-->

<?php echo $this->fetch('/footer-new-1.html'); ?>
<!--底部结束-->
<script src="/public/global/jquery-1.8.3.min.js"></script>
<script src="/public/static/pc/js/public.js"></script>

</body>
</html>
