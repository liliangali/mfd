<?php

/**
 *    系统通知
 *    @author    yhao.bai
 *    @usage    none
 */
class SysmessageApp extends BackendApp
{
    var $_message_mod;
    function __construct()
    {
        parent::BackendApp();
        $this->_message_mod = &m("sysmessage");
    }

    function index()
    {
        
        $page   =   $this->_get_page(10);
        $message=$this->_message_mod->find(array(
            'conditions'  => "admin_id = '{$_SESSION['admin_info']["user_id"]}'",
            'limit'   => $page['limit'],
            'count'   => true,
            "order"   => "is_read DESC, dateline DESC",
        ));
        
        $ids = array();
        
        foreach((array)$message as $key => $val){
            if($val["is_read"] == 0){
                $ids[] = $val["id"];
            }
        }
        
        if($ids){
            import("notice.lib");
            $notice = new notice();
            $notice->read($ids);
        }
        
        $page['item_count']=$this->_message_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('message', $message);
        $this->display('sysmessage.index.html');
    }
    
    function notice(){
        import("notice.lib");
        $msg = new Notice();
        $count = $msg->notice();
        die($this->json_result($count));
    }
}

?>