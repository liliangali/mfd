<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
<!--//<![CDATA[
$(function(){
    <?php echo $this->_var['payment']['onconfig']; ?>
});
//]]>-->
</script>

<div id="rightTop">
  <ul class="subnav">
    <li><span>安装支付方式</span></li>
    <li><span><a class="btn1" href="index.php?app=payment">返回支付列表</a></span></li>
  </ul>
</div>

<div class="info">
    
        <form method="post" action="index.php?app=payment&amp;act=<?php echo $_GET['act']; ?>&amp;code=<?php echo $_GET['code']; ?>&amp;payment_id=<?php echo $_GET['payment_id']; ?>">
      <table class="infoTable">
      <tr>
        <th class="paddingT15"> <label for="site_name">支付方式名称:</label></th>
        <td class="paddingT15 wordSpacing5"><?php echo htmlspecialchars($this->_var['payment']['name']); ?></td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_name">描述:</label></th>
        <td class="paddingT15 wordSpacing5"><textarea class="text" name="payment_desc"><?php echo htmlspecialchars($this->_var['payment']['payment_desc']); ?></textarea><span>该信息将在用户下单时被看到</span></td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_name">是否启用:</label></th>
        <td class="paddingT15 wordSpacing5"><?php echo $this->html_radios(array('options'=>$this->_var['yes_or_no'],'checked'=>$this->_var['payment']['enabled'],'name'=>'enabled')); ?></td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_name">手机支付:</label></th>
        <td class="paddingT15 wordSpacing5"><?php echo $this->html_radios(array('options'=>$this->_var['yes_or_no'],'checked'=>$this->_var['payment']['ismobile'],'name'=>'ismobile')); ?></td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_name">排序:</label></th>
        <td class="paddingT15 wordSpacing5"><input type="text" class="text width2" value="<?php echo $this->_var['payment']['sort_order']; ?>" name="sort_order"/></td>
      </tr>
      <?php $_from = $this->_var['payment']['config']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('conf', 'info');if (count($_from)):
    foreach ($_from AS $this->_var['conf'] => $this->_var['info']):
?>
      <tr>
        <th class="paddingT15"> <label for="site_name"><?php echo $this->_var['info']['text']; ?>:</label></th>
        <td class="paddingT15 wordSpacing5">
		<?php if ($this->_var['info']['type'] == 'text'): ?> <input type="text" name="config[<?php echo $this->_var['conf']; ?>]" id="ctrl_<?php echo $this->_var['conf']; ?>" value="<?php echo $this->_var['config'][$this->_var['conf']]; ?>" size="<?php echo $this->_var['info']['size']; ?>" onfocus="<?php echo $this->_var['info']['onfocus']; ?>" onchange="<?php echo $this->_var['info']['onchange']; ?>" onblur="<?php echo $this->_var['info']['onblur']; ?>" class="text"/>
		<?php elseif ($this->_var['info']['type'] == 'select'): ?>
                <select name="config[<?php echo $this->_var['conf']; ?>]" id="ctrl_<?php echo $this->_var['conf']; ?>" onchange="<?php echo $this->_var['info']['onchange']; ?>" class="width8 padding4">
                       <?php echo $this->html_options(array('options'=>$this->_var['info']['items'],'selected'=>$this->_var['config'][$this->_var['conf']])); ?>
                 </select>
                 <?php elseif ($this->_var['info']['type'] == 'textarea'): ?>
                 <textarea cols="<?php echo $this->_var['info']['cols']; ?>" rows="<?php echo $this->_var['info']['rows']; ?>" name="config[<?php echo $this->_var['conf']; ?>]" id="ctrl_<?php echo $this->_var['conf']; ?>" onfocus="<?php echo $this->_var['info']['onfocus']; ?>" onchange="<?php echo $this->_var['info']['onchange']; ?>" onblur="<?php echo $this->_var['info']['onblur']; ?>" class="text" ><?php echo $this->_var['config'][$this->_var['conf']]; ?></textarea>
                 <?php elseif ($this->_var['info']['type'] == 'radio'): ?>
                       <?php echo $this->html_radios(array('options'=>$this->_var['info']['items'],'checked'=>$this->_var['config'][$this->_var['conf']],'name'=>$this->_var['info']['name'])); ?>
                 <?php elseif ($this->_var['info']['type'] == 'checkbox'): ?>
                    <?php echo $this->html_checkbox(array('options'=>$this->_var['info']['items'],'checked'=>$this->_var['config'][$this->_var['conf']],'name'=>$this->_var['info']['name'])); ?>
                 <?php endif; ?>
                 <span><?php echo $this->_var['info']['desc']; ?>
		</td>
      </tr>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      <?php if ($this->_var['payment']['is_online']): ?>
            <tr>
        <th class="paddingT15"> <label for="site_name">区别码:</label></th>
        <td class="paddingT15 wordSpacing5"><input type="text" name="config[pcode]" value="<?php echo $this->_var['config']['pcode']; ?>" size="3" class="text" /> <span>正常情况下可留空，仅当支付时频繁出错时使用</span></td>
      </tr>
       <?php endif; ?>
  <tr>
        <th></th>
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交" />
          <input class="formbtn" type="reset" name="Submit2" value="重置" />        </td>
      </tr>
      </table>
        </form>
    </div>
<?php echo $this->fetch('footer.html'); ?>