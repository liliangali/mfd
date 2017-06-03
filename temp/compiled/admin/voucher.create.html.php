<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=setting&amp;act=voucher">优惠券列表</a></li>
        <li><span>生成优惠券</span></li>
        <li><a class="btn1" href="index.php?app=setting&amp;act=voucher_batch">批次日志</a></li>
    </ul>
</div>
<style>
    .infoTable{padding-top: 0px;}
    .infoTable p{padding:10px 0;}
    .infoTableInput{width: 120px;}
    .voucherborder{border: 2px solid #A7D6A9;margin: 20px;padding-bottom:20px;}
    #loading {
        display: none;
        height: 100%;
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
    }
    .loading-div{
        background-color: #000;
        height: 100%;
        opacity: 0.2;
        position: absolute;
        width: 100%;
    }
    .loading-text {
        color: #000;
        font-size: 16px;
        left: 40%;
        position: absolute;
        text-align: center;
        top: 50%;
        width: 20%;
    }
</style>
<div class="info">
    <h2 style="padding: 20px 0px 0px 20px;">备注：一次生成数量建议不要超过500个,需要更多优惠券可以分多次生成,生成需要时间请耐心等待</h2>
    <form method="post" enctype="multipart/form-data" class="create_voucher">
        <div class="voucherborder">
            <table class="infoTable">
                <tr>
                    <th class="paddingT15"></th>
                    <td class="paddingT15 wordSpacing5">
                        <p>
                            券名称:
                            <input class="infoTableInput i_name" id="debit" type="text" name="data[name]" value="优惠券 <?php echo $this->_var['arrdata']['date']; ?>"/>
                        </p>
                        <p>
                            券品类:
                            <select id="time_zone" name="data[category]">
                                <option value="1,2">&nbsp;&nbsp;通用&nbsp;&nbsp;</option>
                                <option value="1">&nbsp;&nbsp;定制商品&nbsp;&nbsp;</option>
                                <option value="2">&nbsp;&nbsp;普通商品&nbsp;&nbsp;</option>
                            </select>
                        </p>
                        <p>
                            券价格:
                            <input class="infoTableInput i_money" id="debit" type="text" name="data[money]" value="0" onkeyup="this.value=this.value.replace(/\D/g,'')"  onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="5" size="14"/> (元)
                        </p>
                        <p>
                            生成数量:
                            <input class="infoTableInput i_num" id="debit" type="text" name="data[num]" value="100" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="5" size="14"/> (个)
                        </p>
                        <p>
                            券使用期:
                            生效时间: <input data_name="debit_time_o" name="data[start_time]"  class="infoTableInput Wdate i_start_time" onClick="WdatePicker()"  type="text"  value="<?php echo $this->_var['setting']['debit_time_o']; ?>"  />
                            过期时间: <input data_name="debit_time_o" name="data[end_time]"  class="infoTableInput Wdate i_end_time" onClick="WdatePicker()"  type="text"  value="<?php echo $this->_var['setting']['debit_time_o']; ?>"  />
                        </p>
                    </td>
                </tr>
                <!--
                <tr>
                    <td class="paddingT15"></td>&nbsp;
                    <td class="paddingT15 wordSpacing5">
                        是否启用:
                            <select name="data[status]">
                                <option value="1">&nbsp;&nbsp;是&nbsp;&nbsp;</option>
                                <option value="0">&nbsp;&nbsp;否&nbsp;&nbsp;</option>
                            </select>
                        <span class="grey"></span>
                    </td>
                </tr>
                -->
            </table>
        </div>
        <input class="tijia" type="button" id="Submit" value=" 生 成 " style="margin:20px 0 20px 200px;" />
    </form>
</div>
<div id="loading"><div class="loading-div"></div><div class="loading-text">生成中...</div></div>
<script>
    $(function(){
        $("#loading").ajaxStart(function(){
            $(this).show();
        });
        $.ajaxSetup({
            async: false
        });
        $('#Submit').click(function(){
            var formarr=$("form.create_voucher").serializeArray();
            var msgtext='';
            $.each(formarr, function(i, field){
                if(field.name == 'data[name]' && field.value == ''){
                    msgtext='优惠券名称不能为空';
                }
                if(field.name == 'data[category]' && field.value == ''){
                    msgtext='券分类不能为空';
                }
                if(field.name == 'data[money]' && field.value == ''){
                    msgtext='券价格不能为空';
                }
                if(field.name == 'data[num]' && field.value == ''){
                    msgtext='生成数量不能为空';
                }
                if(field.name == 'data[start_time]' && field.value == ''){
                    msgtext='生效时间不能为空';
                }
                if(field.name == 'data[end_time]' && field.value == ''){
                    msgtext='过期时间不能为空';
                }
            });
            if(msgtext!=''){
                alert(msgtext);
                return false;
            }
            var formstr = $("form.create_voucher").serialize();
            $.post("index.php?app=setting&act=voucher_create", formstr,
                function(data){
                    if(data == 'ok'){
                        alert('生成完成');
                        window.location.href='index.php?app=setting&act=voucher';
                    }else{
                        alert('生成异常');
                        window.location.href='index.php?app=setting&act=voucher_create';
                    }
            });
        });
    });
    
    function changeCate(obj)
    {
        var debit_time = "<?php echo $this->_var['setting']['debit_time']; ?>";
        var cate = $(obj).val();
        var data_name = $(obj).next().attr('data_name');
        if (cate == 1)
        {
            $(obj).next().attr('name', data_name + '1');
            $(obj).next().attr('onClick', '');
            $(obj).next().attr('class', 'infoTableInput');
            $(obj).next().val('');
        } else
        {
            $(obj).next().attr('name', data_name + '2');
            $(obj).next().attr('onClick', 'WdatePicker()');
            $(obj).next().attr('class', 'infoTableInput Wdate');
            $(obj).next().val('');
        }
    }
</script>
<?php echo $this->fetch('footer.html'); ?>