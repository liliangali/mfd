<?php
/**
*-----------------------------------------------------------
*阿里大于短信发送接口
*-----------------------------------------------------------
*@access public
*@author cyrus <2621270755@qq.com>
*@date 2016年8月1日
*@version 1.0
*@return 
*/
function sendDaYuMessage($params,$phone,$code,$callbackpara=''){
	include "TopSdk.php";
	$c = new TopClient;

	$c->appkey = '23419177';
	$c->secretKey = 'e2f55083b9d948a2e3b7c5d41932e856';
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setExtend("");
	$req->setSmsType("normal");
	$req->setSmsFreeSignName("麦富迪高端定制");
	$req->setSmsParam($params);//"{\"order_sn\":\"sdlfjlksdjf\",\"product\":\"heihei\"}"
	$req->setRecNum($phone);//群发短信需传入多个号码，以英文逗号分隔，一次调用最多传入200个号码
	$req->setSmsTemplateCode($code);
	$resp = $c->execute($req);

	return $resp;
}

