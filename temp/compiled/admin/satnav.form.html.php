<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#satnav_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {  
        	satnavname : {
        		required : true,
        		 remote   : {
                     url :'index.php?app=satnav&act=check_sat_name',
                     type:'get',
                     data:{
                    	 satnavname : function()
                     	{
                             return $('#satnavname').val();
                         },
                         id : '<?php echo $this->_var['find_data']['satnav_id']; ?>'
                     }
            }
        	},
            title : {
                required : true
            },
            sort_order:{
               number   : true
            }
        },
        messages : {
        	satnavname : {
       		 required : '必填',
                remote   : '名称已存在'
            },
            title : {
                required : 'title_empty'
            },
         
            sort_order  : {
                number   : 'number_only'
            }
        }
    });
});


</script>

<?php echo $this->_var['build_editor']; ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=satnav">导航管理</a></li>
        <li><span>导航新增</span></li>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="satnav_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                   	*导航名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id=satnavname type="text" name="satnavname" value="<?php echo htmlspecialchars($this->_var['find_data']['name']); ?>" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                                                         导航titile值:</th>
              <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id=title type="text" name="title" value="<?php echo htmlspecialchars($this->_var['find_data']['title']); ?>" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="cate_id">上级导航:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="parent_id" name="parent_id"><option value="0">一级分类</option><?php echo $this->html_options(array('options'=>$this->_var['parents'],'selected'=>$this->_var['find_data']['parent_id'])); ?></select>
                </td>
            </tr>
            
             <tr>
                <th class="paddingT15">
                                                         导航链接:</th>
              <td class="paddingT15 wordSpacing5">
                    <select id="url_rule"  onchange="change_url(this)"><option value=''>请选择</option><?php echo $this->html_options(array('values'=>$this->_var['url_rule_values'],'output'=>$this->_var['url_rule_names'],'selected'=>$this->_var['find_data']['link'])); ?></select><label class='field_notice'>固定url规则</label><br>
                    <input class="infoTableInput" id=link type="text" name="link" value="<?php echo htmlspecialchars($this->_var['find_data']['link']); ?>" /><label class='field_notice'>可选择固定url（需填写参数），也可以自定义url</label>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">图标 :</th>
                <td class="paddingT15 wordSpacing5">
                    <select class="querySelect" id="lcon" name="lcon">
                        <?php echo $this->html_options(array('options'=>$this->_var['type'],'selected'=>$this->_var['find_data']['lcon'])); ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th class="paddingT15">
                    排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sort_order" type="text" name="sort_order" value="<?php echo $this->_var['find_data']['sort_order']; ?>" />
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    <label for="if_show">是否新窗口打开:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <input id="yes" type="radio" name="alone" value="1" <?php if ($this->_var['find_data']['alone'] == 1): ?> checked="checked"<?php endif; ?> />
                    <label for="yes">是</label>
                    <input id="no" type="radio" name="alone" value="0" <?php if ($this->_var['find_data']['alone'] == 0): ?> checked="checked"<?php endif; ?> />
                    <label for="no">否</label>
                </td>
            </tr>
            
            
            <tr>
                <th class="paddingT15">
                    <label for="if_show">显示:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <input id="yes" type="radio" name="if_show" value="1" <?php if ($this->_var['find_data']['if_show'] == 1): ?> checked="checked"<?php endif; ?> />
                    <label for="yes">是</label>
                    <input id="no" type="radio" name="if_show" value="0" <?php if ($this->_var['find_data']['if_show'] == 0): ?> checked="checked"<?php endif; ?> />
                    <label for="no">否</label>
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

function searchGoods(obj)
{
    	var catid = $(obj).parents("td").children("select").val();
    	var goods_name = $(obj).parents("td").children("input").val();
    	
    	if(!catid && goods_name.length ==0){
 		    alert('alert_catandgoods');
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
  	  	  		$("#goodsid").empty().append("<option value=0>-=nogoods=-</option>")
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
	        msg = 'goods_exist';
		}
	})
	
	if(msg)
	{
	    alert(msg);
	    return false;
	}	
   ObjGoods.append("<li><input type='checkbox' checked='true' name='ids[]' value='"+goodsId.val()+"'> "+goodsId.find("option:selected").text()+"</li>");
}

function change_url(obj){
    var url=$(obj).children(':selected').val();
    if(url){
        $(obj).siblings('input').val(url);
    }
    
}

</script>
<?php echo $this->fetch('footer.html'); ?>
