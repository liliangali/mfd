<?php echo $this->fetch('header.html'); ?>
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
            brand_name : {
                required : true,
                remote   : {                //唯一
                url :'index.php?app=brand&act=check_brand',
                type:'get',
                data:{
                    brand_name : function(){
                        return $('#brand_name').val();
                        },
                    id  : '<?php echo $this->_var['brand']['brand_id']; ?>'
                    }
                }
            },
            logo : {
                accept  : 'gif|png|jpe?g'
            },
            brand_web : {
                url  : true,
            },
            sort_order : {
                number   : true,
                range	 :  [0,255]
            }
        },
        messages : {
            brand_name : {
                required : 'brand_empty',
                remote   : 'name_exist'
            },
            logo : {
                accept : 'limit_img'
            },
            brand_web : {
                url : '必须输入正确的网址'
            },
            sort_order  : {
                number   : 'number_only',
                range	 : '排序数字必须介于0~255'
            }
        }
    });
});
</script>

<?php echo $this->_var['baidu_editor']; ?>

<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=feed">管理</a></li>
        <?php if ($this->_var['brand']['brand_id']): ?>
        <li><a class="btn1" href="index.php?app=feed&amp;act=add">新增</a></li>
        <?php else: ?>
        <li><span>新增</span></li>
        <?php endif; ?>
        <!--<li><a class="btn1" href="index.php?app=brand&wait_verify=1">wait_verify</a></li>-->
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="brand_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    犬类型:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="fbtype">
                        <option value="0">请选择</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['type_list'],'selected'=>$this->_var['brand']['fbtype'])); ?>
                    </select>
            </tr>
            <tr>
                <th class="paddingT15">
                    犬期:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="age_id">
                        <option value="0">请选择</option>
                        <?php echo $this->html_options(array('options'=>$this->_var['age_list'],'selected'=>$this->_var['brand']['age_id'])); ?>
                    </select>
            </tr>
            <tr>
                <th class="paddingT15">
                    时间:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="time_id">
                        <option value="0">请选择</option>
                        <?php echo $this->html_options(array('options'=>$this->_var['time_list'],'selected'=>$this->_var['brand']['time_id'])); ?>
                    </select>
            </tr>

            <tr>
                <th class="paddingT15">
                    体况:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="body_condition">
                        <option value="0">请选择</option>
                        <?php echo $this->html_options(array('options'=>$this->_var['body_list'],'selected'=>$this->_var['brand']['body_condition'])); ?>
                    </select>
            </tr>


            <tr>
                <th class="paddingT15">
                    运动量:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="run_time">
                        <option value="0">请选择</option>
                        <?php echo $this->html_options(array('options'=>$this->_var['run_list'],'selected'=>$this->_var['brand']['run_time'])); ?>
                    </select>
            </tr>
            <tr>
                <th class="paddingT15">
                    体重下限:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="wt_min" type="text" name="wt_min" value="<?php echo htmlspecialchars($this->_var['brand']['wt_min']); ?>" /> <label class="field_notice"></label>
                </td>
            </tr>

            <tr>
                <th class="paddingT15">
                    体重上限</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="wt_max" type="text" name="wt_max" value="<?php echo htmlspecialchars($this->_var['brand']['wt_max']); ?>" /> <label class="field_notice"></label>
                </td>
            </tr>

            <tr>
                <th class="paddingT15">
                    默认体重</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="default_weight" type="text" name="default_weight" value="<?php echo htmlspecialchars($this->_var['brand']['default_weight']); ?>" /> <label class="field_notice"></label>
                </td>
            </tr>

            <tr>
                <th class="paddingT15">
                    能量需求参数乘积:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="enesum" type="text" name="enesum" value="<?php echo htmlspecialchars($this->_var['brand']['enesum']); ?>" /> <label class="field_notice"></label>
                </td>
            </tr>

            <tr>
                <th class="paddingT15">
                    狗粮卡里路:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="kcal" type="text" name="kcal" value="<?php echo htmlspecialchars($this->_var['brand']['kcal']); ?>" /> <label class="field_notice"></label>
                </td>
            </tr>

            <tr>
                <th class="paddingT15">
                    减肥瘦身卡里路:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="redkcal" type="text" name="redkcal" value="<?php echo htmlspecialchars($this->_var['brand']['redkcal']); ?>" /> <label class="field_notice"></label>
                </td>
            </tr>


            <tr>
                <th class="paddingT15">
                    每天饲喂次数:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="nums" type="text" name="nums" value="<?php echo htmlspecialchars($this->_var['brand']['nums']); ?>" /> <label class="field_notice"></label>
                </td>
            </tr>

            <!--<tr>-->
                <!--<th class="paddingT15">-->
                    <!--是否自由喂食:</th>-->
                <!--<td class="paddingT15 wordSpacing5">-->
                    <!--<?php echo $this->html_radios(array('options'=>$this->_var['yes_or_no'],'checked'=>$this->_var['brand']['feed'],'name'=>'feed')); ?></td>-->
            <!--</tr>-->

            <!--<tr>-->
                <!--<th class="paddingT15">-->
                   <!--饲喂量:</th>-->
                <!--<td class="paddingT15 wordSpacing5">-->
                    <!--<input class="infoTableInput2" id="feed" type="text" name="feed" value="<?php echo htmlspecialchars($this->_var['brand']['feed']); ?>" /> <label class="field_notice">如果自由采食填0</label>-->
                <!--</td>-->
            <!--</tr>-->

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
<?php echo $this->fetch('footer.html'); ?>
