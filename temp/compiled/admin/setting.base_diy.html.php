<?php echo $this->fetch('header.html'); ?>

<script type="text/javascript">
//<!CDATA[
$(function(){
    $(".show_image").mouseover(function(){
        $(this).next("div").show();
    });
    $(".show_image").mouseout(function(){
        $(this).next("div").hide();
    });
});
//]]>
</script>

<div id="rightTop">
  <ul class="subnav">
    <li><a class="btn1" href="index.php?app=fabric_brand">属性管理</a></li>
     <li><a class="btn1" href="index.php?app=fabric_brand&amp;act=add">添加属性</a></li>
    <li><span>基本设置</span></li>
  </ul>
</div>
<div class="info">
  <form method="post" enctype="multipart/form-data">
    <table class="infoTable">
      <tr>
        <th class="paddingT15"> <label for="site_name">*功能料均价 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="diy_aprice" type="text" name="diy_aprice" value="<?php echo $this->_var['setting']['diy_aprice']; ?>" class="infoTableInput"/>        </td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="site_title">*单个功能料占比 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="diy_ratio" type="text" name="diy_ratio" value="<?php echo $this->_var['setting']['diy_ratio']; ?>" class="infoTableInput"/>        </td>
      </tr>
      <tr>
        <th class="paddingT15" valign="top"> <label for="site_description">*功能料种数上限 :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="diy_maxnum" type="text" name="diy_maxnum" value="<?php echo $this->_var['setting']['diy_maxnum']; ?>" class="infoTableInput"/>        </td>
      </tr>
      
       <tr>
        <th class="paddingT15" valign="top"> <label for="site_description">*折扣最低重量(百克) :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="kg_minnum" type="text" name="kg_minnum" value="<?php echo $this->_var['setting']['kg_minnum']; ?>" class="infoTableInput"/>        </td>
      </tr>
      
      <tr>
        <th class="paddingT15" valign="top"> <label for="site_description">*最高折扣(请填写小于1的小数) :</label></th>
        <td class="paddingT15 wordSpacing5"><input id="zhekou_max" type="text" name="zhekou_max" value="<?php echo $this->_var['setting']['zhekou_max']; ?>" class="infoTableInput"/>        </td>
      </tr>


      <tr id="member_p" >
        <th class="paddingT15"> <label for="site_name">*主人寄语词库:</label></th>
        <td class="paddingT15 wordSpacing5 obj">
          <input type='text' name='word_libraries' placeholder='输入寄语添加' id='word_libraries'/> <input type='button' value='确定' id='addok'/>
          <br><div style='border:1px dashed #BBBBBB;width:500px;height:auto;min-height:200px;padding-left:5px' id='member_text'>
          <?php if ($this->_var['setting']['word_libraries']): ?>
            <?php $_from = $this->_var['setting']['word_libraries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'word');if (count($_from)):
    foreach ($_from AS $this->_var['word']):
?>
            <p style="height: 20px; margin:10px 5px;"><span style=' font-size: 14px; cursor: pointer; padding-right: 5px;'onclick="del(this)">x</span><input name="word_val[]" type="text" value="<?php echo $this->_var['word']; ?>"  style="width:92%;height: 100%; padding: 0 10px;"></p>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          <?php endif; ?>
          </div>
        </td>
      </tr>
      
      <tr>
        <th></th>
        <td class="ptb20"><input class="tijia" type="submit" name="Submit" value="提交" />
        </td>
      </tr>
    </table>
  </form>
  
</div>
<script type="text/javascript" src="<?php echo $this->res_base . "/" . 'js/luck/luck.js'; ?>" charset="utf-8"></script>
<script type='text/javascript'>
$("#addok").click(function(){

  var word = $("#word_libraries").val();

  //检查是否已经在添加文件中存在
  var input_length = parseInt($("#member_text input").length);
  if(input_length > 0)
  {
    for(var i=0;i<input_length;i++)
      {
        var info = $('#member_text input:eq('+i+')').val();
        if(info == word)
        {
          luck.alert('系统提示','此寄语已添加成功!请勿重复添加',6);
          return false;
        }    
      }
  }
  var extend=parseInt(input_length)+1
  var sp = "<p style='height: 20px; margin:10px 5px;'><span style=' font-size: 14px; cursor: pointer; padding-right: 5px;'onclick='del(this)'>x</span><input name='word_val["+extend+"]' type='text' value="+word+"  style='width:92%;height: 100%; padding: 0 10px;'></p>";
   
  $("#member_text").append(sp);
})
function del(objs)
      {
        luck.confirm('系统提示','确认删除',function(obj){
              if(obj)
              {
                //luck.alert('系统提示',t);
                  $(objs).parent().remove();
              }
        },['确定','取消']);
      }
</script>
<?php echo $this->fetch('footer.html'); ?>