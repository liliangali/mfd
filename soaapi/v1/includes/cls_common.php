<?php


/**
 * 这里写点啥呢？？？
 * ============================================================================
 * API auther: liang.li
 * ----------------------------------------------------------------------------
 */
include_once('../config.php');
// include_once PROJECT_PATH.'soap/ServiceSoap.php';
function getSoapClients($class, $method, $para)
{

	//beg
	include_once PROJECT_PATH.'kernel/class/'.$class.'.class.php';//这个类是集中处理返回值的
	$class_obj = new $class();
	$res = call_user_func_array(array($class_obj, $method), $para);
	if(is_array($res))
	{
		global $json;
		$res=$json->encode($res);
	}
	return $res;
	//end





	
// 	echo phpinfo();
    $noWsdl = 1;
	$AppID = 101;
	$key   = 'gamebean';
	try{ 
// 		$wsdlUrl = WSDL_URL."soap/WSDL/SoapService.wsdl?WSDL";
// 		$wsdlUrl = 'http://api.dev.mfd.cn:8080/soap/ServiceSoap.php';
	    $wsdlUrl = WSDL_URL.'soap/ServiceSoap.php';
		$SoapUrl = WSDL_URL."soap/ServiceSoap.php";

		$AppTime = date("U");
		$AppCtid = md5($key.$AppID.$AppTime);
		
// 		if (file_get_contents($wsdlUrl) === false) {
// 			echo "ERROR: file_get_contents <br/>";
// 			exit();
// 		}
// 		$client = new SoapClient ( $wsdlUrl, array (
// 				'trace' => true,
// 				'exceptions' => true,
// 				'cache_wsdl' => WSDL_CACHE_NONE 
// 		) );
        
		 
		if ($noWsdl) 
		{
// 		    $wsdlUrl = WSDL_URL."soap/WSDL/SoapService.wsdl?WSDL";
		    $client = new SoapClient(null,array('location'=>$wsdlUrl,'uri' => 'ServiceSoap.php'));;
		}
		else 
		{
		    $wsdlUrl = WSDL_URL."soap/WSDL/SoapService.wsdl?WSDL";
    		$client = new SoapClient ( $wsdlUrl, array (
    				'trace' => true,
    				'exceptions' => true,
    				'cache_wsdl' => WSDL_CACHE_NONE
    		) );
		}
// 		var_dump($client);
// 		exit();
		
		//头验证信息
		$headInfo= array(
				'AppID'=>$AppID,
				'AppCtid'=>$AppCtid,
				'AppTime'=>$AppTime,
		);
		
		$headers = new SoapHeader($SoapUrl,"Authorized",array($headInfo),false, SOAP_ACTOR_NEXT);
		$client->__setSoapHeaders(array($headers));
// print_exit($client);
// echo $method;exit;

		$result = $client->Router($class,$method,$para);

	/* 	echo "<pre>";
		$temArr = json_decode($result,true);
		find_array_key('imgUrl', &$temArr);
		var_dump($result);
		echo "</pre>";exit; */
// dump($result);
		if(is_array($result))
		{
			global $json;
			$result=$json->encode($result);
		}
		
		return $result;
	}catch (Exception $e){
// 		print_r($e); exit;
		var_dump($e->getMessage())."<br/>";
		echo "<br/>";
		echo $client->__getLastRequest()."<br/>";
		echo "<br/>";
// 		$client = new SoapClient( $wsdlUrl ,array( 'trace' => 1 ) );
// 	    $client = new SoapClient( $wsdlUrl ,array( 'trace' => 1 ) );
		echo $client->__getLastResponse()."<br/>";
		
	}
}


//登录成功后，要返回token？
/**
token的规则：
1、token是用在与会员有关的地方；
2、token的生成规则就用会员的md5（username+password）；
3、会员登录成功后，将生成好的token返回，往后需要的地方他们都带过来；
4、将获取过来的token进行验证，如果对不上，将statuscode返回1000；
5、会员密码更新时，token也要相应的更新。
*/

/**
* 1.获取会员令牌（token） API
*
* @access      public
* @param       string       $username       用户名
*/
function getSubscriberToken($username) 
{
	return check_token($username);
}

//如果会员token过期，重置
function renewSubscriberToken($username) 
{
	return check_token($username);
}

/**
* 检查当前用户的 token，若过期，则重新获取
* @param string $username
*  指定用户名
* @access public
* @return false|string
*/
function check_token($username) 
{
	global $_api_token; 
	if (empty($username)) return false;
	
	//这里进行一下SQL查询，将该会员的password查询出来，MD5(username+password)来生成token 然后return $token


	// 在同一次请求中，重复利用 token
	if (!empty($_api_token)) return $_api_token;

}


?>
