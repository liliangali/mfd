<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="index.php?app=jx&amp;act=export">导出</a></li>
    </ul>

</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get">
             <div class="left">
                <input type="hidden" name="app" value="jx" />
                <input type="hidden" name="act" value="index" />
                <select class="querySelect" name=field_name><?php echo $this->html_options(array('options'=>$this->_var['query_fields'],'selected'=>$_GET['field_name'])); ?>
                </select>
          		<input type="text" class="zzinput"  name="field_value" value="<?php echo htmlspecialchars($_GET['field_value']); ?>"/>
          		
          	创业者等级：<select class="querySelect" name=member_lv_id>
          			   	<option value="">全部</option>
          		 	 <?php echo $this->html_options(array('options'=>$this->_var['lang']['MEMBER_LV_ID'],'selected'=>$_GET['member_lv_id'])); ?>
                </select>
                创业者性别：
                <select class="querySelect" name=gender>
          			 <option value="">全部</option>
                	 <?php echo $this->html_options(array('options'=>$this->_var['sex_option'],'selected'=>$_GET['gender'])); ?>
                </select>

                 激活BD码时间从：<input class="queryInput2" type="text" value="<?php echo $this->_var['query']['add_time_from']; ?>" style="width:80px" id="add_time_from" name="add_time_from" class="pick_date" />
                 至：<input class="queryInput2" type="text" value="<?php echo $this->_var['query']['add_time_to']; ?>" style="width:80px" id="add_time_to" name="add_time_to" class="pick_date" />


      <!--           注册时间从：<input class="queryInput2" type="text" value="<?php echo $this->_var['query']['add_time_from']; ?>" style="width:80px" id="add_time_from" name="add_time_from" class="pick_date" />
              至：<input class="queryInput2" type="text" value="<?php echo $this->_var['query']['add_time_to']; ?>" style="width:80px" id="add_time_to" name="add_time_to" class="pick_date" />
-->
                <input type="submit" class="formbtn" value="查询" />
            </div>
        </form>
    </div>
    <div class="fontr">
       <?php echo $this->fetch('page.top.html'); ?>
    </div>
</div>
<br/>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">

        <tr class="tatr1">
        	  <td width="3%" class="firstCell"><input type="checkbox" class="checkall" /></td>
            <td width="5%"><span ectype="order_by" fieldname="user_id">ID</span></td>
          	<td width="21%"><span  ectype="order_by" fieldname="user_name">创业者昵称&nbsp;|&nbsp;用户名</span></td>

            <td width="10%"><span>下单数</span></td>
            <!--<td width="10%"><span  ectype="order_by" fieldname="gender">性别</span></td>-->
        	  <!--<td width="10%"><span  ectype="order_by" fieldname="region_id">所在地区</span></td>-->
           	<td width="15%"><span  ectype="order_by" fieldname="reg_time">注册时间</span></td>
            <td width="15%"><span  ectype="order_by" fieldname="add_time">激活BD码时间</span></td>
            <!--<td width="15%"><span  ectype="order_by" fieldname="to_time">使用特权码时间</span></td>-->

            <td width="10%"><span  ectype="order_by" fieldname="member_lv_id">当前等级</span></td>
       		  <td><span>推广员|手机</span></td>
            <td><span>操作</span></td>
        </tr>

        <?php if (! empty ( $this->_var['list'] )): ?>
        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item']):
?>
        <tr class="tatr2">
            <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['item']['id']; ?>" /></td>
          	<td><?php echo $this->_var['item']['user_id']; ?></td>
            <td><?php echo $this->_var['item']['nickname']; ?>&nbsp;|&nbsp;<?php echo $this->_var['item']['user_name']; ?></td>
            <td><?php echo $this->_var['item']['order_num']; ?></td>
            <!--<td><?php echo $this->_var['item']['gender']; ?></td>-->
            <!--<td><?php echo $this->_var['item']['region_id']; ?></td>-->
            <td><?php echo $this->_var['item']['reg_time']; ?></td>
            <td><?php echo $this->_var['item']['add_time']; ?></td>
            <!--<td><?php echo $this->_var['item']['to_time']; ?>&lt;!&ndash;(<?php echo $this->_var['c_arr'][$this->_var['item']['cate']]; ?>)&ndash;&gt;</td>-->

            <td><?php echo $this->_var['item']['member_lv_id']; ?></td>
            <td><?php echo $this->_var['item']['inviter']; ?>&nbsp;|&nbsp;<?php echo $this->_var['item']['name']; ?></td>
            <td class="indexlabel">
            <a href="index.php?app=jx&act=view&id=<?php echo $this->_var['item']['id']; ?>&amp;page=<?php echo $this->_var['pageye']; ?>&amp;field_name=<?php echo $this->_var['fn']; ?>&amp;field_value=<?php echo $this->_var['fv']; ?>&amp;member_lv_id=<?php echo $this->_var['ml']; ?>&amp;gender=<?php echo $this->_var['g']; ?>&amp;add_time_to=<?php echo $this->_var['t']; ?>&amp;add_time_from=<?php echo $this->_var['f']; ?>">查看详情 </a>
                |
                <a href="javascript:void(0)" onclick="if(confirm('确认解绑该用户吗？！（请慎重操作！！）'))location.href='index.php?app=jx&act=drop&id=<?php echo $this->_var['item']['id']; ?>&amp;page=<?php echo $this->_var['pageye']; ?>&amp;field_name=<?php echo $this->_var['fn']; ?>&amp;field_value=<?php echo $this->_var['fv']; ?>&amp;member_lv_id=<?php echo $this->_var['ml']; ?>&amp;gender=<?php echo $this->_var['g']; ?>&amp;add_time_to=<?php echo $this->_var['t']; ?>&amp;add_time_from=<?php echo $this->_var['f']; ?>'"> 解绑</a>
            </td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <?php else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; ?>
    </table>
    <?php if (! empty ( $this->_var['list'] )): ?>
    <div id="dataFuncs">
        <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
        <div class="clear"></div>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">


//日期
$('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
$('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});
</script>
<?php echo $this->fetch('footer.html'); ?>