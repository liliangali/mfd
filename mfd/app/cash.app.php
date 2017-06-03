<?php
/**
 *个人认证控制器
 * @author liang.li <1184820705@qq.com>
 * @version 1.0
 * @copyright Copyright 2014 caifeng.com
 * @package authperson.app.php
*/
class CashApp extends BackendApp
{
	var $_mod;
	var $_web_url;
	function __construct()
	{
		define("DIR", "cash/");
		$this->_web_url = SITE_URL."/upload/cash/";
		$this->_mod = & m('cash');
		
		$this->assign('is_long',array(0=>'否',1=>'是'));
		$this->assign('status',array(1=>'审核成功',2=>'审核失败'));
		//$this->assign('cash_type',array(0=>'余额提现', 1=>'收益提现'));
		
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
		
	    $cash_tax = 0.2;
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
		   /*  array(
		        'field' => 'type',
		        'equal' => '=',
		    ), */
				
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
		    'order' => "id DESC",
		));
		
		//===== 银行卡信息  =====
		$member_bank_mod = m('member_bank');
		$bank = include_once ROOT_PATH.'/includes/arrayfiles/bank.arrayfile.php';
	
		if ($person_list) 
		{
		    foreach ($person_list as $key => &$value) 
		    {
		        /* $person_list[$key]['real_money'] = $value['cash_money'];
		        if ($value['type'] == 1) //=====  收益提现  =====
		        {
		            $person_list[$key]['real_money'] = $value['cash_money'] * (1 - $cash_tax);
		        } */
		        $value['bank_id'] = $bank[$value['bank_id']];
		       // $value['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
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
	    $cash_tax = 0.2;
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		
		$info = $this->_mod->get(array(
		    'conditions'	=> 	"id=".$id,
		    'fields'		=> "*"
		));
		if(!IS_POST)
		{
			$person_mod = m('auth');
			$person_info = $person_mod->get("user_id = {$info['user_id']}");
			$info['real_name'] = $person_info['realname'];
			$info['real_money'] = $info['cash_money'];
			if ($info['type'] == 1) 
			{
			    $info['real_money'] = $info['cash_money']*(1 - $cash_tax);
			}
			
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
		        $this->show_warning('此申请已审核完成 不允许再次修改');
		        return ;
		    }
			$stauts = $_POST['status'];
			$fail_reason = isset($_POST['fail_reason']) ? $_POST['fail_reason'] : '';
		
			if ($stauts == 2 && !$fail_reason)
			{
				$this->show_warning('请填写失败原因a');
				return ;
			}
			//=====  审核成功 扣除member表的金额  =====
			if ($stauts == 1 || $stauts == 2) 
			{
			    $transaction = $this->_mod->beginTransaction();
			    $admin_id = $this->visitor->info['user_id'];
			    $res = $this->_mod->submitCash($id,$stauts);
			    if ($res['code'])
			    {
			        $this->_mod->rollback();
			        $this->show_warning($res['msg']);
			        return ;
			    }
			    $this->_mod->commit($transaction);
			    
			    if ($stauts == 2) //=====   审核失败发 辛格  =====
			    {
			        //加入系统消息
			        sendSystem($info['user_id'], '16', '余额提现', '您的提现审核失败,失败原因:'.$fail_reason);
			    }
			}
			//======   后台操作日志记录提现审核操作 by zxr ======
			import('operationlog.lib');
			$type='edit';
			$operate_log=new OperationLog();
			$mod=&m('member');
			$member=$mod->get_info($info['user_id']);
			if($stauts==1){
				$mark="管理员".$this->visitor->get('user_name')."审核<font style='color:red'>通过</font>了会员{$member['user_name']}的提现请求";
			}
			if($stauts==2){
				$mark="管理员".$this->visitor->get('user_name')."审核<font style='color:red'>不通过</font>会员{$member['user_name']}的提现请求,备注（{$fail_reason}）";
			}
			$operate_log->operation_log('',$type,$mark);
			//========= END =========
			if ($this->_mod->edit($id,array('msg'=>$fail_reason,'admin_id'=>$admin_id,'audit_time'=>time()),0) !== false)
			{
				$this->show_message('操作成功!',
						'back_list', 'index.php?app=cash&act=index'
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
        $this->show_message('删除成功!', 'back_list', 'index.php?app=cash&act=index&page=' . $ret_page);
	}
	
	/**
	 * 审核
	 * @version 1.0.0
	 * @author liang.li <1184820705@qq.com>
	 * @2015-1-29
	 */
	function view()
	{
	    $cash_tax = 0.2;
	    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	
	    $info = $this->_mod->get(array(
	        'conditions'	=> 	"id=".$id,
	        'fields'		=> "*"
	    ));
	
        $person_mod = m('auth');
        $person_info = $person_mod->get("user_id = {$info['user_id']}");
        $info['real_name'] = $person_info['realname'];
        $info['real_money'] = $info['cash_money'];
        if ($info['type'] == 1)
        {
            $info['real_money'] = $info['cash_money']*(1 - $cash_tax);
        }
        	
        $bank_card = $info['bank_card'];
        $b1 = substr($bank_card,0,4);
        $b2 = substr($bank_card,4,4);
        $b3 = substr($bank_card,8,4);
        $b4 = substr($bank_card,12,4);
        $b5 = substr($bank_card,16);
        $info['bank_card'] = $b1." ".$b2." ".$b3." ".$b4." ".$b5;
        	
        $this->assign('data',$info);
        $this->display(DIR."view.html");
	    

	
	}
	
	
	
	
	
	
	
	
	
	
	
	
}




































