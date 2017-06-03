<?php if ($_SESSION['user_info']['member_lv_id'] > 1): ?>
<?php echo $this->fetch('member.menu.entrepreneur.html'); ?>
<?php else: ?>
<?php echo $this->fetch('member.menu.consumer.html'); ?>
<?php endif; ?>