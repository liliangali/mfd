<?php
class XingeChat
{
	//安卓应用信息
	const ANDROID_ACCESS_ID  = '2100119953';  
    const ANDROID_SECRET_KEY = 'b27f6fb53e4f344ea3abb8572e88d5db';	
	
	//ios应用信息--麦富迪企业证书发布
	const IOS_ACCESS_ID_COM  = '2200122410';  
    const IOS_SECRET_KEY_COM = '2625b5ca763d022ad4ed1b4888468190';
	
	//ios应用信息--appstore
	const IOS_ACCESS_ID_STORE  = '2200119954';  
    const IOS_SECRET_KEY_STORE = 'cba1c3b73819c10bedc430260c586690';
	
	/**
	 * 构造函数
	 * @param string $username
	 *  可设置当前用户
	 * @access protected
	 * @return void
	 */
	//function __construct($msg_id) {
	//	$this->toStoreXinApp($msg_id);
	//}	
	
	/**
	 *  我的消息推送到创业者app
	 *
	 * @param  array $arr 消息数组信息
	 *
	 * @return  string
	 */
	public function toStoreXinApp($msg)
	{
		global $json;
		$db  = connection();

		//$sql = "SELECT um.*, m.user_token, m.user_name  FROM ".DB_PREFIX."usermessage um LEFT JOIN ".DB_PREFIX."member m ON um.to_user_id = m.user_id  WHERE msg_id = '{$msg_id}'";
		$sql = "SELECT user_token  FROM ".DB_PREFIX."member   WHERE user_id = '{$msg['to_user_id']}'";
		$result = $db->getRow($sql);
		
		$param = array(
			'location_id' => 0,
			'url_type'    => 'service',//客服
		);
		
		if($msg['con_type'] == 'img') {//图片推送
			$msg['content'] = '[图片]';	
		}
		$account = $result['user_token'];
	    $title = '客服消息';
		
		//发送信鸽
		//include 'F:/www/mfd/trunk/mfd/includes/xinge/xinge.class.php';
		$this->toXingeAndroid($account, $title, $msg['content'], $param);
		$this->toXingeIosCom($account, $title, $msg['content'], $param);
		$this->toXingeIosStore($account, $title, $msg['content'], $param);
	}
	
	/**
	 * app信鸽--Android
	 *
	 * @param  string $token 当前用户token；string $title 标题；string  $content 发送内容
	 *
	 * @return  string
	 */
	public function toXingeAndroid($account, $title, $content, $param)
	{	
		$push = new XingeApp(self::ANDROID_ACCESS_ID, self::ANDROID_SECRET_KEY);
		/*
		//通过用户帐号，获得对应设备token
		$result = $push->QueryTokensOfAccount($account);

		if($result['ret_code'] == 0 && isset($result['result']['tokens'])) {//获取成功，
		    $size = count($result['result']['tokens']);
			$tokens = !empty($result['result']) ? $result['result']['tokens'][$size-1] : '';
		}else{
			return false;
		}*/

		//下发安卓
		$mess = new Message();
		$mess->setType(Message::TYPE_NOTIFICATION);
		$mess->setTitle($title);
		$mess->setContent($content);
		$mess->setExpireTime(86400);
		//$style = new Style(2);
		$style = new Style(2, 1, 1, 1, 0);

		#含义：样式编号0，响铃，震动，不可从通知栏清除，不影响先前通知
		$action = new ClickAction();
		$action->setActionType(ClickAction::TYPE_ACTIVITY);
		$mess->setStyle($style);
		$mess->setAction($action);
		$mess->setCustom($param);
		//$retAndroid = $push->PushSingleDevice($tokens, $mess);
		$retAndroid = $push->PushSingleAccount(0, $account, $mess);
		return true;
	}
	
	/**
	 * app信鸽--IOS--麦富迪企业证书发布
	 *
	 * @param  string $token 当前用户token；string $title 标题；string  $content 发送内容
	 *
	 * @return  string
	 */
	public function toXingeIosCom($account, $title, $content, $param)
	{	
		$pushios = new XingeApp(self::IOS_ACCESS_ID_COM, self::IOS_SECRET_KEY_COM);
		/*
		//通过用户帐号，获得对应设备token
		$result = $pushios->QueryTokensOfAccount($account);
		if($result['ret_code'] == 0) {//获取成功，
			$tokens = !empty($result['result']) ? $result['result']['tokens']['0'] : '';
		} else {
			return false;
		}*/
			
		$messios = new MessageIOS();
		$messios->setExpireTime(86400);
		$messios->setAlert($content);
		$messios->setBadge(1); 
		$messios->setSound("beep.wav");
		$messios->setCustom($param);
		$acceptTime1 = new TimeInterval(0, 0, 23, 59);
		$messios->addAcceptTime($acceptTime1);
		//$retios = $pushios->PushSingleDevice($tokens, $messios, XingeApp::IOSENV_PROD);	
		$retios = $pushios->PushSingleAccount(0, $account, $messios, XingeApp::IOSENV_PROD);	
		return true;
	}
	
	/**
	 * app信鸽--IOS--appstore
	 *
	 * @param  string $token 当前用户token；string $title 标题；string  $content 发送内容
	 *
	 * @return  string
	 */
	public function toXingeIosStore($account, $title, $content, $param)
	{	
		$pushios = new XingeApp(self::IOS_ACCESS_ID_STORE, self::IOS_SECRET_KEY_STORE);
			
		$messios = new MessageIOS();
		$messios->setExpireTime(86400);
		$messios->setAlert($content);
		$messios->setBadge(1); 
		$messios->setSound("beep.wav");
		$messios->setCustom($param);
		$acceptTime1 = new TimeInterval(0, 0, 23, 59);
		$messios->addAcceptTime($acceptTime1);
		//$retios = $pushios->PushSingleDevice($tokens, $messios, XingeApp::IOSENV_PROD);	
		$retios = $pushios->PushSingleAccount(0, $account, $messios, XingeApp::IOSENV_PROD);	
		return true;
	}

}