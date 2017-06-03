<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#process_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {    
            title : {
                required : true
            },
            cate_id :{
                required : true
            },
            link    :{
                url     : true
            },
            sort_order:{
               number   : true
            }
        },
        messages : {
            title : {
                required : 'title_empty'
            },
            cate_id : {
                required : 'cate_empty'
            },
            link    : {
                url     : 'link_limit'
            },
            sort_order  : {
                number   : 'number_only'
            }
        }
    });
});

var shops = <?php echo $this->_var['shops']; ?>;

</script>
<?php echo $this->_var['build_editor']; ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=feedback">管理</a></li>
        <li><span>详情</span></li>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="process_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    反馈角色:</th>
                <td class="paddingT15 wordSpacing5">
                    <?php echo $this->_var['feedback']['user_name']; ?> | <?php echo $this->_var['feedback']['nickname']; ?>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    联系方式:</th>
                <td class="paddingT15 wordSpacing5">
                    <?php echo $this->_var['feedback']['mobile']; ?>
                </td>
            </tr>
             <tr>
                <th class="paddingT15">
                    反馈时间:</th>
                <td class="paddingT15 wordSpacing5">
                    <?php echo local_date("Y-m-d H:i:s",$this->_var['feedback']['add_time']); ?>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    来源地址:</th>
                <td class="paddingT15 wordSpacing5">
                    <?php echo $this->_var['feedback']['url']; ?>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    图片:</th>
                <td class="paddingT15 wordSpacing5">
                    <?php $_from = $this->_var['feedback']['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
      					<a target="_blank" href="<?php echo $this->_var['list']['img_url']; ?>"><img height="60" src="<?php echo $this->_var['list']['img_url']; ?>"></a>&nbsp;
      				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                   反馈内容:</th> 
                <td class="paddingT15 wordSpacing5">
                    <textarea rows="300" cols="200" readonly><?php echo $this->_var['feedback']['description']; ?></textarea>
                </td>
            </tr>

            <tr>
                <th class="paddingT15">
                    回复内容:</th>
                <td class="paddingT15 wordSpacing5">
                    <textarea rows="300" cols="200" <?php if ($this->_var['feedback']['replay']): ?>readonly<?php endif; ?> name='replay'><?php echo $this->_var['feedback']['replay']; ?></textarea>
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="ptb20">
                    <input type="hidden" name="id" value="<?php echo $this->_var['feedback']['id']; ?>">
                    <input class="tijia" type="submit" name="Submit" value="提交" />
                    <input class="congzi" type="reset" name="Submit2" value="重置" />
                </td>
            </tr>

            <!--             <tr>
                            <th class="paddingT15">
                                <label for="cate_id">category:</label></th>
                            <td class="paddingT15 wordSpacing5">
                                <select id="cate_id" name="cate_id"><option value="">请选择...</option><?php echo $this->html_options(array('options'=>$this->_var['parents'],'selected'=>$this->_var['process']['cate_id'])); ?></select>
                            </td>
                        </tr> -->
            
           <!--             <tr>
                <th class="paddingT15">
                    <label for="cate_id">商品分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="parent_id" name="goods_cat">
                    <option value="0">请选择...</option>
                     <?php echo $this->html_options(array('options'=>$this->_var['cat_list'],'selected'=>$this->_var['process']['goods_cat'])); ?>
                    </select>
                </td>
            </tr>
            
           <tr>
                <th class="paddingT15">
                    <label for="cate_id">所属商铺:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="city_id" name="city_id" onchange="selectShop(this)"><option value="">请选择...</option><?php echo $this->html_options(array('options'=>$this->_var['city_list'],'selected'=>$this->_var['process']['city_id'])); ?></select>
                      <select id="shop_id" name="shop_id">
                    <option value="0">请选择商铺:</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['site_list'],'selected'=>$this->_var['process']['shop_id'])); ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    链接:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="link" type="text" name="link" value="<?php echo htmlspecialchars($this->_var['process']['link']); ?>" />
                </td>
            </tr>
			-->
<!--             <tr>
                <th class="paddingT15">
                    <label for="if_show">显示:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <input id="yes" type="radio" name="if_show" value="1" <?php if ($this->_var['process']['if_show'] == 1): ?> checked="checked"<?php endif; ?> />
                    <label for="yes">是</label>
                    <input id="no" type="radio" name="if_show" value="0" <?php if ($this->_var['process']['if_show'] == 0): ?> checked="checked"<?php endif; ?> />
                    <label for="no">否</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sort_order" type="text" name="sort_order" value="<?php echo $this->_var['process']['sort_order']; ?>" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="process">content:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <textarea id="process" name="content" style="width:650px;height:400px;"><?php echo htmlspecialchars($this->_var['process']['content']); ?></textarea>
                </td>
            </tr>

            
        <tr>
            <th></th>
            <td class="ptb20">
                <input class="tijia" type="submit" name="Submit" value="提交" />
                <input class="congzi" type="reset" name="Submit2" value="重置" />
            </td>
        </tr> -->
        </table>
    </form>
</div>

<?php echo $this->fetch('footer.html'); ?>
