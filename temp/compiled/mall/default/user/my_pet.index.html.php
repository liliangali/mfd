<?php echo $this->fetch('../header-new.html'); ?>
<link type="text/css" href="../static/css/check.css" rel="stylesheet" />
<link href="/public/static/pc/css/public.css" rel="stylesheet">
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<div class="user_box">
<?php echo $this->fetch('member.menu.html'); ?>
<div class="user_right user_rights fr">
<h4>宠物管理</h4>

<div style="padding-top:13px;">
<?php echo $this->fetch('pet/list.html'); ?>
</div>

</div>


</div>
<script src="../static/js/jquery-1.8.3.min.js"></script>
<script src="/public/static/pc/js/public.js"></script>
<script src="../static/js/shopCart.js"></script> 
<script src="../static/js/jquery.min.js"></script>
<script src="../static/expand/layer/layer.min.js"></script>
<?php echo $this->fetch('footer.html'); ?>











