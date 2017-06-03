<?php echo $this->fetch('header.html'); ?>
<style>
 .tdare tr {
    text-align:center;
    line-height:31px;
}
.tdare td{border:1px solid #CCC;} 
.yx_tatr1 td{text-align:center;}
</style>
<script src="templates/js/jquery.imagePreview.1.0.js"></script>
<script type="text/javascript">

$(function(){
    $('#motif_form').validate({
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
            subhead : {
                required : true
            },
            site_id :{
                min : 1
             },
            location_id :{
                min : 1
            },
        },
        messages:{
            title : {
                required : '位置名称不能为空'
            },
            subhead : {
                required : '请填写副标题'
            },
            site_id : {
               min : '请选择所属平台'
            },
            location_id : {
                min : '请选择位置归属'
             },
        }
    });
});


</script>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=motif">栏目列表</a></li>
        <?php if ($this->_var['data']['id']): ?>
        <li><a class="btn1" href="index.php?app=motif&amp;act=add">添加栏目</a></li>
         <?php else: ?>
        <li><span>添加栏目</span></li>
        <?php endif; ?>
        <li><a class="btn1" href="index.php?app=motif&amp;act=group_list">栏目位置列表</a></li>
        <li><a class="btn1" href="index.php?app=motif&amp;act=add_group">添加栏目位置</a></li>

    </ul>
</div>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="motif_form">
        <table class="infoTable">
            
            <tr>
                <th class="paddingT15">
                    栏目标题:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="title" type="text" name="title" value="<?php echo htmlspecialchars($this->_var['data']['title']); ?>" />
                    <?php if ($this->_var['data']): ?><?php $this->assign('switch',$this->_var['data']['title_switch']); ?><?php else: ?><?php $this->assign('switch','1'); ?><?php endif; ?>
                    
                    <?php echo $this->html_radios(array('options'=>$this->_var['switches'],'name'=>'tswitch','checked'=>$this->_var['switch'])); ?>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    副标题:</th>
                <td class="paddingT15 wordSpacing5">
                     <p><?php if ($this->_var['data']): ?><?php $this->assign('twitch',$this->_var['data']['subhead_switch']); ?><?php else: ?><?php $this->assign('twitch','1'); ?><?php endif; ?>
                    
                    <?php echo $this->html_radios(array('options'=>$this->_var['switches'],'name'=>'sswitch','checked'=>$this->_var['twitch'])); ?></p>
                    名称：<input class="infoTableInput" id="subhead" type="text" name="subhead" value="<?php echo htmlspecialchars($this->_var['data']['subhead']); ?>" />
                    链接至<input class="infoTableInput" id="subhead_link" type="text" name="subhead_link" value="<?php echo htmlspecialchars($this->_var['data']['subhead_link']); ?>" value="#" placeholder="#" onfocus="if(value=='#'){value=''}"   
                onblur="if(value==''){value='#'}" /> 

                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                   发布平台:</th>
                <td class="paddingT15 wordSpacing5">
                        <select name="site_id" id="site_id" onchange="get_locations(this)">
                        <option value='0'>请选择</option>
                        <?php echo $this->html_options(array('options'=>$this->_var['sites'],'selected'=>$this->_var['data']['site_id'])); ?>
                    </select>
                </td>
            </tr>
            
            <tr >
                <th class="paddingT15">位置归属：</th>
                <td class="paddingT15 wordSpacing5" >
                    <select name="location_id" id="location_id">
                        <option value="0" >请选择...</option>
                        <?php if ($this->_var['locations']): ?><?php echo $this->html_options(array('options'=>$this->_var['locations'],'selected'=>$this->_var['data']['location_id'])); ?><?php endif; ?>
                    </select>
                </td>
            </tr>
            
            <tr >
                <th class="paddingT15">栏目内容：</th>
                <td class="paddingT15 wordSpacing5" >
                <div class="tdare">
                    <table width="100%" cellspacing="1" class="dataTable">
                        <tr class="yx_tatr1">
                            <td>图片</td><td>链接</td><td>是否新窗口打开</td><td>排序</td><td>是否显示</td><td>操作</td>
                        </tr>
                        <?php if ($this->_var['motif_rc']): ?>
                        <?php $_from = $this->_var['motif_rc']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'content');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['content']):
?>
                        <tr class="tatr2" attr_num="<?php echo $this->_var['k']; ?>">
                            <td><?php echo $this->input_img(array('name'=>$this->_var['content']['imgname'],'dir'=>'motif','value'=>$this->_var['content']['img'],'attr_src'=>$this->_var['content']['img'],'title'=>'单击上传图片')); ?>
                                名称：<input  type="text"  class="infoTableInput" name="title<?php echo $this->_var['k']; ?>"  id="title<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['content']['title']; ?>"/><br/>
                                简介：<textarea class="infoTableInput" style="height:45px" name="intro<?php echo $this->_var['k']; ?>"  id="intro<?php echo $this->_var['k']; ?>"><?php echo $this->_var['content']['intro']; ?></textarea>
                            </td>
						    <td><select id="url_rule"  onchange="change_url(this)"><option value=''>请选择</option><?php echo $this->html_options(array('values'=>$this->_var['url_rule_values'],'output'=>$this->_var['url_rule_names'],'selected'=>$this->_var['data']['link_url'])); ?></select><br>
						    <input  type="text"  class="infoTableInput require" name="link_url<?php echo $this->_var['k']; ?>"  id="link_url<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['content']['link_url']; ?>"/></td>
						    <td><select name="is_blank<?php echo $this->_var['k']; ?>" id="is_blank<?php echo $this->_var['k']; ?>" ><option value='1' <?php if ($this->_var['content']['is_blank']): ?>selected<?php endif; ?>>是</option><option value='0' <?php if (! $this->_var['content']['is_blank']): ?>selected<?php endif; ?>>否</option></select></td>
						    <td><input type="text" name="sort_order<?php echo $this->_var['k']; ?>" id="sort_order<?php echo $this->_var['k']; ?>" class='require' value="<?php echo $this->_var['content']['sort_order']; ?>"/></td>
						    <td><select name="is_show<?php echo $this->_var['k']; ?>" id="is_show<?php echo $this->_var['k']; ?>" ><option value='1' <?php if ($this->_var['content']['is_show']): ?>selected<?php endif; ?>>是</option><option value='0' <?php if (! $this->_var['content']['is_show']): ?>selected<?php endif; ?>>否</option></select></td>
						    <td><a href="javascript:void(0)" class="exclude" onclick="if(confirm('确定删除吗？')) delete_content(this)">删除</a><input type="hidden" id="id<?php echo $this->_var['k']; ?>" name="id<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['content']['id']; ?>"/>
						    </td>
                           
                        </tr>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        <?php endif; ?>
                    </table>
                    <span><input type="hidden" id="did" name="did" value=""/></span>
                    </div>
                    <div style="color:blue" id="add_content" onclick="loadcontent(this)" style="cursor:pointer">+ 添加</div>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    栏目排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="sort_order" type="text" name="sort_order" value="<?php echo htmlspecialchars($this->_var['data']['sort_order']); ?>" />
                    <label class="field_notice">值越大越靠前，为空则按添加顺序自动排列</label>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    是否显示:</th>
                <td class="paddingT15 wordSpacing5">
                    <?php if ($this->_var['data']): ?><?php $this->assign('show',$this->_var['data']['is_show']); ?><?php else: ?><?php $this->assign('show','1'); ?><?php endif; ?>
                    <?php echo $this->html_radios(array('options'=>$this->_var['shows'],'name'=>'is_show','checked'=>$this->_var['show'])); ?>
                </td>
            </tr>

            <tr>
                <th></th>
                <td class="ptb20">
                    <input class="tijiao" type="submit" name="Submit" value="提交" />
                    <input class="congzi" type="reset" name="Submit2" value="重置" />
                    <input type="hidden" name="id" id="mid" value="<?php echo $this->_var['data']['id']; ?>"/>

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
    if(flag==1){
    	return
    }else{
    	$("#add_content").bind("onclick",loadcontent);
    }
    $.get("./index.php?app=motif&act=load_add_content",{num:num},function(res){
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
//              console.log($(this).children('.require').length) 
            if($(this).children('.require').length>0){
            	if($(this).children('.require').val().length==0){
                    $("#add_content").unbind("click");
                    flag=1;
                    if(!$(this).children('.require').next('.empty').length){
                         $(this).children('input').after("<label class='empty'><font style='color:red'>不能为空</font></label>")
                    }
                   
                    return false;
                }else if($(this).children('.require').next('.empty').length){
                    $(this).children('.empty').remove()
                }
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

function change_url(obj){
    var url=$(obj).children(':selected').val();
    if(url){
    	$(obj).siblings('input').val(url);
    }
	
}
	
	
	
</script>
<?php echo $this->fetch('footer.html'); ?>
