<!DOCTYPE html>
<html lang="zh_cn">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="pragma" content="no-cache">
    <title></title>
    <link rel="stylesheet" type="text/css" href="static/css/style.css" media="screen">
</head>
<body>
<div class="cus_center_box" style="margin-top:30px;">
<div class="two_head">
				<div class="wrap">
					<span class="back" onClick="history.go(-1)"></span>
					<h1></h1>
				</div>
				<div class="sta"></div>
			</div>
  <div class="cgzhmm main">
    <div class="warning"  style="text-align:center; height:500px; width:93%; margin:0 auto;">
        <h3 style="background:none; margin-top:150px; font-size:14px; font-weight:normal; line-height:22px; overflow:hidden;"><?php echo $this->_var['message']; ?><br />&nbsp;</h3>
        <p class="wexitisi" style="display:none;">
		<?php $_from = $this->_var['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
          <a style="color:#aaa;" href="<?php echo $this->_var['item']['href']; ?>">>> <?php echo $this->_var['item']['text']; ?></a><br />
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </p>
    </div>
  </div>
</div>

<div class="content" style="display:none;">
    <div class="particular" style="width:1000px; margin:0 auto; padding:100px; border:3px solid #cccccc;">
        <div class="particular_wrap" style=" margin:0 auto;">
            <p class="<?php if ($this->_var['icon'] == "notice"): ?>success<?php else: ?>defeated<?php endif; ?>">
                
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
// twoMenu() 
// anav()
//<!CDATA[
<?php if ($this->_var['redirect']): ?>
window.setTimeout("<?php echo $this->_var['redirect']; ?>", 3000);
<?php endif; ?>
//]]>
</script>
<?php endif; ?>
</body>
</html>