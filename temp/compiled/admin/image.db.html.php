<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=images&w=<?php echo $this->_var['water']; ?>&d=<?php echo $this->_var['dir']; ?>&callback=<?php echo $this->_var['callback']; ?>">本地上传图片</a></li>
        <li><span>图库选择图片</span></li>
        <li><a class="btn1" href="index.php?app=images&act=fromweb&w=<?php echo $this->_var['water']; ?>&d=<?php echo $this->_var['dir']; ?>&callback=<?php echo $this->_var['callback']; ?>">网络上的图片</a></li>
    </ul>
</div>
<div class="info qdhqx_tab">

        <table class="infoTable">
        <tr>
                <td class="wlxskrs">
                     
					文件名称:<input type="text" name="filename" value="<?php echo $this->_var['filename']; ?>" id="filename">

                	上传时间：<input type="text" name="uptime" id="uptime" value="<?php echo $this->_var['uptime']; ?>">
					<input type="button" value="搜索" id="search">
					
					<input type="hidden" name="w" value="<?php echo $this->_var['water']; ?>" id="water">
					<input type="hidden" name="d" value="<?php echo $this->_var['dir']; ?>" id="dir">
					<input type="hidden" name="callback" value="<?php echo $_GET['callback']; ?>" id="callback">
					</td>
            </tr>
            <tr>
            	<td>
					<ul id="thumbnails">
						<?php $_from = $this->_var['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['img']):
?>
						<li style="height:115px;">
						  <p><img src="<?php echo $this->_var['site_url']; ?>/<?php echo $this->_var['img']['file_path']; ?>" width="80" height="80" title="<?php echo $this->_var['img']['file_name']; ?>" alt="<?php echo $this->_var['img']['file_name']; ?>"></p>
						  <p style="height:24px; line-height: 24px; overflow:hidden;"><?php echo $this->_var['img']['file_name']; ?></p>
						</li>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</ul>
				</td>
            </tr>
            <tr>
            	<td>
					<?php echo $this->fetch('page.bottom.html'); ?>
				</td>
            </tr>
            <tr>
                <td class="qdhqx">
                  <input type="button" value="确定"  class="tijia save"/>
                  <input type="button" value="关闭"  class="congzi"/>
                </td>
            </tr>
            </table>
</div>
<<script src="<?php echo $this->lib_base . "/" . 'jquery.min.js'; ?>"></script>
<script>
$(document).ready(function(){
	$(".save").click(function(){
		var src  = $("ul>li.on").find("img").attr("src");
		if(!src){
			alert('请选择图片！');
			return false;
		}
		window.opener.<?php echo $this->_var['callback']; ?>(src);
		window.close();  
	})
	
	$("#search").click(function(){
		var filename = $("#filename").val();
		var uptime   = $("#uptime").val();
		var water    = $("#water").val();
		var dir      = $("#dir").val();
		var callback = $("#callback").val();
		location.href="index.php?app=images&act=fromdb&filename="+filename+"&uptime="+uptime+"&w="+water+"&d="+dir+"&callback="+callback;
	})
	
	$("#thumbnails").find("li").each(function(){
		$(this).click(function(){
			$(this).parents("ul").find("li").each(function(){
				$(this).removeClass("on");
			})
			$(this).addClass("on");
		})
	})
	
	$(".congzi").click(function(){
		window.close();  
	})
})
</script>
<?php echo $this->fetch('footer.html'); ?>