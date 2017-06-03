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
    $('#acategory_form').validate({
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
/*             cate_name : {
                required : true,
                remote   : {
                url :'index.php?app=diyM&act=check_acategory',
                type:'get',
                data:{
                    cate_name : function(){
                        return $('#cate_name').val();
                    },
                    parent_id : function() {
                        return $('#parent_id').val();
                    },
                    id : '<?php echo $this->_var['fdiy_management']['cate_id']; ?>'
                  }
                }
            }, */
            sort_order : {
                number   : true
            }
        },
        messages : {
            cate_name : {
                required : '！分类名称必填',
                remote   : '！已存在该分类'
            },
            sort_order  : {
                number   : '排序仅能为数字'
            }
        }
    });
});
</script>
<div id="rightTop">

    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=diyM">管理</a></li>
        <li><?php if ($this->_var['fdiy_management']['cate_id']): ?><a class="btn1" href="index.php?app=diyM&amp;act=add">新增</a><?php else: ?><span>新增</span><?php endif; ?></li>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="acategory_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    分类名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="cate_name" type="text" name="cate_name" value="<?php echo htmlspecialchars($this->_var['fdiy_management']['cate_name']); ?>" />
                    <label class="field_notice">分类名称</label>              </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    <label for="parent_id">上级分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="parent_id" name="parent_id"><option value="0">请选择...</option><?php echo $this->html_options(array('options'=>$this->_var['parents'],'selected'=>$this->_var['fdiy_management']['parent_id'])); ?></select>                </td>
            </tr>
            
            
             <tr>
            <th><FONT  style="color: rgb(209, 72, 54);" >提示：</FONT></th>
           <td><FONT  style="color: rgb(209, 72, 54);" >输入id,只有维护在属性管理且显示的一级分类</FONT></td></tr>

               <tr>
                <th class="paddingT15 wordSpacing5" valign="top">关联定制项:</th>
                <td>
                    <ul class="items" data-hname="items">
                    <?php $_from = $this->_var['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'dict');if (count($_from)):
    foreach ($_from AS $this->_var['dict']):
?>
                    <li data-id="<?php echo $this->_var['dict']['item_id']; ?>" data-p="<?php echo $this->_var['dict']['menu_id']; ?>">
                    <input type='hidden' name='item[<?php echo $this->_var['dict']['cate_id']; ?>]' value="<?php echo $this->_var['dict']['id']; ?>">
                    <input type='text' value="<?php echo $this->_var['dict']['cate_id']; ?>" autocomplete='off' readonly='readonly'>
                    <?php echo $this->_var['dict']['cate_name']; ?><?php if ($this->_var['dict']['assign']): ?> <input type='text' name="assign[<?php echo $this->_var['dict']['item_id']; ?>]" value="<?php echo $this->_var['dict']['assign']; ?>" autocomplete='off' readonly='readonly'><?php endif; ?>
                    <span onclick='dropItem(this)' style='cursor:pointer;'>x<span> </li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    <li class="add"><input type="text" value="<?php echo $this->_var['fdiy_management']['blcode']; ?>" style="height: 20px;" class="diyItem"></li>
                    </ul>
                </td>
            </tr>
            
            	<tr>
            <th>分类图:</th>
            <td height="100" valign="top">
            	 <?php echo $this->input_img(array('name'=>'small_img','value'=>$this->_var['fdiy_management']['small_img'],'dir'=>'dcategory')); ?>
            </td>
            </tr>
 
            
            <tr>
                <th class="paddingT15">
                    排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sort_order" type="text" name="sort_order" value="<?php echo $this->_var['fdiy_management']['sort_order']; ?>" />
                    <label class="field_notice">更新排序</label>             </td>
            </tr>
            
             <tr>
                <th class="paddingT15">
                    <label for="if_show">显示:</label></th>
                <td class="paddingT15 wordSpacing5">
                 <?php if ($this->_var['fdiy_management']): ?>
                <?php echo $this->html_radios(array('name'=>'if_show','options'=>$this->_var['show_items'],'checked'=>$this->_var['fdiy_management']['if_show'])); ?>
                <?php else: ?>
                <label>                	
                	
                  <input type="radio" name="if_show" value="1" checked="checked" />
                  是</label>
                <label>
                  <input type="radio" name="if_show" value="0" />
                  否</label> 
                  <?php endif; ?>
            </tr>	

          <tr>
            <th></th>
            <td class="ptb20">
                <input class="tijia" type="submit" name="Submit" value="提交" />
                <input class="congzi" type="reset" name="reset" value="重置" />            </td>
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
    $(".add").show();
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
    $.get("index.php?app=dictC&act=loadItem",{cat:val}, function(res){

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
                    checked = "";
                }
            });

            var input = '';
            
            var html = "<li data-id='"+data.id+"' data-p='"+data.pid+"'><input type='hidden' name='item["+data.id+"]' value='"+data.id+"'><input type='text' value='"+data.code+"' autocomplete='off' readonly='readonly'> "+ data.name+input+"<span onclick='dropItem(this)' style='cursor:pointer;'>x<span> </li>";
            var li = $(this).parents("li");
            if(e){
                li.before(html);
            }
        });
    })
}
</script>
<?php echo $this->fetch('footer.html'); ?>
