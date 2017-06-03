<?php
namespace Cyteam\Message;
class ZhichiMessage{
	public  $appId = '2b17cdee375a475e963aeed478c37fbf';
	public  $appKey='FSf9YNDX96vPrzXg';
	public  $tokenUrl='http://open.sobot.com/open/platform/getAccessToken.json';
	public  $actualUrl='http://open.sobot.com/open/platform/api.json';
	function __construct(){
	}
	/*获取access_token
	Get||Post：https://open.sobot.com/open/platform/getAccessToken.json
	请求参数说明：
	version	是	String	版本号，v1
	sign	是	String	签名
	appId	是	String	公司id
	createTime	是	Long	创建access_token时间
	expire	否	Integer	token过期时间，单位小时
	(sign生成方式：appId，appKey，createTime以字符串方式拼接后经过MD5加密。expire默认2小时,1<=expire<=24,如果大于24，则token时效为24小时。)
	响应参数说明：
	code	String	响应码（1000:成功;1001:sign校验失败;）
	access_token	String	生成的令牌
	*/
	function getAccessToken(){
		$post_data=array();
		$time=time();
		$appId=$this->appId;
		$appKey=$this->appKey;
		$url=$this->tokenUrl;
		$post_data=array(
				'version'=>'v1',
				'sign'=>md5($appId.$appKey.$time),
				'appId'=>$appId,
				'createTime'=>$time,
			);
		
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        ob_start();  
        $result = curl_exec($ch);
        $data = ob_get_contents() ;  
        ob_end_clean(); 
        $data = str_replace("\&quot;",'"',$data );
        $data = json_decode($data,true);
        curl_close($ch);
        if($data['code']=='1001'){
        	return false;
        }else{
        	return $data['data']['access_token'];
        }
	}
	/*Post：https://open.sobot.com/open/platform/api.json
		获取智齿客服实时数据
		请求示例：
		{
			 "action": "chat_get_oncedata”,
			 "version": "v1",
			 "access_token": "access_token",
			 "data"  : {
				 "pid" : "XXX"
			 }
		 }
		action	是	String	事件名称
		version	是	String	版本
		access_token	是	String	令牌
		pid	   是	String	公司id

		响应示例：
		{
				"code":"1000"，
				"data":{
					"adminSize":XX,
					"onlineUserSize":XX,
					"waitUserSize":XX,
					"robotUserSize":XX,
					"adminList":	[
						 {
							"id": "XXXXX",
							"groupName": [
								"XXX",
								"XXX",
								……
							]	
							"count": XXXX,
							"email": "XXXXX",
							"status": XX,
							"name":"XXX"
						},
						……
					]
				}
			}
		code	String	响应码（1000:成功;1001:失败;1002:access_token校验失败;）
		data	JsonNode	实时数据信息
		adminSize	Integer	在线客服数量
		onlineUserSize	Integer	在线用户数量
		waitUserSize	Integer	排队用户数量
		robotUserSize	Integer	与机器人会话数量
		adminList	list 	在线客服列表
		id	String	客服id
		groupName	list	客服所在技能组列表
		count	Long	客服实时接待用户数量
		email	String	客服邮箱
		status	String 	客服状态1-在线，2-忙碌
		name	String	客服姓名
	*/
		function getActualData($access_token){
			if(!$access_token){
				return json_encode(array(
						'code'=>'1001',
						'msg'=>'sign校验失败',
						'status'=>0,
					));
			}
			$pid=$this->appId;
			$actualUrl=$this->actualUrl;
			$post_data=array(
					'action' => 'chat_get_oncedata',
					'version' => 'v1',
		 			'access_token' => $access_token,
					'data'  =>	array(
						 'pid' => $pid,
					 ),
				);
			
			 $json=json_encode($post_data,true);
			 $data=$this->http_post_data($actualUrl,$json);
			$data=$data[1];
			$data=json_decode($data,true);
	        if($data['code']=='1001'){
	        	return json_encode(array(
						'code'=>'1001',
						'msg'=>'失败',
						'status'=>0
					));
	        }else if($data['code']=='1002'){
	        	return json_encode(array(
						'code'=>'1002',
						'msg'=>'access_token校验失败',
						'status'=>0
					));
	        }else{
	        	$data['status']=1;
	        	return json_encode($data,true);
	        }
		}
		//获取post数据
		 function http_post_data($url, $data_string) {
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	            'Content-Type: application/json; charset=utf-8',
	            'Content-Length: ' . strlen($data_string))
	        );
	        ob_start();
	        curl_exec($ch);
	        $return_content = ob_get_contents();
	        ob_end_clean();

	        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        return array($return_code, $return_content);
	    }
}
