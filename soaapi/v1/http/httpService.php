<?php
include './../config.php';//核心配置文件
//允许访问的IP
// if(auth::authIP()){//IP是否允许访问
	if(isset($_POST['xmlData'])){//POST的数据是否为空
		$postXmlData = $_POST['xmlData'];//取得从客户端传过来的XML数据
		$postXmlData = str_replace("\\", "", $postXmlData);
		$dom = new DOMDocument();
		$dom->loadXML($postXmlData);//PHP装载XML数据
		
		$tmpTagObj = $dom->getElementsByTagName('ctid');
		$ctidTagValue = $tmpTagObj->item(0)->nodeValue;//获取请求中验证码的数据
		
		$tmpTagObj = $dom->getElementsByTagName('appid');
		$appidTagValue = $tmpTagObj->item(0)->nodeValue;//获取请求中访问者的ID
		
		$tmpTagObj = $dom->getElementsByTagName('submittime');
		$submitTimeTagValue = $tmpTagObj->item(0)->nodeValue;//获取请求中时间戳数据
		
		$tmpTagObj = $dom->getElementsByTagName('parameter');
		//验证客户端头信息
		$status = Auth::header($ctidTagValue,$appidTagValue,$submitTimeTagValue);
		if( 200 ==$status){//头信息验证是否成功 
			$tmpTagObj = $dom->getElementsByTagName('class');
			$classTagValue = $tmpTagObj->item(0)->nodeValue;//获取请求中：类名
							
			$tmpTagObj = $dom->getElementsByTagName('method');
			$methodTagValue = $tmpTagObj->item(0)->nodeValue;//获取请求中：方法名
			
			$paraTagObj = $dom->getElementsByTagName('parameter');//获取请求中的：方法参数
			$paraArr = array();//保存方法参数
			foreach ($paraTagObj as $one){
				//判断参数类型:二维数组 一维数组  或者  普通类型
				if('array' == $one->getAttribute('type')){//这是二维数组
					$arr = $one->getElementsByTagName("array");
					foreach ($arr as $oneArr){
						$arrElement = $oneArr->getElementsByTagName("element");
						$tmp = array();
						foreach ($arrElement as $o){
							$tmp []= array($o->getAttribute('name') => $o->nodeValue );
						}
						$tt = $tmp[0];
						for($i=1;$i<count($tmp);$i++){
							$tt = array_merge($tt,$tmp[$i]);
						}
						$paraArrTmp[] = $tt;
					}
					$paraArr[] = array($one->getAttribute('name') => $paraArrTmp);
				}else if('simple_array' == $one->getAttribute('type')){//一维数组
						$arrElement = $one->getElementsByTagName("element");
						$tmp = array();
						foreach ($arrElement as $o){
							$tmp [$o->getAttribute('name')] = $o->nodeValue;
						}
						$paraArr[] = array($one->getAttribute('name')=>$tmp);
				}else{//普通类型
					$paraArr[] = array($one->getAttribute('name')=> $one->nodeValue);
				}
				
			}
			//类名，方法名，参数 一切 就绪，准备开始执行
			$authRs = Auth::body($classTagValue,$methodTagValue,$paraArr);
			$status = $authRs['status'];
			if(3006 == $status){//3006,证明是服务器代码内部错误~
				$errorMsg = $authRs['msg'];
			}
			
		}
		$key = Auth::getAppPINKey($appId);//密钥
		$curr_time = time();
		//返回XML格式文件的，头信息加密处理
		$local_auth_key = md5($key.$codeTagValue.$appidTagValue.$curr_time);
		if(200 == $status){//200访问成功
			if($authRs['data'] || $authRs['data'] === 0){//客户端请求的方法，执行后，是否有返回值
				if(is_array($authRs['data'])){//客户端请求的方法，执行后，判断是否为：一维数组 多维数组  普通类型 
					$body = "<array>";
					if(is_array($authRs['data'][0])){//判断返回的信息是一维数组还是二组数组
						foreach ($authRs['data'] as $key1=>$row){
							foreach ($row as $key=>$value){
								$body .= "<$key>".$value."</$key>";
							}
							$body .= "</array>";
							if($key1 != count($authRs['data']) - 1){
								$body .= "<array>";
							}
						}
					}else{
						foreach ($authRs['data'] as $key=>$value){
							$body .= "<$key>".$value."</$key>";
						}
						$body .= "</array>";
					}
					var_dump($body);
				}else{
					$body .= $authRs['data'];//普通类型数据
				}
			}else{
				$status = 1004;
			}
		}else{
			//上面已经有STATUS值，这里不需要设置值了
		}
	}else{
		$status = 1002;//POST值为空
	}
// }else{
// 	$status = 1001;//ip错误
// }
// if($body){
// 		$body = base64_encode(md5($body));
// }
//返回的XML格式文件信息		
$xml = <<<EOD
<?xml version="1.0" encoding="utf8" ?>
<msg>
	<Head>
		<appid>$appidTagValue</appid>
	    <class>$classTagValue</class>
	    <method>$methodTagValue</method>
	    <ctid>$local_auth_key</ctid>
	    <submittime>$curr_time</submittime>
	    <status>$status</status>
  	</Head>
  	<body>$body</body>
  	<errorMsg>$errorMsg</errorMsg>
</msg>
EOD;
	
echo $xml;