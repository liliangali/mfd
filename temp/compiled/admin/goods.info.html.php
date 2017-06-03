<?php echo $this->fetch('header.html'); ?>
<style>
.ac_results {padding: 0px; border: 1px solid WindowFrame; background-color: Window; overflow: hidden; z-index: 19891020; /* 1000 */}
.ac_results ul {width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0;}
.ac_results iframe {display: none;/*sorry for IE5*/ display/**/: block;/*sorry for IE5*/ position: absolute; top: 0; left: 0; z-index: -1; filter: mask(); width: 3000px; height: 3000px;}
.ac_results li {margin: 0px; padding: 2px 5px; cursor: pointer; display: block; font: menu; font-size: 12px; overflow: hidden;}
.ac_over {background-color: Highlight; color: HighlightText;}
#thumbnails li{
	    border: 1px solid #eee;
    cursor: pointer;
    display: inline;
    float: left;
    height: 100px;
    margin: 0 10px 10px 0;
    text-align: center;
    width: 100px;
}
.dives{border:1px solid #000; padding:10px 0; width:96%; margin:0px 2% 20px 2%;} 
.infs{width:96%; margin:0 2%; line-height: 40px;}
</style>
<script type="text/javascript" src="/includes/libraries/baidu/ueditor.config.js"></script>
<script type="text/javascript" src="/includes/libraries/baidu/ueditor.all.min.js"></script>

<script type="text/javascript">
$(function(){
    $('#brand_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : {
            name : {
                required : true,
            },
            bn : {
        		required : true,
        		 remote   : {
                     url :'index.php?app=goods&act=check_bn',
                     type:'get',
                     data:{
                    	 bn : function()
                     	{
                             return $('#bn').val();
                         },
                         id : '<?php echo $this->_var['data']['goods_id']; ?>'
                     }
                     }
        	    },
        	    cat_id : {
        	    	min:1
                    
                },
                price : {
                    required : true,
                },
        },
       
        messages : {
            name : {
                required : '请填写产品名称!',
            },
            bn : {
                required : '请填写货号',
                remote   : '货号已存在',
            },
            cat_id : {
                min:'请选择分类id'
            },
            price : {
                required : '请填写销售价',
            },
            
        }
    });
});

</script>

<?php echo $this->_var['build_editor']; ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=goods&amp;act=index">管理</a></li>
        <?php if ($this->_var['data']['id']): ?>
        <li><a class="btn1" href="index.php?app=goods&amp;act=add">添加</a></li>
        <?php else: ?>
        <li><span>添加</span></li>
        <?php endif; ?>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="brand_form">
         <div class="infs"> 基本信息</div>
         <table class="dives">
            <tr>
                <th class="paddingT15">
                   产品名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="name" style="width:300px;"type="text" name="name" value="<?php echo htmlspecialchars($this->_var['data']['name']); ?>" />
                </td>
            </tr>
            
                    <tr >
                <th class="paddingT15">
                  产品分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="cat_id" id="cat_id"  onchange="cats(this)" >
                                <option value="0">请选择</option>
                                <?php $_from = $this->_var['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'op');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['op']):
?>
                                    <option value="<?php echo $this->_var['key']; ?>"  <?php if ($this->_var['data']['cat_id'] == $this->_var['key']): ?>selected<?php endif; ?>><?php echo $this->_var['op']; ?></option>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                             </select>
                </td>
            </tr>
          
            
             <tr>
                <th class="paddingT15" valign="top">
                                                                           扩展分类 :</th>
                <td class="paddingT15 wordSpacing5">
                     <table width="100%" align="center" class="attrlist">
                     <?php if ($this->_var['categorys']): ?>
                     <?php $_from = $this->_var['categorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'attr');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['attr']):
        $this->_foreach['loop']['iteration']++;
?>
                     <tr class="fitem"><td>
                     <?php if ($this->_foreach['loop']['iteration'] == 1): ?>
                     <a href="javascript:;" class="addattr">[+]</a>
                     <?php else: ?>
                     <a href="javascript:;" onclick="drop_attr(this)">[-]</a>
                     <?php endif; ?>
                    <select name="type[]" class="parent" onchange="loadChild(this)">
                                        <option value="0">--请选择--</option>
                                        <?php echo $this->html_options(array('options'=>$this->_var['options'],'selected'=>$this->_var['attr']['cate_id'])); ?>
                                    </select>
                                   
                                    </td></tr>
                     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    <?php else: ?>
                    <tr><td><a href="javascript:;" class="addattr">[+]</a>
                    <select name="type[]" class="parent" onchange="loadChild(this)">
                                        <option value="0">--请选择--</option>
                                        <?php echo $this->html_options(array('options'=>$this->_var['options'],'selected'=>$this->_var['gcategory']['theme'])); ?>
                                    </select>
                                    
                                    </td></tr>
                    <?php endif; ?>
                  </table>
                  </td>
            </tr>
            
             <tr>
                <th class="paddingT15">
                                            产品类型:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="type_id" id="type_id" onchange="change(this)">
                        <option value="0">请选择</option>
                        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'style');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['style']):
?>
                            <option value="<?php echo $this->_var['key']; ?>" <?php if ($this->_var['data']['type_id'] == $this->_var['key']): ?>selected<?php endif; ?>><?php echo $this->_var['style']; ?></option>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </select>
                </td>
            </tr>
             <tr>
                <th class="paddingT15">
                  产品图片:</th>
                <td class="paddingT15 wordSpacing5">
                     <?php echo $this->input_img(array('name'=>'big_pic','value'=>$this->_var['data']['thumbnail_pic'],'dir'=>'cst')); ?>
                </td>
            </tr>
             <tr >
                <th>产品相册图(APP) :</th>
                <td id="gallery">
                <?php echo $this->_var['build_upload']; ?>
        <table class="infoTable qdhqx_tab">
        <tr>
                <td class="paddingT15 wordSpacing5">
					 <div id="divSwfuploadContainer">
                <div id="divButtonContainer" style="float:left;">
                    <span id="spanButtonPlaceholder"></span>
                </div>
                
                <div id="divFileProgressContainer"></div>
            </div>
                </td>
            </tr>

            <tr>
            	<td>
            	文件列表(双击图片进行删除，可移动排序)
            	
					<ul id="thumbnails">
						<?php $_from = $this->_var['gallery_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'gallery');if (count($_from)):
    foreach ($_from AS $this->_var['gallery']):
?>
						  <li id="gallery_<?php echo $this->_var['gallery']['img_id']; ?>"  data_id ="<?php echo $this->_var['gallery']['img_id']; ?>"  onclick="selectImg(this)">
						  <img ondblclick="drop_gallery(<?php echo $this->_var['gallery']['img_id']; ?>)" src="<?php echo $this->_var['gallery']['thumb_url']; ?>" width="80" height="80"></li>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</ul>
				</td>
            </tr>
            </table>
                </td>
            </tr>
            
            </table>
            
      <!--  <div class="infs"> 产品规格</div> -->
         <table class="dives">     

            <tr>
                <th class="paddingT15">
                   货号:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="bn" type="text" name="bn" value="<?php echo htmlspecialchars($this->_var['data']['bn']); ?>" />
                </td>
            </tr>
           <!--  <tr>
                <th class="paddingT15">
                   重量:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="weight" type="text" name="weight" value="<?php echo htmlspecialchars($this->_var['data']['weight']); ?>" />克（g）
                </td>
            </tr> 
            <tr>
                <th class="paddingT15">
                   库存:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="store" type="text" name="store" value="<?php echo htmlspecialchars($this->_var['data']['store']); ?>" />
                </td>
            </tr>  -->
           <?php if ($this->_var['data']['goods_id']): ?>
            <tr class="displayno">
                <th class="paddingT15">
                   规格:</th>
                <td class="paddingT15 wordSpacing5">
                    <input  id="spec_desc"  onclick="onspec(this)" type="button" name="spec_desc" value="开启规格" />
                </td>
            </tr>
            <?php endif; ?>
          </table>  
            
        <div class="infs"> 产品属性</div>
         <table class="dives" id="recommend">
           <?php if ($this->_var['attrbutes']): ?>
           <?php $_from = $this->_var['attrbutes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'attrbu');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['attrbu']):
?>
           <?php if ($this->_var['attrbu']['attr_input_type'] == 1): ?>
             <tr >
                <th class="paddingT15">
                  <?php echo $this->_var['attrbu']['attr_name']; ?>:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="attr_name[<?php echo $this->_var['key1']; ?>]">
                                <option value="0">请选择</option>
                                <?php $_from = $this->_var['attrbu']['attr_values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'op');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['op']):
?>
                                    <option value="<?php echo $this->_var['op']; ?>"  <?php if ($this->_var['op'] == $this->_var['goodsattrs'][$this->_var['key1']]): ?>selected<?php endif; ?>><?php echo $this->_var['op']; ?></option>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                             </select>
                </td>
            </tr>
           <?php else: ?>
           <tr >
                <th class="paddingT15">
                  <?php echo $this->_var['attrbu']['attr_name']; ?>:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2"  type="text" name="attr_name[<?php echo $this->_var['key1']; ?>]" value="<?php echo $this->_var['goodsattrs'][$this->_var['key1']]; ?>" />
                </td>
            </tr>
            
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
           <?php endif; ?>
         </table>   
            
           <div class="infs"> 业务信息</div>
         <table class="dives">
            <tr >
            <th class="paddingT15">
               发往平台:</th>
            <td class="paddingT15 wordSpacing5">
                <input type="checkbox" name="is_pc" value="1" <?php if ($this->_var['data']['is_pc']): ?>checked="checked"<?php endif; ?>> pc
                <input type="checkbox" name="is_app" value="1" <?php if ($this->_var['data']['is_app']): ?>checked="checked"<?php endif; ?>> app
                <input type="checkbox" name="is_wap" value="1" <?php if ($this->_var['data']['is_wap']): ?>checked="checked"<?php endif; ?>> wap
            </td>
        </tr>
           <tr>
                <th class="paddingT15">
                   排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="p_order" type="text" name="p_order" value="<?php echo htmlspecialchars($this->_var['data']['p_order']); ?>" />（值越大排序越靠前）
                </td>
            </tr> 

             <tr>
                <th class="paddingT15">
                   是否允许使用优惠券:</th>
                <td class="paddingT15 wordSpacing5">
                   <input type="radio" name="is_debit" value="1" <?php if ($this->_var['data']['is_debit']): ?>checked<?php endif; ?>/>是
                   <input type="radio" name="is_debit" value="0" <?php if (! $this->_var['data']['is_debit']): ?>checked<?php endif; ?>/>否
                </td>
            </tr> 
            <tr>
                <th class="paddingT15">
                   是否上架:</th>
                <td class="paddingT15 wordSpacing5">
                   <input type="radio" name="marketable" value="1" <?php if ($this->_var['data']['marketable']): ?>checked<?php endif; ?>/>是
                   <input type="radio" name="marketable" value="0" <?php if (! $this->_var['data']['marketable']): ?>checked<?php endif; ?>/>否
                </td>
            </tr> 
         </table> 
        <!--  <div class="infs">产品简介</div>
         <table style="padding:10px 0; width:96%; margin:0px 2% 20px 2%;">
          <tr>
                
                <td class="paddingT15 wordSpacing5">
                <textarea  name="brief" id="brief" style="width:650px;height:100px;"><?php echo htmlspecialchars($this->_var['data']['brief']); ?></textarea>
                </td>
            </tr>
         </table>  -->
         
     <div class="infs"> 详细介绍</div>
            <table style="padding:10px 0; width:96%; margin:0px 2% 20px 2%;">
           <tr >
               
                <td class="paddingT15 wordSpacing5">
                <!--<textarea  name="intro" id="intro" style="width:650px;height:400px;"><?php echo htmlspecialchars($this->_var['data']['intro']); ?></textarea>-->
                </td>
            </tr>

                <script id="editor" type="text/plain" style="width:1024px;height:500px;"><?php echo $this->_var['data']['intro']; ?></script>
        <tr>
            <th></th>
            <td class="ptb20">
                <input type="hidden" name="id" value="<?php echo $this->_var['data']['id']; ?>">
                <input class="tijia" type="submit" name="Submit" value="提交" />
                <input class="congzi" type="reset" name="Submit2" value="重置" />
            </td>
        </tr>
        
         
        </table>
        
        
        
       
        
        
    </form>
</div>
<script type="text/javascript" src="/static/expand/jquery.autocomplete.js"></script>
<script src="<?php echo $this->res_base . "/" . 'js/jquery.dragsort-0.5.1.js'; ?>"></script>
<script>
    var ue = UE.getEditor('editor');
    var goods_id = "<?php echo $this->_var['data']['goods_id']; ?>";
    function cats(obj){
	var cat_id = $("#cat_id").val();
	 $.get('index.php?app=goods&act=gettype&cat_id='+cat_id,function(result){
		 var result = eval("("+result+")");
		  if(result.done == true){
	        $("#type_id").val(result.retval);
	        var type_id = $("#type_id").val();
	        if(type_id != 0){
            	$(".displayno").removeAttr("style");
            	
            	  $.get('index.php?app=goods&act=attrbutes&type_id='+type_id,function(result){
            	        var result = eval("("+result+")");
            	        if(result.done == true){
            	        	var html='';
            	        	
                                 for(var i=0;i<result.retval.length;i++){
                                	 if(result.retval[i].attr_input_type == 1)
                       	       	  {
                                 	  html+='<tr><th class="paddingT15">';
                                 	html +=result.retval[i].attr_name+'</th> <td class="paddingT15 wordSpacing5">  <select name="attr_name['+result.retval[i].attr_id+']"><option value="0">请选择</option>';
                                  for(var y=0;y<result.retval[i].attr_values.length;y++){
                                 	html +='<option value="'+result.retval[i].attr_values[y]+'">'+result.retval[i].attr_values[y]+'</option>';
                                 } 
                                  html +='</select> </td> </tr>';
                       	       }else{
                 	       		  html+='<tr><th class="paddingT15">';
                 	       		  html +=result.retval[i].attr_name+'</th> <td class="paddingT15 wordSpacing5"><input class="infoTableInput2"  type="text" name="attr_name['+result.retval[i].attr_id+']" value="" />';	
                 	       		 html +='</select> </td> </tr>';
                 	       	  }
                                 }
            $("#recommend").html(html)
            	        }else{
            	            alert(result.msg);
            	            return false;    
            	        }
            	    });
            }
	        
	        }else{
	            alert(result.msg);
	            return false;    
	        }
	 })
}



$("#thumbnails").dragsort({dragSelector: "li", dragBetween: false,placeHolderTemplate: "<li style='background-color:white; border:dashed 1px gray'></li>",dragEnd:stor});
function stor(){
	var data=[],li=$("#thumbnails li");
	for(var i=0;i<li.length;i++){
		data.push(li[i].getAttribute('data_id')+':'+i)	
	}
 
	$.ajax({
		url:"index.php?app=goods&act=gallarysort",
        type: "POST",
		data:{
			photo_id:data.join()	
		},
		success: function(){}
	})
}
function onspec(v1){
	var ids=$("#type_id  option:selected").val();
	var url = "index.php?app=products&act=setspec&goods_id="+goods_id;
	 window.open(url,'选择规格','height=600,width=800,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no')
}
 function change(obj) {
            var type_id = $("#type_id").val();
            
            if(type_id != 0){
            	$(".displayno").removeAttr("style");
            	
            	  $.get('index.php?app=goods&act=attrbutes&type_id='+type_id,function(result){
            	        var result = eval("("+result+")");
            	        if(result.done == true){
            	        	var html='';
                for(var i=0;i<result.retval.length;i++){
                	  html+='<tr><th class="paddingT15">';
                	html +=result.retval[i].attr_name+'</th> <td class="paddingT15 wordSpacing5">  <select name="attr_name['+result.retval[i].attr_id+']"><option value="0">请选择</option>';
                 for(var y=0;y<result.retval[i].attr_values.length;y++){
                	html +='<option value="'+result.retval[i].attr_values[y]+'">'+result.retval[i].attr_values[y]+'</option>';
                } 
                 html +='</select> </td> </tr>';
                }
            $("#recommend").html(html)
            	        }else{
            	            alert(result.msg);
            	            return false;    
            	        }
            	    });
            }
        }
function add_uploadedfile(file_data)
{
    var newImg = '<li id="' + file_data.file_id + '" onclick="selectImg(this)">'+
    '<input type=hidden name=gallery[] value='+SITE_URL + '/' + file_data.file_path+'>'+
    '<img width="80px" height="80px" src="' + SITE_URL + '/' + file_data.file_path + '" ondblclick="drop_uploadedfile(' + file_data.file_id + ');"/></li>';
    $('#thumbnails').prepend(newImg);
}
function selectImg(obj){
	$(obj).parents("ul").find("li").each(function(){
		$(this).removeClass("on");
	});
	
	$(obj).addClass("on");
}
function drop_uploadedfile(file_id)
{
    if(!window.confirm('确定要移除该图片吗？')){
        return;
    }

    $('#' + file_id).remove();

    $.getJSON('index.php?app=article&act=drop_uploadedfile&file_id=' + file_id, function(result){
        if(result.done){

        }else{
            alert('drop_error');
        }
    });
}
function drop_gallery(file_id)
{
    if(!window.confirm('确定要移除该图片吗？')){
        return;
    }

    $('#gallery_' + file_id).remove();
    
    $.getJSON('index.php?app=goods&act=drop_gallery&id=' + file_id, function(result){
        if(result.done == false){
            alert('drop_error');
        }
    });
}

$('.addattr').click(function(){
    var selType = [];
    var selAttr = [];
    var msg = '';
	$(".attrlist tr").each(function(){
		 var selObj = $(this).find("select");
		 if(selObj[0].selectedIndex == 0){
			  msg += '- select_type\n';
		 }

	
	     
    });
	
	if(msg){
		alert(msg);
	    return false;
	}
	
    $(this).parents(".attrlist").append('<tr class="fitem"><td> <a href="javascript:;" onclick="drop_attr(this)">[-]</a>&nbsp;'+
        '<select name="type[]" class="parent" onchange="loadChild(this)">'+
    '<option value="0">--请选择--</option>'+
    '<?php echo $this->html_options(array('options'=>$this->_var['options'],'selected'=>$this->_var['gcategory']['theme'])); ?>'+
    '</select>'+
    '</td></tr>'
    );
});
function drop_attr(obj)
{
    $(obj).parents('.fitem').remove();
}

function loadChild(obj)
{
    var value = obj.value;
    $.get("index.php?app=gcategory",{act:"loadAttr",type:value}, function(res){
 	     var res = eval("("+res+")");
  	    $(obj).next("select").empty().append('<option value="0">select_attr</option>');
  	    for(var i = 0 ; i<res.retval.length;i++){
    	     $(obj).next("select").append("<option value='" + res.retval[i].id +"'>" + res.retval[i].name + "</option>");
  	    }
    })    
}
</script>
<?php echo $this->fetch('footer.html'); ?>
