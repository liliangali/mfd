<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function()
{
    var map = <?php echo $this->_var['map']; ?>;
    if (map.length > 0)
    {
        var option = {openImg: "templates/style/images/treetable/tv-collapsable.gif", shutImg: "templates/style/images/treetable/tv-expandable.gif", leafImg: "templates/style/images/treetable/tv-item.gif", lastOpenImg: "templates/style/images/treetable/tv-collapsable-last.gif", lastShutImg: "templates/style/images/treetable/tv-expandable-last.gif", lastLeafImg: "templates/style/images/treetable/tv-item-last.gif", vertLineImg: "templates/style/images/treetable/vertline.gif", blankImg: "templates/style/images/treetable/blank.gif", collapse: false, column: 1, striped: false, highlight: true, state:false};
        $("#treet1").jqTreeTable(map, option);
    }
});
</script>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=icategory&amp;act=add">新增</a></li>
          <li><a class="btn1" href="index.php?app=messagetemplate&amp;act=index">消息管理</a></li>
    </ul>
</div>

<div class="info2">
    <table class="distinction">
        <?php if ($this->_var['acategories']): ?>
        <thead>
            <tr>
                <th class="w30"><input id="checkall_1" type="checkbox" class="checkall" /></th>
                <th width="50%"><span class="all_checkbox">
                    <label for="checkall_1">全选</label>
                    </span>分类名称</th>
<!--                 <th>分类类型</th> -->
                <th class="handler">操作</th>
            </tr>
        </thead>
        <tbody id="treet1">
            <?php endif; ?>
            <?php $_from = $this->_var['acategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'icategory');if (count($_from)):
    foreach ($_from AS $this->_var['icategory']):
?>
            <tr>
                <td class="align_center w30"><?php if (! $this->_var['icategory']['code']): ?>
                    <input type="checkbox" class="checkitem" value="<?php echo $this->_var['icategory']['cate_id']; ?>" />
                    <?php endif; ?></td>
                <td class="align_center" ><span ectype="inline_edit" fieldname="cate_name" fieldid="<?php echo $this->_var['icategory']['cate_id']; ?>" required="1" class="node_name editable"><?php echo htmlspecialchars($this->_var['icategory']['cate_name']); ?></span></td>
               <!--  <td class="align_center" ><span ectype="inline_edit" fieldname="type" fieldid="<?php echo $this->_var['icategory']['cate_id']; ?>" required="1" class="node_name editable"><?php echo htmlspecialchars($this->_var['icategory']['type']); ?></span></td> -->
                <td class="handler"><span>
                    <?php if (! $this->_var['icategory']['code']): ?>
                    <a href="index.php?app=icategory&amp;act=edit&amp;id=<?php echo $this->_var['icategory']['cate_id']; ?>">编辑</a> | <a href="javascript:if(confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗'))window.location = 'index.php?app=icategory&amp;act=drop&amp;id=<?php echo $this->_var['icategory']['cate_id']; ?>';">删除</a>
                    <?php endif; ?>
                    <?php if ($this->_var['icategory']['layer'] < $this->_var['max_layer'] && $this->_var['icategory']['parent_children_valid']): ?>
                    <?php if (! $this->_var['icategory']['code']): ?>
                    |
                    <?php endif; ?>
                    <a href="index.php?app=icategory&amp;act=add&amp;pid=<?php echo $this->_var['icategory']['cate_id']; ?>">新增下级</a>
                    <?php endif; ?>
                    </span> </td>
            </tr>
            <?php endforeach; else: ?>
            <tr class="no_data">
                <td colspan="4">暂无消息分类</td>
            </tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php if ($this->_var['acategories']): ?>
        </tbody>
        <?php endif; ?>
        <tfoot>
            <tr class="tr_pt10">
                <?php if ($this->_var['acategories']): ?>
                <td class="align_center"><label for="checkall1">
                    <input id="checkall_2" type="checkbox" class="checkall">
                    </label></td>
                <td colspan="3" id="batchAction"><span class="all_checkbox">
                    <label for="checkall_2">全选</label>
                    </span>&nbsp;&nbsp;
                    <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=icategory&act=drop" presubmit="confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗');" />
                    <!--<input class="formbtn batchButton" type="button" value="lang.update_order" name="id" presubmit="updateOrder(this);" />-->
                </td>
                <?php endif; ?>
            </tr>
        </tfoot>
    </table>
</div>
<?php echo $this->fetch('footer.html'); ?>