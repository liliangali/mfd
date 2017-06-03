<?php
/**
 *个人认证控制器
 * @author liang.li <1184820705@qq.com>
 * @version 1.0
 * @copyright Copyright 2014 caifeng.com
 * @package authperson.app.php
*/
class FigurecashApp extends BackendApp
{
	var $_mod;
	var $_web_url;
	function __construct()
	{
		define("DIR", "figurecash/");
		$this->_web_url = SITE_URL."/upload/cash/";
		$this->_mod = & m('figurecash');
		
		$this->assign('is_long',array(0=>'否',1=>'是'));
		$this->assign('status',array(1=>'审核成功',2=>'审核失败'));
		
		$bank = include_once ROOT_PATH.'/includes/arrayfiles/bank.arrayfile.php';
		$this->assign('bank',$bank);
		
		$this->PersonApp();
	}
	function PersonApp()
	{
		parent::BackendApp();
	}
	
	/**
	* 提现列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-28
	*/
	function index() 
	{
		
		$_GET['status'] = isset($_GET['status']) ?  $_GET['status'] : 0;
		$conditions = $this->_get_query_conditions(array(
				array(
						'field' => 'bank_card',
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
		
		$person_list = $this->_mod->find(array(
			'conditions'	=> "1=1 ".$conditions,
			'fields'		=> "*",
			'count'			=> true,
			'limit' => $page['limit'],
		));
		
		$figure_auth_mod = m('figureauth');
		if ($person_list)
		{
		    foreach ($person_list as $key => $value) 
		    {
		        $info = $figure_auth_mod->get("user_id = {$value['user_id']}");
		        $person_list[$key]['bank_card'] = $info['bankcard'];
		        if ($value['create_time']) 
		        {
		            $person_list[$key]['create_time'] = date("Y-m-d H:i:s",$value['create_time']);
		        }
		        if ($value['audit_time'])
		        {
		            $person_list[$key]['audit_time'] = date("Y-m-d H:i:s",$value['audit_time']);
		        }
		        
		    }
		}
		$page['item_count'] = $this->_mod->getCount();
		$this->_format_page($page);
		$this->assign('page_info', $page);
		
		$this->assign("list",$person_list);
		$this->display(DIR."index.html");
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
		
		if(!IS_POST)
		{
			$info = $this->_mod->get(array(
					'conditions'	=> 	"id=".$id,
// 					'join'			=> "belongs_to_member",
					'fields'		=> "*"
			));
			
		    if ($info) 
		    {
		        $info['create_time'] = date("Y-m-d H:i:s",$info['create_time']);
		    }
			
			$person_mod = m('figureauth');
			$person_info = $person_mod->get("user_id = {$info['user_id']}");
			$info['real_name'] = $person_info['realname'];
			
			$bank_card = $info['bank_card'];
			$b1 = substr($bank_card,0,4);
			$b2 = substr($bank_card,4,4);
			$b3 = substr($bank_card,8,4);
			$b4 = substr($bank_card,12,4);
			$b5 = substr($bank_card,16);
			$info['bank_card'] = $b1." ".$b2." ".$b3." ".$b4." ".$b5;
			
			
			
			$this->assign('data',$info);
			$this->display(DIR."edit.html");
		}
		else
		{
		    //=====  如果已经审核成功 不允许再次提交成功  因为第一次点击审核成功的时候 已经把member表的金额扣除了  =====
		    if ($info['status'] != 0)
		    {
		        $this->show_warning('此提现申请已审核完毕');
		        return ;
		    }
		    
		    
			$stauts = $_POST['status'];
			$fail_reason = isset($_POST['fail_reason']) ? $_POST['fail_reason'] : '';
			if ($stauts == 2 && !$fail_reason)
			{
				$this->show_warning('请填写失败原因');
				return ;
			}
			
			//=====  审核成功 扣除member表的金额  =====
	        $transaction = $this->_mod->beginTransaction();
		    $res = $this->_mod->submitCash($id,$stauts);
		    if ($res['code'])
		    {
		        $this->_mod->rollback();
		    }
		    $this->_mod->commit($transaction);
		     
		    if ($res['code'])
		    {
		        $this->show_warning($res['msg']);
		        return ;
		    }
			
			
			
			if ($this->_mod->edit($id,array('status'=>$stauts,'msg'=>$fail_reason)) !== false)
			{
				$this->show_message('操作成功!',
						'back_list', 'index.php?app=figurecash&act=index'
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
        
        
        $this->_mod->drop(db_create_in($id, 'id'));
        $this->show_message('删除成功!', 'back_list', 'index.php?app=figurecash&act=index&page=' . $ret_page);
	}
	
	
	
	
	
	
	
	
	
	
	
	
}




































