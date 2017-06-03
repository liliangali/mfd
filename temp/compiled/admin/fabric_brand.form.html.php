<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#fabric_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
//         onfocusout : false,
        onkeyup    : false,
        rules : {
            brand_name : {
                required : true,
                remote   : {
                    url :'index.php?app=fabric_brand&act=ajax_check_brand',
                    type:'get',
                    data:{
                        id:function()
                        {
                        	return $("#brand_id").val();
                        }
                    }, 
                },
            },
            ve : {
            	required : true
            },
    /*         region_id : {
            	required : true,
            	min : 1
            }, */
            uprice : {
            	required : true,
            	number : true
            },
            fprice : {
            	required : true,
            	number : true
            },
            sort_order : {
                number   : true,
            },
            // letter_retrieval:{
            //     onlyletter : true,
            // }

        },
        messages : {
            brand_name : {
                required : '!该名称不能为空',
                remote:'该名称已被使用，请填写其他品牌名称'
            },
            ve : {
                required : '!属性值不能为空',
            },
  /*           region_id : {
                required : '该分类不能为空',
                min : '请选择分类'
            }, */
            uprice  : {
            	required : '!请输入单价',
                number   : '分类排序仅能为数字'
            },
            fprice  : {
            	required : '!请输入固定价',
                number   : '分类排序仅能为数字'
            },
            sort_order  : {
                number   : '分类排序仅能为数字'
            },
            // letter_retrieval:{
            // }
        }
    });
});
jQuery.validator.addMethod("onlyletter", function(value, element) {   
        return /^[A-Za-z]$/.test(value)
    }, "请填写属性名称第一个字的拼音首字母");
</script>
<div id="rightTop">
    
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=fabric_brand">属性管理</a></li>
        <li><span>添加属性</span></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=base_diy">基本设置</a></li>
    </ul>
</div>
<?php echo $this->_var['build_editor']; ?>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="fabric_form">
        <table class="infoTable">
        
                   <tr>
                <th class="paddingT15">
                    *属性名称:</th>
                <td class="paddingT15 wordSpacing5">
                	<input class="infoTableInput" id="brand_name" type="text" name="brand_name" value="<?php echo htmlspecialchars($this->_var['brand']['cate_name']); ?>"/>
                </td>
            </tr>

            <tr>
                <th class="paddingT15">
                    别名(多个别名<i style="color: red;font-size: larger">英文</i>逗号隔开):</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="sname" type="text" name="sname" value="<?php echo htmlspecialchars($this->_var['brand']['sname']); ?>"/>
                </td>
            </tr>
            
                       <tr>
                <th class="paddingT15">
                   *属性值:</th>
                <td class="paddingT15 wordSpacing5">
                	<input class="infoTableInput" id="ve" type="text" name="ve" value="<?php echo htmlspecialchars($this->_var['brand']['ve']); ?>"/>
                </td>
            </tr>
        
                   <tr>
                <th class="paddingT15">
                   *上级分类:</th>
                <td class="paddingT15 wordSpacing5">
                   <select name="region_id" id="region_id" class="sgcategory" onchange="get_brands(this)">
                           <option value="0">请选择所属分类</option>
                           <?php echo $this->html_options(array('options'=>$this->_var['region'],'selected'=>$this->_var['pid'])); ?>
                   	</select><label class="field_notice"></label>
                </td>
            </tr>
            
            
                        
            
                       <tr>
                <th class="paddingT15">
                    *单价:</th>
                <td class="paddingT15 wordSpacing5">
                	<input class="infoTableInput" id="uprice" type="text" name="uprice" value="<?php echo $this->_var['brand']['uprice']; ?>"/>
                </td>
            </tr>
            
                        
            
                       <tr>
                <th class="paddingT15">
                    *固定价:</th>
                <td class="paddingT15 wordSpacing5">
                	<input class="infoTableInput" id="fprice" type="text" name="fprice" value="<?php echo $this->_var['brand']['fprice']; ?>"/>
                </td>
            </tr>
                        

 			<tr>
            <th>属性缩略图:</th>
            <td height="100" valign="top">
            	 <?php echo $this->input_img(array('name'=>'small_img','value'=>$this->_var['brand']['small_img'],'dir'=>'diy')); ?>
            </td>
            </tr>
            
             <tr>
            <th>属性大图:</th>
            <td height="100" valign="top">
            	 <?php echo $this->input_img(array('name'=>'source_img','value'=>$this->_var['brand']['source_img'],'dir'=>'diy')); ?>
            </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sort_order" type="text" name="sort_order" value="<?php echo ($this->_var['brand']['sort_order'] == '') ? '255' : $this->_var['brand']['sort_order']; ?>" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    字母检索:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="letter_retrieval" id="letter_retrieval" type="text" name="letter_retrieval" value="<?php echo $this->_var['brand']['letter_retrieval']; ?>" />
                    <label for="if_show">请填写属性名称第一个字的拼音首字母</label>
                </td>
            </tr>

            <tr>
                <th class="paddingT15">
                    功效描述(不同犬期对应的功效描述):</th>
                <td class="paddingT15 wordSpacing5">
                    <textarea name="gongxiao_content"><?php echo $this->_var['brand']['gongxiao_content']; ?></textarea>
                </td>
            </tr>
            
              <tr>
                <th class="paddingT15">
                    <label for="if_show">显示:</label></th>
                <td class="paddingT15 wordSpacing5">
                 <?php if ($this->_var['brand']): ?>
                <?php echo $this->html_radios(array('name'=>'if_show','options'=>$this->_var['show_items'],'checked'=>$this->_var['brand']['if_show'])); ?>
                <?php else: ?>
                <label>                	
                	
                  <input type="radio" name="if_show" value="1" checked="checked" />
                  是</label>
                <label>
                  <input type="radio" name="if_show" value="0" />
                  否</label> 
                  <?php endif; ?>
              </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="if_show">设为默认选项:</label></th>
                <td class="paddingT15 wordSpacing5">
                 <?php if ($this->_var['brand']): ?>
                <?php echo $this->html_radios(array('name'=>'is_def','options'=>$this->_var['def_items'],'checked'=>$this->_var['brand']['is_def'])); ?>
                <?php else: ?>
                <label>                	
                  <input type="radio" name="is_def" value="1" checked="checked" />
                  是</label>
                <label>
                  <input type="radio" name="is_def" value="0" />
                  否</label> 
                  <?php endif; ?>
                 </td>
            </tr>
           
           <tr>
                <th class="paddingT15">
                    <label for="if_show">是否多选:</label></th>
                <td class="paddingT15 wordSpacing5">
                 <?php if ($this->_var['brand']): ?>
                <?php echo $this->html_radios(array('name'=>'is_box','options'=>$this->_var['box_items'],'checked'=>$this->_var['brand']['is_box'])); ?>
                <?php else: ?>
                <label>                	
                	
                  <input type="radio" name="is_box" value="1" checked="checked" />
                  是</label>
                <label>
                  <input type="radio" name="is_box" value="0" />
                  否</label> 
                  <?php endif; ?>
              </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="if_show">是否常用:</label></th>
                <td class="paddingT15 wordSpacing5">
                 <?php if ($this->_var['brand']): ?>
                <?php echo $this->html_radios(array('name'=>'if_common','options'=>$this->_var['box_items'],'checked'=>$this->_var['brand']['if_common'])); ?>
                <?php else: ?>
                <label>                 
                    
                  <input type="radio" name="if_common" value="1" checked="checked" />
                  是</label>
                <label>
                  <input type="radio" name="if_common" value="0" />
                  否</label> 
                  <?php endif; ?>
              </td>
            </tr>
            
             <tr>
                <th class="paddingT15">
                    <label for="if_show">是否独立功效(当选择且仅选择此功效的时候,基料价格为0,功效占比100%):</label></th>
                <td class="paddingT15 wordSpacing5">
                 <?php if ($this->_var['brand']): ?>
                <?php echo $this->html_radios(array('name'=>'is_alone','options'=>$this->_var['alone_items'],'checked'=>$this->_var['brand']['is_alone'])); ?>
                <?php else: ?>
                <label>                	
                	
                  <input type="radio" name="is_alone" value="1"  />
                  是</label>
                <label>
                  <input type="radio" name="is_alone" value="0" checked="checked" />
                  否</label> 
                  <?php endif; ?>
            </tr>

            <tr>
                <th class="paddingT15">
                    <label for="if_show">是否试吃属性:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <?php if ($this->_var['brand']): ?>
                    <?php echo $this->html_radios(array('name'=>'is_try','options'=>$this->_var['alone_items'],'checked'=>$this->_var['brand']['is_try'])); ?>
                    <?php else: ?>
                    <label>
                        <input type="radio" name="is_try" value="1"  checked="checked" />
                        是</label>
                    <label>
                        <input type="radio" name="is_try" value="0"  />
                        否</label>
                    <?php endif; ?>
            </tr>



            <tr>
                <th class="paddingT15">
                    <label for="if_show">犬种大小:</label></th>
                <td class="paddingT15 wordSpacing5">

                    <label>
                        <input type="radio" name="ftype" value="1" <?php if ($this->_var['brand']['ftype'] == 1): ?> checked="checked"<?php endif; ?> />
                        大型犬</label>
                    <label>
                        <input type="radio" name="ftype" value="2" <?php if ($this->_var['brand']['ftype'] == 2): ?> checked="checked"<?php endif; ?> />
                        中型犬
                    </label>
                    <label>
                        <input type="radio" name="ftype" value="3" <?php if ($this->_var['brand']['ftype'] == 3): ?> checked="checked"<?php endif; ?> />
                        小型犬
                    </label>
                </td>
            </tr>


            
<!--                
            <tr>
                <th class="paddingT15">
                    <label for="dis">品牌描述:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <textarea id="content" name="content" style="width:650px;height:400px;"><?php echo htmlspecialchars($this->_var['brand']['content']); ?></textarea>
                </td>
            </tr> -->
            
        <tr>
            <th></th>
            <td class="ptb20">
                <input class="tijia" type="submit" name="Submit" value="提交" />
                <input type="hidden" class='brand_id' name="brand_id" id="brand_id" value="<?php echo $this->_var['brand']['cate_id']; ?>"/>
                <input class="congzi" type="reset" name="Submit2" value="重置" />
            </td>
        </tr>
        </table>
    </form>
</div>
<?php echo $this->fetch('footer.html'); ?>
