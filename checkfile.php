<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>文件md5值</title>

</head>

<body>
<form enctype="multipart/form-data" action="checkfile.php" method="post">
    <table style="">
        <tr style="align:center">
            <td><label for="txtname"><font color="red">文件相对路径：</font></label></td>
            <td><input type="text" id="source" name="source" value="" onkeydown="this.onkeyup();" onkeyup="this.size=(this.value.length>4?this.value.length:4);" size="4"/> </td>
        </tr>

        <tr style="text-align:center">
            <td colspan=2>
            	<input type="submit" />
                <input type="reset" />
                
            </td>
        </tr>
    </table>
</form> 


</body></html>
<?php

		$source=isset($_REQUEST['source'])?$_REQUEST['source']:'';
		if(!$source){
			
			
		}else {
			$arr=explode('/',$source);
			$count=count($arr);
			if(!$count){
				$content="填写文件的相对路径";
			}else{
				if(substr($source,0,1)!='/'){
					$source='/'.$source;
				}
				$source=$_SERVER['DOCUMENT_ROOT'].$source;
				//123
				$filedir=dirname($source);
				if(is_dir($filedir)){
					if(is_dir($source)){
						
						$content="该目录存在";
					}else{
						if(file_exists($source)){
							$md5file=md5_file($source);
							$content=$md5file;
						}else {
							$filename=basename($source);
							if(strpos($filename,'.')){
								
								$content="文件不存在";
							}else{
								$content="目录不存在";
							}
							
						}
					}
					
				}else {
					$content="文件目录不存在";
				}
			}
			echo "<div style='text-align:center;font-size:40px'>{$_REQUEST['source']}</div><div style='text-align:center;font-size:40px'><font color='red' >{$content}</font></div>";
		}



?>
