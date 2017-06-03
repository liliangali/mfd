<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>优惠券列表</span></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=voucher_create">生成优惠券</a></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=voucher_batch">批次日志</a></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get" action="index.php?app=setting&act=voucher" class="sform">
            <div class="left">
<!--
	用户名、昵称、注册账号
	使用状态、未使用、已使用
	过期状态、未过期、已过期
	生成时间、开始 结束
	生成批次（备注说明、时间）
-->
                <input type="hidden" name="app" value="setting" />
                <input type="hidden" name="act" value="voucher" />
                <input type="text" class="zzinput"  name="s[binding_username]" value="<?php echo $this->_var['stj']['binding_username']; ?>" style="width:100px;" placeholder="账号"/>
                &nbsp;&nbsp;
                <input type="text" class="zzinput"  name="s[code]" value="<?php echo $this->_var['stj']['code']; ?>" style="width:100px;" placeholder="激活码" />
                &nbsp;&nbsp;
                <select class="querySelect" name='s[batch_id]'>
                    <option value="">生成批次</option>
                    <?php $_from = $this->_var['batchList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['value']):
?>
                    <option value="<?php echo $this->_var['value']['id']; ?>" <?php if ($this->_var['stj']['batch_id'] == $this->_var['value']['id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['value']['name']; ?></option>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </select>
                <select class="querySelect" name='s[binding_status]'>
                    <option value="">激活状态</option>
                    <option value="0" <?php if ($this->_var['stj']['binding_status'] == "0"): ?>selected="selected"<?php endif; ?>>未激活</option>
                    <option value="1" <?php if ($this->_var['stj']['binding_status'] == "1"): ?>selected="selected"<?php endif; ?>>已激活</option>
                </select>
                <select class="querySelect" name='s[use_status]'>
                    <option value="">使用状态</option>
                    <option value="0" <?php if ($this->_var['stj']['use_status'] == "0"): ?>selected="selected"<?php endif; ?>>未使用</option>
                    <option value="1" <?php if ($this->_var['stj']['use_status'] == "1"): ?>selected="selected"<?php endif; ?>>已使用</option>
                </select>
                <select class="querySelect" name='s[end_time]'>
                    <option value="">过期状态</option>
                    <option value="0" <?php if ($this->_var['stj']['end_time'] == "0"): ?>selected="selected"<?php endif; ?>>未过期</option>
                    <option value="1" <?php if ($this->_var['stj']['end_time'] == "1"): ?>selected="selected"<?php endif; ?>>已过期</option>
                </select>
                <input type="submit" class="formbtn" value="查询" />
                &nbsp;&nbsp;
                <input type="reset" class="formbtn" value="重置查询条件" />
            </div>
        </form>
    </div>

    <div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <tr class="tatr1">
            <td width="7%"><span  ectype="order_by" fieldname="debit_name">券名称</span></td>
            <td width="7%"><span  ectype="order_by" fieldname="debit_name">品类</span></td>
            <td width="7%"><span  ectype="order_by" fieldname="debit_sn">价格</span></td>
            <td width="7%"><span  ectype="order_by" fieldname="debit_sn">生成时间</span></td>
            <td width="7%"><span  ectype="order_by" fieldname="debit_sn">生效时间</span></td>
            <td width="5%"><span  ectype="order_by" fieldname="debit_sn">过期时间</span></td>
            <!--<td width="5%"><span  ectype="order_by" fieldname="debit_sn">是否启用</span></td>-->
            <td width="7%"><span  ectype="order_by" fieldname="debit_sn">激活码</span></td>
            <td width="7%"><span  ectype="order_by" fieldname="debit_sn">激活时间</span></td>
            <td width="7%"><span  ectype="order_by" fieldname="debit_sn">激活用户</span></td>
            <td width="5%"><span  ectype="order_by" fieldname="debit_sn">是否使用</span></td>
            <td width="5%"><span  ectype="order_by" fieldname="debit_sn">关联订单号</span></td>
        </tr>
        <?php if (! empty ( $this->_var['list'] )): ?>
        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item']):
?>
        <tr class="tatr2">
            <td class="firstCell"><?php echo $this->_var['item']['name']; ?></td>
            <td class="firstCell"><?php if ($this->_var['item']['category'] == "1"): ?>定制商品<?php elseif ($this->_var['item']['category'] == "2"): ?>普通商品<?php else: ?>通用<?php endif; ?></td>
            <td><?php echo $this->_var['item']['money']; ?></td>
            <td><?php echo local_date("Y-m-d",$this->_var['item']['create_time']); ?></td>
            <td><?php echo local_date("Y-m-d",$this->_var['item']['start_time']); ?></td>
            <td><?php echo local_date("Y-m-d",$this->_var['item']['end_time']); ?></td>
            <!--<td>.</td>-->
            <td><?php echo $this->_var['item']['code']; ?></td>
            <td><?php echo local_date("Y-m-d",$this->_var['item']['binding_time']); ?></td>
            <td><?php if ($this->_var['item']['binding_username'] == ""): ?>未激活<?php else: ?><?php echo $this->_var['item']['binding_username']; ?><?php endif; ?></td>
            <td><?php if ($this->_var['item']['use_status'] == "1"): ?>已使用<?php else: ?>未使用<?php endif; ?></td>
            <td><?php echo $this->_var['item']['order_id']; ?></td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <?php else: ?>
        <tr class="no_data">
            <td colspan="11">没有符合条件的记录</td>
        </tr>
        <?php endif; ?>
    </table>
    <?php if (! empty ( $this->_var['list'] )): ?>
    <div id="dataFuncs">
        <!--
            <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
                <input class="export_btn formbtn" data-type="all" type="button" value="导出检索订单" />
                <input type="hidden" id="conditions" value="<?php echo $this->_var['conditions']; ?>">
            </div>
        -->
        <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
        <div class="clear"></div>
    </div>
    <?php endif; ?>
</div>
<script type="text/javascript">
    //日期
    $('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
    $('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});
$(function(){
    $('.querySelect').change(function(){
        $('form.sform').submit();
    });
    $('input[type="reset"]').click(function(){
        window.location.href = 'index.php?app=setting&act=voucher';
    });
});
</script>
<?php echo $this->fetch('footer.html'); ?>