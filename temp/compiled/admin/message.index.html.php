<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=messagetemplate&amp;act=add&amp;parent_id=<?php echo htmlspecialchars($_GET['parent_id']); ?>">新增</a></li>
       <li><a class="btn1" href="index.php?app=icategory&amp;act=index">消息分类</a></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get">
            <div class="left">
                <input type="hidden" name="app" value="messagetemplate" />
                <input type="hidden" name="act" value="index" />
                标题:
                <input class="queryInput" type="text" name="title" value="<?php echo htmlspecialchars($this->_var['query']['title']); ?>" />
                使用标记:
                <input class="queryInput" type="text" name="mt_type" value="<?php echo htmlspecialchars($this->_var['query']['mt_type']); ?>" />
                消息分类:
                <select class="querySelect" id="parent_id" name="parent_id">
                <option value="">请选择...</option>
                <?php echo $this->html_options(array('options'=>$this->_var['parents'],'selected'=>$_GET['parent_id'])); ?>
                </select>
                <input type="submit" class="formbtn" value="查询" />
                <!-- <input type='button' class='test' value='test'/>  -->
            </div>
            <?php if ($this->_var['filtered']): ?>
            <a class="left formbtn1" href="index.php?app=messagetemplate">撤销检索</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="fontr">
        <?php echo $this->fetch('page.top.html'); ?>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['messagetemplates']): ?>
        <tr class="tatr1">
            <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
            <td align="left">标题</td>
            <td>所属分类</td>
            <td>使用标记</td>
            <td>是否特殊消息</td>
            <td>添加时间</td>
            <td>修改时间</td>
            <td>操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['messagetemplates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'messagetemplate');if (count($_from)):
    foreach ($_from AS $this->_var['messagetemplate']):
?>
        <tr class="tatr2">
            <td class="firstCell"><?php if (! $this->_var['messagetemplate']['code']): ?><input type="checkbox" class="checkitem" value="<?php echo $this->_var['messagetemplate']['mt_id']; ?>"/><?php endif; ?></td>
            <td><?php echo htmlspecialchars($this->_var['messagetemplate']['mt_title']); ?></td>
            <td><?php echo htmlspecialchars($this->_var['messagetemplate']['icn']); ?></td>
            <td><?php echo htmlspecialchars($this->_var['messagetemplate']['mt_type']); ?></td>
            <td><?php if ($this->_var['messagetemplate']['is_special']): ?>是<?php else: ?>否<?php endif; ?></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['messagetemplate']['add_time']); ?></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['messagetemplate']['alter_time']); ?></td>
            <td><a href="index.php?app=messagetemplate&amp;act=edit&amp;id=<?php echo $this->_var['messagetemplate']['mt_id']; ?>">编辑</a>
                <?php if (! $this->_var['messagetemplate']['code']): ?>|
                <a href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=messagetemplate&amp;act=drop&amp;id=<?php echo $this->_var['messagetemplate']['mt_id']; ?>');">删除</a><?php endif; ?>				
                </td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['messagetemplates']): ?>
    <div id="dataFuncs">
        <div class="pageLinks">
            <?php echo $this->fetch('page.bottom.html'); ?>
        </div>
        <div id="batchAction" class="left paddingT15">
            &nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=messagetemplate&act=drop" presubmit="confirm('您确定要删除它吗？');" />
            &nbsp;&nbsp;
            <!--<input class="formbtn batchButton" type="button" value="lang.update_order" name="id" presubmit="updateOrder(this);" />-->
        </div>
    </div>
    <div class="clear"></div>
    <?php endif; ?>
</div>
<!-- <script type='text/javascript' src='templates/js/jquery-1.7.2.min.js'></script> -->
<script>
/*  $(".test").click(function(){
	$.post('index.php?app=messagetemplate&act=sendmessage',{num:1},function(res){
			alert(res.retval);
	},'json');
})  */
</script>
<?php echo $this->fetch('footer.html'); ?>
