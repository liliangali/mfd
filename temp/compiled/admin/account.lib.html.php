<table width="745" border="0" class="ddxxltsj">
        <tr class="y_l_atr">
          <td>用户：<?php echo $this->_var['info']['user_name']; ?></td>

        <!-- </tr>
        <tr class="y_l_atr">
          <td>麦富迪币：<?php if ($this->_var['info']['coin_type'] == 1): ?><span style="color:#0000FF;">+<?php echo $this->_var['info']['coin']; ?></span><?php elseif ($this->_var['info']['coin_type'] == 2): ?><span style="color:red;">-<?php echo $this->_var['info']['coin']; ?></span><?php endif; ?></td>

        </tr> -->
        <tr class="y_l_atr">
          <td>积分：<?php if ($this->_var['info']['point_type'] == 1): ?><span style="color:#0000FF;">+<?php echo $this->_var['info']['point']; ?></span><?php elseif ($this->_var['info']['point_type'] == 2): ?><span style="color:red;">-<?php echo $this->_var['info']['point']; ?></span><?php endif; ?></td>

        <!-- </tr>
        <tr class="y_l_atr">
          <td>余额：<?php if ($this->_var['info']['money_type'] == 1): ?><span style="color:#0000FF;">+<?php echo $this->_var['info']['money']; ?></span><?php elseif ($this->_var['info']['money_type'] == 2): ?><span style="color:red;">-<?php echo $this->_var['info']['money']; ?></span><?php endif; ?></td>

        </tr> -->
        <tr class="y_l_atr">
          <td>等级变更：<?php echo $this->_var['info']['newlv']; ?></td>

        </tr class="y_l_atr">
        <tr class="y_l_atr">
          <td>调解原因：<?php echo $this->_var['info']['brief']; ?></td>

        </tr>
        <tr>
          <td>凭证：
          <?php if ($this->_var['imgs']): ?>
          <?php $_from = $this->_var['imgs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['link']):
?>
          <a target="_blank" href="<?php echo $this->_var['link']['source_img']; ?>"><img style="margin:8px 0 0 10px;" width="60" height="60" src="<?php echo $this->_var['link']['source_img']; ?>"></a>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          <?php else: ?>
          暂无凭证
          <?php endif; ?>
          </td>
        </tr>
        <tr class="y_l_b">
          <td>驳回原因:
          <p class="xd"><textarea name="content" id="content" cols="" rows="" placeholder=""></textarea></p>
          </td>

        </tr>

        <tr>
        <td class="y_l_a">
        <input type="hidden" name="ac_id" id="ac_id"  value="<?php echo $this->_var['info']['id']; ?>" />
	    <input class="congzi" type="button" name="bh" id="bh"  value="驳回" />
	    <input class="tijia" type="button"name="tg"  id="tg" value="通过" />
		 </td>
        </tr>
</table>
