<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=goods&amp;act=add">新增</a></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get">
             <div class="left">
                <input type="hidden" name="app" value="goods" />
                <input type="hidden" name="act" value="index" />
                产品编号:<input type="text" value="<?php echo $this->_var['query']['bn']; ?>" name="bn" class="pick_date" />
                &nbsp;&nbsp;产品名称:<input type="text" value="<?php echo $this->_var['query']['name']; ?>" name="name" class="pick_date" />
                &nbsp;&nbsp;商品分类:<select class="querySelect mySelect" name="p_id"><option value="">全部</option><?php echo $this->html_options(array('options'=>$this->_var['pList'],'selected'=>$this->_var['query']['p_id'])); ?></select>
                &nbsp;&nbsp;商品子类:<select class="querySelect mySelect" name="son_id"><option value="">全部</option><?php echo $this->html_options(array('options'=>$this->_var['sonList'],'selected'=>$this->_var['query']['son_id'])); ?></select>
                <input type="submit" class="formbtn" value="查询" />

            </div>
                        <?php if ($this->_var['query']['name'] || $this->_var['query']['bn'] || $this->_var['query']['p_id']): ?>
            <a class="left formbtn1" href="index.php?app=goods">撤销检索</a>
            <?php endif; ?>
        </form>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['goodslist']): ?>
        <tr class="tatr1">
            <td align="left">编号</td>
            <td>产品名称</td>
            <td align="left">产品价格</td>
            <!--<td align="left">库存</td>-->
            <td>所属分类</td>
            <td>发往平台</td>
            <td>是否上架</td>
            <td>排序</td>
            <td>操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['goodslist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
        <tr class="tatr2">
            <td><?php echo htmlspecialchars($this->_var['goods']['bn']); ?></td>
            <td><a href="/goods-<?php echo $this->_var['goods']['goods_id']; ?>.html" target="_blank"><?php echo htmlspecialchars($this->_var['goods']['name']); ?></a></td>
            <td><?php echo price_format($this->_var['goods']['price']); ?></td>
            <!--<td><?php echo $this->_var['goods']['store']; ?></td>-->
            <td><?php echo $this->_var['options'][$this->_var['goods']['cat_id']]; ?></td>
            <td><?php if ($this->_var['goods']['is_pc'] == 1): ?>pc|<?php endif; ?><?php if ($this->_var['goods']['is_wap'] == 1): ?>wap|<?php endif; ?><?php if ($this->_var['goods']['is_app'] == 1): ?>app<?php endif; ?></td>
            <?php if ($this->_var['goods']['marketable']): ?>
            <td>是</td>
            <?php else: ?>
            <td>否</td>
            <?php endif; ?>
            <td><?php echo $this->_var['goods']['p_order']; ?></td>
            <td>
                <a href="index.php?app=goods&amp;act=edit&amp;id=<?php echo $this->_var['goods']['goods_id']; ?>">编辑</a>|
                <a href="index.php?app=products&amp;act=setspec&amp;goods_id=<?php echo $this->_var['goods']['goods_id']; ?>" target="_blank">编辑货品</a>|
                <a href="javascript:drop_confirm('您确定要删除该商品吗（不可恢复）？', 'index.php?app=goods&amp;act=drop&amp;id=<?php echo $this->_var['goods']['goods_id']; ?>');">删除</a>
			</td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['goodslist']): ?>
    <div id="dataFuncs">
        <div class="pageLinks">
            <?php echo $this->fetch('page.bottom.html'); ?>
        </div>
        <div id="batchAction" class="left paddingT15">
        </div>
    </div>
    <div class="clear"></div>
    <?php endif; ?>
</div>
<script type="text/javascript">

    
    $('.mySelect').change(function() {
        console.log(123)
        var p_id=$("select[name='p_id']").val();
        var son_id=$("select[name='son_id']").val();
        var name=$("input[name='name']").val()
        window.location.href="index.php?app=goods&act=index&name="+name+"&p_id="+p_id+"&son_id="+son_id;
    }) 

</script>
<?php echo $this->fetch('footer.html'); ?>
