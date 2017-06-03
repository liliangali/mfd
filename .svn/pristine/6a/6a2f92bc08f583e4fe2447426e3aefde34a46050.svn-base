<?php
include_once('../config.php');
class SoapService {

	static $wsdlUrl = "soap/WSDL/SoapService.wsdl";
	static $SoapUrl = "soap/ServiceSoap.php";
	public static $noWsdl = 1; 

	//验证
	 public function Authorized($Arr){
	 	ini_set("soap.wsdl_cache_enabled", "0"); //这个非常需要注意
	 	$xml = file_get_contents('php://input');
	 	$dom = new DOMDocument();
		$AppidKey = array('AppID', 'AppCtid','AppTime');
		$for_num = count($AppidKey);
		$dom->loadXML($xml);
		$tmpTagObj = $dom->getElementsByTagName('key');
		for ($i=0; $i<$for_num; $i++)
		{
			if (!in_array($tmpTagObj->item($i)->nodeValue,$AppidKey)){
				throw new SoapFault('Server', 'SoapHeader未定义!无权访问');
			}
			$appidTagKey[] = $tmpTagObj->item($i)->nodeValue;//获取请求中访问者的KEY
		}
		$tmpTagObj = $dom->getElementsByTagName('value');
		for ($i=0;$i<$for_num;$i++){
			$appidTagValue[] = $tmpTagObj->item($i)->nodeValue;//获取请求中访问者的ID
		}
		//获取soap头部的数组
		$header_arr = array_combine( $appidTagKey,$appidTagValue );
// 		auth::authIP();
		//验证加密
		$header_res =  Auth::header($header_arr['AppCtid'],$header_arr['AppID'],$header_arr['AppTime']);
		if ($header_res != 200){
			throw new SoapFault('Server', '您无权访问');
		}
	}
    	
    
	/**
	 * soap路由根据参数调用相应的类和方法,参数做的映射！所以在传值的时候必须按照严格的类型格式
	 * 
	 * @param  $Class 	string	类名
	 * @param  $Mod		string	方法名
	 * @param  $Arr	 	array()	参数 
	 * @return array() or int
	 * 
	 */
	
	public function Router($Class,$Mod,$Arr) {

		return Auth::SoapRouter($Class,$Mod,$Arr);
	}
	
	static function getSoapClient($AppID,$class,$method,$para,$wsdl =0 ){
		$AppTime = date("U");
		$AppCtid = md5(auth::$key.$AppID.$AppTime);
		if (self::noWsdl){
			        /* no-wsdl模式 */
			        $client = new SoapClient(null,array('location'=>self::SoapUrl,'uri' => 'ServiceSoap.php'));
		}else{
					$client = new SoapClient( self::WSDL_URL.$wsdlUrl ,array( 'trace' => 1 ) );
		}

		//头验证信息
		$headInfo= array(
				'AppID'=>$AppID,
				'AppCtid'=>$AppCtid,
				'AppTime'=>$AppTime,
		);
		$headers = new SoapHeader(self::WSDL_URL.$SoapUrl,"Authorized",array($headInfo),false, SOAP_ACTOR_NEXT);
		$client->__setSoapHeaders(array($headers));
		$arr = array();
		$result = $client->Router($class,$method,$arr);
		return $result;
	} 
}


$wsdlUrl = WSDL_URL.'/soap/ServiceSoap.php';


if (SoapService::$noWsdl){	/* no-wsdl模式 */
	$server = new SoapServer(null, array('location'=>$wsdlUrl,'uri' => 'ServiceSoap.php','soap_version' => SOAP_1_2 ) );}else{
	$server = new SoapServer('WSDL/SoapService.wsdl', array('soap_version' => SOAP_1_2 ) );}
$server->setClass("SoapService");
$server->handle();
