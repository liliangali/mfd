<?php

/**
 * 充值
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: recharge.app.php 12203 2015-11-28 09:00:23Z lil $
 * @copyright Copyright 2014 redcollar
 */

class RechargeApp extends MemberbaseApp{
    var $_mod_pay;
    
    function __construct()
    {
        $this->RechargeApp();
    }
    
    function RechargeApp()
    {
        parent::__construct();
        $this->_mod_pay = &m("payment");
    }
 
	/**
     * 用户充值页面
	 *
     * @param string $token 用户token, int  $pageSize 页面大小, int $pageIndex 页码
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function index()
    {	
		$user_info = $this->visitor->get();
		$this->_config_seo('title', Conf::get('site_title').'账户充值');

		$this->assign('payments', $this->payments());
		$this->assign('user_info', $user_info);
		$this->display('recharge.form.html');
    }
    
    /**
     * liang.li
     * 支付列表(pc端剔除wxpay)
     */
    function payments(){
        $mPayment = &m("payment");
        return $mPayment->find(array(
        				'conditions' => "enabled=1 AND ismobile=0 and payment_code != 'wxpay'",
        				'order'      => "sort_order DESC"
        ));
    }
	
	/**
     * 用户充值处理
	 *
     * @param string $token 用户token, int  $pageSize 页面大小, int $pageIndex 页码
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function goToPay()
	{ 
        $money = isset($_POST['je']) ? intval($_POST['je']) : 0;
        $code  = isset($_POST['cd']) ? trim($_POST['cd']) : '';

       //$money = 0.01;//测试使用
        if($money <= 0 || $code == ''){
            $this->show_warning('参数错误!');
            return;
        }

        $payment_model =& m('payment');

         
        $payment_info  = $payment_model->get("payment_code = '{$code}'");
//    print_exit($payment_info);
        /* 没有启用，则不允许使用 */
        if (!$payment_info['enabled'])
        {
            $this->show_warning('payment_disabled');
            return;
        } 
        /* 生成支付URL或表单 */
        $payment    = $this->_get_payment($code , $payment_info);
//   print_exit($payment);  
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
                'order_amount'  => $money,
                'order_id'      => $paysn
        ), 'account');
        if ($code == 'weixin') //=====  微信支付单独display页面  =====
        {
//      print_exit($payment_form);
            $this->assign('money',$money);
            $this->assign('payment_img',$payment_form);
            $this->display('../paycenter/weixin.html');
            return;
        }
//  print_exit($payment_form);
        $this->assign('payform', $payment_form);
    	$this->assign('payment', $payment_info);
    	header('Content-Type:text/html;charset=' . CHARSET);
//  print_exit($payment_info);
    	$this->display('../paycenter/paycenter.dopay.html');
    }
    
    

}

