<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <p><strong>支付方式管理</strong></p>
</div>
<div class="tdare info">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['payment']): ?>
        <tr class="tatr1">
            <td class="firstCell" width="15%">支付方式名称</td>
            <td>支付方式描述</td>
            <td width="5%">启用</td>
            <td width="10%">手机支付</td>
            <td width="10%">支持的货币</td>
            <td width="10%">作者</td>
            <td width="10%" class="table-center">版本</td>
            <td width="50" class="handler" style="width: 100px">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['payments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'payment');if (count($_from)):
    foreach ($_from AS $this->_var['payment']):
?>
        <tr class="tatr2">
            <td class="firstCell"><?php echo $this->_var['payment']['name']; ?></td>
            <td><span class="padding1"><?php echo $this->_var['payment']['desc']; ?></span></td>
            <td><?php if ($this->_var['payment']['enabled']): ?>是<?php else: ?>否<?php endif; ?></td>
            <td><?php if ($this->_var['payment']['ismobile']): ?>是<?php else: ?>否<?php endif; ?></td>
            <td><span class="padding1"><?php echo $this->_var['payment']['currency']; ?></span></td>
            <td><a href="<?php echo $this->_var['payment']['website']; ?>" target="_blank" title="作者链接"><?php echo $this->_var['payment']['author']; ?></a></td>
            <td class="table-center"><?php echo $this->_var['payment']['version']; ?></td>
            <td class="handler" width="50" style="width: 100px">
             <?php if ($this->_var['payment']['installed']): ?>
                        <a href="index.php?app=payment&amp;act=config&payment_id=<?php echo $this->_var['payment']['payment_id']; ?>&amp;code=<?php echo $this->_var['payment']['code']; ?>">配置</a> <a href="index.php?app=payment&amp;act=uninstall&payment_id=<?php echo $this->_var['payment']['payment_id']; ?>" class="delete">卸载</a>
                    <?php else: ?>
                        <a href="index.php?app=payment&amp;act=install&code=<?php echo $this->_var['payment']['code']; ?>">安装</a>
                    <?php endif; ?>
                </td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">尚未安装任何支付方式</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
</div>

<script>
$(document).ready(function(){
	$('*[ectype="dialog"]').click(function(){
		var id = $(this).attr('dialog_id');
		var title = $(this).attr('dialog_title') ? $(this).attr('dialog_title') : '';
		var url = $(this).attr('uri');
		var width = $(this).attr('dialog_width');
		ajax_form(id, title, url, width);
		return false;
		}); 
})
</script>
<?php echo $this->fetch('footer.html'); ?>
