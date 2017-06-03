<?php
/**
 *个人认证控制器
 * @author liang.li <1184820705@qq.com>
 * @version 1.0
 * @copyright Copyright 2014 caifeng.com
 * @package authperson.app.php
*/
class AuthcyzApp extends BackendApp
{
	var $_auth_mod;
	var $_member_mod;
	var $_auth_web_url;
	var $_status;
	var $_is_long;
	var $_job;
	var $_sta;
	var $_region_mod;
	var $_invite_mod;
	var $_gen_mod;
	var $_ask2_mod;
	var $_order_first;
	var $_order_mod;
	var $_message_mod;
	var $_article_mod;
	var $_authlog_mod;
	var $_memberinvite_mod;
	var $_generalizemem_mod;
	
	
	  
	function __construct()
	{
		define("PERSON", "person/");
		$this->_auth_web_url = SITE_URL."/upload/auth/";
		$this->_auth_mod = & m('auth');
		$this->_member_mod = & m('member');
		$this->_region_mod = & m('region');
		$this->_invite_mod = & m('memberinvite');
		$this->_ask2_mod   = & m('askv2');
		$this->_gen_mod    = & m('generalize_member');
		$this->_order_first = & m('orderfirstlog');
		$this->_order_mod	= & m('order');
		$this->_message_mod = & m('usermessage');
		$this->_article_mod = & m('article');
		$this->_authlog_mod = & m('auth_log');
	    $this->_memberinvite_mod =& m('memberinvite');
	    $this->_generalizemem_mod =& m('generalize_member');
		$this->_status = array(1=>'审核成功',2=>'审核失败');
		$this->assign('status',$this->_status);
		$this->assign('sex',array('1'=>'男','2'=>'女'));
		$this->_sta = array(0=>'未审核',1=>'认证成功',2=>'认证失败');
		$job = include_once ROOT_PATH.'/includes/arrayfiles/job.arrayfile.php';
		$this->assign('job',$job);
		$this->_job = $job;
		
		$this->PersonApp();
	}
	function PersonApp()
	{
		parent::BackendApp();
	}
	
	/**
	* 认证列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-28
	*/
	function index() 
	{
		//$_GET['status'] = isset($_GET['status']) ?  $_GET['status'] : 0;
		$conditions = $this->_get_query_conditions(array(
			
			array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'like',
            ),		
            array(
                'field' => 'last_update_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'last_update_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
            ),
            array(
                'field' => 'auth.status',
                'name'  => 'status',
                'equal' => '=',
                'type'  => 'numeric',
            )
				
		));
		
		if($_REQUEST['user']=='ph')
		{
			  $conditions .= " AND member_lv_id = 1 ";
		}
		
		if($_REQUEST['user']=='cyz')
		{
			  $conditions .= " AND member_lv_id > 1 ";
		}
		
		if (isset($_GET['sort']) && isset($_GET['order'])) {
			$sort = strtolower(trim($_GET['sort']));
			$order = strtolower(trim($_GET['order']));
			if (!in_array($order, array('asc', 'desc'))) {
				$sort = 'id';
				$order = 'desc';
			}
		} else {
			$sort = 'id';
			$order = 'desc';
		}
		$page = $this->_get_page(30);
		// ,CONCAT('$this->_auth_web_url',card_face_img) as card_face_img,CONCAT('$this->_auth_web_url',card_back_img) as card_back_img
		$auth_list = $this->_member_mod->find(array(
			'conditions'	=> "member.user_id = auth.user_id ".$conditions,
			'join'			=>'has_auth',
			'fields'		=> "member.user_name,member.invite,member.member_lv_id,auth.*",
			'order'         =>"auth.create_time DESC,auth.last_update_time DESC",
			'count'			=> true,
			'limit' => $page['limit'],
		));
	 
		foreach ($auth_list as $key => $value) {
			$auth_list[$key]['last_update_time'] = date("Y-m-d H:i:s",$value['last_update_time']);
			$auth_list[$key]['province']         = $this->_region_mod->getRegionName($value['province']);
			$auth_list[$key]['city']             = $this->_region_mod->getRegionName($value['city']);
		}
		
	  
		$page['item_count'] = $this->_member_mod->getCount();
		$this->_format_page($page);
		$this->assign('page_info', $page);
		$this->assign("list",$auth_list);
		$this->assign('query_fields', array(
        	'member.user_name' => '会员ID',
            'auth.realname'    => '真实姓名',
        ));

        $this->assign('user_fields', array(
        	'cyz' => '创业者',
        	'ph'   => '普通会员',
        
          
        ));

        $this->assign('status', array(
        	'0' 	=> '未审核',
            '1'     => '认证成功',
            '2'     => '认证失败'
        ));
        
        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
		$this->display(PERSON."auth.index.html");
	}
	
	/**
	* 审核
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-29
	*/
	function edit() 
	{   
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$auth_info = $this->_member_mod->get(array(
				'conditions'	=> 	"auth.id=".$id,
				'join'			=> "has_auth",
				'fields'		=> "auth.*,member.user_name,member.invite,member.member_lv_id,member.user_token"
			));
			
		
			
		$user_lv = $auth_info['member_lv_id'];
		if($auth_info['member_lv_id'] ==1)
		{   
			$auth_info['member_lv_id'] = '普通会员';
		}
		
		
		if($auth_info['member_lv_id'] > 1)
		{
			$auth_info['member_lv_id'] = '创业者';
		}
		// BD ma 
		$invite_info = $this->_invite_mod->get(" type=1  AND invitee =".$auth_info['user_id']);
	
		if($invite_info)
		{
			$bdinfo = $this->_gen_mod->get($invite_info['inviter']);
			$auth_info['bdcode'] = $bdinfo['invite'];	
		}else
		{
		
			$auth_info['bdcode'] = '无';
		}

		// 查询是否有首单体验款 的订单号码  
	    // 查询是否有首单体验款 的订单号码  
	    $order_first_info = $this->_order_first->get(array(
	    	'conditions'=>"is_active=1 AND user_id=".$auth_info['user_id'],
	    	'order'		=>'id ASC'
	    
	    ));
	    if($order_first_info['id'])
	    {
	    	$order_info = $this->_order_mod->get("order_id=".$order_first_info['order_id']);
	    	$auth_info['order_sn'] = $order_info['order_sn'];
	    }else
	    {
	    	$auth_info['order_sn'] = '无首单体验订单';
	    	
	    }
	    
		    // 创业顾问   
	  	$invites = $this->_memberinvite_mod->get("invitee ='{$auth_info['user_id']}' ");
	    if($invites['inviter'])
	    {
	    	if($invites['type'] ==1)
	    	{
		    	$cygw = $this->_generalizemem_mod->get("id ='{$invites['inviter']}'");
		    	$auth_info['tjrname'] = $cygw['name'];
		    	$auth_info['tjrcode']   = $cygw['invite'];
		    	$auth_info['tjrbs']     ='BD码';
	    	}
	    	if($invites['type'] ==0)
	    	{
	    		$cygw = $this->_member_mod->get("user_id ='{$invites['inviter']}'");
	    		if($cygw['nickname'])
	    		{
	    				$auth_info['tjrname'] = $cygw['nickname'];
	    		}elseif($cygw['real_name'])
	    		{
	    				$auth_info['tjrname'] = $cygw['real_name'];
	    		}else
	    		{
	    				$auth_info['tjrname'] = $cygw['user_name'];
	    		}
	    	
		    	$auth_info['tjrcode']   = $cygw['invite'];
		    	$auth_info['tjrbs']     ='邀请码';
	    	}
	    	
	    
	    }
	        // 审核人
	    // 审核人
	   
	  
		
	    $auth_info['status'] = $this->_sta[$auth_info['status']];
	    $auth_info['last_update_time'] = date("Y-m-d H:i:s", $auth_info['last_update_time']);
	    if($auth_info['create_time'])
	    {
	    	
	    	  $auth_info['create_time'] = date("Y-m-d H:i:s", $auth_info['create_time']);
	    }else
	    {
	    	   $auth_info['create_time'] = '还未审核';
	    
	    }
	   
	    $auth_info['card_face_img'] =SITE_URL.'/'.$auth_info['card_face_img'];
		if(!IS_POST)
		{
			
			//===== 如果此时状态为0未审核 那么应该修改为3 已受理 =====
			if ($auth_info['is_accept'] == 0)
			{
				$this->_auth_mod->edit($id,array('is_accept'=>1),0);
			}
			
			
		
		  
			$auth_info['province']         = $this->_region_mod->getRegionName($auth_info['province']);
			$auth_info['city']             = $this->_region_mod->getRegionName($auth_info['city']);
			if($auth_info['auditor']){
			    $authors  = $this->_member_mod->get($auth_info['auditor']);
			    $auth_info['auther'] = isset($authors['user_name']) ? $authors['user_name']: 'admin';
			     
			}else{
			    $auth_info['auther'] ='admin';
			}
			
			$this->assign('auth_info',$auth_info);
			$this->display(PERSON."auth.edit.html");
		}
		else
		{
		    $info = $this->user_id = $this->visitor->get(); // 审核人
			$fail_a = $_POST['area'];
		    
			$fail_reason = isset($_POST['fail_reason']) ? $_POST['fail_reason'] : '';
			
			
			if($fail_a || $fail_reason)
			{	
				if($fail_a)
				{
					$temp_a = implode(";",$fail_a);
				}
			
				$data['status'] = 2;//认证失败
				if($fail_reason && $fail_a)
				{
					$data['fail_reason'] = $temp_a.';'.'其它:'.$fail_reason;
				}elseif($fail_a)
				{
					$data['fail_reason'] = $temp_a;
				}elseif($fail_reason){
					$data['fail_reason'] = $fail_reason;
				}
				
				$mes_xin = '失败';
			}else
			{
				$data['status'] = 1;//认证成功
				$mes_xin = '成功';
			}
			
			
			
			$data['create_time'] = time();//审核时间
			$data['auditor'] = $info['user_id'];
			if ($this->_auth_mod->edit($id,$data,0) !== false)
			{
				import('operationlog.lib');
				$type='edit';
				$operate_log=new OperationLog();
				if($data['status']==1){
					$mark="管理员".$this->visitor->get('user_name')."审核通过了会员{$auth_info['user_name']}的{$auth_info['member_lv_id']}认证";
				}
				if($data['status']==2){
					$mark="管理员".$this->visitor->get('user_name')."审核了会员{$auth_info['user_name']}的{$auth_info['member_lv_id']}认证,做不通过的操作";
				}
				$operate_log->operation_log('',$type,$mark);
		        $this->show_message('操作成功!',
						'back_list', 'index.php?app=authcyz&act=index'
				);  
			  
				$au_info = $this->_auth_mod->get($id);

				// 判断 用户是否绑定 bd 码 并且 为 普通会员
				
				 change_lv($au_info['user_id']);
				
				// 如果认证成功同步一份数据到认证日志表里面 做记录
				if($au_info['status'] ==1){
					
					unset($au_info['id']);
					$this->_authlog_mod->add($au_info,false,0);
				}
				$au_info['status'] = $this->_sta[$au_info['status']];

				// 认证成功 或者失败 给 申请人 发通知 消息
				/* $arr = array(
					  'title'         => '实名认证通知',
                      'from_user_id'  => $info,
                      'to_user_id'    => $auth_info['user_id'],
                      'from_nickname' => $user_info['nickname'],
                      'location_url'  => "article-".$id.".html",
                      'type'          => '5',
                      'content'       => '尊敬的用户,您的实名认证申请'.$mes_xin,
                      'add_time'      => time()
                );
				$this->_message_mod->add($arr); */
			        //=====  信鸽  =====
              /*  include(ROOT_PATH . '/includes/xinge/xinggemeg.php');    
               $push = new XingeMeg();
               $push->toMfdXinApp($auth_info['user_token'], '实名认证通知', '尊敬的用户,您的实名认证申请'.$mes_xin, array('url_type'=>'auth', 'location_id'=>$auth_info['user_id'])); */
				$res=send_message('cyz_auth',array($au_info['user_id']),array('mes_xin'=>$mes_xin),$info_type=5);
			}
			
			
		
	
			
		}
		
	}


	function view(){
		
	
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	    
		
		if($id)
		{
			$auth_info = $this->_member_mod->get(array(
				'conditions'	=> 	"auth.id=".$id,
				'join'			=> "has_auth",
				'fields'		=> "auth.*,member.user_name,member.invite,member.member_lv_id"
			));
		}else{
			$this->show_warning('请选择要查看的数据!');
            return;

		}

	 	$auth_info['province']         = $this->_region_mod->getRegionName($auth_info['province']);
		$auth_info['city']             = $this->_region_mod->getRegionName($auth_info['city']);
		if($auth_info['member_lv_id'] ==1)
		{   
			$auth_info['member_lv_id'] = '普通会员';
		}
		
		
		if($auth_info['member_lv_id'] > 1)
		{
			$auth_info['member_lv_id'] = '创业者';
		}
	    
		$invite_info = $this->_invite_mod->get(" invitee ={$auth_info['user_id']} AND type=1 ");
		
	
		
/*		if($invite_info)
		{
			$bdinfo = $this->_gen_mod->get($invite_info['inviter']);
		
			$auth_info['bdcode'] = $bdinfo['invite'];	
		}else
		{
		
			$auth_info['bdcode'] = '无';
		}*/
		// 信息表核实  
		
		$askinfo  = $this->_ask2_mod->get("phone = {$auth_info['user_name']} ");
		
		if($askinfo){
			$auth_info['askid'] = $askinfo['id'];
		}else{
			$auth_info['askid'] = '';
		}
	
	    // 查询是否有首单体验款 的订单号码  
	    $order_first_info = $this->_order_first->get(array(
	    	'conditions'=>"is_active=1 AND user_id=".$auth_info['user_id'],
	    	'order'		=>'id ASC'
	    
	    ));
	 
	    if($order_first_info['id'])
	    {
	    	$order_info = $this->_order_mod->get("order_id=".$order_first_info['order_id']);
	    	$auth_info['order_sn'] = $order_info['order_sn'];
	    }else
	    {
	    	$auth_info['order_sn'] = '无首单体验订单';
	    	
	    }
	    // 创业顾问   
	    $invites = $this->_memberinvite_mod->get("invitee ='{$auth_info['user_id']}' ");
	    if($invites['inviter'])
	    {
	    	if($invites['type'] ==1)
	    	{
		    	$cygw = $this->_generalizemem_mod->get("id ='{$invites['inviter']}'");
		    	$auth_info['tjrname'] = $cygw['name'];
		    	$auth_info['tjrcode']   = $cygw['invite'];
		    	$auth_info['tjrbs']     ='BD码';
	    	}
	    	if($invites['type'] ==0)
	    	{
	    		$cygw = $this->_member_mod->get("user_id ='{$invites['inviter']}'");
	    		if($cygw['nickname'])
	    		{
	    				$auth_info['tjrname'] = $cygw['nickname'];
	    		}elseif($cygw['real_name'])
	    		{
	    				$auth_info['tjrname'] = $cygw['real_name'];
	    		}else
	    		{
	    				$auth_info['tjrname'] = $cygw['user_name'];
	    		}
	    	
		    	$auth_info['tjrcode']   = $cygw['invite'];
		    	$auth_info['tjrbs']     ='邀请码';
	    	}
	    	
	    
	    }
	    // 审核人
		if($auth_info['auditor']){
		    $authors  = $this->_member_mod->get($auth_info['auditor']);
		    $auth_info['auther'] = isset($authors['user_name']) ? $authors['user_name']: 'admin';
			     
		}else
		{
			 $auth_info['auther'] ='admin';
		}
	    
		$auth_info['status'] = $this->_sta[$auth_info['status']];
		$auth_info['last_update_time'] = date("Y-m-d H:i:s", $auth_info['last_update_time']);
		if($auth_info['create_time'])
	    {
	    	
	    	  $auth_info['create_time'] = date("Y-m-d H:i:s", $auth_info['create_time']);
	    }else
	    {
	    	   $auth_info['create_time'] = '还未审核';
	    
	    }
	    $auth_info['card_face_img'] =SITE_URL.'/'.$auth_info['card_face_img'];
	    // 失败原因 换行处理
	    if($auth_info['fail_reason']){
	    	 $tmpAttr = explode(";", $auth_info['fail_reason']);
	    	 $auth_info['fail_reason'] = $tmpAttr;
	    }
		$this->assign("auth_info",$auth_info);
		$this->assign("auth_id",$id);
		$this->display(PERSON."auth.view.html");

	}
	
	
	/**
	* 删除审核记录      后期要屏蔽这个方法 危险操作
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-29
	*/
	function drop() 
	{
		$id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        if (!$id) {
            $this->show_warning('请选择要删除的数据!');
            return;
        }
        
        //===== 删除对应的审核上传的图片 =====
        #TDDO
        
        
        $this->_auth_mod->drop(db_create_in($id, 'id'));
        $this->show_message('删除成功!', 'back_list', 'index.php?app=authcyz&act=index&page=' . $ret_page);
	}
	
	
	/**
	* 认证详情的打印
	* @version 1.0.0
	* @authortangshoujian <963830611@qq.com>
	* @2015-8-28
	*/
	
	function auth_print(){
		
		$id = isset($_GET['id']) ? trim($_GET['id']):'';
		
	     header("Content-type:text/html;charset=utf-8");
         if(empty($id)){
             $this->show_message("请选择要打印的认证订单！");
             return false;
         }
         
		if($id)
		{
			$auth_info = $this->_member_mod->get(array(
				'conditions'	=> 	"auth.id=".$id,
				'join'			=> "has_auth",
				'fields'		=> "auth.*,member.user_name,member.invite,member.member_lv_id"
			));
		}else{
			$this->show_warning('请选择要查看的数据!');
            return;

		}
		
		if($auth_info['member_lv_id'] ==1)
		{   
			$auth_info['member_lv_id'] = '普通会员';
		}
		
		
		if($auth_info['member_lv_id'] > 1)
		{
			$auth_info['member_lv_id'] = '创业者';
		}
	    
		$invite_info = $this->_invite_mod->get(" invitee ={$auth_info['user_id']} AND type=1 ");
		
		$askinfo  = $this->_ask2_mod->get("phone = {$auth_info['user_name']} ");
		
		if($askinfo){
			$auth_info['askid'] = $askinfo['id'];
		}else{
			$auth_info['askid'] = '';
		}
		
		  // 查询是否有首单体验款 的订单号码  
	    $order_first_info = $this->_order_first->get(array(
	    	'conditions'=>"is_active=1 AND user_id=".$auth_info['user_id'],
	    	'order'		=>'id ASC'
	    
	    ));
	 
	    if($order_first_info['id'])
	    {
	    	$order_info = $this->_order_mod->get("order_id=".$order_first_info['order_id']);
	    	$auth_info['order_sn'] = $order_info['order_sn'];
	    }else
	    {
	    	$auth_info['order_sn'] = '无首单体验订单';
	    	
	    }
	    
		    // 创业顾问   
	    $invites = $this->_memberinvite_mod->get("invitee ='{$auth_info['user_id']}' AND type=1");
	    if($invites['inviter']){
	    	$cygw = $this->_generalizemem_mod->get("id ='{$invites['inviter']}'");
	    	$auth_info['cygwname'] = $cygw['name'];
	    	$auth_info['cygwbd']   = $cygw['invite'];
	    
	    }
	      
	    if($auth_info['auditor']){
	        $authors  = $this->_member_mod->get($auth_info['auditor']);
	        $auth_info['auther'] = isset($authors['user_name']) ? $authors['user_name']: 'admin';
	    
	    }else
	    {
	        $auth_info['auther'] ='admin';
	    }
	
		$auth_info['status'] = $this->_sta[$auth_info['status']];
		$auth_info['last_update_time'] = date("Y-m-d H:i:s", $auth_info['last_update_time']);
		if($auth_info['create_time'])
	    {
	    	
	    	  $auth_info['create_time'] = date("Y-m-d H:i:s", $auth_info['create_time']);
	    }else
	    {
	    	   $auth_info['create_time'] = '还未审核';
	    
	    }
	    $auth_info['card_face_img'] =SITE_URL.'/'.$auth_info['card_face_img'];
	    // 失败原因 换行处理
	    if($auth_info['fail_reason']){
	        $tmpAttr = explode(";", $auth_info['fail_reason']);
	        $auth_info['fail_reason'] = $tmpAttr;
	    }
		$this->assign("auth_info",$auth_info);
		$this->display(PERSON."auth.print.html");
	
	
	}

}






