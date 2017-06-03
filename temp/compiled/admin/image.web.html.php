<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=images&w=<?php echo $this->_var['water']; ?>&d=<?php echo $this->_var['dir']; ?>&callback=<?php echo $this->_var['callback']; ?>">本地上传图片</a></li>
        <li><a class="btn1" href="index.php?app=images&act=fromdb&w=<?php echo $this->_var['water']; ?>&d=<?php echo $this->_var['dir']; ?>&callback=<?php echo $this->_var['callback']; ?>">图库选择图片</a></li>
        <li><span>网络上的图片</span></li>
    </ul>
</div>
<div class="info qdhqx_tab">
        <table class="infoTable">
        <tr>
           <td class="wlxskrs">
					输入图片地址:<input type="text" name="file_path" value="" id="file_path">
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
<script>
$(document).ready(function(){
	$selected = '';
	$(".save").click(function(){
		
		$src = $("#file_path").val();
		
		if(!$src){
			alert('请输入图片地址！');
			return false;
		}

		var t = /^http:\/\/(.*?)\.(gif|jpg|jpeg|png)$/;
		
		if(!t.test($src))
		{
			alert("不是有效的网络图片地址!");
			return false;
		}
		window.opener.<?php echo $this->_var['callback']; ?>($src);
		window.close();  
			
	})
	
	$(".congzi").click(function(){
		window.close();  
	})
})
</script>
<?php echo $this->fetch('footer.html'); ?>