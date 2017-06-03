<?php echo $this->fetch('header.html'); ?>

<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'ajax_tree4.js'; ?>" charset="utf-8"></script>
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
        <li><span>属性管理</span></li>
        <li><a class="btn1" href="index.php?app=fabric_brand&amp;act=add">添加属性</a></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=base_diy">基本设置</a></li>
    </ul>
</div>

<div class="info2">
    <table  class="distinction">
        <?php if ($this->_var['acategories']): ?>
        <thead>
        <tr class="tatr1">
        	<td>ID</td>
            <td > 属性名称</td>
            <td>单价或固定价</td>
            <td>属性值</td>
            <td>缩略图</td>
            <td>默认选项</td>
            <td>排序</td>
            <td>显示</td>
            <td>是否独立显示</td>
            <td>是否试吃属性</td>
            <!--<td>犬类型</td>-->
            <td class="handler">操作</td>
        </tr>
        </thead>
        <?php endif; ?>
       
       
               <tbody id="treet1">
            <?php $_from = $this->_var['acategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'acategory');if (count($_from)):
    foreach ($_from AS $this->_var['acategory']):
?>
            <tr>
                <td><?php echo $this->_var['acategory']['cate_id']; ?></td>
                <td class="node" width="20%"><span ectype="inline_edit" fieldname="cate_name" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" required="1" class="node_name editable"><?php echo htmlspecialchars($this->_var['acategory']['cate_name']); ?></span></td>
              
                <td  width="20%"><?php echo $this->_var['acategory']['uprice']; ?> | <?php echo $this->_var['acategory']['fprice']; ?></td>
             	<td class="align_center"><?php echo $this->_var['acategory']['ve']; ?></td>
            	<td><img src="<?php echo $this->_var['acategory']['small_img']; ?>" height="50" width="50"></td>
            	<td><?php if ($this->_var['acategory']['is_def']): ?><img src="templates/style/images/positive_enabled.gif" ectype="inline_edit" fieldname="is_def" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" fieldvalue="1" title="可编辑"/><?php else: ?><img src="templates/style/images/positive_disabled.gif" ectype="inline_edit" fieldname="is_def" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" fieldvalue="0" title="可编辑"/><?php endif; ?></td>
                <td class="align_center"><?php if (! $this->_var['acategory']['code']): ?>
                    <span ectype="inline_edit" fieldname="sort_order" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" datatype="pint" maxvalue="255" class="editable"><?php echo $this->_var['acategory']['sort_order']; ?></span>
                    <?php endif; ?></td>
                <td class="align_center"><?php if ($this->_var['acategory']['if_show']): ?><img src="templates/style/images/positive_enabled.gif" ectype="inline_edit" fieldname="if_show" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" fieldvalue="1" title="可编辑"/><?php else: ?><img src="templates/style/images/positive_disabled.gif" ectype="inline_edit" fieldname="if_show" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" fieldvalue="0" title="可编辑"/><?php endif; ?></td>
                <td class="align_center"><?php if ($this->_var['acategory']['is_alone']): ?><img src="templates/style/images/positive_enabled.gif" ectype="inline_edit"  fieldname="is_alone" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" fieldvalue="1" title="可编辑"/><?php else: ?><img src="templates/style/images/positive_disabled.gif" ectype="inline_edit" fieldname="is_alone" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" fieldvalue="0" title="可编辑"/><?php endif; ?></td>
                <td class="align_center"><?php if ($this->_var['acategory']['is_try']): ?><img src="templates/style/images/positive_enabled.gif" ectype="inline_edit"  fieldname="is_try" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" fieldvalue="1" title="可编辑"/><?php else: ?><img src="templates/style/images/positive_disabled.gif" ectype="inline_edit" fieldname="is_try" fieldid="<?php echo $this->_var['acategory']['cate_id']; ?>" fieldvalue="0" title="可编辑"/><?php endif; ?></td>
                <!--<td class="node" width="20%"><span   required="1" class="node_name"><?php if ($this->_var['acategory']['btype'] == 1): ?>大型犬<?php elseif ($this->_var['acategory']['btype'] == 2): ?>中型犬<?php else: ?>小型犬<?php endif; ?></span></td>-->
                <td class="handler"><span>
                    <?php if (! $this->_var['acategory']['code']): ?>
                    <a href="index.php?app=fabric_brand&amp;act=edit&amp;id=<?php echo $this->_var['acategory']['cate_id']; ?>&amp;pid=<?php echo $this->_var['acategory']['parent_id']; ?>">编辑</a>
                    | <a href="javascript:if(confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗'))window.location = 'index.php?app=fabric_brand&amp;act=drop&amp;id=<?php echo $this->_var['acategory']['cate_id']; ?>';">删除</a> 
                    <?php endif; ?>
                    <?php if ($this->_var['acategory']['layer'] < $this->_var['max_layer'] && $this->_var['acategory']['parent_children_valid']): ?>
                    <?php if (! $this->_var['acategory']['code']): ?>
                    |
                    <?php endif; ?>
                    <a href="index.php?app=fabric_brand&amp;act=add&amp;pid=<?php echo $this->_var['acategory']['cate_id']; ?>">新增下级</a>
                    <?php endif; ?>
                    </span> </td>
            </tr>
            <?php endforeach; else: ?>
            <tr class="no_data">
                <td colspan="6">暂无商品分类</td>
            </tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php if ($this->_var['acategories']): ?>
        </tbody>
        <?php endif; ?>

        <tfoot>
        </tfoot>
    </table>
</div>

<?php echo $this->fetch('footer.html'); ?>