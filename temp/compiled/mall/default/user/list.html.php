<div class="address" id="pet">
  <ul>
    <?php $_from = $this->_var['petlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');$this->_foreach['ads'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['ads']['total'] > 0):
    foreach ($_from AS $this->_var['list']):
        $this->_foreach['ads']['iteration']++;
?>
      <li data-id="<?php echo $this->_var['list']['pet_id']; ?>">
      <p class="underlined"><?php echo $this->_var['list']['name']; ?></p>
      <p><?php if ($this->_var['list']['gender'] == 1): ?>公<?php else: ?>母<?php endif; ?></p>
      <p><?php echo $this->_var['list']['region_name']; ?></p>
      <p><?php echo $this->_var['list']['summary']; ?></p>
      <div class="caozuoBtn">
      <input type="button" value="编辑" class="edit" />
      <input type="button" value="删除" class="del" />
      </div>
      </li>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
    <li class="syxdz" id="addPet">
    <span>+</span>
    使用新宠物
    </li>
  </ul>
</div>

