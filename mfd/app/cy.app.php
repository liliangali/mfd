<?php

/* 会员控制器 */
class CyApp extends BackendApp
{
	var $_mod;
    function __construct()
    {
        $this->ApplyApp();
    }

    function ApplyApp()
    {
        parent::__construct();
        $this->_mod =& m('cy');
    }

    function index()
    {
        
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'name',         //可搜索字段title
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
                'assoc' => 'AND',           //关系类型,可以是AND, OR
                'name'  => 'name',         //GET的值的访问键名
                'type'  => 'string',        //GET的值的类型
            ),
            array(
                'field' => 'phone',         //可搜索字段title
                'equal' => '=',          //等价关系,可以是LIKE, =, <, >, <>
                'assoc' => 'AND',           //关系类型,可以是AND, OR
                'name'  => 'phone',         //GET的值的访问键名
                'type'  => 'string',        //GET的值的类型
            ),
        ));
    
        $page = $this->_get_page(30);
        $list = $this->_mod->find(array(
            'conditions' => '1=1' . $conditions,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        
        if ($list) 
        {
            foreach ($list as $key => $value) 
            {
                $list[$key]['add_time'] = date("Y-m-d H:i:s",$value['add_time']);
            }
        }
		        $this->assign('ask',$this->_mod->getAsk());
        $this->assign('list', $list);
        $page['item_count'] = $this->_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        
        
        $this->display('cy/index.html');
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
    
    function info(){
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
    
    /**
     *testdata
     *@author liang.li <1184820705@qq.com>
     *@2015年4月30日
     */
    function testdate()
    {
        echo date('Y-m-d H:i:s',1430362342);
    }
    
}

?>
