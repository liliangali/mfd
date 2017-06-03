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
        <li><span>导航管理</span></li>
        <li><a class="btn1" href="index.php?app=satnav&amp;act=add">导航新增</a></li>
    </ul>
</div>

<div class="info2">

    <table  class="distinction">
        <?php if ($this->_var['acategories']): ?>
        <thead>
        <tr class="tatr1">
            <td colspan="2" style="padding-left: 10px;">导航名称</td>
            <td>排序</td>
            <td>导航title</td>
             <td >链接</td>
            <td>图标</td>
            <td>显示</td>
            <td class="handler">操作</td>
        </tr>
        </thead>
        <?php endif; ?>
       
       
             <tbody id="treet1">
         
            <?php $_from = $this->_var['acategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'dh');if (count($_from)):
    foreach ($_from AS $this->_var['dh']):
?>
            <tr>
                <td class="align_center w30"></td>
                <td class="node" width="15%"><span ectype="inline_edit" fieldname="name" fieldid="<?php echo $this->_var['dh']['satnav_id']; ?>" required="1" class="node_name editable"><?php echo htmlspecialchars($this->_var['dh']['name']); ?></span></td>
                <td class="align_center" width="10%"><span ectype="inline_edit" fieldname="sort_order" fieldid="<?php echo $this->_var['dh']['satnav_id']; ?>" datatype="pint" maxvalue="255" class="editable"><?php echo $this->_var['dh']['sort_order']; ?></span></td>
                <td class="align_center" width="10%"><span ectype="inline_edit" fieldname="title" fieldid="<?php echo $this->_var['dh']['satnav_id']; ?>"  maxvalue="255" class="editable"><?php echo htmlspecialchars($this->_var['dh']['title']); ?></span></td>
                
               <td class="align_center" width="15%"><span ectype="inline_edit" fieldname="link" fieldid="<?php echo $this->_var['dh']['satnav_id']; ?>"  maxvalue="255" class="editable"><?php echo htmlspecialchars($this->_var['dh']['link']); ?></span></td>
               <td class="align_center" width="10%"><?php echo $this->_var['dh']['lcon']; ?></td>
               <td class="align_center" width="10%"><?php if ($this->_var['dh']['if_show']): ?><img src="templates/style/images/positive_enabled.gif" ectype="inline_edit" fieldname="if_show" fieldid="<?php echo $this->_var['dh']['satnav_id']; ?>" fieldvalue="1" title="单击可以编辑"/><?php else: ?><img src="templates/style/images/positive_disabled.gif" ectype="inline_edit" fieldname="if_show" fieldid="<?php echo $this->_var['dh']['satnav_id']; ?>" fieldvalue="0" title="单击可以编辑"/><?php endif; ?></td>
                
             <td class="align_center">
            <a href="index.php?app=satnav&amp;act=edit&amp;id=<?php echo $this->_var['dh']['satnav_id']; ?>">编辑</a>
           | <a href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=satnav&amp;act=drop&amp;id=<?php echo $this->_var['dh']['satnav_id']; ?>');">删除</a>
           | <a href="index.php?app=satnav&amp;act=add_satnavcat&amp;id=<?php echo $this->_var['dh']['satnav_id']; ?>">新增下级</a>
           
            </td> 	
            </tr>
            <?php endforeach; else: ?>
            <tr class="no_data">
                <td colspan="6">没有符合条件的记录</td>
            </tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
           
        </tbody>
       

        <tfoot>
        </tfoot>
    </table>

 
</div>
<?php echo $this->fetch('footer.html'); ?>
