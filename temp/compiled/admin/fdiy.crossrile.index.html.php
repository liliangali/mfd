<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=dictC&amp;act=add">新增</a></li>
    </ul>
</div>
<!-- <div class="mrightTop">
    <div class="fontl">
        <form method="get">
             <div class="left">
                <input type="hidden" name="app" value="dictC" />
                <input type="hidden" name="act" value="index" />
                                                 单品名称:<input type="text" value="<?php echo $this->_var['query']['name']; ?>" name="name" class="pick_date" />
               <select name="cid">
                <option value="0">请选择品类</option><?php echo $this->html_options(array('options'=>$this->_var['cat_list'],'vals'=>'name','selected'=>$this->_var['query']['cid'])); ?>
               </select>
                <input type="submit" class="formbtn" value="查询" />

            </div>
                        <?php if ($this->_var['query']['name'] || $this->_var['query']['to_site']): ?>
            <a class="left formbtn1" href="index.php?app=dictC">撤销检索</a>
            <?php endif; ?>
        </form>
    </div>
</div> -->
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['_list']): ?>
        <tr class="tatr1">
           <!--  <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td> -->
          <!--   <td align="left">品类</td> -->
            <td>主属性</td>
            <td align="left">冲突属性</td>
  <!--           <td align="left">平台</td>
            <td>添加时间</td>
            <td>修改时间</td> -->
             <td>操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'custom');if (count($_from)):
    foreach ($_from AS $this->_var['custom']):
?>
        <tr class="tatr2">
           <!--  <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['custom']['id']; ?>"/></td> -->
          <!--   <td><?php echo $this->_var['custom']['cid']; ?></td> -->
            <td><?php echo $this->_var['custom']['cn']; ?></td>
            <td><?php echo $this->_var['custom']['rn']; ?></td>
   <!--          <td><?php echo $this->_var['to_site'][$this->_var['custom']['to_site']]; ?></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['custom']['add_time']); ?></td>
            <td><?php echo local_date("Y-m-d H:i:s",$this->_var['custom']['last_time']); ?></td> -->
             <td>
                <a href="../custom-<?php echo $this->_var['custom']['id']; ?>.html" target="_blank" style="display:none;">查看</a>
                <a href="index.php?app=dictC&amp;act=edit&amp;id=<?php echo $this->_var['custom']['id']; ?>">编辑</a>
                <a href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=dictC&amp;act=drop&amp;id=<?php echo $this->_var['custom']['id']; ?>');">删除</a>
			</td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['_list']): ?>
    <div id="dataFuncs">
        <div class="pageLinks">
            <?php echo $this->fetch('page.bottom.html'); ?>
        </div>
<!--         <div id="batchAction" class="left paddingT15">
            &nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=dictC&act=drop" presubmit="confirm('您确定要删除它吗？');" />
        </div> -->
    </div>
    <div class="clear"></div>
    <?php endif; ?>
</div>
<?php echo $this->fetch('footer.html'); ?>
