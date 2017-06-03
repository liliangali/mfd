<?php echo $this->fetch('header.html'); ?>

<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=shuffling">轮播图列表</a></li>
        <?php if ($this->_var['data']['id']): ?>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=add">添加轮播图</a></li>
        <?php else: ?>
        <li><span>轮播图添加</span></li>
        <?php endif; ?>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=group_list">轮播图图分组</a></li>
        <li><a class="btn1" href="index.php?app=shuffling&amp;act=add_group">添加轮播图分组</a></li>
    </ul>
</div>
<?php echo $this->_var['build_editor']; ?>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="dis_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    轮播图名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="name"  type="text" name="name" value="<?php echo htmlspecialchars($this->_var['data']['name']); ?>" onblur="changetitle(this)" />
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    轮播图title:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="title" type="text" name="title" value="<?php echo htmlspecialchars($this->_var['data']['title']); ?>" />
                    <label class="field_notice">默认与名称相同</label>
                </td>
            </tr>

            <tr >
                <th class="paddingT15">发布平台：</th>
                <td class="paddingT15 wordSpacing5" >
                    <select name="site_id" id="site_id" onchange="get_groups(this)">
                        <option value="0" class="site_id">请选择...</option><?php echo $this->html_options(array('options'=>$this->_var['sites'],'selected'=>$this->_var['data']['site_id'])); ?>
                    </select>
                </td>
            </tr>
			
			<tr >
				<th class="paddingT15">所属分组：</th>
                <td class="paddingT15 wordSpacing5" >
                    <select name="group" id="group">
                        <option value="0" class="group">请选择...</option>
                        <?php if ($this->_var['group']): ?><?php echo $this->html_options(array('options'=>$this->_var['group'],'selected'=>$this->_var['data']['groups'])); ?><?php endif; ?>
                    </select>
                </td>
			</tr>
			
			<tr style="display:none" class="is_blank">
                <th class="paddingT15">
                    <span style="color:red">*</span>是否新窗口打开:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="is_blank" value=1 <?php if ($this->_var['data']['is_blank']): ?>checked<?php endif; ?>>是
                    <input type="radio" name="is_blank" value=0 <?php if (! $this->_var['data']['is_blank']): ?>checked<?php endif; ?>>否
                </td>
            </tr>

            <tr >
                <th class="paddingT15">链接url：</th>
                <td class="paddingT15 wordSpacing5" >
                    <select id="url_rule"  onchange="change_url(this)"><option value=''>请选择</option><?php echo $this->html_options(array('values'=>$this->_var['url_rule_values'],'output'=>$this->_var['url_rule_names'],'selected'=>$this->_var['data']['match_url'])); ?></select><label class='field_notice'>固定url规则</label><br>
                    <?php if ($this->_var['data']['cat_id']): ?><select  id="cat_id"  onchange="setCat(this)" >
                                <option value="0">请选择</option>
                                <?php $_from = $this->_var['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'op');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['op']):
?>
                                    <option value="<?php echo $this->_var['key']; ?>"  <?php if ($this->_var['data']['cat_id'] == $this->_var['key']): ?>selected<?php endif; ?>><?php echo $this->_var['op']; ?></option>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                       </select><label class='field_notice'>固定url规则</label><br><?php endif; ?>
                    <input type="text" name="link_url" value="<?php echo $this->_var['data']['link_url']; ?>" class="link_url" id="link_url"/><label class='field_notice'>可选择固定url（需填写参数），也可以自定义url</label>
                </td>
            </tr>

            
            <tr>
                <th class="paddingT15">
                 上传图片:</th>
                <td class="paddingT15 wordSpacing5" >
                     <?php echo $this->input_img(array('name'=>'img','value'=>$this->_var['data']['img'],'dir'=>'shuffling')); ?><label class="field_notice">建议规格700*500</label>
                </td>
            </tr>
    
        <tr>
                <th class="paddingT15">
                    排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sort_order" type="text" name="sort_order" value="<?php echo $this->_var['data']['sort_order']; ?>" />
                </td>
            </tr>
			
			<tr>
                <th class="paddingT15">
                    <span style="color:red">*</span>是否显示:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="status" value=1 <?php if ($this->_var['data']['status'] || $this->_var['status']): ?>checked<?php endif; ?>>是
                    <input type="radio" name="status" value=0 <?php if ($this->_var['data']['status'] === 0): ?>checked<?php endif; ?>>否
                </td>
            </tr>
			
			<tr>
				<th></th>
				<td class="ptb20">
					<input class="tijia" type="submit" name="Submit" value="提交" />
					<input class="congzi" type="reset" name="Submit2" value="重置" />
					<input type="hidden" name="id" value="<?php echo $this->_var['data']['id']; ?>"/>

				</td>
			</tr>
        </table>
    </form>
</div>
<script type="text/javascript">
$(function(){
	var site=$("#site_id").val()
	if(site==1){
        $(".is_blank").show();
    }
})
function get_groups(obj)
{
    var p_id = $(obj).val();
    if(p_id==1){
    	$(".is_blank").show();
    }else{
    	$(".is_blank").hide();
    }
    $.post("./index.php?app=shuffling&act=ajax_get_groups",{pid:p_id}, function(res){
        if(res.done){
        	$('#group').empty();
            $('#group').append(res.retval)
        }
           
    },'json');
}
function changetitle(obj)
{
	var info=$(obj).val();
	var title=$("#title").val();
	if(title){
	}else{
		$("#title").val(info)
	}
}
function change_url(obj){
    var url=$(obj).children(':selected').val();
    $(obj).siblings('select').next().next().remove()
    $(obj).siblings('select').next().remove()
    $(obj).siblings('select').remove()
    if(url=='productlist/分类ID'){
    	var type=1
    	$.post('./index.php?app=shuffling&act=ajaxGetId',{type:type},function(res){
    		if(res.done){
    			res.retval='<br>'+res.retval+'<label class="field_notice">设置对应ID</label>'
    			
    			$(obj).next().after(res.retval)
    		}else{
    			alert(res.msg)
    		}
    	},'json')
    }
    if(url){
        $(obj).siblings('input').val(url);
    }
    
}

function setCat(obj){
	var id=$(obj).val();
	var val=$(obj).siblings('input').val();
	var arr=val.split('/')
	$(obj).siblings('input').val(arr[0]+'/'+id);
	
}
</script>
<?php echo $this->fetch('footer.html'); ?>
