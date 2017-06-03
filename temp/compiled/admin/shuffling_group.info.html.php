<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#dis_form').validate({
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
                    url:'index.php?app=shuffling&act=ajax_code_unique',
                    type:'post',
                    data:{
                        id:function(){
                            return $("#gid").val();
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
                required : 'name_empty'
            },
            site_id : {
               min : 'site_empty'
            },
            code    : {
                remote     : 'code_unique'
            },
        }
    });
});
</script>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=shuffling">轮播图列表</a></li>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=add">添加轮播图</a></li>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=group_list">轮播图分组列表</a></li>
        <?php if ($this->_var['data']['id']): ?>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=add_group">添加轮播图分组</a></li>
        <?php else: ?>
        <li><span>轮播图分组添加</span></li>
        <?php endif; ?>
    </ul>
</div>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="dis_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                   分组所属站点:</th>
                <td class="paddingT15 wordSpacing5">
                        <?php if ($this->_var['data']): ?>
                        <input type='text' readonly='readonly' disabled='true' value="<?php echo $this->_var['sites'][$this->_var['data']['site_id']]; ?>"/>
                        <?php else: ?>
                        <select name="site_id" id="site_id">
                        <?php echo $this->html_options(array('options'=>$this->_var['sites'])); ?>
                    </select>
                        <?php endif; ?>
                        
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    轮播图分组名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="name" type="text" name="name" value="<?php echo htmlspecialchars($this->_var['data']['name']); ?>" />
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    分组标记:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="code" type="text" name="code" value="<?php echo htmlspecialchars($this->_var['data']['code']); ?>" /><label class="field_notice">使用标记唯一，最好使用英文和'_'组成的短语</label>
                </td>
            </tr>


            <tr>
                <th></th>
                <td class="ptb20">
                    <input class="tijia" type="submit" name="Submit" value="提交" />
                    <input class="congzi" type="reset" name="Submit2" value="重置" />
                    <input type="hidden" name="id" id="gid" value="<?php echo $this->_var['data']['id']; ?>"/>

                </td>
            </tr>
        </table>
    </form>
</div>

<?php echo $this->fetch('footer.html'); ?>
