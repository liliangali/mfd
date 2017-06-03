<?php
include_once('../config.php');

//User:类名  add:方法名  $arr参数
$name = 'lirp';
$pass = md5('123456');
$arr = array(
		'name'=>$name,
		'pwd'=>$pass
);

$rs = getSoapClients('Webserver','addtest',$arr);
echo $rs;

function getSoapClient2($class,$method,$para)
{
	$AppID = 101;
	$key   = 'gamebean';
	try{
		$wsdlUrl = WSDL_URL."soaapi/v1/soap/WSDL/SoapService.wsdl";
		$SoapUrl = WSDL_URL."soaapi/v1/soap/ServiceSoap.php";
	
		$AppTime = date("U");
		$AppCtid = md5($key.$AppID.$AppTime);
		$client = new SoapClient( $wsdlUrl ,array( 'trace' => 1 ) );
		//头验证信息
		$headInfo= array(
				'AppID'=>$AppID,
				'AppCtid'=>$AppCtid,
				'AppTime'=>$AppTime,
		);
		$headers = new SoapHeader($SoapUrl,"Authorized",array($headInfo),false, SOAP_ACTOR_NEXT);
		$client->__setSoapHeaders(array($headers));
		$result = $client->Router($class,$method,$para);
		return $result;
	}catch (Exception $e){
		@var_dump($e->getMessage());
	}
}


