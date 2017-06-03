<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=discount&amp;act=add">添加</a></li>
    </ul>
</div>

<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['list']): ?>
        <tr class="tatr1">
            <!-- <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td> -->
            <td align="left">规则名称</td>
            <td align="left">是否排斥</td>
            <td align="left">开启状态</td>
            <td align="left">起始时间</td>
            <td align="left">结束时间</td> 
            <td align="left">优先级</td>  
            <td align="left">优惠条件</td> 
            <td align="left">优惠方案</td>       
            <td>操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'info');if (count($_from)):
    foreach ($_from AS $this->_var['info']):
?>
        <tr class="tatr2">
            <td><?php echo htmlspecialchars($this->_var['info']['name']); ?></td>
            <td><?php if ($this->_var['info']['if_ex']): ?>是<?php else: ?>否<?php endif; ?></td>
            <td><?php if ($this->_var['info']['is_open']): ?>是<?php else: ?>否<?php endif; ?></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['info']['starttime']); ?></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['info']['endtime']); ?></td>
            <td><?php echo $this->_var['info']['level']; ?></td>
            <td><?php echo $this->_var['info']['favorable']; ?></td>
             <td><?php echo $this->_var['info']['yhcase']; ?></td>
            <td>
            <a href="index.php?app=discount&act=edit&id=<?php echo $this->_var['info']['id']; ?>">编辑</a>|
             <a href="index.php?app=discount&act=drop&id=<?php echo $this->_var['info']['id']; ?>">删除</a>
              <a href="<?php echo $this->_var['site_url']; ?>/activegoods.html?active_id=<?php echo $this->_var['info']['id']; ?>" target="_blank">预览</a>
			</td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['list']): ?>
    <div id="dataFuncs">
        <div class="pageLinks">
            <?php echo $this->fetch('page.bottom.html'); ?>
        </div>
        <div id="batchAction" class="left paddingT15">
            &nbsp;&nbsp;
            &nbsp;&nbsp;
            <!--<input class="formbtn batchButton" type="button" value="lang.update_order" name="id" presubmit="updateOrder(this);" />-->
        </div>
    </div>
    <div class="clear"></div>
    <?php endif; ?>
</div>
<?php echo $this->fetch('footer.html'); ?>
