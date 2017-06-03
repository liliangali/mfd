<?php

/* 会员控制器 */
class ApplyApp extends BackendApp
{
	var $_apply_mod;
    function __construct()
    {
        $this->ApplyApp();
    }

    function ApplyApp()
    {
        parent::__construct();
        $this->_apply_mod =& m('apply');
    }

    function index()
    {
    	$type = isset($_GET['type']) ? intval($_GET['type']) : 0;
    	$username = isset($_GET['username']) ? trim($_GET['username']) : '';
    	
    	$conditions = '';
    	if($type > 0){
    		$conditions .= " AND type = '{$type}'";
    	}
    	
    	if($username){
    		$conditions .= " AND member.user_name LIKE '%".$username."%'";
    	}
        $page = $this->_get_page();
        $apply = $this->_apply_mod->find(array(
            'join' => 'belongs_to_user',
            'fields' => 'this.*,member.user_name',
            'conditions' => '1=1' . $conditions,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
		
        $this->assign('apply_list', $apply);
        $page['item_count'] = $this->_apply_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->assign('username', $username);
        
        $this->assign('type', $type);
        
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('apply.index.html');
    }
    
    function view()
    {
    	$id = intval($_GET['id']);
    	if($id <=0){
    		show_message("非法操作!");
    		return;
    	}
    
		$apply = $this->_apply_mod->find(array(
            'join' => 'belongs_to_user',
            'fields' => 'this.*,member.user_name',
            'conditions' => "id='{$id}'",
        ));
		
		
		
		$row =  $apply ? current($apply) : array();
		 
		$this->assign('apply', $row);
	
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    
    	$this->display('apply.view.html');
    }
    
    function verify(){
    	$id = intval($_GET["id"]);
    	
    	$row = $this->_apply_mod->get_info($id);
    	
    	if($row['stauts'] != APPLY_UNPROCESS){
    		$this->show_message("非法操作！");
    		return ;
    	}
    	
		$data = array();
    	if(isset($_POST['finished'])){
    		$data['admin_remark'] = $_POST['admin_remark'];
    		$data['status'] = APPLY_FINISHED;
    		if($row['type'] == DEPOSIT){
    			$data['is_paid'] = 1;
    			$data['paid_time'] = gmtime();
    		}
    	}
    	
    	if(isset($_POST["invalid"])){
    		if(empty($_POST['admin_remark'])){
    			$this->show_message("操作备注不能为空！");
    			return ;
    		}
    		$data['admin_remark'] = $_POST['admin_remark'];
    		$data['status'] = APPLY_INVALID;
    	}
    	
    	if(empty($data)){
    		$this->show_message("非法操作！");
    		return ;
    	}
    	
    	
    	
    	$res = $this->_apply_mod->edit($id, $data);
    	
    	if($res){
    		if($data['is_paid'] == 1){
		    	$log = array(
		    			'dateline' => gmtime(),
		    			'remark'   => "用户充值",
		    			'money'	   => $row['money'],
		    			'frozen'   => 0,
		    			'user_id'  => $row["user_id"],
		    			'type'     => CHANGE_TYPE2,
		    	);
		    	 
		    	$field = "money=money+{$row['money']}";
		    	 
		    	ac($log, $field);
    		}
    		$msg = "操作成功";
    	}else{
    		$msg = "操作失败";
    	}
    	
    	$this->show_message($msg,
    			'返回审核列表',    'index.php?app=apply'
    	);
    }
    
}

?>
