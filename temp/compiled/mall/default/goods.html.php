        <div class="mt">
            <h2 style="border:0">商品清单</h2>
            <div class="qingdan">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th colspan="2">商品名称</th>
                    <th>商品信息</th>
                    <th>单价（元）</th>
                    <th>数量</th>
                    <th>金额（元）</th>
                </tr>
                <?php $_from = $this->_var['cart']['object']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>            
                <tr data-id="<?php echo $this->_var['list']['ident']; ?>" data-type="<?php echo $this->_var['list']['type']; ?>">
                    <td width="50"><a href="<?php if ($this->_var['list']['goods']['goods_id'] > 0): ?><?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['list']['goods']['goods_id'])); ?><?php else: ?><?php echo $this->_var['list']['goods']['goods_url']; ?><?php endif; ?>"><img src="<?php echo $this->_var['list']['goods']['image']; ?>" width="50" height="50"></a></td>
                    <td><a href="<?php if ($this->_var['list']['goods']['goods_id'] > 0): ?><?php echo $this->build_url(array('app'=>'goods','arg'=>$this->_var['list']['goods']['goods_id'])); ?><?php else: ?><?php echo $this->_var['list']['goods']['goods_url']; ?><?php endif; ?>">
					   <?php if ($this->_var['list']['type'] == 'fdiy'): ?>
						<?php $_from = $this->_var['list']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ls');if (count($_from)):
    foreach ($_from AS $this->_var['ls']):
?><?php echo $this->_var['ls']['dog_name']; ?><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>的专属狗粮<?php else: ?><?php echo $this->_var['list']['goods']['name']; ?><?php endif; ?></a></td>
                    <td>
						<?php if ($this->_var['list']['type'] == 'fdiy'): ?>
						定制商品：<span style="padding-left: 5px;"><?php echo $this->_var['list']['goods']['dname']; ?></span>
						<?php else: ?>
						<?php $_from = $this->_var['list']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ls');if (count($_from)):
    foreach ($_from AS $this->_var['ls']):
?> 
						<span style="padding-left: 5px;"><?php echo $this->_var['ls']['params']['oProducts']['spec_info']; ?></span><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?><?php endif; ?>
						</td>
                    <td><?php echo price_format($this->_var['list']['goods']['basePrice']); ?></td>
                    <td><?php echo $this->_var['list']['goods']['quantity']; ?></td>
                    <td><?php echo price_format($this->_var['list']['goods']['basePrice']); ?></td>
                </tr>
           
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </table>
            </div>
        </div>