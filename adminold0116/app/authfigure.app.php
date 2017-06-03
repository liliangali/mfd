<?php
/**
 *个人认证控制器
 * @author liang.li <1184820705@qq.com>
 * @version 1.0
 * @copyright Copyright 2014 caifeng.com
 * @package authperson.app.php
*/
class AuthFigureApp extends BackendApp
{
	var $_figureauth_mod;
	var $_auth_web_url;
	var $_status;
	var $_is_long;
	var $_job;
	function __construct()
	{
		define("PERSON", "figure/");
		$this->_auth_web_url = SITE_URL."/upload/auth/";
		$this->_figureauth_mod =  &m('figureauth');
		$this->_status = array(1=>'审核成功',2=>'审核失败');
/*		$this->_is_long = array(0=>'否',1=>'是');
		$this->assign('is_long',$this->_is_long);*/
		$this->assign('status',$this->_status);
		$this->assign('sex',array('1'=>'男','2'=>'女'));
		
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
		$_GET['status'] = isset($_GET['status']) ?  $_GET['status'] : 0;
		$conditions = $this->_get_query_conditions(array(
				array(
						'field' => 'realname',
						'equal' => 'like',
				),
				array(
						'field' => 'status',
						'equal' => '=',
				),
				
		));
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
		
		$figure_list = $this->_figureauth_mod->find(array(
			'conditions'	=> "1=1 ".$conditions,
			'fields'		=> "*",
			'count'			=> true,
			'limit' => $page['limit'],
		));
	
		$page['item_count'] = $this->_figureauth_mod->getCount();
		$this->_format_page($page);
		$this->assign('page_info', $page);
		
		$this->assign("list",$figure_list);
		$this->display(PERSON."index.html");
	}
	
	/**
	* 审核
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-29
	*/
	function edit() 
	{	
		$this->_figureauth_mod =  &m('figureauth');
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$auth_figure_info = $this->_figureauth_mod->get(array(
				'conditions'	=> 	"id=".$id,
				'join'			=> "belongs_to_user",
				'fields'		=> "figureauth.*,member.*"
		));
		$auth_figure_info['card_face_img'] = FIGUREURL.'/upload/figure_auth/'.$auth_figure_info['card_face_img'];
	    $auth_figure_info['card_back_img'] = FIGUREURL.'/upload/figure_auth/'.$auth_figure_info['card_back_img'];
		if(!IS_POST)
		{
			
			//===== 如果此时状态为0未审核 那么应该修改为3 已受理 =====
			if ($auth_figure_info['is_accept'] == 0)
			{  
				$issess = $this->_figureauth_mod->edit($id,array('is_accept'=>1));

					
			}
			
		
			$this->assign('data',$auth_figure_info);
			$this->display(PERSON."edit.html");
		}
		else
		{   

			$status = $_POST['status'];

			$fail_reason = isset($_POST['fail_reason']) ? $_POST['fail_reason'] : '';
			if ($status == 2 && !$fail_reason)
			{
				$this->show_warning('请填写失败原因');
				return ;
			}
			
			//===== 正则验证身份证 =====
			$preg18 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";
			$preg15 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/";
			$card = $_POST['card'];
			
			
		/* 	if (!preg_match($preg18, $card) && !preg_match($preg15, $card))
			{
				$this->show_warning('请输入正确的身份证件号码');
				return;
			} */
	
			
			
		
			$mem['gender'] = in_array($_POST['gender'], array(1,2)) ? $_POST['gender'] : 1;
			$mem['phone_mob']	 = $_POST['phone_mob'];
		/*	$data['is_long'] = isset($_POST['is_long']) ? $_POST['is_long'] : 0;*/
	/*		$data['card_due_time'] = !empty($_POST['card_due_time']) ? strtotime($_POST['card_due_time']) : '';*/
		/*	$data['job_id']  = $_POST['job_id'];*/
			
		/*	$data['address'] = $_POST['address'];*/
			

			$data['status'] = $status;
			$data['card'] = $card;
			$data['realname'] = $_POST['realname'];
			$data['fail_reason'] = $fail_reason;
			
			
		
			if ($this->_figureauth_mod->edit($id,$data) !== false)
			{
				$member_mod = m('member');
				$member_mod->edit("user_id = '$auth_figure_info[user_id]'",$mem);
		
				$this->show_message('操作成功!',
						'back_list', 'index.php?app=authfigure&act=index'
				);
			}
			
		}
		
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
        
        
        $this->_figureauth_mod->drop(db_create_in($id, 'id'));
        $this->show_message('删除成功!', 'back_list', 'index.php?app=authfigure&act=index&page=' . $ret_page);
	}
	
	
	
	
	
	
	
	
	
	
	
	
}




































