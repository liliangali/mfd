<script type="text/javascript" src="/static/js/pettype/catmlselection.js"></script>
<script src="/static/js/jquery.min.js"></script>
<script src="/static/expand/layer/layer.min.js"></script>
<script type="text/javascript">
//<!CDATA[
var typeUrl = "<?php echo $this->build_url(array('app'=>'mlselection')); ?>";
$(function(){
    typeInit("pettype");
});
</script>  


 <form method="post"  id="pet_form" target="iframe_post">
    <div class='addto' >
    <input type="hidden" name="pet_id" value="<?php echo $this->_var['data']['pet_id']; ?>" id="pet_id" />
    <input type="hidden" name="region_id" value="<?php echo $this->_var['data']['region_id']; ?>" id="region_id" class="mls_id" />
    <input type="hidden" name="region_name" value="<?php echo htmlspecialchars($this->_var['data']['region_name']); ?>" class="mls_names" />
    <input type="hidden" name="region_list" value="<?php echo $this->_var['data']['region_id']; ?>" id="type_list" />
    <input type='text' name='name' value="<?php echo $this->_var['data']['name']; ?>" placeholder='宠物昵称' class='tyinput' validate='required' >
    <label><input type="radio" <?php if ($this->_var['data']['gender'] == 1): ?>checked="checked"<?php endif; ?> value="1" name="gender">公</label>
    <label><input type="radio" <?php if ($this->_var['data']['gender'] == 2): ?>checked="checked"<?php endif; ?> value="2" name="gender">母</label>
    
                        <div id="pettype">
                        <select name='text' class='tccxl' validate='required'>
                        <?php if ($this->_var['defdata']['0']): ?>
                           <?php echo $this->html_options(array('options'=>$this->_var['typesA'],'selected'=>$this->_var['defdata']['0'])); ?>
                        <?php else: ?>
                          <option value='0'>请选择</option>  
                         <?php echo $this->html_options(array('options'=>$this->_var['typesA'])); ?>
                        <?php endif; ?>
                        </select>
                       <?php if ($this->_var['defdata']['1']): ?>
                        <select name='text' class='tccxl tccxls' validate='required'>
                            <?php echo $this->html_options(array('options'=>$this->_var['typesB'],'selected'=>$this->_var['defdata']['1'])); ?>
                        </select>
                        <?php endif; ?>
                         <?php if ($this->_var['defdata']['2']): ?>
                         <select name='text' class='fstccxl' validate='required'>
                          <?php echo $this->html_options(array('options'=>$this->_var['typesC'],'selected'=>$this->_var['defdata']['2'])); ?>
                        </select>
                          <?php endif; ?>
                    </div>


    <input type='text' name ='birthday' value="<?php echo $this->_var['data']['birthday']; ?>" placeholder='宠物生日' class='tyinput' validate='' />
    <p><textarea class='textwby' name='summary' placeholder='宠物简介' validate='required'><?php echo $this->_var['data']['summary']; ?></textarea></p>
    <div class='ddbutt'><input type='button' value='取消' id='quxiao' class='ddquxiao' />
    <input type='button' value='保存' id='save' class='ddquxiao ddbaocun' />
    </div>
    </div>
</form>