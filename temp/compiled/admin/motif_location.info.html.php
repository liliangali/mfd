<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#group_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {    
            name : {
                required : true
            },
            site_id :{
                min : 1
            },
             code    :{
                remote     : {
                    url:'index.php?app=motif&act=ajax_code_unique',
                    type:'post',
                    data:{
                        id:function(){
                            return $("#locationid").val();
                        },
                        code:function(){
                            return $("#code").val();
                        }       
                    }
                }
            },
        }, 
        messages : {
            name : {
                required : '位置名称不能为空'
            },
            site_id : {
               min : '请选择所属平台'
            },
            code    : {
                remote     : '该文章标记已被使用，请换用其他标识'
            },
        }
    });
});


</script>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=motif">栏目列表</a></li>
        <li><a class="btn1" href="index.php?app=motif&amp;act=add">添加栏目</a></li>
        <li><a class="btn1" href="index.php?app=motif&amp;act=group_list">栏目位置列表</a></li>
        <?php if ($this->_var['data']['id']): ?>
        <li><a class="btn1" href="index.php?app=motif&amp;act=add_group">添加栏目位置</a></li>
        <?php else: ?>
        <li><span>添加栏目位置</span></li>
        <?php endif; ?>
    </ul>
</div>
<?php echo $this->_var['build_editor']; ?>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="group_form">
        <table class="infoTable">
            
            <tr>
                <th class="paddingT15">
                    栏目位置名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="name" type="text" name="name" value="<?php echo htmlspecialchars($this->_var['data']['name']); ?>" />
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    位置标记:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="code" type="text" name="code" value="<?php echo htmlspecialchars($this->_var['data']['code']); ?>" /><label class="field_notice">使用标记唯一，最好使用英文和'_'组成的短语</label>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                   所属平台:</th>
                <td class="paddingT15 wordSpacing5">
                <?php if ($this->_var['data']): ?>
                    <input type='text'  disabled='true' value="<?php echo $this->_var['sites'][$this->_var['data']['site_id']]; ?>"/>
                <?php else: ?>
                        <select name="site_id" id="site_id">
                        <option value='0'>请选择</option>
                        <?php echo $this->html_options(array('options'=>$this->_var['sites'])); ?>
                    </select>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th></th>
                <td class="ptb20">
                    <input class="tijia" type="submit" name="Submit" value="提交" />
                    <input class="congzi" type="reset" name="Submit2" value="重置" />
                    <input type="hidden" id="locationid" name="id" value="<?php echo $this->_var['data']['id']; ?>"/>

                </td>
            </tr>
        </table>
    </form>
</div>

<?php echo $this->fetch('footer.html'); ?>
