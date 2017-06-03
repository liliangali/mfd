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
		/**
            name : {
                required : true,
                byteRange: [3,25,'<?php echo $this->_var['charset']; ?>'],
                remote   : {
                    url :'index.php?app=lv&act=check_name',
                    type:'get',
                    data:{
                        name : function(){
                            return $('#name').val();
                        },
                        id : '<?php echo $this->_var['user']['user_id']; ?>'
                    }
                }
            },**/
            dis_count: {
            	number   : true
            },
            experience   : {
            	number   : true
            }
            <?php if (! $this->_var['set_avatar']): ?>
            ,
            portrait : {
                accept : 'png|gif|jpe?g'
            }
            <?php endif; ?>
        },
        messages : {
            name : {
                required : '请填写等级名称',
                byteRange: '请填写等级名称长度',
                //remote   : '等级名称已存在'
            },
            dis_count : {
            	number   : '有效范围0～1'
            },
            experience : {
            	number   : '数字类型'
            },
            email  : {
                required : 'email_not_empty',
                email   : 'currect_email'
            }
            <?php if (! $this->_var['set_avatar']): ?>
            ,
            portrait : {
                accept : '未选择文件'
            }
            <?php endif; ?>
        }
    });
    
    $("input[name='default_lv']").click(
    		function() {
    			var dlv = $("input[name='default_lv']:checked").val();
    			var dty = $("#select1").val();
    			if(!dty)
    			{
    				dty = '<?php echo $this->_var['user']['lv_type']; ?>';
    			}
    			if(1 == dlv){
            		$.ajax({
            			type: "POST",
            			url: "index.php?app=lv&act=check_defaultlv&ty="+dty,
            			dataType:"json",
            			success: function(msg){
            				if(msg.res == 0 ){
            					alert(msg.name+'已经是默认！');
            					$("input[name='default_lv'][value=0]").attr("checked",true);  
            				}
            			}
            			})
    			}
    			

    		}
);
    		
});
</script>
<div id="rightTop">
  <ul class="subnav">
    <li><a class="btn1" href="index.php?app=lv">管理</a></li>
    <li>
      <?php if ($this->_var['user']['user_id']): ?>
      <a class="btn1" href="index.php?app=lv&amp;act=add">新增</a>
      <?php else: ?>
      <span>新增</span>
      <?php endif; ?>
    </li>
  </ul>
</div>
<div class="info">
  <form method="post" enctype="multipart/form-data" id="user_form">
    <table class="infoTable">
          <?php if (! $this->_var['user']['member_lv_id']): ?>
       <tr>
        <th class="paddingT15"> 等级类型:</th>
        <td class="paddingT15 wordSpacing5">  
	        <select id="select1" name="lv_type">
	        <?php echo $this->html_options(array('options'=>$this->_var['tname'],'selected'=>'member')); ?>
			</select>    
	     </td>
      </tr>
      <?php endif; ?>
      <tr>
        <th class="paddingT15"> 等级名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="name" type="text" name="name" value="<?php echo htmlspecialchars($this->_var['user']['name']); ?>" />
          <label class="field_notice">等级名称</label>
        </td>
      </tr>


      <tr>
        <th class="paddingT15"> 是否默认:</th>
        <td class="paddingT15 wordSpacing5"><p>
            <label>
            <input name="default_lv" type="radio" value="0" <?php if ($this->_var['user']['default_lv'] == 0): ?>checked="checked"<?php endif; ?> />
            否</label>
            <label>
            <input type="radio" name="default_lv" value="1" <?php if ($this->_var['user']['default_lv'] == 1): ?>checked="checked"<?php endif; ?> />
            是</label>
          </p></td>
      </tr>

      <tr>
        <th class="paddingT15"> 经验值或业绩:</th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableInput2" name="experience" type="text" id="experience" value="<?php echo $this->_var['user']['experience']; ?>" />  <label class="field_notice">按经验值（或业绩）增长时会员经验值达到此标准后会自动升级为当前等级</label>        </td>
      </tr>

     <?php if (! $this->_var['set_avatar']): ?>
      <tr>
        <th class="paddingT15">会员等级图标:</th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableFile2" type="file" name="lv_logo" id="lv_logo" />
          <?php if ($this->_var['user']['lv_logo']): ?><br /><img src="../<?php echo $this->_var['user']['lv_logo']; ?>" alt="" width="100" height="100" /><?php endif; ?>           </td>
      </tr>
     <?php else: ?>
        <?php if ($_GET['act'] == 'edit'): ?>
      <tr>
        <th class="paddingT15">portrait:</th>
        <td class="paddingT15 wordSpacing5"><?php echo $this->_var['set_avatar']; ?></td>
      </tr>
        <?php endif; ?>
     <?php endif; ?>
      <tr>
        <th></th>
        <td class="ptb20"><input class="tijia" type="submit" name="Submit" value="提交" />
          <input class="congzi" type="reset" name="Reset" value="重置" />        </td>
      </tr>
    </table>
  </form>
</div>
<?php echo $this->fetch('footer.html'); ?>