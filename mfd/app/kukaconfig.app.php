<?php

/**
*麦富迪币的配置
*@author liang.li <1184820705@qq.com>
*@2015年10月16日
*/
class KukaconfigApp extends BackendApp
{
	var $_mod;
    private $_cate ;
    function __construct()
    {
        $this->ApplyApp();
    }

    function ApplyApp()
    {
        parent::__construct();
        $this->_mod =& m('kukaconfig');
        
        $path = array(
            22 => array(
                'sign'=>'C',
                'name'=>'麦富迪E卡',
                'work'=>'用户激活后，可用于购物结算。',//等级不变  积分在用户原来基础上+  ！！！
                'description'=>'用户激活后,可用于购物结算。', //for app
                'message'=>'您已获得创业者麦富迪E卡特权，输入麦富迪E卡，直接享受麦富迪币福利', // for user
            ),
        );
        $this->_cate =  $path;
        $this->type = array(
            '0003' => '西服',
            '0004' => '西裤',
            '0005' => '马甲',
            '0006' => '衬衣',
            '0007' => '大衣',
        );
        $this->assign('type',$this->type);
    }

    function index()
    {
        
      $page = $this->_get_page(10);
      $list = $this->_mod->find(array(
            'conditions' => '1=1',
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        foreach ($list as $key => $value) {
            $type =  explode(',', $value['type']); 
            foreach ($type as $k=>$v){
                $type[$k] = $this->type[$v];
            }
            $list[$key]['type'] = $type;
            
        }
  
        $this->assign('list', $list);
        $page['item_count'] = $this->_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        
        
        $this->display('kukaonline/index.html');
    }
    
    /**
    *添加币
    *@author liang.li <1184820705@qq.com>
    *@2015年10月16日
    */
    function add() 
    {
        if(!IS_POST)
        {
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('info',array('is_show'=>1));
            $this->assign('list', $this->_cate['22']);
           
            $this->display('kukaonline/add.html');
        }
        else 
        {
            $data['kuka_name'] = $_POST['kuka_name'];
            $data['kuka_num'] = $_POST['kuka_num'];
            $data['sale_price'] = $_POST['sale_price'];
            $data['is_show'] = $_POST['is_show'];
            $type = $_POST['type'];
       
            if (!$type)
            {
                $this->show_warning('品类必选');
                return;
            }
            $type = implode(',', $type);
            $data['type'] = $type;
          
            $data['add_time'] = time();
            $data['up_time'] = time();
            $expire_time = strtotime($_REQUEST['expire_time'])+57599;//16*3600-1;
            $data['expire_time'] = $expire_time;
            $data['content'] = $_POST['description'];
            if(!$this->_mod->add($data))
            {
                $this->show_warning('添加失败');
            }
            $this->show_message('添加成功','返回列表',    'index.php?app=kukaconfig');
        }
        
    }
    
    /**
     *添加币
     *@author liang.li <1184820705@qq.com>
     *@2015年10月16日
     */
    function edit()
    {
        $id = $_REQUEST['id'];
        if(!IS_POST)
        {
            $info = $this->_mod->get_info($id);
            if (!$info) 
            {
                $this->show_warning('无此数据');
                return;
            }
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $info['expire_time'] = date("Y-m-d",$info['expire_time']);
            $cof=explode(',', $info['type']);
            $this->assign('info',$info);
            $this->assign('cof',$cof);
            $this->assign('list', $this->_cate['22']);
            $this->display('kukaonline/add.html');
        }
        else
        {

            $data['kuka_name'] = $_POST['kuka_name'];
            $data['kuka_num'] = $_POST['kuka_num'];
            $data['sale_price'] = $_POST['sale_price'];
            $data['is_show'] = $_POST['is_show'];
            $type = $_POST['type'];
            if (!$type)
            {
                $this->show_warning('品类必选');
                return;
            }
            $type = implode(',', $type);
            $data['type'] = $type;
            $data['up_time'] = time();
            $expire_time = strtotime($_REQUEST['expire_time'])+57599;//16*3600-1;
            $data['expire_time'] = $expire_time;
            $data['content'] = $_POST['description'];
            $this->_mod->edit($id,$data);
            $this->show_message('编辑成功','返回列表', 'index.php?app=kukaconfig');
        }
    
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
