<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : {
            attr_name : {
                required : true,
            },
            type_id: {
            	required : true,
            },
            sort_order : {
                number   : true,
            },
            alias:{
            	required: true,
            },
        },
        messages : {
            attr_name : {
                required : 'attr_empty',
            },
            sort_order  : {
                number   : 'sort_num'
            },
            type_id     : {
            	required : 'type_req'
            },
            alias:{
            	required: '别外不能为空!',
            },
        }
    });
});

</script>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=goodsattribute&amp;type_id=<?php echo $this->_var['data']['type_id']; ?>">属性列表</a></li>
        <?php if ($this->_var['data']['attr_id']): ?>
        <li><a class="btn1" href="index.php?app=goodsattribute&amp;act=add&type_id=<?php echo $this->_var['data']['type_id']; ?>">新增</a></li>
        <?php else: ?>
        <li><span>新增</span></li>
        <?php endif; ?>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="form" name="theForm">
        <table class="infoTable">

            
                        <tr>
                <th class="paddingT15">
                                                属性名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="attr_name" type="text" name="attr_name" value="<?php echo htmlspecialchars($this->_var['attr']['attr_name']); ?>" required/>
                </td>
            </tr>
                       <!--  <tr>
                <th class="paddingT15">
                                               属性 别名（|分割）:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" type="text" name="alias" value="<?php echo htmlspecialchars($this->_var['data']['alias']); ?>" />
                </td>
            </tr> -->
          
           <tr>
                <th class="paddingT15">
                                               属性录入方式:</th>
                <td class="paddingT15 wordSpacing5">
              <input type="radio" name="attr_input_type" value="0" <?php if ($this->_var['attr']['attr_input_type'] == 0): ?> checked="true" <?php endif; ?> onclick="radioClicked(0)"/>
          手工录
          <input type="radio" name="attr_input_type" value="1" <?php if ($this->_var['attr']['attr_input_type'] == 1): ?> checked="true" <?php endif; ?> onclick="radioClicked(1)"/>
        从下面的列表中选择（一行代表一个可选值
                     
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                                               所属类型:</th>
                <td class="paddingT15 wordSpacing5">
                     <select name="type_id"><?php echo $this->html_options(array('options'=>$this->_var['types'],'selected'=>$this->_var['attr']['type_id'])); ?></select>
                </td>
            </tr>
            
             <tr>
                <th class="paddingT15">
                                               属性值（一行代表一个值）:</th>
                <td class="paddingT15 wordSpacing5">
                <textarea name="attr_values" cols="30" rows="5"><?php echo $this->_var['attr']['attr_values']; ?></textarea>
                </td>
            </tr>
            
            <script>
            radioClicked(<?php echo $this->_var['attr']['attr_input_type']; ?>);
            /**
             * 点击类型按钮时切换选项的禁用状态
             */
            function radioClicked(n)
            {
            	
              document.forms['theForm'].elements["attr_values"].disabled = n > 0 ? false : true;
            }
            </script>
        <tr>
            <th></th>
            <td class="ptb20">
                <input type="hidden" name="attr_id" value="<?php echo $this->_var['attr']['attr_id']; ?>">
                <input class="formbtn" type="submit" name="Submit" value="提交" />
                <input class="formbtn" type="reset" name="Submit2" value="重置" />
            </td>
        </tr>
        </table>
    </form>
</div>
<?php echo $this->fetch('footer.html'); ?>
