        <?php $_from = $this->_var['comment_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
        <li>
			<p class="yxtx"><a href="#"><img src="<?php echo $this->_var['list']['face']; ?>"></a><span><?php echo $this->_var['list']['nickname']; ?></span></p>
			<div class="div_1">
				<div class="fwzl">
                  <div class="fw fl"><p class="fl"></p><span class="xing_<?php echo $this->_var['list']['star']; ?>"></span></div>
                  <p class="date"><?php echo local_date("Y-m-d H:i:s",$this->_var['list']['addtime']); ?></p>
                </div>
				<p class="p1"><?php echo $this->_var['list']['content']; ?></p>
			</div>
		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		<?php if ($this->_var['page_info']['page_count'] > 1): ?>
			<!--翻页开始-->
          <div class="cc_page">
            <?php if ($this->_var['page_info']['prev_link']): ?>
            <a href="javascript:;" onclick="goToPage(this)" data-url="<?php echo $this->_var['page_info']['prev_link']; ?>" title="上一页" class="zjt">&lt;</a>
            <?php endif; ?>
             <?php $_from = $this->_var['page_info']['page_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('page', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['page'] => $this->_var['link']):
?>
             <?php if ($this->_var['page_info']['curr_page'] == $this->_var['page']): ?>
             <span class="active"><?php echo $this->_var['page']; ?></span>
              <?php else: ?>
              <a href="javascript:;" onclick="goToPage(this)" data-url="<?php echo $this->_var['link']; ?>"><?php echo $this->_var['page']; ?></a>
                  <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php if ($this->_var['page_info']['next_link']): ?>
            <a href="javascript:;" onclick="goToPage(this)" data-url="<?php echo $this->_var['page_info']['next_link']; ?>" title="下一页" class="zjt">&gt;</a>
            <?php endif; ?>
         </div>  
        <!--翻页结束-->
		<?php endif; ?>