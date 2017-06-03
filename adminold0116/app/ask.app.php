<?php

/* 会员控制器 */
class AskApp extends BackendApp
{
	var $_mod;
    function __construct()
    {
        $this->ApplyApp();
    }

    function ApplyApp()
    {
        parent::__construct();
        $this->_mod =& m('ask');
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
                'field' => 'ident',         //可搜索字段title
                'equal' => '=',          //等价关系,可以是LIKE, =, <, >, <>
                'assoc' => 'AND',           //关系类型,可以是AND, OR
                'name'  => 'ident',         //GET的值的访问键名
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
        
        
        $this->display('ask/index.html');
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
     * excel
     * @author sauren  <6582701@qq.com>
     * @2015-5-14
     * 本方法用于将需求列表内所有的信息生成excel表
     * 
     */
        function export (){
        
            $ask = $this->_mod->getAsk();
        	$orders =$this->_mod->findAll(array(
        			"conditions" => "",
        			'fields' => 'id,ident,name,phone,weixin,sex,add_time,address,fabric,style,color',
        	));
        	foreach ($orders as &$row){
        		switch ($row['sex']){
        			case 1:
        				$row['sex'] = '男';
        				break;
        			case 2:
        				$row['sex'] = '女';
        				break;
        			default:
        				$row['sex'] = '未知';
        				break;
        		}
        		
        		//=====  面料  =====
        	    if ($row['fabric']) 
        	    {
        	        $arr = explode(",", $row['fabric']);
        	        $row['fabric'] = '';
        	        foreach ($arr as $key => $value) 
        	        {
        	            $row['fabric'] .= $ask['fabric'][$value].',';
        	        }
        	        $row['fabric'] = trim($row['fabric'],',');
        	    }
        	    
        	    //=====  着装风格  =====
        	    if ($row['style'])
        	    {
        	        $arr = explode(",", $row['style']);
        	        $row['style'] = '';
        	        foreach ($arr as $key => $value)
        	        {
        	            $row['style'] .= $ask['style'][$value].',';
        	        }
        	        $row['style'] = trim($row['style'],',');
        	    }
        	    
        	    //=====  面料  =====
        	    if ($row['color'])
        	    {
        	        $arr = explode(",", $row['color']);
        	        $row['color'] = '';
        	        foreach ($arr as $key => $value)
        	        {
        	            $row['color'] .= $ask['color'][$value].',';
        	        }
        	        $row['color'] = trim($row['color'],',');
        	    }
        		
        	}
        	$fields_name = array('ID','编号','姓名','手机号','微信号','性别','时间','地址','面料','着装风格','服装花型');
        	array_unshift($orders,$fields_name);
        	$this->export_to_csv($orders, 'ask', 'gbk');
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
