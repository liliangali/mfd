<?php if ($this->_var['zc_userid']): ?>
<script src="http://www.sobot.com/chat/pc/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf&partnerId=<?php echo $this->_var['zc_userid']; ?>&uname=<?php echo $this->_var['zc_uname']; ?>&tel=<?php echo $this->_var['zc_tel']; ?>&email=<?php echo $this->_var['zc_email']; ?>" id="zhichiload" class="zhichi"></script>
<?php else: ?>
<script src="http://www.sobot.com/chat/pc/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf" id="zhichiload" class="zhichi"></script>
<?php endif; ?>