<?php
//webserver
class Auth { 
	static $appIDs = array("101","102");//项目代号
	static $key = "gamebean";//密钥
	static $regClass = array("Cart","User","Store","Sitecitys","Club","Order","Work","Profit","SYB","Feedback","First","Loginregister","Shao", "Gallery","Tang", "Fabricbook","Bookorder","Rcmtm", "Channel", "Coin", "Product","gcategorylist");//允许访问的类
	static $allowIp = array("127.0.0.1","223.223.176.37","192.168.1.100","117.79.91.203","60.247.21.66","100.100.50.189");//允许访问的IP
	//http与SOAP共用，头信息验证
	static function header( $ctidTagValue,$appidTagValue,$submitTimeTagValue ){
		if( $ctidTagValue && $appidTagValue && $submitTimeTagValue){//请求者的数据中是否为空
			if(in_array($appidTagValue,self::$appIDs)){//请求者的APPID是否正确
				//把请求者的数据进行解密
				$auth_key = md5(self::$key.$appidTagValue.$submitTimeTagValue);
				if($auth_key == $ctidTagValue){//判断用户提交的数据是否合法
					$status = 200;
				}else{
					$status = 2004;//非法请求：MD5值错误
				} 
			}else{
					$status = 2002;//非法的请求:请求的APPID错误
				}
		}else{
			$status = 2001;//XML请求格式错误
		}

		return $status;
	}
	//http方式，执行
	static function body($classTagValue,$methodTagValue,$paraArr){
		if(in_array($classTagValue,self::$regClass)){//判断访问的类，是否允许
			$controller = WEBSER_LIB_CTRL."/".$classTagValue.".class.php";
			include $controller;
// 		echo $classTagValue;exit;
			if(class_exists($classTagValue)){//请求的类是否存在
				if(method_exists($classTagValue,$methodTagValue)){//请求的方法是否存在
					//PHP反射类
					$refMethod = new ReflectionMethod($classTagValue,$methodTagValue);
					$classParaAttr = $refMethod->getParameters();//取得 请求类的 参数信息
					$methodParas = array();
					foreach ($classParaAttr as $row){
						$methodParas[] = $row->name;//遍历参数
					}
					$rs = '';
					$countmethodParas = count($methodParas);//统计 方法 共有几个参数
					$class = new $classTagValue();

					if(!$countmethodParas){//如果该方法没有参数
						$rs = $class->$methodTagValue();
						if( !isset($rs['error']) ){
							$returnRs = array('data'=>$rs,'status' => 200);
						}else{
							//参数值有问题
							$returnRs = array('status' => 3006,'code'=>$rs['code'], 'msg'=> $rs['msg']);
						}
					}else{
						//获取请求者的 参数值
						$postParas = array();//请求者的参数名
						$postParasValues = array();//请求者的参数值
						foreach ($paraArr as $key=>$one){
							$postParas[] = key($one);
							$postParasValues[] = $one[key($one)];
						}
						$finalParaValues = array();//最终 请求的参数值
						$countCompare = 0;//记录 请求的参数与实际的参数 相同数
						if($countmethodParas == count($postParas)){//判断，请求的参数个数，与实际参数的个数
							foreach ($methodParas as $methodParaName){
								foreach ($postParas as $key=>$postParaName){
									if($methodParaName == $postParaName){
										$finalParaValues[] = $postParasValues[$key];
										$countCompare++;
									}
								}
							}
							if($countCompare == $countmethodParas){//如果实请求的参数与实际的参数 个数相同
								$rs = call_user_func_array(array(&$class,$methodTagValue),$finalParaValues);
								if( 0 ==  $rs['error'] ){
									$returnRs = array('data'=>$rs['data'],'status' => 200);
								}else{
									//参数值有问题
									$returnRs = array('status' => 3006,'code'=>$rs['code'], 'msg'=> $rs['msg']);
								}
							}else{
								//参数名有误
								$returnRs = array('status' => 3005);
							}
						}else{
							//用户传输的参数个数与实际的个数不同
							$returnRs = array('status' =>3004);
						}
					}
				}else{
					//方法不存在
					$returnRs = array('status' =>3003);
				}
			}else{
				//类不存在
				$returnRs = array('status' => 3002);
			}
		}else{
			//此类不允许访问
			$returnRs = array('status' => 3001);
		}

		return $returnRs;
	}
	//SOAP方式执行
	static function SoapRouter($classTagValue,$methodTagValue,$paraArr){

		if(in_array($classTagValue,self::$regClass)){

			if(class_exists($classTagValue)){//请求的类是否存在

				if(method_exists($classTagValue,$methodTagValue)){//请求的方法是否存在

					//PHP反射类
					$refMethod = new ReflectionMethod($classTagValue,$methodTagValue);
					$classParaAttr = $refMethod->getParameters();//取得 请求类的 参数信息

					$methodParas = array();
					foreach ($classParaAttr as $row){
						$methodParas[] = $row->name;//遍历参数
					}
					$rs = '';
					$countmethodParas = count($methodParas);//统计 方法 共有几个参数

					$class = new $classTagValue();

					if(!$countmethodParas){//如果没有参数

						$rs = $class->$methodTagValue();
						return $rs;


					}else{
						$postParas = array();//请求者的参数名
						$postParasValues = array();//请求者的参数值

						foreach ($paraArr as $key=>$one){
								$postParas[] = $key;
								$postParasValues[$key] = $paraArr[$key];
						}
						$finalParaValues = array();//最终 请求的参数值
						$countCompare = 0;//记录 请求的参数与实际的参数 相同数

// 				return $countCompare;
						if($countmethodParas == count($postParas)){


							foreach ($methodParas as $methodParaName){
								foreach ($postParas as $postParaName){

									if($methodParaName == $postParaName){
										$finalParaValues[] = $postParasValues[$postParaName];
										$countCompare++;
									}


								}
							}
// 					return $countCompare;
							if($countCompare == $countmethodParas){//如果实请求的参数与实际的参数 个数相同

								$rs = call_user_func_array(array(&$class,$methodTagValue),$finalParaValues);

								return $rs;
							}else{

								$returnRs = array('status' => 1008,'data1'=>$countmethodParas,'data2'=>$countCompare,'method'=>$methodTagValue,'para'=>$paraArr,'tt'=>$methodParas);//参数名有误
								return $returnRs;
							}
						}else{
							$returnRs = array('status' =>1007);//用户传输的参数个数与实际的个数不同
								return $returnRs;
						}
					}
				}else{
					$returnRs = array('status' =>10062);//方法不存在
					return $returnRs;
				}
			}else{
				$returnRs = array('status' => 1005);//类不存在
				return $returnRs;
			}
		}
	}
	//根据APPID取得密钥值
	static function getAppPINKey($appId){
		return "infobird";
	}
	//验证IP
	static function authIP(){
		$clientIp = get_client_ip();//取得客户端IP
		if(in_array($clientIp,self::$allowIp)){//IP是否允许访问
			return 1;
		}
		return 0;
	}
}
