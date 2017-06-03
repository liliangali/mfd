<?php echo $this->fetch('header.html'); ?>

<div class="mrightTop">
  <div class="fontl">
    <form method="get">
       <div class="left">
          <input type="hidden" name="app" value="operation" />
          <input type="hidden" name="act" value="index" />
          操作员 : <input class="queryInput" type="text" name="field_value" value="<?php echo htmlspecialchars($_GET['field_value']); ?>" />
          模块管理 :<select class="querySelect" name="field_module" onchange="get_submodule(this)"><?php echo $this->html_options(array('options'=>$this->_var['module'],'selected'=>$_GET['field_module'])); ?>
          </select>
          子模块 :<select class="querySelect" name="field_submodule" id="submodule" onchange="get_feature(this)"><?php echo $this->html_options(array('options'=>$this->_var['submodule'],'selected'=>$_GET['field_submodule'])); ?>
          </select>
          功能 :<select class="querySelect" name="field_feature" id="feature"><?php echo $this->html_options(array('options'=>$this->_var['feature'],'selected'=>$_GET['field_feature'])); ?>
          </select>
          操作 :<select class="querySelect" name="operate_type"><?php echo $this->html_options(array('options'=>$this->_var['operate_type'],'selected'=>$_GET['operate_type'])); ?>
          </select>
        	操作时间从:<input class="queryInput2" type="text" value="<?php echo $this->_var['query']['add_time_from']; ?>" style="width:80px" id="add_time_from" name="add_time_from" class="pick_date" />
                至:<input class="queryInput2" type="text" value="<?php echo $this->_var['query']['add_time_to']; ?>" style="width:80px" id="add_time_to" name="add_time_to" class="pick_date" />
          
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <?php if ($this->_var['filtered']): ?>
      <a class="left formbtn1" href="index.php?app=operation&act=view">撤销检索</a>
      <?php endif; ?>
    </form>
  </div>
  <div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_var['users']): ?>
    <tr class="tatr1">
   <!--    <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td> -->
      <td>操作员</td>
      <td><span ectype="order_by" fieldname="ip">ip</span></td>
      <td><span ectype="order_by" fieldname="dateline">操作时间</span></td>
      <td>操作流程</td>
<!--       <td>子模块</td> -->
      <td>操作</td>
      <td>描述</td>
<!-- 	  <td width="16%">操作</td> -->
    </tr>
    <?php endif; ?>
    <?php $_from = $this->_var['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'user');if (count($_from)):
    foreach ($_from AS $this->_var['user']):
?>
    <tr class="tatr2">
   <!--    <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['user']['id']; ?>" /></td> -->
      <td><?php echo htmlspecialchars($this->_var['user']['username']); ?></td>
      <td><?php echo htmlspecialchars($this->_var['user']['ip']); ?></td>
      <td><?php echo $this->_var['user']['dateline']; ?></td>
      <td><?php echo $this->_var['user']['operate_key']; ?></td>
<!--       <td><?php echo $this->_var['user']['submodule']; ?></td> -->
     <td><?php echo $this->_var['user']['operate_type']; ?></td>
     <td><?php echo $this->_var['user']['memo']; ?></td>
<!--      <td><a href="index.php?app=operation&act=info&id=<?php echo $this->_var['user']['id']; ?>">查看</a></td> -->
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="10">没有符合条件的记录</td>
    </tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  <?php if ($this->_var['users']): ?>
  <div id="dataFuncs">
    <!-- <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=lv&act=drop" presubmit="confirm('你确定要删除它吗？该操作不会删除ucenter及其他整合应用中的用户');" />
    </div> -->
    <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
    <div class="clear"></div>
  </div>
  <?php endif; ?>
</div>
<script>
//日期
/* jquery 通过url获取get参数  */
/* $(function(){
	var aQuery = window.location.href.split("?");  //取得Get参数
	var aGET = new Array();
    if(aQuery.length > 1)
    {
        var aBuf = aQuery[1].split("&");
        for(var i=0, iLoop = aBuf.length; i<iLoop; i++)
        {
            var aTmp = aBuf[i].split("=");  //分离key与Value
            aGET[aTmp[0]] = aTmp[1];
        }
     }
	console.log(aGET);
}); */
$('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
$('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});
function get_submodule(obj)
{
	var module = $(obj).val();
	$.ajax({
		   type: "POST",
		   url: "index.php?app=operation&act=get_submodule",
		   data: "module="+module,
		   success: function(msg)
		   {
			   msg=eval("("+msg+")");
			   //msg = jQuery.parseJSON(msg)
			     if(msg.done == true)
		    	 {
			    	 $('#submodule').empty();
			    	 $('#submodule').append(msg.retval)
		    	 }
		   },
			error:function(){
				console.log('获取子模块失败');
			}
		});
}
function get_feature(obj)
{
	var module=$("[name='field_module']").val();
	var submodule = $(obj).val();
	$.post('index.php?app=operation&act=get_feature',{module:module,submodule:submodule},function(res){
		if(res.done){
			$('#feature').empty();
	    	 $('#feature').append(res.retval);
		}else{
			console.log('获取子模块失败');
		}
	},'json');
/* 	$.ajax({
		   type: "POST",
		   url: "index.php?app=operation&act=get_feature",
		   data: "module="+module,
		   success: function(msg)
		   {
			   msg=eval("("+msg+")");
			   //msg = jQuery.parseJSON(msg)
			     if(msg.done == true)
		    	 {
			    	 $('#submodule').empty();
			    	 $('#submodule').append(msg.retval)
		    	 }
		   },
			error:function(){
				console.log('获取子模块失败');
			}
		}); */
}
</script>
<?php echo $this->fetch('footer.html'); ?>