<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#user_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : {
            user_name : {
                required : true,
                byteRange: [3,15,'<?php echo $this->_var['charset']; ?>'],
                remote   : {
                    url :'index.php?app=user&act=check_user',
                    type:'get',
                    data:{
                        user_name : function(){
                            return $('#user_name').val();
                        },
                        id : '<?php echo $this->_var['user']['user_id']; ?>'
                    }
                }
            },
            password: {
                <?php if ($_GET['act'] == 'add'): ?>
                required : true,
                <?php endif; ?>
                maxlength: 20,
                minlength: 6
            },
            email   : {
//                required : true,
                email : true
            }
            <?php if (! $this->_var['set_avatar']): ?>
            ,
            portrait : {
                accept : 'png|gif|jpe?g'
            }
            <?php endif; ?>
        },
        messages : {
            user_name : {
                required : '会员名称不能为空',
                byteRange: '用户名的长度应在3-15个字符之间',
                remote   : '该会员名已经存在了，请您换一个'
            },
            password : {
                <?php if ($_GET['act'] == 'add'): ?>
                required : '密码不能为空',
                <?php endif; ?>
                maxlength: '密码长度应在6-20个字符之间',
                minlength: '密码长度应在6-20个字符之间'
            },
            email  : {
//                required : '电子邮箱不能为空',
                email   : '请您填写有效的电子邮箱'
            }
            <?php if (! $this->_var['set_avatar']): ?>
            ,
            portrait : {
                accept : '支持格式gif,jpg,jpeg,png'
            }
            <?php endif; ?>
        }
    });
});

</script>
<div id="rightTop">
  <ul class="subnav">
    <li><a class="btn1" href="index.php?app=user">管理</a></li>
    <li>
      <?php if ($this->_var['user']['user_id']): ?>
<!--       <a class="btn1" href="index.php?app=user&amp;act=add">新增</a> -->
      <?php else: ?>
      <span>新增</span>
      <?php endif; ?>
    </li>
        <li><a class="btn1" href="index.php?app=user&amp;act=admin_userlog">日志查询</a></li>
  </ul>
</div>
<div class="info">
  <form method="post" enctype="multipart/form-data" id="user_form">
    <table class="infoTable">
      <tr>
        <th class="paddingT15"> 会员名:</th>
        <td class="paddingT15 wordSpacing5"><?php if ($this->_var['user']['user_id']): ?>
          <?php echo htmlspecialchars($this->_var['user']['user_name']); ?>
          <?php else: ?>
          <input class="infoTableInput2" id="user_name" type="text" name="user_name" value="<?php echo htmlspecialchars($this->_var['user']['user_name']); ?>" />
          <label class="field_notice">会员名</label>
          <?php endif; ?>        </td>
      </tr>
      

      <!--<tr>
        <th class="paddingT15"> 会员类型:</th>
        <td class="paddingT15 wordSpacing5">  
            <select id="lv_type" name="lv_type">
            <?php echo $this->html_options(array('options'=>$this->_var['tname'],'selected'=>'1')); ?>
            </select>    
         </td>
      </tr>
          <tr>
        <th class="paddingT15"> 会员等级:</th>
        <td class="paddingT15 wordSpacing5" id="member_lv_id">  
            <select name="member_lv_id">
            <?php $_from = $this->_var['lvs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'l');if (count($_from)):
    foreach ($_from AS $this->_var['l']):
?>
            <option value="<?php echo $this->_var['l']['member_lv_id']; ?>" <?php if ($this->_var['l']['member_lv_id'] == $this->_var['user']['member_lv_id']): ?>selected=""<?php endif; ?>><?php echo $this->_var['l']['name']; ?></option>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
            </select>    
         </td>
      </tr> -->
      <tr>
        <th class="paddingT15"> 密码:</th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableInput2" name="password" type="text" id="password" />
          <?php if ($this->_var['user']['user_id']): ?>
          <span class="grey">留空表示不修改密码</span>
          <?php endif; ?>        </td>
      </tr>


      <tr>
        <th class="paddingT15"> 电子邮箱:</th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableInput2" name="email" type="text" id="email" value="<?php echo htmlspecialchars($this->_var['user']['email']); ?>" />
            <label class="field_notice">电子邮箱</label>        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 真实姓名:</th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableInput2" name="real_name" type="text" id="real_name" value="<?php echo htmlspecialchars($this->_var['user']['real_name']); ?>" />        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 性别:</th>
        <td class="paddingT15 wordSpacing5"><p>
            <label>
            <input name="gender" type="radio" value="0" <?php if ($this->_var['user']['gender'] == 0): ?>checked="checked"<?php endif; ?> />
            保密</label>
            <label>
            <input type="radio" name="gender" value="1" <?php if ($this->_var['user']['gender'] == 1): ?>checked="checked"<?php endif; ?> />
            男</label>
            <label>
            <input type="radio" name="gender" value="2" <?php if ($this->_var['user']['gender'] == 2): ?>checked="checked"<?php endif; ?> />
            女</label>
          </p></td>
      </tr>
      
      <?php if ($this->_var['invitation']): ?>
      <tr>
        <th class="paddingT15">绑定时间:</th>
        <td class="paddingT15 wordSpacing5"><?php echo local_date("Y-m-d H:i:s",$this->_var['invitation']['add_time']); ?></td>
      </tr>
      
      <tr>
        <th class="paddingT15">绑定信息:</th>
        <td class="paddingT15 wordSpacing5"><?php echo $this->_var['invitation']['name']; ?></td>
      </tr>
      <?php endif; ?>
      

     <?php if (! $this->_var['set_avatar']): ?>
<!--       <tr>
        <th class="paddingT15">头像:</th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableFile2" type="file" name="portrait" id="portrait" />
          <label class="field_notice">支持格式gif,jpg,jpeg,png</label>
          <?php if ($this->_var['user']['portrait']): ?><br /><img src="../<?php echo $this->_var['user']['portrait']; ?>" alt="" width="100" height="100" /><?php endif; ?>           </td>
      </tr> -->
     <?php else: ?>
        <?php if ($_GET['act'] == 'edit'): ?>
      <tr>
        <th class="paddingT15">头像:</th>
        <td class="paddingT15 wordSpacing5"><?php echo $this->_var['set_avatar']; ?></td>
      </tr>
        <?php endif; ?>
     <?php endif; ?>
      <tr>
        <th></th>
        <td class="ptb20"><input class="tijia" type="submit" name="Submit" value="提交" />
        <input type="hidden" value="<?php echo $this->_var['user']['user_name']; ?>" name="log_user_name">
        <input type="hidden" value="<?php echo $this->_var['tid']; ?>" name="log_lv_type">
        <input type="hidden" value="<?php echo $this->_var['user']['member_lv_id']; ?>" name="log_member_lv_id">
        <input type="hidden" value="<?php echo $this->_var['user']['password']; ?>" name="log_password">
        <input type="hidden" value="<?php echo $this->_var['user']['email']; ?>" name="log_email">
        <input type="hidden" value="<?php echo $this->_var['user']['real_name']; ?>" name="log_real_name">
        <input type="hidden" value="<?php echo $this->_var['user']['gender']; ?>" name="log_gender">
        <input type="hidden" value="<?php echo $this->_var['user']['im_qq']; ?>" name="log_im_qq">
        <input type="hidden" value="<?php echo $this->_var['user']['im_msn']; ?>" name="log_im_msn">
        <input class="congzi" type="reset" name="Reset" value="重置" />        </td>
      </tr>
    </table>
  </form>
</div>
<script>
//$('#lv_type').change(function(){
//
//	$.post("/mfd/index.php?app=user&act=getLvs",{mty:$(this).children('option:selected').val()}, function(res){
//	   var res = eval("("+res+")");
//	   console.log(res.retval.content);
//	   if(res.done == true){
//	        $("#member_lv_id").html(res.retval.content);
//	    }
//	});
//
//	});
	

</script>
<?php echo $this->fetch('footer.html'); ?>