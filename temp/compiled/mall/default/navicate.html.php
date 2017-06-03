<ul class="toolbar">
    <li class="toolbar-cart"><a href="cart.html"><span class="tab-text">购物车</span><i class="tab-ico"></i></a></li>
    <li class="toolbar-follow"><a href="member.html"><span class="tab-text">账户中心</span><i class="tab-ico"></i></a></li>
    <li class="toolbar-app"><a href="down.html"><span class="tab-text">APP下载</span><i class="tab-ico"></i></a></li>
</ul>
<ul class="toolbar toolbar-2">
    <li class="toolbar-kefu"><a href="javascript:void(0);"  class="zhichi"><span class="tab-text">在线客服</span><i class="tab-ico"></i></a></li>
    <li class="toolbar-fankui"><a href="javascript:void(0)" id="feedback"><span class="tab-text">意见反馈</span><i class="tab-ico"></i></a></li>
    <li class="toolbar-top"><a href="javascript:void(0)" onClick="window.scroll(0,0)"><span class="tab-text">返回顶部</span><i class="tab-ico"></i></a></li>
</ul>
<?php if ($this->_var['zc_userid']): ?>
<script src="http://www.sobot.com/chat/pc/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf&partnerId=<?php echo $this->_var['zc_userid']; ?>&uname=<?php echo $this->_var['zc_uname']; ?>&tel=<?php echo $this->_var['zc_tel']; ?>&email=<?php echo $this->_var['zc_email']; ?>" id="zhichiload" class="zhichi"></script>
<?php else: ?>
<script src="http://www.sobot.com/chat/pc/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf&groupId=84c09075813e45f68eae414cf91e897a" id="zhichiload" class="zhichi"></script>
<?php endif; ?>