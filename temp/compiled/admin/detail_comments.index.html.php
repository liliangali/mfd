<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
  <p>订单评论</p>
</div>
<div class="mrightTop">
  <div class="fontl">
  		 <form method="get">
       <div class="left">
          <input type="hidden" name="app" value="detail_comments" />
          <input type="hidden" name="act" value="index" />
          状态：
           <select name="status">

               <option value="all" <?php if ($this->_var['status'] == "all"): ?>selected<?php endif; ?>>全部</option>
               <option value="0" <?php if ($this->_var['status'] === 0): ?>selected<?php endif; ?>>未审核</option>
               <option value="1" <?php if ($this->_var['status'] == 1): ?>selected<?php endif; ?>>审核通过</option>
               <option value="2" <?php if ($this->_var['status'] == 2): ?>selected<?php endif; ?>>审核未通过</option>
           </select>
           分类：
           <select name="cate">
               <option value="all"<?php if ($this->_var['cate'] == "all" || ! $this->_var['cate']): ?>selected<?php endif; ?>>全部</option>
               <option value="wk" <?php if ($this->_var['cate'] == "custom"): ?>selected<?php endif; ?>>商品</option>
               <option value="cy" <?php if ($this->_var['cate'] == "suit"): ?>selected<?php endif; ?>>套装</option>
           </select>
           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           订单ID:<input type="text" name="order_id"  value="<?php echo $this->_var['order_id']; ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           被评论商品ID:<input type="text" name="comment_id"  value="<?php echo $this->_var['comment_id']; ?>" />

          <input type="submit" class="formbtn" value="查询" />
      </div>
      <?php if ($this->_var['filtered']): ?>
      <a class="left formbtn1" href="index.php?app=detail_comments">撤销检索</a>
      <?php endif; ?>
    </form>
  </div>
  <!--<div class="fontr"><?php echo $this->fetch('page.top.html'); ?></div>-->
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <?php if ($this->_var['list']): ?>
    <tr class="tatr1">
        <td><span ectype="order_by" fieldname="id">ID</span></td>
        <td><span ectype="order_by" fieldname="order_id">订单ID</span></td>
        <td><span ectype="order_by" fieldname="comment_id">评论商品ID</span></td>
        <td><span ectype="order_by" fieldname="cate">类型</span></td>
        <td><span ectype="order_by" fieldname="star">评分</span></td>
        <td><span ectype="order_by" fieldname="nickname">评论者</span></td>
        <td><span ectype="order_by" fieldname="nickname">用户名</span></td>
        <td><span ectype="order_by" fieldname="content">内容</span></td>
        <td><span ectype="order_by" fieldname="come_from">来源</span></td>
        <td><span ectype="order_by" fieldname="addtime">评论时间</span></td>
      <td><span ectype="order_by" fieldname="status">状态</span></td>
      <td class="handler">操作</td>
    </tr>
    <?php endif; ?>



    <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['v']):
?>
    <tr class="tatr2">
      <td><?php echo $this->_var['v']['id']; ?></td>
      <td><?php echo $this->_var['v']['order_id']; ?></td>
      <td><?php echo $this->_var['v']['comment_id']; ?></td>
      <td><?php if ($this->_var['v']['cate'] == 'custom'): ?>商品<?php elseif ($this->_var['v']['cate'] == 'suit'): ?>套装<?php endif; ?></td>
      <td><?php echo $this->_var['v']['star']; ?></td>
      <td><?php echo $this->_var['v']['nickname']; ?></td>
      <td><?php echo $this->_var['v']['user_name']; ?></td>
      <td><span title="<?php echo $this->_var['v']['content']; ?>"><?php echo sub_str($this->_var['v']['content'],60); ?></span></td>
      <td><?php echo $this->_var['v']['come_from']; ?></td>
      <td><?php echo local_date("Y-m-d H:i:s",$this->_var['v']['addtime']); ?></td>
      <td><?php if ($this->_var['v']['status'] == '0'): ?>未审核<?php elseif ($this->_var['v']['status'] == '1'): ?>审核通过<?php else: ?>审核未通过<?php endif; ?></td>
      <td class="handler">
          <a href="index.php?app=detail_comments&amp;act=info&amp;id=<?php echo $this->_var['v']['id']; ?>">处理</a> |
          <a href="index.php?app=detail_comments&amp;act=del&amp;id=<?php echo $this->_var['v']['id']; ?>">删除</a>
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr class="no_data">
      <td colspan="7">没有符合条件的记录</td>
    </tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  <?php if ($this->_var['list']): ?>
  <div id="dataFuncs">
    <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
    <div class="clear"></div>
  </div>
  <?php endif; ?>
</div>
<?php echo $this->fetch('footer.html'); ?>