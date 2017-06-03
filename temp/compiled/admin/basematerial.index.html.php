<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=BaseMaterial&amp;act=add">新增</a></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get">
             <div class="left">
                <input type="hidden" name="app" value="BaseMaterial" />
                <input type="hidden" name="act" value="index" /> 
                                                 基础材料名称:<input type="text" value="<?php echo $this->_var['query']['name']; ?>" name="name" class="pick_date" />
                <input type="submit" class="formbtn" value="查询" />

            </div>
                        <?php if ($this->_var['query']['name'] || $this->_var['query']['to_site']): ?>
            <a class="left formbtn1" href="index.php?app=BaseMaterial">撤销检索</a>
            <?php endif; ?>
        </form>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['basematerial_list']): ?>
        <tr class="tatr1">
            <!-- <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td> -->
            <td align="left">基料ID</td>
            <td align="left">基料名称</td>
            <td>价格(百克)</td>
            <td>编码</td>
            <td align="left">所属犬种</td>
            <td>所属犬期</td>
            <td class="handler">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['basematerial_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'basematerial');if (count($_from)):
    foreach ($_from AS $this->_var['basematerial']):
?>
        <tr class="tatr2">
            <td><?php echo htmlspecialchars($this->_var['basematerial']['base_id']); ?></td>
            <td><?php echo htmlspecialchars($this->_var['basematerial']['base_name']); ?></td>
            <td ><?php echo price_format($this->_var['basematerial']['base_price']); ?></td>
            <td><?php echo $this->_var['basematerial']['base_code']; ?></td>
            
            <td ><?php echo $this->_var['basematerial']['cname']; ?></td>
            <td ><?php echo $this->_var['basematerial']['aname']; ?></td>
            <td>
                <a href="index.php?app=BaseMaterial&amp;act=edit&amp;id=<?php echo $this->_var['basematerial']['base_id']; ?>">编辑</a>
                <a href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=BaseMaterial&amp;act=drop&amp;id=<?php echo $this->_var['basematerial']['base_id']; ?>');">删除</a>
			</td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['basematerial_list']): ?>
    <div id="dataFuncs">
        <div class="pageLinks">
            <?php echo $this->fetch('page.bottom.html'); ?>
        </div>
        <div id="batchAction" class="left paddingT15">
            &nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=BaseMaterial&act=drop" presubmit="confirm('您确定要删除它吗？');" />
        </div>
    </div>
    <div class="clear"></div>
    <?php endif; ?>
</div>
<?php echo $this->fetch('footer.html'); ?>
