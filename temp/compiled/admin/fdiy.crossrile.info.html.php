<?php echo $this->fetch('header.html'); ?>
<style>
.ac_results {padding: 0px; border: 1px solid WindowFrame; background-color: Window; overflow: hidden; z-index: 19891020; /* 1000 */}
.ac_results ul {width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0;}
.ac_results iframe {display: none;/*sorry for IE5*/ display/**/: block;/*sorry for IE5*/ position: absolute; top: 0; left: 0; z-index: -1; filter: mask(); width: 3000px; height: 3000px;}
.ac_results li {margin: 0px; padding: 2px 5px; cursor: pointer; display: block; font: menu; font-size: 12px; overflow: hidden;}
.ac_over {background-color: Highlight; color: HighlightText;}
</style>
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
        },
        messages : {
        }
    });
});
</script>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=dictC&amp;act=index">管理</a></li>
        <?php if ($this->_var['data']['id']): ?>
        <li><a class="btn1" href="index.php?app=dictC&amp;act=cadd">新增</a></li>
        <?php else: ?>
        <li><span>新增</span></li>
        <?php endif; ?>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="brand_form">
        <table class="infoTable">

               <tr>
                <th class="paddingT15">
             选择定制品类:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="category" id="cat" <?php if ($this->_var['data']['category']): ?>disabled<?php endif; ?>>
                        <option value="0">请选择</option>
                        <?php $_from = $this->_var['cat_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'cat');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['cat']):
?>
                            <option value="<?php echo $this->_var['cat']['cate_id']; ?>" <?php if ($this->_var['data']['cid'] == $this->_var['cat']['cate_id']): ?>selected<?php endif; ?>><?php echo $this->_var['cat']['cate_name']; ?></option>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </select>
                    <?php if ($this->_var['data']['category']): ?>
                    <input type="hidden" name="category" value="<?php echo $this->_var['data']['category']; ?>">
                    <?php endif; ?>
                </td>
            </tr>
               <tr>
                <th class="paddingT15 wordSpacing5" valign="top">选择主属性:</th>
                <td>
                    <ul class="items" data-hname="items">
                    <?php $_from = $this->_var['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'dict');if (count($_from)):
    foreach ($_from AS $this->_var['dict']):
?>
                    <li data-id="<?php echo $this->_var['dict']['cate_id']; ?>" data-p="<?php echo $this->_var['dict']['parent_id']; ?>">
                    <input type='hidden' name='item[<?php echo $this->_var['dict']['cate_id']; ?>]' value="<?php echo $this->_var['dict']['parent_id']; ?>">
                    <input type='text' value="<?php echo $this->_var['dict']['cate_id']; ?>" autocomplete='off' readonly='readonly'>
                    <?php echo $this->_var['dict']['cate_name']; ?>
                    <span onclick='dropItem(this)' style='cursor:pointer;'>x<span> </li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    <li class="add"><input type="text" style="height: 20px;" class="diyItem"></li>
                    </ul>
                </td>
            </tr>
               <tr> <th  class="paddingT15 wordSpacing5"> </th><td>********************************************</td></tr>
               <tr> <th  class="paddingT15 wordSpacing5"></th><td>**********冲突工艺组合***************</td></tr>
               <tr> <th  class="paddingT15 wordSpacing5"></th><td>********************************************</td></tr>
            
            <tr>
               <th class="paddingT15">
          <a href="javascript:;" class="recommend" style="color:red; line-height:50px;">
         选择于其互斥的属性:</a></th>
                <td class="paddingT15 wordSpacing5 relitem" id="recommend">
		<input type="hidden" name="linkid" value="<?php echo $this->_var['data']['rules']; ?>" id="input_linkid">
                <ul>
                <?php $_from = $this->_var['crossrile']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['link']):
?>
                   <li data-id='<?php echo $this->_var['link']['cate_id']; ?>' class='item'> <a href='javascript:;'>x</a><?php echo $this->_var['link']['cate_name']; ?></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </ul>
                </td>
            </tr>
               

        <tr>
            <th></th>
            <td class="ptb20">
                <input type="hidden" name="id" value="<?php echo $this->_var['data']['cate_id']; ?>">
                <input class="tijia" type="submit" name="Submit" value="提交" />
                <input class="congzi" type="reset" name="Submit2" value="重置" />
            </td>
        </tr>
        </table>
    </form>
</div>
<script type="text/javascript" src="/static/expand/jquery.autocomplete.js"></script>


<script>
var cat = "<?php echo $this->_var['data']['category']; ?>";
$(document).ready(function(){
	loadItem(cat);
    $("#cat").change(function(){
        var val = $(this).val();
        $(".items li").each(function(){
            if(!$(this).hasClass("add")){
                $(this).remove();
            }
        })
        
        loadItem(val);
    });

})

function dropItem(obj){
    var li = $(obj).parents("li");
    var p = li.attr("data-p");
    var check = $(obj).parents("li").find("input[type=radio]").attr("checked");
    li.remove();
    $(".items li").each(function(){
    	if(check){
            if($(this).attr("data-p") == p){
               // $(this).find("input[type=radio]").attr("checked", "checked");
                return;
            }
    	}
    })

}

function loadItem(val){
	if(val.length == 0){
	    return;
	}
    $.get("index.php?app=dictC&act=loadItem&node=1	",{pid:val}, function(res){
    	var cData = eval("("+res+")");
    	data = cData.retval;
    		//alert(data.length);
    		$(".diyItem").autocomplete(data,{
                minChars: 0,//自动完成激活之前填入的最小字符
                max:20,//列表条目数
                width: 500,//提示的宽度
                scrollHeight: 200,//提示的高度
                matchContains: false,//是否只要包含文本框里的就可以
                autoFill:false,//自动填充
                //cacheLength:10000,
                formatItem: function(data, i, max) {//格式化列表中的条目 row:条目对象,i:当前条目数,max:总条目数
                   // alert(data.code);
                	return data.code + ' ['+data.name+']';
            	},
        		formatResult: function(data) {//定义最终返回的数据，比如我们还是要返回原始数据，而不是formatItem过的数据
        			return data.code; 
        		}
          }).result(function(event,data){
        	  $(this).val('');
        	  var e = true;
        	  var checked = 'checked';
        	  $(".items li").each(function(){
        		  if($(this).attr("data-id") == data.id){
          			    alert("已选了相同的工艺，请重新选择");
          			    e = false;
            	  }

            	  if($(this).attr("data-p") == data.pid){
          		       //checked = "";
                  }
              });
         
              var input = '';
              
              var html = "<li data-id='"+data.id+"' data-p='"+data.pid+"'><input type='hidden' name='item["+data.id+"]' value='"+data.pid+"'><input type='text' value='"+data.code+"' autocomplete='off' readonly='readonly'> "+ data.name+input+"     <span onclick='dropItem(this)' style='cursor:pointer;'>x<span> </li>";
              var li = $(this).parents("li");
              if(e){
            	  li.before(html);
              }
          });
    		
    })
}
</script>
<?php echo $this->fetch('footer.html'); ?>
<script>

$(document).ready(function(){
	$(".recommend").click(function(){
	    var ids=$("#input_linkid").val();
	   
	    $.cookie("ids",null);
		var url = "index.php?app=dictC&act=showItems";
		if(ids){
		    url += "&ids="+ids;
	     }
	    window.open(url,'选择互斥属性','height=800,width=800,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no')
	})

	$(".relitem").find("a").each(function(){
	$(this).click(function(){
		var temp =new Array();
		var linkids = $("#input_linkid").val();
		var id = $(this).parent("li").attr('data-id');
		
		  $(this).parent("li").remove();
		  
		  $("#input_linkid").val(removeHiddenIdsText(id,linkids));
	})
})
})

function customCallBack(ids){

	 $.post('index.php?app=dictC&act=loadItems&ids='+ids,function(result){
	        var result = eval("("+result+")");
	        if(result.done == true){
	            var html='<ul><input type="hidden" name="linkid" value="'+ids+'" id="input_linkid">';
	            for(var i=0;i<result.retval.length;i++){
	                html += "<li data-id='"+result.retval[i].id+"' class='item'> <a href='javascript:;'>x</a>"+result.retval[i].name+" </li>";
	            }
	            html += "</ul>";
	            $("#recommend").html(html);

					$(".relitem").find("a").each(function(){
						$(this).click(function(){
						var temp =new Array();
						var linkids = $("#input_linkid").val();
						var id = $(this).parent("li").attr('data-id');
						$(this).parent("li").remove();
					   $("#input_linkid").val(removeHiddenIdsText(id,linkids));
				   })
			})
	        }else{ 
	            alert(result.msg);
	            return false;    
	        }
	    });

}

function removeHiddenIdsText(value, container) {
    if (value.length == 0)
        return '';
            
    //去除前后逗号    
    value = value.replace(/^,/, '').replace(/,$/, '');
    container = container.replace(/^,/, '').replace(/,$/, '');
            
    if (container == value)
    {
        return '';
    }
            
    var sArray = container.split(',');
    for (var i = sArray.length - 1; i >= 0; --i)
    {
        if (sArray[i] == value)
            sArray[i] = undefined;
    }
            
    var result = sArray.join(',');
    //因为undefined会连接成,,所以要将,,换成,            
    result = result.replace(/,,/,',');
    result = result.replace(/^,/, '').replace(/,$/, '');
    return result;
}

</script>
