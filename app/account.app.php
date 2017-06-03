<?php

class AccountApp extends MemberbaseApp
{
	var $_account_mod;
	var $_apply_mod;
	var $_member_mod;
	function __construct(){
		 $this->_account_mod =& m('account');
		 $this->_apply_mod   =& m("apply");
		 $this->_member_mod   =& m("member");
		 parent::__construct();
	}
    /**
     *    资金管理
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function index()
    {
        /* 当前所处子菜单 */
        $this->_curmenu('index');
        
        /* 当前用户中心菜单 */
        $this->_curitem('account');
        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
        
        $page = $this->_get_page(10);
        
        $user = $this->visitor->get();
        
    	$account = $this->_account_mod->find(array(
    			'fields' => '*',
    			'conditions' => "user_id = '{$user["user_id"]}' AND money != 0",
    			'limit' => $page['limit'],
    			'order' => "id DESC",
    			'count' => true,
    	));
    	
    	
        $page['item_count'] = $this->_account_mod->getCount();
        
        $this->_format_page($page);

        $this->assign('page_info', $page);
    	
        $this->assign('user', $user);
        
        $this->assign('account_list', $account);
        $this->_config_seo('title', Lang::get('member_center') . ' - 资金管理');
        $this->display('account.html');
    }

    /**
     *   充值
     *
     *    @author    Hyber
     *    @return    void
     */
    function deposit()
    {
    	
        if (!IS_POST){
        	/* 当前所处子菜单 */
	        $this->_curmenu('deposit');
	        $this->import_resource(array(
	            'script' => array(
	                array(
	                    'path' => 'dialog/dialog.js',
	                    'attr' => 'id="dialog_js"',
	                ),
	                array(
	                    'path' => 'jquery.ui/jquery.ui.js',
	                    'attr' => '',
	                ),
	                array(
	                    'path' => 'jquery.plugins/jquery.validate.js',
	                    'attr' => '',
	                ),
	            ),
	            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
	        ));
	        /* 当前用户中心菜单 */
	        $this->_curitem('account');

	        $this->assign('messages', $messages);
	        $this->_config_seo('title', Lang::get('user_center') . ' - 充值');
	        
	        $payment_model =& m('payment');
	        $payments = $payment_model->get_enabled($order_info['seller_id']);
	        if (empty($payments))
	        {
	        	$this->show_warning('store_no_payment');
	        
	        	return;
	        }
	        
	        $all_payments = array('online' => array(), 'offline' => array());
	        foreach ($payments as $key => $payment)
	        {
	        	if ($payment['is_online'])
	        	{
	        		$all_payments['online'][] = $payment;
	        	}
	        	else
	        	{
	        		$all_payments['offline'][] = $payment;
	        	}
	        }
	        
	        $this->assign('payments', $all_payments);
	        
	        $this->display('account.deposit.html');
        }
        else
        { 
	            /* 验证支付方式是否可用，若不在白名单中，则不允许使用         */
        	
        		$payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
        		$money = trim($_POST['money']);
        		$remark = trim($_POST['remark']);
        		
        		$payment_model =& m('payment');
        		
        		$payment_info  = $payment_model->get($payment_id);

	            /* 若卖家没有启用，则不允许使用         */
	            if (!$payment_info['enabled'])
	            {
	                $this->show_warning('payment_disabled');
	
	                return;
	            }
	            
	            if(!preg_match("/^[0-9]+(\.[0-9]{1,2})?$/", $money)){
	            	$this->show_warning('请输入正确的充值金额!');
	            	
	            	return;
	            }
	
	            if($money <= 0){
	            	$this->show_warning('充值金额必须在于0!');
	            	
	            	return;
	            }

	            $user = $this->visitor->get();
	            
	            $data = array(
	            		'user_id' => $user["user_id"],
	            		'type'	  => DEPOSIT,
	            		'money'   => $money,
	            		'remark'  => $remark,
	            		'add_time' => time(),
	            		'status'  => APPLY_UNPROCESS,
	            		'pay_id'  => $payment_id
	            );
	             
	           $id = $this->_apply_mod->add($data);
	            
	            /* 生成支付URL或表单         
	            $payment    = $this->_get_payment($payment_info['payment_code'], $payment_info);
        	
	            $order = array(
	            	'order_id' => $id,
	            	'order_sn' => $id,
	            	'order_amount' => $money		
	            );
	            
	            $payment_form = $payment->get_payform($order, 'account');
	            
	            $this->assign('payform', $payment_form);
	            $this->assign('payment', $payment_info);
	            header('Content-Type:text/html;charset=' . CHARSET);
	            $this->display('cashier.payform.html');
	            */
	            header('Location:index.php?app=cashier&act=deposit&id='.$id);
        }
    }
    
    function apply()
    {
            if (!IS_POST){
        	/* 当前所处子菜单 */
	        $this->_curmenu('apply');
	        $this->import_resource(array(
	            'script' => array(
	                array(
	                    'path' => 'dialog/dialog.js',
	                    'attr' => 'id="dialog_js"',
	                ),
	                array(
	                    'path' => 'jquery.ui/jquery.ui.js',
	                    'attr' => '',
	                ),
	                array(
	                    'path' => 'jquery.plugins/jquery.validate.js',
	                    'attr' => '',
	                ),
	            ),
	            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
	        ));
	        /* 当前用户中心菜单 */
	        $user = $this->visitor->get();
	        $this->assign('user', $user);
	        $this->_curitem('account');
	        $this->_config_seo('title', Lang::get('user_center') . ' - 充值');
	        $this->display('account.apply.html');
        }
        else
        {
        	$money  = $_POST['money'];
        	$remark = trim($_POST["remark"]);
        	$user = $this->visitor->get();
        	
        	if($money > $user["money"]){
        		$this->show_message('提现金额超出可用资金!', 'go_back', 'index.php?app=account&act=apply');
        	}
        	
        	$data = array(
        		'user_id' => $user["user_id"],
        		'type'	  => APPLY,
        		'money'   => $money,
        		'remark'  => $remark,
        		'add_time' => time(),
        		'status'  => APPLY_UNPROCESS	
        	);
        	
        	$this->_apply_mod->add($data);
        	
        	$field = "money=money-{$money}, frozen=frozen+{$money}";
        	$this->_member_mod->changeAccount($user["user_id"], $field);
        	
        	$log = array(
				'dateline' => time(),
				'remark'   => '用户申请提现，冻结资金',
				'money'	   => -$money,
				'frozen'   => $money,
				'user_id'  => $user["user_id"],
				'type'     => CHANGE_TYPE2,
			);
			
    		$this->_account_mod->add($log);
        	
        	$this->show_message('提现申请已提交!', 'go_back', '/index.php?app=account&act=apply_list');
        }
    }

    /**
     *    三级菜单
     *
     *    @author    Hyber
     *    @return    void
     */
    function _get_member_submenu()
    {
    	$menus = array(
    			array(
    					'name'  => 'index',
    					'url'   => '/index.php?app=account',
    					'text'  => "交易明细",
    			),
    			array(
    					'name'  => 'apply_list',
    					'url'   => '/index.php?app=account&amp;act=apply_list',
    					'text'  => '申请纪录',
    			),
    			array(
    					'name'  => 'deposit',
    					'url'   => '/index.php?app=account&amp;act=deposit',
    					'text'  => "充值",
    			),
    			array(
    					'name'  => 'apply',
    					'url'   => '/index.php?app=account&amp;act=apply',
    					'text'  => "提现",
    			),
    	);
    
    	return $menus;
    }

    /**
     *    申请纪录
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function apply_list()
    {
    	/* 当前所处子菜单 */
    	$this->_curmenu('apply_list');
    
    	/* 当前用户中心菜单 */
    	$this->_curitem('account');
    	$this->import_resource(array(
    			'script' => array(
    					array(
    							'path' => 'dialog/dialog.js',
    							'attr' => 'id="dialog_js"',
    					),
    					array(
    							'path' => 'jquery.ui/jquery.ui.js',
    							'attr' => '',
    					),
    					array(
    							'path' => 'jquery.plugins/jquery.validate.js',
    							'attr' => '',
    					),
    			),
    			'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
    	));
    
    	$page = $this->_get_page(10);
    
    	$user = $this->visitor->get();
    
    	$apply_list = $this->_apply_mod->find(array(
    			'fields' => '*',
    			'conditions' => "user_id = '{$user["user_id"]}'",
    			'limit' => $page['limit'],
    			'order' => "id DESC",
    			'count' => true,
    	));
    	 
    	$this->assign('apply_list', $apply_list);
    	 
    	 
    	$page['item_count'] = $this->_apply_mod->getCount();
    
    	$this->_format_page($page);
    
    	$this->assign('page_info', $page);
    	 
    	$this->assign('user', $user);
    
    	$this->_config_seo('title', Lang::get('member_center') . ' - 申请纪录');
    	$this->display('account.apply_list.html');
    }
    
    function cancel(){
    	$id = intval($_GET['id']);
    	$user = $this->visitor->get();
    	
    	$data = $this->_apply_mod->get(array("conditions" => array('id' => $id, 'type' => APPLY ,"user_id" => $user["user_id"], "status" => APPLY_UNPROCESS)));
    	
    	if(empty($data)){
    		$this->show_message('非法操作!', 'go_back', '/index.php?app=account&act=apply_list');
    	}
    	
    	$this->_apply_mod->edit("id='{$id}'",array('status' => APPLY_CANCELED));
    	 
    	$field = "money=money+{$data['money']}, frozen=frozen-{$data['money']}";
    	$this->_member_mod->changeAccount($user["user_id"], $field);
    	 
    	$log = array(
    			'dateline' => time(),
    			'remark'   => '用户取消提现申请，解除冻结资金',
    			'money'	   => $data['money'],
    			'frozen'   => -$data['money'],
    			'user_id'  => $user["user_id"],
    			'type'     => CHANGE_TYPE2,
    	);

    	$this->_account_mod->add($log);
    	
    	$this->show_message('申请取消成功!', 'go_back', '/index.php?app=account&act=apply_list');
    }
}
?>
