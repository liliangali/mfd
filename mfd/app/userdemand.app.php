<?php

class UserdemandApp extends BackendApp
{
    var $_dmd_mod;
    var $_dmd_tmp_mod;
    var $_ctc_mod;
    var $admin;
    var $_member_mod;
    var $_user_msg;
    var $_ol_mod;
    function __construct()
    {
        parent::BackendApp();
        //老数据
        $this->_dmd_mod =& m('userdemand');
        //新数据
        $this->_dmd_tmp_mod =& m('userdemand_tmp');
        //联系人
        $this->_ctc_mod = &m("contacts");
        
        $this->admin = $_SESSION['admin_info'];
        
        $this->_member_mod = &m("member");
        
        $this->_user_msg = &m("user_message");
		$this->admin_mod = & m('userpriv');
        
        $this->_ol_mod = &m("online");
    }
    
    function index()
    {
        $this->assign("admin", $this->admin);
        $this->display('userdemand.index.html');
    }
    
    function demand(){
        $query['user_name'] = isset($_GET["user_name"]) ? trim($_GET['user_name']) : '';
        $query['status']    = isset($_GET['status'])    ? intval($_GET['status']) : "-1";
        $conditions = " 1 ";
        if($query['user_name']){
            $conditions .= " AND user_name LIKE '%{$query['user_name']}%'";
        }
        
        if($query['status'] != "-1"){
            $conditions .= " AND status = '{$query["status"]}'";
        }
        
        $page = $this->_get_page(30);
        $dmdlist = $this->_dmd_mod->find(array(
            'conditions' => $conditions,
            "order"      => "id DESC,status ASC",
            'limit'      => $page['limit'],
            'count'      => true,
        ));
        
        $page['item_count'] = $this->_dmd_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        
        foreach($dmdlist as $key => $val){
            $imgs = explode(",", $val["imgs"]);
            $dmdlist[$key]["imgs"] = $imgs;
        }
        
        $this->assign("dmdlist", $dmdlist);
        
        $this->assign("query", $query);
        $this->assign("statusArr", array(
            "0" => "未处理",
            '1' => "已处理",
            "2" => "已拒绝"
        ));
        $this->display('userdemand.html');
    }
    //加载新的需求
    function loadlist(){
        $dmdlist = $this->_dmd_tmp_mod->find(array(
            "order"      => "add_time DESC",
        ));
        
        foreach($dmdlist as $key => $val){
            $imgs = explode(",", $val["imgs"]);
            $dmdlist[$key]["imgs"] = count($imgs) > 1 ? $imgs[0] : '';
        }
        
        $this->assign("dmdlist", $dmdlist);
        $content = $this->_view->fetch("demand.list.html");
        die($this->json_result($content));
    }

    //响应新的请求
    function active(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $info = $this->_dmd_tmp_mod->get($id);
        if(!empty($info)){
           $info["admin_id"] = $this->admin["user_id"];
           $info["admin_name"] = $this->admin["user_name"];
           if(!$_SESSION['admin_info']){
               die($this->json_error("登录超时", 'login'));
           }
           $info['last_time'] = gmtime();
           unset($info["id"]);
           $oid = $this->_dmd_mod->add($info);
           if($oid){
               $exits = $this->_ctc_mod->get("admin_id='{$info["admin_id"]}' && user_id = '{$info['user_id']}'");
               if(!$exits){
                   $this->_ctc_mod->add(array(
                       'admin_id' => $info["admin_id"],
                       'user_id'  => $info["user_id"],
                       'dateline' => gmtime(),
                   ));
               }
               $this->_dmd_tmp_mod->drop($id);
           }
           die($this->json_result(array("user_id" => $info["user_id"], 'dmd_id' => $oid)));
        }else{
           die($this->json_error("已被其它人响应"));
        }
    }
    
    //加载会话信息
    function loadMsg(){
        $ids = isset($_GET['ids']) ? trim($_GET['ids']) : '';
        if(!$ids){
            $this->json_error("error");
            die();
        }
        
        $idArr = explode("-",$ids);
        if(!is_array($idArr)){
            $this->json_error("error");
            die();
        }
        
        $msglist = $this->_user_msg->find(array(
             "conditions" => "from_user_id ='{$idArr[0]}' AND dmd_id = '{$idArr[1]}' AND is_read = '0' AND to_user = '0'",
             'order'      => "msg_id ASC",
        ));
        
        $msgs = array();
        $mids = array();
        foreach($msglist as $key => $val){
            $mids[] = $val["msg_id"];
            $val['time'] = $val['dateline'];
            $msgs[] = $val;
        }
        
        $this->_user_msg->edit("msg_id ".db_create_in($mids), array("is_read" => 1));
        
        $this->json_result($msgs);
        die();
    }
	
	/**
     * 即使聊天-获得聊天记录
	 *
     * @param array $data  参数集合
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function loadMsgChat()
	{
        $id = isset($_GET['ids']) ? trim($_GET['ids']) : '';//当前顾客id
        if(!$id){
            $this->json_error("error");
            die();
        }
        //查看次用户七天以内消息
		$meg_time = strtotime("-1 week");
        $msglist = $this->_user_msg->find(array(
           // "conditions" => "(from_user_id ='{$id}' || to_user_id ='{$id}') AND dateline >= '{$meg_time}' ",
			"conditions" => "(from_user_id ='{$id}' &&  to_user_id ='{$this->admin["user_id"]}') || (from_user_id ='{$this->admin["user_id"]}' &&  to_user_id ='{$id}') ",
			//'limit'      => 50,
            'order'      => "dateline ASC",
        ));

		
        $msgs = array();
        $mids = array();
        foreach($msglist as $key => $val){
			if($val['to_user']==0) {
				$mids[] = $val["msg_id"];
			}
			$val['dateline'] = local_date("Y-m-d H:i:s", $val['dateline']);
			$msgs[] = $val;
        }

        $this->_user_msg->edit("msg_id ".db_create_in($mids), array("is_read" => 1));
   
        $this->json_result($msgs);
        die();
    }
    
	/**
     * 即使聊天-获得未读消息
	 *
     * @param array $data  参数集合
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	function countNewMsgChat(){
        $exc = isset($_GET['exc']) ? $_GET['exc'] : 0;
        $conditions = " is_read = '0' AND to_user = '0'";
        if($exc){
            $ids = explode("-", $exc);
            if(is_array($ids)){
                $conditions .= " AND from_user_id != 'exc' ";
            }
        }
        
        $list = $this->_user_msg->find(array(
            'conditions' => $conditions,
            'fields'     => "COUNT(1) AS count, from_user_id",
        ));
        
        $result = array("status" => 1, "msg" => "ok", 'data' => array());
          
        foreach($list as $key => $val){
            $result['data'][] = $val;
        }
        
        die(json_encode($result));
    }
	
    function countNewMsg(){
        $exc = isset($_GET['exc']) ? $_GET['exc'] : 0;
        $conditions = " is_read = '0' AND to_user = '0' GROUP BY dmd_id";
        if($exc){
            $ids = explode("-", $exc);
            if(is_array($ids)){
                $conditions .= " AND (from_user_id != '{$ids[0]}' AND dmd_id != '{$ids[1]}') ";
            }
        }
        
        $list = $this->_user_msg->find(array(
            'conditions' => $conditions,
            'fields'     => "COUNT(1) AS count, from_user_id, dmd_id",
        ));
        
        $result = array("status" => 1, "msg" => "ok", 'data' => array());
          
        foreach($list as $key => $val){
            $result['data'][] = $val;
        }
        
        die(json_encode($result));
    }
    
    function board(){
        
        $result = array("status" => 1, "msg" => "ok" , "data" => array());
        //联系人
        $users = $this->_ctc_mod->find(array(
            "conditions" => "admin_id = '{$this->admin["user_id"]}'",
        ));
        
        $_ids = array();
        foreach($users as $key => $val){
            $_ids[] = $val["user_id"];
        }
        
        $members = $this->_member_mod->find(array(
            "conditions"  => " user_id ".db_create_in($_ids),
        ));
        
         $online = $this->_ol_mod->find(array(
             "conditions" => "user_id ".db_create_in($_ids),
         ));
         
         
  
         $ols = array();
         foreach($online as $key => $val){
             $ols[] = $val["user_id"];
         }
        $dmds = $this->_dmd_mod->find(array(
            "conditions" => "user_id ".db_create_in($_ids) . " AND admin_id = '{$this->admin["user_id"]}'",
            "order"      => "id ASC",
        ));
        
        $data = array();
        $i = 0;
        foreach($members as $key => $val){
            $data[$i]["id"]   = $val["user_id"];
            $data[$i]["name"] = $val["nickname"];
            $data[$i]["face"] = $val["avatar"];
            $data[$i]['online'] = in_array($val["user_id"], $ols) ? 1 : 0;
            $num = 0;
            foreach($dmds as $dk => $dv){
                if($val['user_id'] == $dv["user_id"]){
                        $pic = explode(",", $dv["imgs"]);
                        $data[$i]["order"][$num]["id"]  = $dv["id"];
                        $data[$i]["order"][$num]["pic"] = $pic;
                        $num+=1;
                }
            }
            $i+=1;
        }
        //add by sin bgn
        $result['kf'] = $_SESSION['admin_info'];
        //add by sin end
        
        $result['data'] = $data;
        
        die(json_encode($result));
    }
	
	/**
     * 即使聊天-获得好友列表(当前客服联系过的客户)
	 *
     * @param array $data  参数集合
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function boardFrind()
	{
        $result = array("status" => 1, "msg" => "ok" , "data" => array());
		//获得聊过天的用户
		$users = $this->_user_msg->find(array(
            "conditions" => "to_user_id ='{$this->admin["user_id"]}' ",
			//'limit'      => 50,
            'order'      => "msg_id ASC",
        ));
        
        $_ids = array();
        foreach($users as $key => $val){
			//判断是否是管理员
			if(!$this->admin_mod->check_admin($val["from_user_id"])) {
				continue;
			}
            $_ids[] = $val["from_user_id"];
        }
        //获得用户信息
        $members = $this->_member_mod->find(array(
            "conditions"  => " user_id ".db_create_in($_ids),
        ));
        
		//获得未读信息条数
		$msgCount = $this->_user_msg->find(array(
            "conditions" => "to_user_id ='{$this->admin["user_id"]}' AND is_read = '0'  AND from_user_id ".db_create_in($_ids),
			'fields'     => 'from_user_id',
            'order'      => "msg_id ASC",
        ));
		
		//获得用户在线状态
        $online = $this->_ol_mod->find(array(
            "conditions" => "user_id ".db_create_in($_ids),
        ));

        $ols = array();
        foreach($online as $key => $val){
            $ols[] = $val["user_id"];
        }
		
		//处理获得未读条数
		$msgArr = array();
		if($msgCount) {
			foreach($msgCount as $key => $val){
				$count_user = $members[$val['from_user_id']];
				if($val['from_user_id'] == $count_user['user_id']) {
					$members[$val['from_user_id']]['count'] = !isset($count_user['count']) ? $count_user['count'] = 1 : $count_user['count'] = $count_user['count'] + 1;
				}
			}
		}

        $data = array();
        $i = 0;
		//引入头像获取类
		require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
		$objAvatar = new Avatar();
		
        foreach($members as $key => $val){
			$avatar = $objAvatar->avatar_show($val['user_id'], 'big');
			
            $data[$i]["id"]       = $val["user_id"];
            $data[$i]["name"]     = $val["nickname"];
            $data[$i]["face"]     = $avatar;
			$data[$i]["msgcount"] = !empty($val["count"]) ? $val["count"] : 0;//未读消息条数
            $data[$i]['online']   = in_array($val["user_id"], $ols) ? 1 : 0;
            $num = 0;
            $i+=1;
        }
        //add by sin bgn
        $result['kf'] = $_SESSION['admin_info'];
        //add by sin end
        $result['data'] = $data;
        
        die(json_encode($result));
	}
	
	/**
     * 即使聊天-上传图片
	 *
     * @param array $data  参数集合
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	function chatUpload() {
        
        $dir = ROOT_PATH.'/upload/chat/';

        $fileName = $this->admin["user_id"]."-".time() . ".jpg";

        $rs = @move_uploaded_file($_FILES["up_file_layim"]["tmp_name"],$dir.$fileName);

        $src= SITE_URL . "/upload/chat/".$fileName;
        $arr = array('src'=>$src,'file'=>$fileName);
        
        echo json_encode($arr);
        exit;
    }

    function upload(){
        
        $dir = ROOT_PATH.'/upload/chat/';

        $fileName = $this->admin["user_id"]."-".time() . ".jpg";

        $rs = @move_uploaded_file($_FILES["up_file_layim"]["tmp_name"],$dir.$fileName);

        //$src= SITE_URL . "/upload/chat/".$fileName;
		$src= "http://192.168.1.13/upload/chat/".$fileName;//用户本地测试
        $arr = array('src'=>$src,'file'=>$fileName);
        
        echo json_encode($arr);
        exit;
    }



}

?>