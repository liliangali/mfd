<?php
include 'XingeApp.php';
class XingeMeg
{

	//==================量体项目=====================
	//安卓应用信息--量体师
	const ANDROID_MASTER_ACCESS_ID  = '2100123953';  
	const ANDROID_MASTER_SECRET_KEY = '8129862c9a8fac3dee7e4f7ba901e93f';
	
	//ios应用信息--量体师--企业证书发布
	const IOS_MASTER_ACCESS_ID  = '2200123954';  
	const IOS_MASTER_SECRET_KEY = '6f7df5df61a13cd65950ef295de4e1e2';
	
	//ios应用信息--量体师--appstore
	const IOS_MASTER_ACCESS_ID_STORE  = '2200125500';  
	const IOS_MASTER_SECRET_KEY_STORE = '25e7ab4c12057c73c8b19b48964879f5';
	
	
	//==================mfd项目=====================
	//安卓应用信息-mfd
	const ANDROID_mfd_ACCESS_ID  = '2100248855';
    const ANDROID_mfd_SECRET_KEY = 'b1bfab5fb96ad220c7a1127f5ac33dce';
	
	//ios应用信息--mfd--麦富迪企业证书发布
	const IOS_mfd_ACCESS_ID  = '2200248856';
    const IOS_mfd_SECRET_KEY = 'ed25e0d9bd0b9d074089306e1c2c0976';
	
	//ios应用信息--mfd--appstore
	const IOS_mfd_ACCESS_ID_STORE  = '2200248856';
    const IOS_mfd_SECRET_KEY_STORE = 'ed25e0d9bd0b9d074089306e1c2c0976';
	
	
	/**
	 *  消息推送到量体师app---量体师
	 *
	 * @param  String $account 用户账户（这里是用户token）；String  $title 推送标题；String  $mess 推送内容；
	 *         array $param 消息数组参数（会携带到app）； array('url_type'=>'跳转类型', 'location_id'=>'跳转类型中对应的id：例如订单id');
	 *
	 * @return  string
	 */
	public function toMasterXinApp($account, $title, $mess, $param)
	{
		//这里可以就行发前部分处理==
		//判断线上线下环境，配置不同环境token
		if (isset($_SERVER['HTTP_HOST'])){
		    if($_SERVER['HTTP_HOST'] == 'm.test.mfd.cn' ||  $_SERVER['HTTP_HOST'] == 'api.test.mfd.cn') {
		        $account = $account.'test';
		    } elseif($_SERVER['HTTP_HOST'] == 'm.dev.mfd.cn:8080' ||  $_SERVER['HTTP_HOST'] == 'api.dev.mfd.cn:8080') {
		        $account = $account.'dev';
		    }
		}
		
	
		//发送信鸽
		XingeApp::toXingeAndroid(self::ANDROID_MASTER_ACCESS_ID, self::ANDROID_MASTER_SECRET_KEY, $title, $mess, $account, $param);
		XingeApp::toXingeIos(self::IOS_MASTER_ACCESS_ID, self::IOS_MASTER_SECRET_KEY, $title, $mess, $account, $param);
		XingeApp::toXingeIos(self::IOS_MASTER_ACCESS_ID_STORE, self::IOS_MASTER_SECRET_KEY_STORE, $title, $mess, $account, $param);
	}
	
	/**
	 *  消息推送到mfd app---mfd
	 *
	 * @param  array $arr 消息数组参数（会携带到app）；String $account 用户账户（这里是用户token）；String  $title 推送标题；String  $mess 推送内容；
	 *         String $send_type 发送终端类型 ios  android all；默认all发送所有终端
	 *         array $param 消息数组参数（会携带到app）； array('url_type'=>'跳转类型', 'location_id'=>'跳转类型中对应的id：例如订单id');
	 * @return  string
	 */
	public function tomfdXinApp($account, $title, $mess, $param, $send_type = "all")
	{
		//这里可以就行发前部分处理===
		//判断线上线下环境，配置不同环境token
	    if (isset($_SERVER['HTTP_HOST'])){
	        if($_SERVER['HTTP_HOST'] == 'm.test.mfd.cn' ||  $_SERVER['HTTP_HOST'] == 'api.test.mfd.cn') {//预发布环境token
	            $account = $account.'test';
	        } elseif($_SERVER['HTTP_HOST'] == 'm.dev.mfd.cn:8080' ||  $_SERVER['HTTP_HOST'] == 'api.dev.mfd.cn:8080') {//测试环境token
	            $account = $account.'dev';
	        }
	    }
	    
		if($send_type == "ios") {
			$ios_result = XingeApp::toXingeIos(self::IOS_mfd_ACCESS_ID, self::IOS_mfd_SECRET_KEY, $title, $mess, $account, $param);
			$ios_store_result = XingeApp::toXingeIos(self::IOS_mfd_ACCESS_ID_STORE, self::IOS_mfd_SECRET_KEY_STORE, $title, $mess, $account, $param);

			if(!$ios_result['ret_code'] || !$ios_store_result['ret_code']) {
				return true;
			}
			return false;
			
		} elseif($send_type == "android") {
			$android_result =  XingeApp::toXingeAndroid(self::ANDROID_mfd_ACCESS_ID, self::ANDROID_mfd_SECRET_KEY, $title, $mess, $account, $param);
			if(!$android_result['ret_code']) {
				return true;
			}
			return false;
			
		} else {
			//发送信鸽(所有设备发送，有一个成功，则视为发送成功)
            echo '<pre>';print_r(44444);exit;
            
			$android_result = XingeApp::toXingeAndroid(self::ANDROID_mfd_ACCESS_ID, self::ANDROID_mfd_SECRET_KEY, $title, $mess, $account, $param);
			$ios_result = XingeApp::toXingeIos(self::IOS_mfd_ACCESS_ID, self::IOS_mfd_SECRET_KEY, $title, $mess, $account, $param);
			$ios_store_result = XingeApp::toXingeIos(self::IOS_mfd_ACCESS_ID_STORE, self::IOS_mfd_SECRET_KEY_STORE, $title, $mess, $account, $param);

			if(!$android_result['ret_code'] || !$ios_result['ret_code'] || !$ios_store_result['ret_code']) {
				return true;
			}
			
			return false;
		}
		
	}
	
	/**
	 *  创建大批量发送任务
	 *
	 * @param  array $arr 消息数组信息
	 *
	 * @return  string
	 */
	public function doCreateMultipush($account, $title, $mess, $param, $send_type = "all")
	{

		if($send_type == "ios") {
			$ios_result       = XingeApp::CreateMultipushIos(self::IOS_mfd_ACCESS_ID, self::IOS_mfd_SECRET_KEY, $title, $mess, $account, $param);
			$ios_store_result = XingeApp::CreateMultipushIos(self::IOS_mfd_ACCESS_ID_STORE, self::IOS_mfd_SECRET_KEY_STORE, $title, $mess, $account, $param);
			
			if(!$ios_result['ret_code'] || !$ios_store_result['ret_code']) {
				return true;
			}
			return false;
			
		} elseif($send_type == "android") {
			$android_result =  XingeApp::CreateMultipushAndroid(self::ANDROID_mfd_ACCESS_ID, self::ANDROID_mfd_SECRET_KEY, $title, $mess, $account, $param);
			if(!$android_result['ret_code']) {
				return true;
			}
			return false;
			
		} else {
			//发送信鸽(所有设备发送，有一个成功，则视为发送成功)
			$android_result = XingeApp::CreateMultipushAndroid(self::ANDROID_mfd_ACCESS_ID, self::ANDROID_mfd_SECRET_KEY, $title, $mess, $account, $param);
			$ios_result = XingeApp::CreateMultipushIos(self::IOS_mfd_ACCESS_ID, self::IOS_mfd_SECRET_KEY, $title, $mess, $account, $param);
			$ios_store_result = XingeApp::CreateMultipushIos(self::IOS_mfd_ACCESS_ID_STORE, self::IOS_mfd_SECRET_KEY_STORE, $title, $mess, $account, $param);

			if(!$android_result['ret_code'] || !$ios_result['ret_code'] || !$ios_store_result['ret_code']) {
				return true;
			}
			
			return false;
		}
		
	}
	
	
	
	/**
	 *  消息推送到mfd 系统消息
	 *
	 * @param  array $arr 消息数组信息
	 *
	 * @return  string
	 */
	public function toStoreXinApp($addtime, $type)
	{
		global $json;
        $conditions = "add_time = '$addtime' AND type = $type";
        $message_mod = m("usermessage");
        $list = $message_mod -> find(array(
            'conditions' => $conditions,
            'order'      => "add_time DESC",
            'count'	     =>	"true",
			'index_key'	 => '',
        ));

		//循环操作，重新组装数据
		$to_user_id = array();
		if($list) {
			foreach($list as $key=>$val ) {
				$content      = $val['content'];
				$to_user_id[] = $val['to_user_id'];
			}
			$to_user_id_str = implode(',', array_unique($to_user_id));
		} else {
			return ;
		}
		
		//发送内容
		$title = '服装定制，找阿里裁缝';
		$mess  = preg_replace("/<(\/?a.*?)>/si", "", $content); //过滤head标签
		
		//需求相关
		$msg_parm_dam = array(4, 7);
		$param = array('location_id'=>'', 'url_type'=>'');
		if(in_array($val['type'], $msg_parm_dam)) {//截取对应的id
			preg_match('/\d+/', $val['location_url'], $location_id);
			$param['location_id'] = $location_id['0'];
			$param['url_type']    = 'demand';//跳转到对应需求详情
		}
		if($val['type'] == 1) {
			$param['url_type']    = 'order';//跳转到我的订单（只顾客）
		}
		if($val['type'] == 2) {
			$param['url_type']    = 'single';//晒单详情
		}
		if($val['type'] == 3 || $val['type'] == 5) {
			$param['url_type']    = 'tailor';//跳转到裁缝主页
		}
		//获得所有需要发送token
		$member = m("member");
		$members = $member->find(array(
            'conditions' => db_create_in($to_user_id_str, 'user_id'),
			'fields'     => 'user_token',
			'index_key'	 => '',
        ));

		$account = array();
		if($members) {
			foreach($members as $val) {
				$account[] = $val['user_token'];
			}
		} else {
			return ;
		}
		
		//$this->toXingeAndroid($tokens, $title, $mess, $param);
		//$this->toXingeIos($tokens, $title, $mess, $param);
		XingeApp::toXingeAndroid(self::ANDROID_SYS_ACCESS_ID, self::ANDROID_SYS_SECRET_KEY, $title, $mess, $account, $param);
		XingeApp::toXingeIos(self::IOS_SYS_ACCESS_ID, self::IOS_SYS_SECRET_KEY, $title, $mess, $account, $param);
	}
	

}