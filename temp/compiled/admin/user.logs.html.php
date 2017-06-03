<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=user">管理</a></li>
        <li><a class="btn1" href="index.php?app=user&act=admin_userlog">日志查询</a></li>
        <li><span>登录记录</span></li>
    </ul>

</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get">
            <div class="left">
                <input type="hidden" name="app" value="user" />
                <input type="hidden" name="act" value="user_log" />
                用户名：
                <input type="text" class="zzinput"  name="user_name" value="<?php echo $_GET['user_name']; ?>"/>

                &nbsp;&nbsp;
                登录平台：<select class="querySelect" name='source'>
                <option value="">全部</option>
                <?php echo $this->html_options(array('options'=>$this->_var['source_conf'],'selected'=>$_GET['source'])); ?>
            </select>

                操作时间：<input class="queryInput2" type="text" value="<?php echo $this->_var['query']['add_time_from']; ?>" style="width:80px" id="add_time_from" name="add_time_from" class="pick_date" />
                至：<input class="queryInput2" type="text" value="<?php echo $this->_var['query']['add_time_to']; ?>" style="width:80px" id="add_time_to" name="add_time_to" class="pick_date" />

                <input type="submit" class="formbtn" value="查询" />
            </div>
        </form>
    </div>

    <div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>
</div>
<br/>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <tr class="tatr1">
            <td width="3%"><span ectype="order_by" fieldname="id">ID</span></td>
            <td width="12%"><span  ectype="order_by" fieldname="user_id">用户名</span></td>
            <!--<td width="6%"><span  ectype="order_by" fieldname="source">登录平台</span></td>-->
            <td width="11%"><span  ectype="order_by" fieldname="client">登陆客户端</span></td>
			<td width="11%"><span  ectype="order_by" fieldname="add_time">登陆时间</span></td>

        </tr>

        <?php if (! empty ( $this->_var['list'] )): ?>
        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item']):
?>
        <tr class="tatr2">
            <td class="firstCell"><?php echo $this->_var['item']['id']; ?></td>
            <td><a href="index.php?app=user&act=member_info&id=<?php echo $this->_var['item']['user_id']; ?>"><?php echo $this->_var['item']['user_name']; ?></a></td>
            <!--<td><?php echo $this->_var['source_conf'][$this->_var['item']['source']]; ?></td>-->
            <td><?php echo $this->_var['item']['client']; ?></td>
			<td><?php echo $this->_var['item']['add_time']; ?></td>

        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <?php else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; ?>
    </table>
    <?php if (! empty ( $this->_var['list'] )): ?>
    <div id="dataFuncs">
        <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
        <div class="clear"></div>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    //日期
    $('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
    $('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});
</script>
<?php echo $this->fetch('footer.html'); ?>