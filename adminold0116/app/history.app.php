<?php

class historyApp extends BackendApp
{
	var $_msg_mod;
    function __construct()
    {
        parent::__construct();
        $this->_msg_mod =& m('user_message');
    }
    
    function index()
    {
        $start = isset($_GET['start']) ? strtotime(trim($_GET['start'])) : 0;
        $end = isset($_GET['end']) ? strtotime(trim($_GET['end']))+24*3600 : 0;
    	$ids = isset($_GET['ids']) ? trim($_GET['ids']) : 0;
        $idArr = explode("-", $ids);
        if(!is_array($idArr)){
            $this->show_warning("非法操作");
            return ;
        }
        $conditions = "(from_user_id = '{$_SESSION['admin_info']["user_id"]}' OR to_user_id = '{$_SESSION['admin_info']["user_id"]}')";
        if($start){
            $conditions .= " AND dateline >= '{$start}'";
        }
        
        if($end){
            $conditions .= " AND dateline <= '{$end}'";
        }
        $page = $this->_get_page(30);
        $msglist = $this->_msg_mod->find(array(
            'fields' => '*',
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'order' => "msg_id DESC",
            'count' => true,
        ));
        
        foreach($msglist as $key => $val){
            $msglist[$key]['time'] = local_date("Y-m-d H:i:s", $val["dateline"]);
        }
        
        $this->assign('msglist', $msglist);
        $page['item_count'] = $this->_msg_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign("query", $_GET);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('history.index.html');
    }
}

?>
