<?php if ($this->_var['page_info']['page_count'] > 1): ?>
<div class="cc_page">
    <?php if ($this->_var['page_info']['prev_link']): ?>
    <a class="former" href="<?php echo $this->_var['page_info']['prev_link']; ?><?php if ($this->_var['page_info']['parameter']): ?><?php echo $this->_var['page_info']['parameter']; ?><?php endif; ?>" title="上一页">上一页</a>
    <?php else: ?>
    上一页
    <?php endif; ?>


    <?php if ($this->_var['page_info']['first_link']): ?>
    <a class="page_link" href="<?php echo $this->_var['page_info']['first_link']; ?><?php if ($this->_var['page_info']['parameter']): ?><?php echo $this->_var['page_info']['parameter']; ?><?php endif; ?>">1&nbsp;<?php echo $this->_var['page_info']['first_suspen']; ?></a>
    <?php endif; ?>
    <?php $_from = $this->_var['page_info']['page_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('page', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['page'] => $this->_var['link']):
?>
    <?php if ($this->_var['page_info']['curr_page'] == $this->_var['page']): ?>
    <span class="active"><?php echo $this->_var['page']; ?></span>
    <?php else: ?>
    <a class="page_link" href="<?php echo $this->_var['link']; ?><?php if ($this->_var['page_info']['parameter']): ?><?php echo $this->_var['page_info']['parameter']; ?><?php endif; ?>"><?php echo $this->_var['page']; ?></a>
    <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php if ($this->_var['page_info']['last_link']): ?>
    <a class="page_link" href="<?php echo $this->_var['page_info']['last_link']; ?><?php if ($this->_var['page_info']['parameter']): ?><?php echo $this->_var['page_info']['parameter']; ?><?php endif; ?>"><?php echo $this->_var['page_info']['last_suspen']; ?>&nbsp;<?php echo $this->_var['page_info']['page_count']; ?></a>
    <?php endif; ?>


    <?php if ($this->_var['page_info']['next_link']): ?>
    <a href="<?php echo $this->_var['page_info']['next_link']; ?><?php if ($this->_var['page_info']['parameter']): ?><?php echo $this->_var['page_info']['parameter']; ?><?php endif; ?>" title="下一页">下一页</a>
    <?php else: ?>
    下一页
    <?php endif; ?>


</div>
<?php endif; ?>