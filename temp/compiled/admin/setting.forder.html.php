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


<div class="info">
  <form method="post" enctype="multipart/form-data">
    <table class="infoTable">

      <tr>
        <th class="paddingT15">是否开启注册送券活动 :</th>
        <td class="paddingT15"><input id="forder" type="radio" name="forder" <?php if ($this->_var['setting']['forder']): ?>checked<?php endif; ?> value="1" />
          <label for="if_photo_disabled">是</label>
          <input type="radio" id="forder" name="forder" <?php if (! $this->_var['setting']['forder']): ?>checked<?php endif; ?> value="0" />
          <label for="if_photo_enable">否</label>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> <label for="hot_search">活动开始时间:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search Wdate" type="text" name="forder_start_time" onClick="WdatePicker()" value="<?php echo $this->_var['setting']['forder_start_time']; ?>" class="infoTableInput"/>
          <label class="field_notice"></label></td>
      </tr>

      <tr>
        <th class="paddingT15"> <label for="hot_search">活动结束时间:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search Wdate" type="text" onClick="WdatePicker()"  name="forder_end_time" value="<?php echo $this->_var['setting']['forder_end_time']; ?>" class="infoTableInput"/>
          <label class="field_notice"></label></td>
      </tr>

      <tr>
        <th class="paddingT15"> <label for="hot_search">券价格:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search Wdate" type="text"   name="forder_money" value="<?php echo $this->_var['setting']['forder_money']; ?>" class="infoTableInput"/>
          <label class="field_notice"></label></td>
      </tr>


      <tr>
        <th class="paddingT15"> <label for="hot_search">券名称:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search " type="text" name="forder_name" value="<?php echo $this->_var['setting']['forder_name']; ?>" class="infoTableInput"/>
          <label class="field_notice"></label></td>
      </tr>

      <tr>
        <th class="paddingT15"> <label for="hot_search">订单价格(满次价格可使用):</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search " type="text" name="forder_order_money" value="<?php echo $this->_var['setting']['forder_order_money']; ?>" class="infoTableInput"/>
          <label class="field_notice"></label></td>
      </tr>


      <tr>
        <th class="paddingT15"> <label for="hot_search">券有效天数:</label></th>
        <td class="paddingT15 wordSpacing5"><input id="hot_search " type="text"  name="forder_days" value="<?php echo $this->_var['setting']['forder_days']; ?>" class="infoTableInput"/>
          <label class="field_notice"></label></td>
      </tr>

      <tr>
        <th class="paddingT15"> <label for="hot_search">券品类:</label></th>
        <td class="paddingT15 wordSpacing5">
          <select id="category" name="forder_category">
            <option value="1,2" <?php if ($this->_var['setting']['forder_category'] == '1,2'): ?>selected<?php endif; ?>>&nbsp;&nbsp;通用&nbsp;&nbsp;</option>
            <option value="1" <?php if ($this->_var['setting']['forder_category'] == '1'): ?>selected<?php endif; ?>>&nbsp;&nbsp;定制商品&nbsp;&nbsp;</option>
            <option value="2" <?php if ($this->_var['setting']['forder_category'] == '2'): ?>selected<?php endif; ?>>&nbsp;&nbsp;普通商品&nbsp;&nbsp;</option>
          </select>
        </td>
      </tr>

      <tr>
        <th></th>
        <td class="ptb20"><input class="tijia" type="submit" name="Submit" value="提交" />
          <input class="congzi" type="reset" name="Submit2" value="重置" />        </td>
      </tr>
    </table>
  </form>
</div>
<?php echo $this->fetch('footer.html'); ?>