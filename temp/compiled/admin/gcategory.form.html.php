<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#gcategory_form').validate({
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
                remote   : {                
                url :'index.php?app=gcategory&act=check_gcategory',
                type:'get',
                data:{
                    cate_name : function(){
                        return $('#cate_name').val();
                    },
                    parent_id : function() {
                        return $('#parent_id').val();
                    },
                    id : '<?php echo $this->_var['gcategory']['cate_id']; ?>'
                  }
                }
            },
            sort_order : {
                number   : true
            }
        },
        messages : {
            cate_name : {
                required : '分类名称不能为空',
                remote   : '该分类名称已经存在了，请您换一个'
            },
            sort_order  : {
                number   : '分类排序仅能为数字'
            }
        }
    });
});
</script>
<div id="rightTop">
    
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=gcategory">管理</a></li>
        <li><?php if ($this->_var['gcategory']['cate_id']): ?><a class="btn1" href="index.php?app=gcategory&amp;act=add">添加</a><?php else: ?><span>添加</span><?php endif; ?></li>
    </ul>
</div>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="gcategory_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    分类名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="cate_name" type="text" name="cate_name" value="<?php echo htmlspecialchars($this->_var['gcategory']['cate_name']); ?>" /> <label class="field_notice">分类名称</label>               </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    <label for="parent_id">上级分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="parent_id" name="parent_id"><option value="0">请选择...</option><?php echo $this->html_options(array('options'=>$this->_var['parents'],'selected'=>$this->_var['gcategory']['parent_id'])); ?></select> <label class="field_notice">上级分类</label>               </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sort_order" type="text" name="sort_order" value="<?php echo $this->_var['gcategory']['sort_order']; ?>" />  <label class="field_notice">更新排序</label>              </td>
            </tr>
            
            
                                            
            <tr> 
              <th class="paddingT15">是否显示:</th>
              <td class="paddingT15 wordSpacing5"><p>
                <label>
                  <input type="radio" name="if_show" value="1" <?php if ($this->_var['gcategory']['if_show']): ?>checked="checked"<?php endif; ?> />
                  是</label>
                <label>
                  <input type="radio" name="if_show" value="0" <?php if (! $this->_var['gcategory']['if_show']): ?>checked="checked"<?php endif; ?> />
                  否</label> <label class="field_notice">新增的分类名称是否显示</label>
              </p></td>
            </tr>
            
            
            <!--   <tr>
                <th class="paddingT15">
                    SEO 关键字:</th>
                <td class="paddingT15 wordSpacing5">
                    
                 <input id="keywords" type="text" name="keywords" value="<?php echo $this->_var['gcategory']['keywords']; ?>" />   
                  <label class="field_notice">页面头部关键字</label>              </td>
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
<script>
$('.addattr').click(function(){
    var selType = new Array();
    var selAttr = new Array();
    var msg = '';
	$(".attrlist tr").each(function(){
		 var selObj = $(this).find("select");
		 if(selObj[0].selectedIndex == 0){
			  msg += '- 请选择类型\n';
		 }

		 if(selObj[1].selectedIndex == 0){
			  msg += '- 请选择属性\n';
	     }else{
	    	  for(var i=0;i<selAttr.length;i++){
	    		  if(selAttr[i] == selObj[1].value){
	    			    msg += '- 属性已存在\n';
		    	  }
		      }
		      selAttr.push(selObj[1].value);
		 }

	     
    })
	
	if(msg){
		alert(msg);
	    return false;
	}
	
    $(this).parents(".attrlist").append('<tr class="fitem"><td> <a href="javascript:;" onclick="drop_attr(this)">[-]</a>&nbsp;'+
        '<select name="type" class="parent" onchange="loadChild(this)">'+
    '<option value="0">请选择类型</option>'+
    '<?php echo $this->html_options(array('options'=>$this->_var['type_list'],'selected'=>$this->_var['gcategory']['theme'])); ?>'+
    '</select>'+
    '<select name="attrid[]" class="child">'+
    '<option value="0">请选择属性</option>'+
    '</select></td></tr>'
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
  	    $(obj).next("select").empty().append('<option value="0">请选择属性</option>');
  	    for(var i = 0 ; i<res.retval.length;i++){
    	     $(obj).next("select").append("<option value='" + res.retval[i].id +"'>" + res.retval[i].name + "</option>");
  	    }
    })    
}

function searchGoods(obj)
{
    	var catid = $(obj).parents("td").children("select").val();
    	var goods_name = $(obj).parents("td").children("input").val();
    	
    	if(!catid && goods_name.length ==0){
 		    alert('请选择分类或者填写商品名称');
 		    return false;
        }
        
    	$.get("index.php?app=gcategory",{act:"loadGoods", catid:catid, name:goods_name}, function(res){
  		    var res = eval("("+ res +")");
  		    if(res.retval.length > 0){
    		      var opt = '';
    		      for(var i=0;i<res.retval.length;i++){
   		    	      opt += "<option value='"+res.retval[i].id+"'>"+res.retval[i].name+"</option>"
        		  }
    		      $("#goodsid").empty().append(opt);
  	  		}else{
  	  	  		$("#goodsid").empty().append("<option value=0>-=未匹配到商品=-</option>")
  	  	  	}
        })
}

function selectGoods()
{
	var ObjGoods = $("#goods_list");
	var goodsId = $("#goodsid");
	var msg = '';
	ObjGoods.find("input").each(function(){
	    if($(this).val() == goodsId.val()){
	        msg = '商品已存在';
		}
	})
	
	if(msg)
	{
	    alert(msg);
	    return false;
	}	
   ObjGoods.append("<li><input type='checkbox' checked='true' name='ids[]' value='"+goodsId.val()+"'> "+goodsId.find("option:selected").text()+"</li>");
}
</script>
<?php echo $this->fetch('footer.html'); ?>
