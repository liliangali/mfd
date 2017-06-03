<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=Material&amp;act=add">新增</a></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form name='import' action="./index.php?app=Material&act=import" method="post" enctype="multipart/form-data">
             <div class="left">
                 <input type="file" id='file' name="inputExcel" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/><input type="button" onclick="doSubmit()" value="导入物料">

            </div>
        </form><br/>
        <form method="get">
             <div class="left">
                <input type="hidden" name="app" value="Material" />
                <input type="hidden" name="act" value="index" /> 
                物料编码:<input type="text" value="<?php echo $_GET['material_code']; ?>" name="material_code" class="pick_date" />
                基料编码:<input type="text" value="<?php echo $_GET['bm_code']; ?>" name="bm_code" class="pick_date" />
                功效编码:<input type="text" value="<?php echo $_GET['ea_code']; ?>" name="ea_code" class="pick_date" />
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
        <?php if ($this->_var['material_list']): ?>
        <tr class="tatr1">
            <!-- <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td> -->
            <td align="left">物料ID</td>
            <td align="left">物料编码</td>
            <td align="center">基料编码</td>
            <td>功效编码❶</td>
            <td align="left">功效编码❷</td>
            <td>功效编码❸</td>
            <td>口味</td>
            <td class="handler">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['material_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'material');if (count($_from)):
    foreach ($_from AS $this->_var['material']):
?>
        <tr class="tatr2">
            <td><?php echo htmlspecialchars($this->_var['material']['material_id']); ?></td>
            <td><?php echo htmlspecialchars($this->_var['material']['material_code']); ?></td>
            <td ><?php echo htmlspecialchars($this->_var['material']['bm_code']); ?></td>
            <td><?php echo $this->_var['material']['ea_code_first']; ?></td>
            
            <td ><?php echo $this->_var['material']['ea_code_second']; ?></td>
            <td ><?php echo $this->_var['material']['ea_code_third']; ?></td>
            <td ><?php echo $this->_var['material']['taste']; ?></td>
            <td>
                <a href="index.php?app=Material&amp;act=edit&amp;id=<?php echo $this->_var['material']['material_id']; ?>">编辑</a>
                <a href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=Material&amp;act=drop&amp;id=<?php echo $this->_var['material']['material_id']; ?>');">删除</a>
			</td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['material_list']): ?>
    <div id="dataFuncs">
        <div class="pageLinks">
            <?php echo $this->fetch('page.bottom.html'); ?>
        </div>
        <div id="batchAction" class="left paddingT15">
            &nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=Material&act=drop" presubmit="confirm('您确定要删除它吗？');" />
        </div>
    </div>
    <div class="clear"></div>
    <?php endif; ?>
</div>

<script type="text/javascript"> 
       function doSubmit(){ 
            var file = document.getElementById('file'); 
            if (file.value == "") { 
                alert("请选择您需要上传的文件！"); 
                return false;
            }else{ 
                document.import.submit(); 
            } 
        } 
</script> 
<?php echo $this->fetch('footer.html'); ?>
