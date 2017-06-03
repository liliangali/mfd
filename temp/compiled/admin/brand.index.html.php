<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><?php if ($this->_var['wait_verify']): ?><a class="btn1" href="index.php?app=feed">管理</a><?php else: ?><span>管理</span><?php endif; ?></li>
        <li><a class="btn1" href="index.php?app=feed&amp;act=add">新增</a></li>

    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="get">
            <div class="left">
                <input type="hidden" name="app" value="feed" />
                <input type="hidden" name="act" value="index" />
                <input type="hidden" name="wait_verify" value="<?php echo $this->_var['wait_verify']; ?>">
                犬类型:
                <select name="fbtype">
                    <option value="0">请选择</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['type_list'],'selected'=>$this->_var['fbtype'])); ?>
                </select>

                犬期:
                <select name="age_id">
                    <option value="0">请选择</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['age_list'],'selected'=>$this->_var['age_id'])); ?>
                </select>



                <input type="submit" class="formbtn" value="查询" />
            </div>

           </form>
    </div>

</div>


<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['brands']): ?>
        <tr class="tatr1">
            <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">犬类型</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">犬期</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">时间</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">体况</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">运动量</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">体重上限</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">体重下限</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">默认体重</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">能量需求参数</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">卡里路</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">减肥卡里路</span></td>
            <td align="left"><span ectype="order_by" fieldname="brand_name">每天饲喂次数</span></td>


            <td class="handler">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
        <tr class="tatr2">
            <td class="firstCell"><input value="<?php echo $this->_var['brand']['brand_id']; ?>" class='checkitem' type="checkbox" /></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['fbtype_name']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['age_id_name']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['time_id_name']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['body_condition_name']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['run_time_name']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['wt_min']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['wt_max']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['default_weight']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['enesum']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['kcal']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['redenesum']); ?></span></td>
            <td align="left"><span class="" ><?php echo htmlspecialchars($this->_var['brand']['nums']); ?></span></td>
            <td class="handler">
            <a href="index.php?app=feed&amp;act=edit&amp;id=<?php echo $this->_var['brand']['id']; ?>&amp;page=<?php echo $this->_var['page_info']['curr_page']; ?>&amp;fbtype=<?php echo $this->_var['fbtype']; ?>&amp;age_id=<?php echo $this->_var['age_id']; ?>">编辑</a>  |
                <a name="drop" href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=feed&amp;act=drop&amp;id=<?php echo $this->_var['brand']['id']; ?>');">删除</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['brands']): ?>
    <div id="dataFuncs">
        <div id="batchAction" class="left paddingT15">
            <?php if ($this->_var['wait_verify']): ?>
            &nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="pass" name="id" uri="index.php?app=brand&act=pass" />
             &nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="refuse" name="id" uri="index.php?app=brand&act=refuse" />
            <?php else: ?>
            &nbsp;&nbsp;
            <?php endif; ?>
        </div>
        <div class="pageLinks">
            <?php if ($this->_var['brands']): ?><?php echo $this->fetch('page.bottom.html'); ?><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="clear"></div>
</div>
<?php echo $this->fetch('footer.html'); ?>
