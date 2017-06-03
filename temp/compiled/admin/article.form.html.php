<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#article_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {    
            title : {
                required : true
            },
            cate_id :{
                required : true,
            },
            code    :{
                remote     : {
                	url:'index.php?app=article&act=ajax_code_unique',
                	type:'post',
                	data:{
                		id:function(){
                			return $("#articleid").val();
                		},
                		code:function(){
                			return $("#code").val();
                		}		
                	}
                }
            },
            sort_order:{
               number   : true
            },
            brief:{
                maxlength:100
            }
        },
        messages : {
            title : {
                required : '文章标题不能为空'
            },
            cate_id : {
                required : '文章分类不能为空'
            },
            code    : {
                remote     : '该文章标记已被使用，请换用其他标识'
            },
            sort_order  : {
                number   : '此项必须为数字'
            },
            brief:{
                maxlength:'{简介长度不超过100个字}'
            }
        }
    });
});
/*jQuery.validator.addMethod("belong", function(value, element) {   

    // var from = $("#time_from").val();
    // return value >= from;
    var flag=1;
    var img=$('#img').val()
    var category_id=$("#cate_id").children(':selected').val()
    if(!category_id){
        return false
    }
    $.get('index.php?app=article&act=ajax_is_classroom',{id:category_id},function(res){
            if(res){
                return value
            }else{
                return 0
            }
      },'json')
    
     return flag

}, "麦富迪讲堂的文章要上传展示图片");*/

</script>

<?php echo $this->_var['build_editor']; ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=article">管理</a></li>
        <?php if ($this->_var['article']['article_id']): ?>
        <li><a class="btn1" href="index.php?app=article&amp;act=add">新增</a></li>
        <?php else: ?>
        <li><span>新增</span></li>
        <?php endif; ?>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="article_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    标题:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="title" type="text" name="title" value="<?php echo htmlspecialchars($this->_var['article']['title']); ?>" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    文章标记:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="code" type="text" name="code" value="<?php echo htmlspecialchars($this->_var['article']['code']); ?>" />
                    <input id="articleid" type="hidden" value="<?php echo $this->_var['id']; ?>" />
                    <label	class="field_notice">使用标记唯一，最好使用英文和'_'组成的短语</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="cate_id">所属分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="cate_id" name="cate_id"><option value="">请选择...</option><?php echo $this->html_options(array('options'=>$this->_var['parents'],'selected'=>$this->_var['article']['cate_id'])); ?></select>
                </td>
            </tr>
            
           <!--             <tr>
                <th class="paddingT15">
                    <label for="cate_id">商品分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="parent_id" name="goods_cat">
                    <option value="0">请选择...</option>
                     <?php echo $this->html_options(array('options'=>$this->_var['cat_list'],'selected'=>$this->_var['article']['goods_cat'])); ?>
                    </select>
                </td>
            </tr>
            
           <tr>
                <th class="paddingT15">
                    <label for="cate_id">所属商铺:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="city_id" name="city_id" onchange="selectShop(this)"><option value="">请选择...</option><?php echo $this->html_options(array('options'=>$this->_var['city_list'],'selected'=>$this->_var['article']['city_id'])); ?></select>
                      <select id="shop_id" name="shop_id">
                    <option value="0">请选择商铺:</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['site_list'],'selected'=>$this->_var['article']['shop_id'])); ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    链接:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="link" type="text" name="link" value="<?php echo htmlspecialchars($this->_var['article']['link']); ?>" />
                </td>
            </tr>
			-->
            <tr>
                <th class="paddingT15">
                    <label for="if_show">显示:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <input id="yes" type="radio" name="if_show" value="1" <?php if ($this->_var['article']['if_show'] == 1): ?> checked="checked"<?php endif; ?> />
                    <label for="yes">是</label>
                    <input id="no" type="radio" name="if_show" value="0" <?php if ($this->_var['article']['if_show'] == 0): ?> checked="checked"<?php endif; ?> />
                    <label for="no">否</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sort_order" type="text" name="sort_order" value="<?php echo $this->_var['article']['sort_order']; ?>" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    展示图片:</th>
                <td class="paddingT15 wordSpacing5 img">
                     <?php echo $this->input_img(array('name'=>'img','value'=>$this->_var['article']['img'],'dir'=>'article')); ?>
                     <label class='field_notice' style='color:#e66800'>麦富迪讲堂的文章要上传展示图片</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    文章简介:</th>
                <td class="paddingT15 wordSpacing5">
                    <textarea name='brief' id='brief' rows="300" cols="200"><?php echo $this->_var['article']['brief']; ?></textarea>
                    <label class='field_notice' style='color:#e66800'>麦富迪讲堂的文章要填写简介</label>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    <label for="article">文章内容:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <!-- <textarea id="article" name="content" style="width:650px;height:400px;"><?php echo htmlspecialchars($this->_var['article']['content']); ?></textarea> -->
                    <!-- 加载Ueditor编辑器的容器 -->
                    <div style="width:50"><script id="container" name="content" type="text/plain">
                        <?php echo $this->_var['article']['content']; ?>
                    </script></div>
                </td>
            </tr>
         
            
        <tr>
            <th></th>
            <td class="ptb20">
                <input class="tijia" type="submit" name="Submit" value="提交" />
                <input class="congzi" type="reset" name="Submit2" value="重置" />
            </td>
        </tr>
        </table>
    </form>

    
</div>
<!-- 配置文件 -->
    <script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'Ueditor/ueditor.config.js'; ?>"></script>
<!-- 编辑器源码文件 -->
    <script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'Ueditor/ueditor.all.js'; ?>"></script>

<script>
<!-- 实例化编辑器 -->
var editor = UE.getEditor('container');
/*$('#article_form').submit(function(){
    var flag=1;
    var img=$('input[name=img]').val()
    var brief=$('textarea[name=brief]').val()
    var category_id=$("#cate_id").children(':selected').val()
    $('textarea[name=brief]').next().remove()
    $('input[name=img]').next().remove()
    if(!category_id){
        // $('#article_form').submit()
         return true
    }
    console.log(444)
    $.get('index.php?app=article&act=ajax_is_classroom',{id:category_id,img:img,brief:brief},function(res){
            if(res.done){
                console.log(111)
                  $('#article_form').submit()
                 return true
            }else{
                if(res.retval=='img'){
                    $('input[name=img]').after("<label class='field_notice' style='color:red'>"+res.msg+"</label>")
                }else{
                    $('textarea[name=brief]').after("<label class='field_notice' style='color:red'>"+res.msg+"</label>")
                }
                console.log(22)
                return false
            }
      },'json')
     return false;
     
})*/


</script>
<?php echo $this->fetch('footer.html'); ?>
