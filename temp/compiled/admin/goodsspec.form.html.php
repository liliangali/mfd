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
        <li><a class="btn1" href="index.php?app=goodsspec">管理</a></li>
    </ul>
</div>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="type_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    	规格名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="spec_name" type="text" name="spec_name" value="<?php echo htmlspecialchars($this->_var['info']['spec_name']); ?>" required/> 
                </td>
            </tr>
             <tr>
                <th class="paddingT15">
                    	规格备注:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="spec_memo" type="text" name="spec_memo" value="<?php echo htmlspecialchars($this->_var['info']['spec_memo']); ?>" /> 
                </td>
            </tr>
             <tr>
                <th class="paddingT15">
                    	规格别名:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="alias" type="text" name="alias" value="<?php echo htmlspecialchars($this->_var['info']['alias']); ?>" />(|分隔,前后|) 
                </td>
            </tr>
              <tr> 
              <th class="paddingT15">显示类型:</th>
              <td class="paddingT15 wordSpacing5"><p>
                <label>
                  <input type="radio" name="spec_type" value="text" <?php if (! $this->_var['info']['spec_type'] || $this->_var['info']['spec_type'] == 'text'): ?>checked="checked"<?php endif; ?> />
                  文字</label>
                <label>
                  <input type="radio" name="spec_type" value="image" <?php if ($this->_var['info']['spec_type'] == 'image'): ?>checked="checked"<?php endif; ?> />
                  图片</label> 
              </p></td>
            </tr>        
            
               <tr >
                <th class="paddingT15">规格值：</th>
                <td class="paddingT15 wordSpacing5" >
                <div class="tdare">
                    <table width="100%" cellspacing="1" class="dataTable">
                        <tr class="yx_tatr1">
                            <td>图片</td><td>规格值</td><!-- <td>是否新窗口打开</td> --><td>排序</td><!-- <td>是否显示</td> --><td>操作</td>
                        </tr>
                        <?php if ($this->_var['spec_value_list']): ?>
                        <?php $_from = $this->_var['spec_value_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'content');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['content']):
?>
                        <tr class="tatr2" attr_num="<?php echo $this->_var['k']; ?>">
                            <td><?php echo $this->input_img(array('name'=>$this->_var['content']['img_name'],'dir'=>'specvalue','value'=>$this->_var['content']['spec_image'],'attr_src'=>$this->_var['content']['img'],'title'=>'单击上传图片')); ?></td>
						    <td><input  type="text"  class="infoTableInput" name="aspec_value[<?php echo $this->_var['content']['spec_value_id']; ?>]"  id="link_url<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['content']['spec_value']; ?>"/><br/></td>
						
						    <td><input type="text" name="asort_value[<?php echo $this->_var['content']['spec_value_id']; ?>]" id="sort_order<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['content']['p_order']; ?>"/></td>
						   
						    <td><a href="javascript:void(0)" class="exclude" onclick="if(confirm('确定删除吗？')) delete_content(this,<?php echo $this->_var['k']; ?>)">删除</a><input type="hidden" id="id<?php echo $this->_var['k']; ?>" name="id<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['content']['id']; ?>"/>
						    </td>
                           
                        </tr>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        <?php endif; ?>
                    </table>
                    <span><input type="hidden" id="did" name="did" value=""/></span>
                    </div>
                    <div style="color:blue" id="add_content" onclick="loadcontent(this)" style="cursor:pointer">+ 添加规格值</div>
                </td>
            </tr>
            
            
            
            <tr> 
              <th class="paddingT15">显示方式:</th>
              <td class="paddingT15 wordSpacing5"><p>
                <label>
                  <input type="radio" name="spec_show_type" value="flat" <?php if (! $this->_var['info']['spec_show_type'] || $this->_var['info']['spec_show_type'] == 'flat'): ?>checked="checked"<?php endif; ?> />
                  平铺</label>
                <label>
                  <input type="radio" name="spec_show_type" value="select" <?php if ($this->_var['info']['spec_show_type'] == 'select'): ?>checked="checked"<?php endif; ?> />
                  下拉框</label>
              </p></td>
            </tr> 
          <tr>
            <th></th>
            <td class="ptb20">
            	<input type="hidden" name="id" value="<?php echo $this->_var['info']['spec_id']; ?>" />
                <input class="tijia" type="submit" id="Submit" name="Submit" value="提交" />
                <input class="congzi" type="reset" name="reset" value="重置" />            
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
    if($('.tatr2').length){
    	$('.tatr2:last').children().each(function(){
    		if($(this).val().length==0){
    			$("add_content").attr("disabled",'true');
    		}
    	})
    }
    
    $("a.preview").preview()    
    
    $("#motif_form").submit(function(){
    	var flag=0;
    	var sflag=check_content(flag)
    	flag=sflag?sflag:0;
    	if(flag){
    		return false;
    	}
    })
})

function delete_content(obj){
	var did=$(obj).siblings('input').val()
	var curr_did=$("#did").val()
	if(curr_did!=''){
		did=curr_did+","+did
	}
	
	$(obj).parents("tr .tatr2").slideUp(1000,function(){
	       $(this).remove();
	       $("#did").val(did)
	})
}
function get_locations(obj)
{
    var site_id = $(obj).val();

    $.post("./index.php?app=motif&act=ajax_get_locations",{siteid:site_id}, function(res){
        if(res.done){
            $('#location_id').empty();
            $('#location_id').append(res.retval)
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
function loadcontent(obj)
{
	var flag=0;
	var mid=$("#mid")
	if($('.tatr2')){
		var num=$('.tatr2:last').attr('attr_num');
	}else{
		var num=1
	}
	rflag=check_content(flag)
	flag=rflag?rflag:0;
	flag = 0;
    if(flag==1){
    	return
    }else{
    	$("#add_content").bind("onclick",loadcontent);
    }
    $.get("./index.php?app=goodsspec&act=load_add_content",{num:num},function(res){
    	if(res.done){
            $('.dataTable').append(res.retval)
            $(this).attr('disabled','true');
        }
    },'json');
}

function check_content(flag){
// 	console.log(flag)
	$('.tatr2').each(function(){
        $(this).children().each(function(){
            //图片是否上传
            if($(this).find('div input').length){
                if($(this).find('div input').val().length==0){
                    $("#add_content").unbind("click");
                    flag=1;
                    if(!$(this).find('.empty').length){
                    	$(this).find('div input').after("<label class='empty'><font style='color:red'>请上传图片</font></label>")
                    }
                    
                    return false;
                }else{
                	if($(this).find('.empty').length){
                		$(this).find('.empty').remove()
                	}
                    return true;
                }
            }
            //其他值是否填写
            //到删除链接没有值直接跳出
            if($(this).children('.exclude').length){
                return 
            }
//              console.log($(this).children())
//             console.log($(this).children().val().length) 
            if($(this).children().val().length==0){
                $("#add_content").unbind("click");
                flag=1;
                if(!$(this).children().next('.empty').length){
                	 $(this).children('input').after("<label class='empty'><font style='color:red'>不能为空</font></label>")
                }
               
                return false;
            }else if($(this).children().next('.empty').length){
            	$(this).children('.empty').remove()
            }
            if(flag){
                return false;
            }
                
        })
        if(flag){
            return false;
        }
    })
    return flag
}

</script>
<?php echo $this->fetch('footer.html'); ?>
