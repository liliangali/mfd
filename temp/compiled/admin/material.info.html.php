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
            name : {
                required : true,
            },
            price : {
                required : true,
            },
            code : {
                required : true,
            },
        },
        messages : {
            name : {
                required : '请填写基料名称',
            },
            price : {
                required : "请填写基料价格",
            },
            code : {
                required : "请填写编码",
            },
        }
    });
});
</script>
<?php echo $this->_var['build_editor']; ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=Material&amp;act=index">管理</a></li>
        <?php if ($this->_var['data']['base_id']): ?>
        <li><a class="btn1" href="index.php?app=Material&amp;act=add">新增</a></li>
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
                   编码:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="material_code" type="text" name="material_code" value="<?php echo htmlspecialchars($this->_var['data']['material_code']); ?>" />
                </td>
        </tr>
        <tr>
            <th class="paddingT15"> 基料编码:</th>
            <td class="paddingT15 wordSpacing5">
            <select  name="bm_code" >
                <option value=0>请选择</option>
                <?php echo $this->html_options(array('options'=>$this->_var['base_codes'],'selected'=>$this->_var['data']['bm_code'])); ?>
             </select>
             </td>
        </tr>
        <tr>
            <th class="paddingT15"> 功效编码❶:</th>
            <td class="paddingT15 wordSpacing5">
            <select  name="ea_code_first" onchange="selectSecond(this)">
                <option value=0>请选择</option>
                <?php echo $this->html_options(array('options'=>$this->_var['ea_codes1'],'selected'=>$this->_var['data']['ea_code_first'])); ?>
             </select>
             <?php if ($this->_var['data']['ea_code_second']): ?>功效编码❷：<?php endif; ?>
             <select  name="ea_code_second" id="ea_code_second" onchange="selectThird(this)"
                <?php if (! $this->_var['data']['ea_code_second']): ?>style='display:none'<?php endif; ?>
             >
                    <option value=0>请选择</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['ea_codes2'],'selected'=>$this->_var['data']['ea_code_second'])); ?>
             </select>
             <?php if ($this->_var['data']['ea_code_third']): ?>功效编码❸：<?php endif; ?>
             <select  name="ea_code_third" id="ea_code_third" <?php if (! $this->_var['data']['ea_code_third']): ?>style='display:none'<?php endif; ?>>
                    <option value=0>请选择</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['ea_codes3'],'selected'=>$this->_var['data']['ea_code_third'])); ?>
             </select>
             </td>
        </tr>
        <tr>
            <th class="paddingT15"> 口味:</th>
            <td class="paddingT15 wordSpacing5">
            <select  name="taste_id" >
                <option value=0>请选择</option>
                <?php echo $this->html_options(array('options'=>$this->_var['tastes'],'selected'=>$this->_var['data']['taste_id'])); ?>
             </select>
             </td>
        </tr>
            <!-- <tr>
                <th class="paddingT15">
             板块选择:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="style" id="style">
                        <option value="0">请选择</option>
                        <?php $_from = $this->_var['style_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'style');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['style']):
?>
                            <option value="<?php echo $this->_var['key']; ?>" <?php if ($this->_var['data']['style'] == $this->_var['key']): ?>selected<?php endif; ?>><?php echo $this->_var['style']; ?></option>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </select>
                </td>
            </tr> -->
            
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
<script type="text/javascript" src="/static/expand/jquery.autocomplete.js"></script>
<script src="<?php echo $this->res_base . "/" . 'js/jquery.dragsort-0.5.1.js'; ?>"></script>
<script>
function selectSecond(obj)
{
	var first = $(obj).find(':selected').val();
    $.get("./index.php?app=Material&act=ajaxGetEffect",{codes:first},function(res){
        if(res.done){
            $(obj).next().children().remove()
            $(obj).next().append('<option value=0>请选择</option>')
            $.each(res.retval,function(n,i){
                $(obj).next().append('<option value='+i+'>'+i+'</option>')

            })
            if($(obj).next(":visible").length==0){
                $(obj).after('&nbsp;&nbsp;功效编码❷：')
            }
            
            $(obj).next().show()
        }
    },'json')
	
}
function selectThird(obj)
{
    var codes=''
    var first=$(obj).prev().find(':selected').val()
    var second = $(obj).find(':selected').val();
    codes+=first+','+second
    $.get("./index.php?app=Material&act=ajaxGetEffect",{codes:codes},function(res){
        if(res.done){
            $(obj).next().children().remove()
            $(obj).next().append('<option value=0>请选择</option>')
            $.each(res.retval,function(n,i){
                $(obj).next().append('<option value='+i+'>'+i+'</option>')

            })
            if($(obj).next(":visible").length==0){
                $(obj).after('&nbsp;&nbsp;功效编码❸：')
            }
            
            $(obj).next().show()
        }
    },'json')
    
}

</script>

<?php echo $this->fetch('footer.html'); ?>
