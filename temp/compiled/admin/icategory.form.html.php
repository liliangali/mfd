<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#icategory_form').validate({
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
            cate_name : {
                required : true,
/*                 remote   : {
                url :'index.php?app=icategory&act=check_icategory',
                type:'get',
                data:{
                    cate_name : function(){
                        return $('#cate_name').val();
                    },
                    parent_id : function() {
                        return $('#parent_id').val();
                    },
                    id : '<?php echo $this->_var['icategory']['cate_id']; ?>'
                  }
                } */
            },
/*             sort_order : {
                number   : true
            } */
        },
        messages : {
            cate_name : {
                required : '消息分类名称不能为空',
//                 remote   : '该分类名称已经存在了，请您换一个'
            },
/*             sort_order  : {
                number   : '排序仅能为数字'
            } */
        }
    });
});
</script>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=icategory">管理</a></li>
        <li><?php if ($this->_var['icategory']['cate_id']): ?><a class="btn1" href="index.php?app=icategory&amp;act=add">新增</a><?php else: ?><span>新增</span><?php endif; ?></li>
         <li><a class="btn1" href="index.php?app=messagetemplate&amp;act=index">消息管理</a></li>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="icategory_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    分类名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="cate_name" type="text" name="cate_name" value="<?php echo htmlspecialchars($this->_var['icategory']['cate_name']); ?>" />
                    <label class="field_notice">消息分类的名称</label>              </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="parent_id">上级分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="parent_id" name="parent_id"><option value="0">请选择...</option><?php echo $this->html_options(array('options'=>$this->_var['parents'],'selected'=>$this->_var['icategory']['parent_id'])); ?></select>                </td>
            </tr>
<!--             <tr>
                <th class="paddingT15">
                    分类类型:</th>
                <td class="paddingT15 wordSpacing5">
                    <input  class="cate_type" id="cate_type" type="text" name="type" style="width:300px" value="<?php echo $this->_var['icategory']['type']; ?>" />
                    <label class="field_notice">更新分类类型</label>                                 </td>
            </tr> -->

          <tr>
            <th></th>
            <td class="ptb20">
                <input class="tijia" type="submit" name="Submit" value="提交" />
                <input class="congzi" type="reset" name="reset" value="重置" />            </td>
        </tr>
        </table>
    </form>
</div>
<?php echo $this->fetch('footer.html'); ?>
