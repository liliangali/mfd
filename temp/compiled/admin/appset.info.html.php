<?php echo $this->fetch('header.html'); ?>
<script charset="utf-8" type="text/javascript" src="<?php echo $this->lib_base . "/" . 'jquery.plugins/jquery.validate.js'; ?>" ></script>
<script type="text/javascript">
$(function(){
    $('#serve_form').validate({
        /*errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },*/
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : {
        	version : {
                required : true,
            },
            large : {
            	 required : true,
            },
            apps : {
           	    required : true,
           },
            link : {
           	 required : false,
           },
           type : {
             	 required : false,
             },
           description : {
               	 required : false,
               },
          
        },
        messages : {
        	version : {
                required : '必填',
            },
            large : {
                required : '必填',
            },
            apps : {
               required : '必填',
            },
            type: {
            	required : '必填',
               
            },
            description : {
            	required : '必填'
            },
            link : {
            	required : '必填'
            },
           
        }
    });
});

</script>
<div id="rightTop">
  <ul class="subnav">
    <li><a class="btn1" href="index.php?app=appset&act=index">管理</a></li>
    <li>
      <span>添加</span>
    </li>
  </ul>
</div>
<div class="info">
  <form method="post" enctype="multipart/form-data" id="serve_form" name="serve_form">
    <table class="infoTable">
        <tr>
        <th class="paddingT15"><span style="color:red;">*</span>版本号:</th>
        <td class="paddingT15 wordSpacing5"><input class="infoTableInput2" name="version" type="text" id="version" value="<?php echo $this->_var['versions']['version']; ?>" /></td>
      </tr>
      <tr>
        <th class="paddingT15"><span style="color:red;">*</span>安装包大小:</th>
        <td class="paddingT15 wordSpacing5">
        <input class="infoTableInput2" name="large" type="text" id="large" value="<?php echo $this->_var['versions']['large']; ?>" />MB
      
      </tr>
      <tr>
         <th class="paddingT15"><span style="color:red;">*</span>下载地址:</th>
        <td class="paddingT15 wordSpacing5">
        <input class="infoTableInput2" name="link" style="width:300px;" type="text" id="link" value="<?php echo $this->_var['versions']['link']; ?>" />
      
      </tr>
   
    <!--  <tr>
        <th class="paddingT15"><span style="color:red;">*</span>app:</th>
        <td class="paddingT15 wordSpacing5"><p>
            <label>
            <input type="radio" name="apps" value="mfd" <?php if ($this->_var['versions']['app'] == "mfd"): ?>checked="checked"<?php endif; ?> />
           麦富迪app</label>
          <label>
            <input type="radio" name="apps" value="figure" <?php if ($this->_var['versions']['app'] == "figure"): ?>checked="checked"<?php endif; ?> />
           量体app</label>
          </p></td>
      </tr>-->
    
       <tr>
        <th class="paddingT15"><span style="color:red;">*</span>型号:</th>
        <td class="paddingT15 wordSpacing5"><p>
            <label>
            <input type="radio" name="type" value="android" <?php if ($this->_var['versions']['type'] == "android"): ?>checked="checked"<?php endif; ?> />
            安卓</label>
            <label>
            <input type="radio" name="type" value="ios" <?php if ($this->_var['versions']['type'] == "ios"): ?>checked="checked"<?php endif; ?> />
    ios</label>
          </p></td>
      </tr>
      <tr>
        <th class="paddingT15"><span style="color:red;">*</span>更新管理:</th>
        <td class="paddingT15 wordSpacing5"><p>
            
          <label>
            <input type="radio" name="updatecode" value="1" <?php if ($this->_var['versions']['updatecode'] == 1): ?>checked="checked"<?php endif; ?> />
         选择更新</label>
           <label>
            <input type="radio" name="updatecode" value="2" <?php if ($this->_var['versions']['updatecode'] == 2): ?>checked="checked"<?php endif; ?> />
           强制更新</label>
          </p></td>
      </tr>
      <tr>
                <th class="paddingT15">
                    更新文字:</th>
                <td class="paddingT15 wordSpacing5">
                <textarea  name="description" id="description" style="width:650px;height:300px;"><?php echo $this->_var['versions']['description']; ?></textarea>
                </td>
            </tr>
     
      <tr>
        <th></th>
        <td class="ptb20">
        	<input class="tijia" type="submit" name="Submit" value="提交" />
          <input class="congzi" type="reset" name="Reset" value="重置" />
         </td>
      </tr>
    </table>
  </form>
</div>
<?php echo $this->fetch('footer.html'); ?>