<?php

if (!defined('IN_ECM'))
{
    die('Hacking attempt');
}
class Question extends Object{
	
	var $id;
	var $type;
	var $page;
	var $_question_mod;
	function __construct(){
		$this->_question_mod = &m("question");
	}
	
	function set_param($param){
	    $this->id   =  $param["id"];
	    $this->type =  $param["type"];
	    $this->page =  $param["page"];
	}
	
	function load(){
	    
	    $list = $this->_question_mod->find(array(
	        "conditions" => "custom_id='{$this->id}' AND type='ask' AND status = 1 AND object='{$this->type}'",
	        'order'      => "dateline DESC",
	        'limit'      => $this->page["limit"],
	        'count'      => true,
	    ));
	    
	    $this->page['item_count'] = $this->_question_mod->getCount();
	    $replyids = array();
	    foreach($list as $key => $val){
	        $replyids[] = $val["id"];
	    }
	    
	    $replylist = $this->_question_mod->find(array(
	        "conditions" => "type='answer' AND parent_id ".db_create_in($replyids),
	    ));
	     
	    foreach($replylist as $key =>$val){
	        if(isset($list[$val["parent_id"]])){
	            $list[$val["parent_id"]]['reply'] = $val;
	        }
	    }
	    
	    return array("page" => $this->page, "list" => $list);
	}
	
	function commit(){
	    $content   = trim($_POST['content']);
        $info = $this->get_data();
        if(empty($info) || empty($content)){
            return false;
        }
        
        $visitor     =& env('visitor');
        
	    $data = array(
	        "content"   => $content,
	        'user_id'   => $visitor->get('user_id'),
	        'user_name' => $visitor->get('user_name'),
	        'status'    => 0,
	        'dateline'  => gmtime(),
	        "type"      => "ask",
	        'custom_id' => $this->id,
	        'object'    => $this->type,
	        'object_name' => $info["suit_name"],
	    );
	     
	    $res = $this->_question_mod->add($data);
	    return $res;
	}
	
	function set_data($data){
	    $this->data = $data;
	}
	
	function get_data(){
	    return $this->data;
	}
}
?>