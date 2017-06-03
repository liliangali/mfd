<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=StandardPackage&amp;act=add">新增</a></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get">
            <div class="left">
                <input type="hidden" name="app" value="StandardPackage" />
                <input type="hidden" name="act" value="index" />
                <input type="hidden" name="wait_verify" value="<?php echo $this->_var['wait_verify']; ?>">
                规格:
                <select name="guige">
                    <option value="0">请选择</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['guige_list'],'selected'=>$this->_var['guige'])); ?>
                </select>
                包装:
                <select name="baozhuang">
                    <option value="0">请选择</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['baozhuang_list'],'selected'=>$this->_var['baozhuang'])); ?>
                </select>



                <input type="submit" class="formbtn" value="查询" />
            </div>

        </form>
    </div>

<!-- <div class="mrightTop">
    <div class="fontl">
        <form method="get">
             <div class="left">
                <input type="hidden" name="app" value="StandardPackage" />
                <input type="hidden" name="act" value="index" /> 
                                                 基础材料名称:<input type="text" value="<?php echo $this->_var['query']['name']; ?>" name="name" class="pick_date" />
                <input type="submit" class="formbtn" value="查询" />

            </div>-->
                        <?php if ($this->_var['query']['name'] || $this->_var['query']['to_site']): ?>
            <!-- <a class="left formbtn1" href="index.php?app=StandardPackage">撤销检索</a> -->
            <?php endif; ?>
      <!--   </form>
    </div>
</div> --> 
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['standardpackage_list']): ?>
        <tr class="tatr1">
            <!-- <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td> -->
            <td align="left">价格ID</td>
            <td>价格</td>
            <td align="left">规格</td>
            <td>包装</td>
            <td class="handler">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['standardpackage_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'standardpackage');if (count($_from)):
    foreach ($_from AS $this->_var['standardpackage']):
?>
        <tr class="tatr2">
            <td><?php echo htmlspecialchars($this->_var['standardpackage']['sp_id']); ?></td>
            <td><?php echo price_format($this->_var['standardpackage']['sp_price']); ?></td>
            <td ><?php echo $this->_var['standardpackage']['sname']; ?></td>
            <td ><?php echo $this->_var['standardpackage']['pname']; ?></td>
            <td>
                <a href="index.php?app=StandardPackage&amp;act=edit&amp;id=<?php echo $this->_var['standardpackage']['sp_id']; ?>">编辑</a>
                <a href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=StandardPackage&amp;act=drop&amp;id=<?php echo $this->_var['standardpackage']['sp_id']; ?>');">删除</a>
			</td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['StandardPackage_list']): ?>
    <div id="dataFuncs">
        <div class="pageLinks">
            <?php echo $this->fetch('page.bottom.html'); ?>
        </div>
        <div id="batchAction" class="left paddingT15">
            &nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=StandardPackage&act=drop" presubmit="confirm('您确定要删除它吗？');" />
        </div>
    </div>
    <div class="clear"></div>
    <?php endif; ?>
</div>
<?php echo $this->fetch('footer.html'); ?>
