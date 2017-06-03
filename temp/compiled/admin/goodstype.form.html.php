<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#type_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        onfocusout : false,
        onkeyup    : false,
        rules : {
            type_name : {
                required : true,
            },
            channel:{
            	required : true,
            },
        },
        messages : {
            type_name : {
                required : '类型名称不能为空',
            },
            channel : {
                required : '所属频道不能为空',
            },
        }
    });
    
});
</script>
<div id="rightTop">
    
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=goods_type">管理</a></li>
    </ul>
</div>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="type_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    	类型名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="name" type="text" name="name" value="<?php echo htmlspecialchars($this->_var['info']['name']); ?>" required/> 
                </td>
            </tr>
             <!-- <tr>
                <th class="paddingT15">
                    	类型别名:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="alias" type="text" name="alias" value="<?php echo htmlspecialchars($this->_var['info']['alias']); ?>" />(|分隔,前后|) 
                </td>
            </tr> -->
                  
                  
                    <tr>
                <th class="paddingT15">
                    	规格:</th>
                <td class="paddingT15 wordSpacing5">
                <?php $_from = $this->_var['speci_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
                    <?php echo $this->_var['item']['spec_name']; ?>：<input id="alias" type="checkbox" <?php if ($this->_var['item']['is_check']): ?>checked<?php endif; ?> name="speci[]" value="<?php echo $this->_var['item']['spec_id']; ?>" /> |
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </td>
            </tr>    
            
          <tr>
            <th></th>
            <td class="ptb20">
            	<input type="hidden" name="id" value="<?php echo $this->_var['info']['type_id']; ?>" />
                <input class="tijia" type="submit" id="Submit" name="Submit" value="提交" />
                <input class="congzi" type="reset" name="reset" value="重置" />            
            </td>
        </tr>
        </table>
    </form>
</div>
<script type="text/javascript">

</script>
<?php echo $this->fetch('footer.html'); ?>
