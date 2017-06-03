<?php

/**
 *    后台通知
 *
 *    @author    yhao.bai
 *    @usage    none
 */
class notice extends Object
{
    protected $_message_mod;
    protected $error;
    function __construct()
    {
        parent::__construct();
        $this->_message_mod = &m("sysmessage");
    }
    
    function send($data = array()){
        $data = $this->format($data);
        $res = $this->_message_mod->add($data);
        if(!$res){
            $this->set_error("意外错误:数据入库失败!");
            return false;
        }else{
            return true;
        }
    }
    
    function read($ids){
        if(!empty($ids)){
           $this->_message_mod->edit("admin_id='{$_SESSION["admin_info"]["user_id"]}' AND id ".db_create_in($ids) ,array("readtime" => gmtime(),"is_read" => "1"));
        }
    }
    
    function notice(){
       
    }
    
    function format($data){
        $user = &m("userpriv");
        
        $admins = $user->get_admin_id();
        
        $msgArr = array();
        
        if(empty($data["content"]) || empty($data["node"]) || empty($admins)){
            $this->set_error("数据不完整");
            return false;
        }
        
        foreach($admins as $key => $val){
            $msgArr[] = array(
                "content"   => $data["content"],
                "node"      => $data["node"],
                'dateline'  => gmtime(),
                "is_read"   => 0,
                "admin_id"  => $val["user_id"],
                "readtime"  => 0,
            );
        }
        
        return $msgArr;
    }
    
    function set_error($msg){
        $this->error = $msg;
    }
    
    function get_error(){
        return $this->error;
    }
}
?>
