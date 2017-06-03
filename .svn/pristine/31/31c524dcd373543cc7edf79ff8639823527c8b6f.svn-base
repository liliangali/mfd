<?php

set_time_limit(0);

function main(){
	
	require 'WebSocket.class.php';
	require 'db.php';
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
		switch($data['type']){
		    
		    case "login":
        		$sql = "SELECT COUNT(1) FROM ".DB_PREFIX."online WHERE user_id = '{$data["user_id"]}'";
        		$result = $db->getOne($sql);
        		if($result){
        		    $sql = "UPDATE ".DB_PREFIX."online SET client = '{$clientID}', ip = '{$ip}' WHERE user_id = '{$data["user_id"]}'";
        		}else{
        		    $sql = "INSERT INTO ".DB_PREFIX."online (client, user_id, ip) VALUES
        		    ('{$clientID}','{$data["user_id"]}', '{$ip}')";
        		}
        		$db->query($sql);
        		foreach ( $webSocketServer->wsClients as $id => $client )
        		    if ( $id != $clientID )
        		        $webSocketServer->wsSend($id, json_encode(array("type" => "login", 'user_id' => $data["user_id"])));
    		    break;
    		    
		    case "say":
		        $time = time();
		        $content = addslashes($data["content"]);
		        $sql = "INSERT INTO ".DB_PREFIX."usermessage (face, from_user_id, to_user_id, user_name, content, dateline, dmd_id, to_user, is_read) VALUES
        		    ('{$data['face']}', '{$data["from_user_id"]}', '{$data["to_user_id"]}','{$data["user_name"]}', '{$content}', '{$time}', '{$data["dmd_id"]}','{$data["to_user"]}', '0')";
		        $res = $db->query($sql);
		        if($res){
    		        $msg = array(
    		            'type' => "say", 
    		            "from_user_id" => $data['from_user_id'],
    		            "to_user_id" => $data['to_user_id'],
    		            'dmd_id'  => $data["dmd_id"], 
    		            'user_name' => $data["user_name"], 
    		            'time' => date("Y-m-d H:i:s", $time), 
    		            'face' => $data["face"],
    		            'content'  => $data["content"],
    		            'to_user' => $data['to_user'],
    		        );
    		        $webSocketServer->wsSend($clientID, json_encode($msg));
    		        
    		       $sql = "SELECT client FROM ".DB_PREFIX."online WHERE user_id = '{$data["to_user_id"]}'";
    		       $userclient = $db->getOne($sql);
    		       
    		       if(isset($webSocketServer->wsClients[$userclient]) && $userclient != $clientID){
    		           $webSocketServer->wsSend($userclient, json_encode($msg));
    		       }

    		       break;
		        }
		        
		    case "read":
		        $sql = "UPDATE ".DB_PREFIX."usermessage SET is_read = 1 WHERE from_user_id = '{$data["from_user_id"]}' AND dmd_id = '{$data["dmd_id"]}'";
		        $db->query($sql);
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
		$sql = "SELECT user_id FROM ".DB_PREFIX."online WHERE client = '{$clientID}'";
		$uid = $db->getOne($sql);
		
		$sql = "DELETE FROM ".DB_PREFIX."online WHERE user_id = '{$uid}'";
		//$webSocketServer->log( "$ip ($clientID) 已断开。" );
	    $db->query($sql);
		//Send a user left notice to everyone in the room
		foreach ( $webSocketServer->wsClients as $id => $client )

			$webSocketServer->wsSend($id, json_encode(array("type" => "logout", 'user_id' => $uid)));
	
	
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
