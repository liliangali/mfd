<?php
namespace Cyteam\Message;
class SendMessage{
	function __construct(){
	}
	function sendCode($params,$phone,$code,$callback=''){
		$path=dirname(dirname(dirname(dirname(__FILE__))));
		include $path."/includes/dayu_message_api/dayu.php";
// 		echo '<pre>';
// 		print_r(include "/includes/dayu_message_api/dayu.php");
// 		die();
		$resp = sendDaYuMessage($params,$phone,$code,$callback);
/* 		$c->appkey = '23419177';
		$c->secretKey = 'e2f55083b9d948a2e3b7c5d41932e856';
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setExtend("123456");
		$req->setSmsType("normal");
		$req->setSmsFreeSignName("注册验证");
		$req->setSmsParam($params);//"{\"order_sn\":\"sdlfjlksdjf\",\"product\":\"heihei\"}"
		$req->setRecNum($phone);
		$req->setSmsTemplateCode("SMS_12826621");
		$resp = $c->execute($req); */
		return $resp;
	}
}
