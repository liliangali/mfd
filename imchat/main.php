<?php

set_time_limit(0);

function main(){
	
	require 'WebSocket.class.php';
	require 'db.php';
	require 'functions.php';
	require 'xinggechat.php';
	require 'XingeApp.php';
	
	$webSocketServer = new WebSocket();
	$onMessage = function ($clientID, $message, $messageLength, $binary) use ($webSocketServer) {
	
		$ip = long2ip( $webSocketServer->wsClients[$clientID][6] );

		$data = json_decode($message,1);
		
		// check if message length is 0
		if ($messageLength == 0) {
			$webSocketServer->wsClose($clientID);
			return;
		}
		
		$db = connection();
		switch($data['type']) {
		    
		    case "login":
				//防止错误发生
			    if(!isset($data["from_user_id"])) {
					$msg = array(
						'type'  => "login", 
						'error' => array(
							'errorCode' => 100,
							'msg'       => '系统错误，请重新登录！',
						),
					);
					
					$webSocketServer->wsSend($clientID, json_encode($msg));
					break;
				}

				//处理当前用户在线信息
        		$sql = "SELECT COUNT(1) FROM ".DB_PREFIX."online WHERE user_id = '{$data["from_user_id"]}'";
        		$result = $db->getOne($sql);

				$inline_type = 'service';
				
				if($data["to_user"] == 0 ) {//顾客登录
					$inline_type = 'member';
				}
				
				if($result) {//更新用户在线状态
					$sql = "UPDATE ".DB_PREFIX."online SET client = '{$clientID}', inline_type = '{$inline_type}', ip = '{$ip}'  WHERE user_id = '{$data["from_user_id"]}'";
				} else {
					$sql = "INSERT INTO ".DB_PREFIX."online (client, user_id, inline_type, service_id, ip) VALUES
					('{$clientID}','{$data["from_user_id"]}', '{$inline_type}', '0', '{$ip}')";
				}	
        		$db->query($sql);
				
				//返回app客服信息（之前调用接口，现在直接返回，配合app）
				$chatlist = chatSeverList($data["from_user_id"]);
				$msg = array(
					'type'         => "login", 
					'to_user'      => $data["to_user"],
					'to_user_id'   => $data["from_user_id"],
					'con_type'     => 'text',//图片：img，文本：text，语音：video
					'chatlist'     => $chatlist,
					'dateline'     => time(), 
				);
				//发送给当前连接用户
				$webSocketServer->wsSend($clientID, json_encode($msg));
				
				if($data["to_user"]) {//客服登录。通知所有在线客户
					foreach ( $webSocketServer->wsClients as $id => $client  ){
						if ( $id != $clientID ) {
							$webSocketServer->wsSend($id, json_encode($msg));
						}
					}
				} else {//客户登录，只通知客服
					foreach ( $webSocketServer->wsClients as $id => $client  ){
						//获得当前连接信息
						$inline_sql = "SELECT inline_type FROM ".DB_PREFIX."online WHERE client = '{$id}'";
						$inline_type = $db->getOne($inline_sql);
						if ( $id != $clientID && $inline_type == 'service'  ) {
							$webSocketServer->wsSend($id, json_encode($msg));
						}
					}
					
				}

    		    break;
    		    
		    case "say":
		        $time = time();
				//防止错误发生
				if(!isset($data["from_user_id"]) || !isset($data["to_user_id"]) ) {
					$msg = array(
						'type'  => "say", 
						'error' => array(
							'errorCode' => 100,
							'msg'       => '系统错误，请重新登录发送！',
						),
					);
					
					$webSocketServer->wsSend($clientID, json_encode($msg));
					break;
				}
				
				if($data['con_type'] == 'img' && $data['to_user'] == 0 ) {//如果为图片顾客发送给客服
					$content = $data["content"];
				} else {
					$content = str_replace("&nbsp;", "", strip_tags(addslashes($data["content"])));//过滤html数据
				}
				
				//获得发送人信息
				if($data["from_user_id"]) {
					$m_sql = "SELECT nickname, avatar FROM ".DB_PREFIX."member WHERE user_id = '{$data["from_user_id"]}'";
					$m_res = $db->getRow($m_sql);
					
					$data["user_name"] = $m_res['nickname'];
					//$data["face"]    = !empty($m_res['avatar']) ? SITE_URL.'/upload/avatar/'.$m_res['avatar'] : SITE_URL.'/avatar/noavatar_big.gif';
					$data["face"]      = avatar_show_src($data["from_user_id"], 'big');
					//print_r($data["face"]);
				} else {
					$data["user_name"] = '游客';
					$data["face"]      = SITE_URL.'/avatar/noavatar_big.gif';//返回默认头像
				}
				
				//获得接收人信息
				$sql = "SELECT client, service_id FROM ".DB_PREFIX."online WHERE user_id = '{$data["to_user_id"]}'";
				$userclientinfo = $db->getRow($sql);
				$userclient =  $userclientinfo['client'];
				
				$is_read = 0;
				if(!empty($userclient ) && $userclientinfo['service_id'] == $data["from_user_id"] ) {//如果当前连接窗口为当前发送客户，将客户未读消息设为已读，
					$is_read = 1;
				}
				
				//记录聊天信息
		        $sql = "INSERT INTO ".DB_PREFIX."usermessage (face, from_user_id, to_user_id, user_name, content, con_type, dateline,  to_user, is_read) VALUES
        		    ('{$data['face']}', '{$data["from_user_id"]}', '{$data["to_user_id"]}','{$data["user_name"]}', '{$content}', '{$data["con_type"]}', '{$time}','{$data["to_user"]}', '{$is_read}')";
		        $res = $db->query($sql);
				//获得当前加入id
				$sql_in = "select last_insert_id() as id from ".DB_PREFIX."usermessage limit 1";
		        $ins_id = $db->getOne($sql_in);
	
				//组装返回数据
		        if($res) {
    		        $msg = array(
						'msg_id'       => $ins_id, 
    		            'type'         => "say", 
    		            "from_user_id" => $data['from_user_id'],
    		            "to_user_id"   => $data['to_user_id'],
    		            'user_name'    => $data["user_name"],
    		            'dateline'     => $time, 
						'time'         => date("Y-m-d H:i:s", $time), 
    		            'face'         => $data["face"],
    		            'content'      => $content,
    		            'to_user'      => $data['to_user'],
						'con_type'     => $data['con_type'],//图片：img，文本：text，语音：video
    		        );
	
					//发送给当前连接用户
    		        $webSocketServer->wsSend($clientID, json_encode($msg));
					
    		        // && $userclientinfo['service_id'] == $data["from_user_id"] 
    		        if(isset($webSocketServer->wsClients[$userclient]) && $data["to_user"] ==1 && empty($userclientinfo['service_id'])  ){//当用在列表页面，没有选择客服时.客服发送用户
						$webSocketServer->wsSend($userclient, json_encode($msg));
    		        }
					
					if(isset($webSocketServer->wsClients[$userclient]) && $data["to_user"] ==1 && $userclientinfo['service_id'] == $data["from_user_id"]  ){//当用在列表页面，没有选择客服时.客服发送用户
						$webSocketServer->wsSend($userclient, json_encode($msg));
    		        }
					
					if(isset($webSocketServer->wsClients[$userclient]) && $userclient != $clientID && $data["to_user"] == 0 ){//客户发送客服
    		            $webSocketServer->wsSend($userclient, json_encode($msg));
    		        }
					
					if(!isset($webSocketServer->wsClients[$userclient]) && $data["to_user"] == 1 ) {//如果当前用户不在线，在信鸽推送
						$XingeChat = new XingeChat();
						$XingeChat->toStoreXinApp($msg);
					}
					
    		       break;
		        }
		    
			case "checklist":
				//防止错误发生
				if(!isset($data["user_id"])) {
					$msg = array(
						'type'  => "say", 
						'error' => array(
							'errorCode' => 100,
							'msg'       => '系统错误，请重新登录发送！',
						),
					);
					
					$webSocketServer->wsSend($clientID, json_encode($msg));
					break;
				}

				//返回app客服信息（之前调用接口，现在直接返回，配合app）
				$chatlist = chatSeverList($data["user_id"]);
				$msg = array(
					'type'         => "checklist",				
					'to_user'      => 1,
					'chatlist'     => $chatlist,
				);

				$webSocketServer->wsSend($clientID, json_encode($msg));
		        break;
				
		    case "read":
				//防止错误发生
				if(!isset($data["from_user_id"]) || !isset($data["to_user_id"]) || !isset($data["to_user"]) ) {
					$msg = array(
						'type'  => "say", 
						'error' => array(
							'errorCode' => 100,
							'msg'       => '系统错误，请重新登录发送！',
						),
					);
					
					$webSocketServer->wsSend($clientID, json_encode($msg));
					break;
				}
			
				//顾客选择客服会话，更新客服id
				if($data["to_user"] == 0 ) {
					$sql = "UPDATE ".DB_PREFIX."online SET service_id = '{$data["to_user_id"]}'  WHERE user_id = '{$data["from_user_id"]}'";
					$db->query($sql);
				}

		        $sql = "UPDATE ".DB_PREFIX."usermessage SET is_read = 1 WHERE from_user_id = '{$data["to_user_id"]}' AND to_user_id = '{$data["from_user_id"]}' ";
		        $db->query($sql);
		        break;
				
			case "logoutbox"://用户关闭当前客服对话框，但未关闭连接
				//防止错误发生
				if(!isset($data["user_id"])) {
					$msg = array(
						'type'  => "say", 
						'error' => array(
							'errorCode' => 100,
							'msg'       => '系统错误，请重新登录发送！',
						),
					);
					
					$webSocketServer->wsSend($clientID, json_encode($msg));
					break;
				}
				
				//返回app客服信息（之前调用接口，现在直接返回，配合app）
				$chatlist = chatSeverList($data["user_id"]);
				
				$sql = "UPDATE ".DB_PREFIX."online SET service_id = 0 WHERE user_id = '{$data["user_id"]}'";
				$db->query($sql);
				$msg = array(
					'type'         => "checklist",				
					'to_user'      => 1,
					'chatlist'     => $chatlist,
				);
				$webSocketServer->wsSend($clientID, json_encode($msg));
		        break;
		}
		
		
		
		//The speaker is the only person in the room. Don't let them feel lonely.
// 		if ( sizeof($webSocketServer->wsClients) == 1 )
// 			$webSocketServer->wsSend($clientID, "没有别的人在房间里，但我还是会听你的。——你可靠的服务器");
// 		else
// 		//Send the message to everyone but the person who said it
// 		foreach ( $webSocketServer->wsClients as $id => $client )
// 		if ( $id != $clientID )
// 			$webSocketServer->wsSend($id, "游客 $clientID ($ip) 说 \"$message\"");
	};
	$onOpen = function ($clientID) use ($webSocketServer) {
		$ip = long2ip( $webSocketServer->wsClients[$clientID][6] );
	};
	
	$onClose = function ($clientID, $status) use ($webSocketServer) {
	
		$ip = long2ip( $webSocketServer->wsClients[$clientID][6] );
		$db = connection();
		$sql = "SELECT user_id,inline_type FROM ".DB_PREFIX."online WHERE client = '{$clientID}'";
		$onlineinfo = $db->getRow($sql);
		$to_user = 1;//1:客服退出 0:用户退出
		if($onlineinfo['inline_type'] == 'member') {//用户退出
			$to_user = 0;
		}

		$msg = array(
			'type'         => "logout",	
			'user_id'      => $onlineinfo['user_id'],					
			'to_user'      => $to_user,
		);
		
		$sql = "DELETE FROM ".DB_PREFIX."online WHERE user_id = '{$onlineinfo['user_id']}'";
		//$webSocketServer->log( "$ip ($clientID) 已断开。" );
	    $db->query($sql);
		//Send a user left notice to everyone in the room
		foreach ( $webSocketServer->wsClients as $id => $client ) {
			$webSocketServer->wsSend($id, json_encode($msg));
		}
	
	};
	$webSocketServer -> bind('message', $onMessage);
	$webSocketServer -> bind('open', $onOpen);
	$webSocketServer -> bind('close', $onClose);
	$serverStatus = $webSocketServer -> wsStartServer('0.0.0.0', 9900);
	if($serverStatus == false){
		echo $webSocketServer -> error;
	}else{
		echo 'webSocketServer Normal end';
	}
}



main();
