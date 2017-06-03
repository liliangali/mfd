<?php

class QuestionApp extends BackendApp
{
	var $_question_mod;
    function __construct()
    {
        parent::__construct();
        $this->_question_mod =& m('question');
    }

    function index()
    {
        
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'content',         //可搜索字段title
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
                'assoc' => 'AND',           //关系类型,可以是AND, OR
                'name'  => 'content',         //GET的值的访问键名
                'type'  => 'string',        //GET的值的类型
            ),

        ));

        $page = $this->_get_page(30);
        $list = $this->_question_mod->find(array(
            'conditions' => "type='ask' {$conditions}",
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        $this->assign('list', $list);
        $page['item_count'] = $this->_question_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign("name", $_GET['content']);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        
        $this->display('question.index.html');
    }
    
    function view()
    {
        if(!IS_POST){
        	$id = intval($_GET['id']);
        	
        	if(!$id){
        		$this->show_message("非法操作!");
        		return;
        	}
        	
    		$question = $this->_question_mod->find(array(
                'conditions' => "id='{$id}' || parent_id = '{$id}'",
                'order'      => "id DESC",
            ));
    		
    		$data = array();
    		
    		foreach($question as $key => $val){
    		    if($val['type'] == "ask"){
    		        $data["ask"] = $val;
    		    }else{
    		        $data['reply'] = $val;
    		    }
    		}
    		
    		//print_R($data);
    		$this->assign('data', $data);
    	
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js'));
        
        	$this->display('question.form.html');
        }else{
            $id        = intval($_POST['id']);
            $content   = trim($_POST["content"]);
            $reply_id = intval($_POST['reply_id']);
            $status  = intval($_POST['status']);  
            $data = array(
                'parent_id'   => $id,
                'content'     => $content,
                "dateline"    => gmtime(),
                'admin_id'    => $_SESSION["admin_info"]['user_id'],
                'admin_name'  => $_SESSION['admin_info']["user_name"],
                'type'        => "answer"
            );
            if($reply_id){
                $this->_question_mod->edit($reply_id,$data);
            }else{
                $this->_question_mod->add($data);
                $this->_question_mod->edit($id, array('status' => $status));
            }

            $this->show_message("回复成功!");
        }
    }
    function onshow(){
        $id        = intval($_GET['id']);
        $status    = intval($_GET['status']);
        $this->_question_mod->edit($id, array('status' => $status));
        $this->show_message("操作成功");
    }
}

?>
