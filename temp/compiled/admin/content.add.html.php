<tr class="tatr2" attr_num="<?php echo $this->_var['num']; ?>">
    <td><?php echo $this->input_img(array('name'=>$this->_var['img'],'dir'=>'motif')); ?>
            名称：<input  type="text"  class="infoTableInput" name="title<?php echo $this->_var['num']; ?>"  id="title<?php echo $this->_var['num']; ?>" value=""/><br/>
           简介：<textarea class="infoTableInput" style="height:45px" name="intro<?php echo $this->_var['num']; ?>"  id="intro<?php echo $this->_var['num']; ?>"></textarea>
    </td>
    <td><select onchange="change_url(this)"><option value=''>请选择</option><?php echo $this->html_options(array('values'=>$this->_var['url_rule_values'],'output'=>$this->_var['url_rule_names'])); ?></select><label class='field_notice'>固定url规则</label><br>
    <input  type="text"  class="infoTableInput require" name="link_url<?php echo $this->_var['num']; ?>"  id="link_url<?php echo $this->_var['num']; ?>" value=''/><label class='field_notice'>可选择固定url（需填写参数），也可以自定义url</label><br/></td>
    <td><select name="is_blank<?php echo $this->_var['num']; ?>" id="is_blank<?php echo $this->_var['num']; ?>" ><option value='1' selected>是</option><option value='0'>否</option></select></td>
    <td><input type="text" name="sort_order<?php echo $this->_var['num']; ?>" class='require' id="sort_order<?php echo $this->_var['num']; ?>" value=''/></td>
    <td><select name="is_show<?php echo $this->_var['num']; ?>" id="is_show<?php echo $this->_var['num']; ?>"><option value='1'>是</option><option value='0'>否</option></select></td>
    <td><a href="javascript:void(0)" class="exclude" onclick="delete_content(this)">删除</a></td>
</tr>
<script type="text/javascript">
$(function(){
    $("a.preview").preview()    
})
</script>