<?php

/**
 * 充值
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: recharge.app.php 14351 2016-05-03 09:11:32Z lil $
 * @copyright Copyright 2014 redcollar
 */

class RechargeApp extends MemberbaseApp{
    var $_mod_pay;
    
    function __construct()
    { 
        $this->RechargeApp();
    }
    
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
        $this->_view->template_dir = ROOT_PATH . "/h5/view/".LANG."/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/h5/temp/compiled/mall/{$template_name}/recharge";
        $this->_view->res_base     = SITE_URL . "/h5/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }

    function RechargeApp()
    {
        parent::__construct();
        $this->_mod_pay = &m("payment");
    }
    
    function index(){

        $user = $this->visitor->get();
        
//         if (!$user['has_store'])
//         {
//             header("Location:apply.html");
//         }
        $this->assign('payments',$this->payments());
        require(ROOT_PATH . '/includes/avatar.class.php');
        $objAvatar = new Avatar();
        $avatar = $objAvatar->avatar_show($user['user_id'],'big');
        $this->assign('avatar', $avatar);
        $this->assign('type','tailor');
        $this->assign('user',$user);
        $this->assign('ac', ACT);
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        //$this->assign('_curitem', 'my_order');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_order'));
        $this->display('/recharge/index.html');
    }
	
	/**
     * 用户充值页面-app
	 *
     * @param string $token 用户token, int  $pageSize 页面大小, int $pageIndex 页码
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function rechargeToApp()
    {	
		$args = $this->get_params();
		$mod_member = m('member');
		$user_id = 0;
		//如果用户没有登录，则视为app嵌套wap，执行验证,及自动登录操作
		if(!empty($args)) {
			//处理获得参数
			$token    = empty($args[0]) ? 0 : $args[0];//用户token
			
			if(empty($token)) {
				//print_r('sss');exit;
				$this->assign('error_msg', '参数有误');
				$this->display('recharge/app_error.html');
				return false;
			}

			//执行初始化、登录
			$this->visitor->logout();
			//判断当前用户是否存在
			$user_info = $mod_member->get(array(
				'conditions' => "user_token = '$token'",
			));
			//处理显示价格
			$user_info['money'] = floor($user_info['money']);
			
			//执行自动登录
			$this->_do_login($user_info['user_id']);
			$user_id = $user_info['user_id'];
		}
		
		//判断当前用户是否已登录
		if(empty($user_id)) {

			$this->assign('error_msg', '您无权访问此页面！');
			$this->display('/recharge/app_error.html');
			return false;
		} else {
			$this->assign('payments', $this->payments());
			$this->assign('user_info', $user_info);
			$this->display('recharge/rechargeApp.form.html');
		}
		
    }
    
    function goToPay(){

        $money = isset($_POST['je']) ? intval($_POST['je']) : 0;
        $code  = isset($_POST['cd']) ? trim($_POST['cd']) : '';

//        $money = 0.01;//测试使用
        if($money <= 0 || $code == ''){
            $this->show_warning('参数错误!');
            return;
        }

        $payment_model =& m('payment');
    
        /* 验证支付方式是否可用，若不在白名单中，则不允许使用 
        if (!$payment_model->in_white_list($code))
        {
            $this->show_warning('payment_disabled_by_system');
            return;
        }*/
         
        $payment_info  = $payment_model->get("payment_code = '{$code}'");
        /* 没有启用，则不允许使用 */
        if (!$payment_info['enabled'])
        {
            $this->show_warning('payment_disabled');
            return;
        } 
        /* 生成支付URL或表单 */
        $payment    = $this->_get_payment($code , $payment_info);
    
        $paysn = $payment->createBillForAccount(array(
                'order_amount' => $money,
                'user_id'      => $this->visitor->get("user_id"),
                'payment_id'   => $payment_info["payment_id"],
                'payment_name' => $payment_info['payment_name'],
                'payment_code' => $payment_info['payment_code']
        ));
   
        if(!$paysn){
            $this->show_warning('意外错误无法支付，请联系客服！');
            return;
        }
        $payment_form = $payment->get_payform(array(
                'order_sn'      => $paysn,
                'final_amount'  => $money,
				'extension'     => 'account',
        ), 'account');
 
        $this->assign('payform', $payment_form);
    	$this->assign('payment', $payment_info);
    	header('Content-Type:text/html;charset=' . CHARSET);
    	$this->display('/paycenter/paycenter.dopay.html');
    }
    
    
    /**
     * 支付方式列表
     *
     * @version 1.0.0 (2015-03-12)
     * @author Ruesin
     */
    function payments(){
        return $this->_mod_pay->find(array(
                'conditions' => "enabled=1 AND ismobile = 0",
                'order'      => "sort_order DESC"
        ));
    }
    

}

