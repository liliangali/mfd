<?php

/**
 *    支付网关通知接口
 *
 *    @author    yhao.bai
 *    @usage    none
 */
class PaynotifyApp extends PaycenterbaseApp
{
    /**
     *    支付完成后返回的URL
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function index()
    { 
    	$args = $this->get_params();
    	
        //这里是支付宝，财付通等当订单状态改变时的通知地址
        $order_sn   = isset($args[0]) ? intval($args[0]) : 0; //订单id
        
        if (!$order_sn){
            /* 无效的通知请求 */
			$this->assign('error_msg', '无效的通知请求');
			$this->display('order/error.html');
			return false;
        }

        /* 获取订单信息 */
        $order_info  = $this->_get_order($order_sn);
        
        if (empty($order_info)){
			/* 没有该订单 */
			$this->assign('error_msg', '没有该订单');
			$this->display('order/error.html');
			return false;
        }

        $store = $this->visitor->get('has_store');
        if(!$store){
            //为了后面的各个支付方式不做太大修改，用户支付时把order变量更改一下。
            $order_info['order_amount'] = $order_info['kh_order_amount'];
            $order_info['payment_id']   = $order_info['kf_payment_id'];
            $order_info['payment_name'] = $order_info['kf_payment_name'];
            $order_info['payment_code'] = $order_info['kh_payment_code'];
            $order_info['out_trade_sn'] = $order_info['kh_out_trade_sn'];
        }
        $order_info['has_store']   = $store;
        
        $model_payment =& m('payment');
        
        $payment_info  = $model_payment->get("payment_code='{$order_info['payment_code']}'");
        if (empty($payment_info))
        {

			/* 没有该订单 */
			$this->assign('error_msg', '没有指定的支付方式');
			$this->display('order/error.html');
			return false;
        }

        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($order_info['payment_code'], $payment_info);
     
        /* 获取验证结果 */
        //$notify_result = $payment->verify_notify($order_info);
		$notify_result = $order_info['payment_code'] == 'malipay' ? $payment->veryfy_malipay($order_info) : $payment->verify_notify($order_info, true);
		
        if ($notify_result === false)
        {
			/* 没有该订单 */
			$this->assign('error_msg', '支付失败');
			$this->display('order/error.html');
			return false;
        }
		
        #TODO 临时在此也改变订单状态为方便调试，实际发布时应把此段去掉，订单状态的改变以notify为准
        $this->_change_order_status($order_info['order_id'], $order_info['extension'], $notify_result);
		//计算订单预计时间
		$del_time = strtotime('+10 day');
		$del_data = date('m月d日', $del_time);
		
        /* 只有支付时会使用到return_url，所以这里显示的信息是支付成功的提示信息 */
        $this->_curlocal(LANG::get('pay_successed'));
        $this->assign('notify',$notify_result);
        $this->assign('has_store',$store);
        $this->assign('order', $order_info);
        $this->assign('payment', $payment_info);
		$this->assign('del_data', $del_data);
        $this->display('paynotify.index.html');
    }

    /**
     *    支付完成后，外部网关的通知地址，在此会进行订单状态的改变，这里严格验证，改变订单状态
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function notify()
    {
        //这里是支付宝，财付通等当订单状态改变时的通知地址
		$args = $this->get_params();
    	
        //这里是支付宝，财付通等当订单状态改变时的通知地址
        $order_sn   = isset($args[0]) ? intval($args[0]) : 0; //订单id
        
        if (!$order_sn)
        {
            /* 无效的通知请求 */
            $this->show_warning('forbidden');
            return;
        }
        /* 获取订单信息 */
        $order_info  = $this->_get_order($order_sn);
        
        if (empty($order_info))
        {
            /* 没有该订单 */
            $this->show_warning('forbidden');
            return;
        }
        $store = $this->visitor->get('has_store');
        if(!$store){
            //为了后面的各个支付方式不做太大修改，用户支付时把order变量更改一下。
            $order_info['order_amount'] = $order_info['kh_order_amount'];
            $order_info['payment_id']   = $order_info['kf_payment_id'];
            $order_info['payment_name'] = $order_info['kf_payment_name'];
            $order_info['payment_code'] = $order_info['kh_payment_code'];
            $order_info['out_trade_sn'] = $order_info['kh_out_trade_sn'];
        }
        $order_info['has_store']   = $store;
        
        $model_payment =& m('payment');
        
        $payment_info  = $model_payment->get("payment_code='{$order_info['payment_code']}'");
        if (empty($payment_info))
        {
            /* 没有指定的支付方式 */
            $this->show_warning('no_such_payment');
            return;
        }

        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($order_info['payment_code'], $payment_info);

        /* 获取验证结果 */
        $notify_result = $order_info['payment_code'] == 'malipay' ? $payment->veryfy_malipay($order_info) : $payment->verify_notify($order_info, true);

        if ($notify_result === false)
        {
            /* 支付失败 */
            $payment->verify_result(false);
            return;
        }

        //改变订单状态
        $this->_change_order_status($order_info['order_id'], $order_info['extension'], $notify_result);
        
        $payment->verify_result(true);
    }
	
	//=========================== 充值 Bgn
    
    /**
     * 返回地址
     */
    function account(){
        $args = $this->get_params();
          
        //这里是支付宝，财付通等当订单状态改变时的通知地址
        $pay_sn   = isset($args[0]) ? trim($args[0]) : 0; //支付单
    
        if (!$pay_sn)
        {
            /* 无效的通知请求 */
            $this->show_warning('forbidden');
            return;
        }
    
        $model_paybill =& m("paymentbills");
    
        /* 获取支付单信息 */
        $bill_info = $model_paybill->get("payment_sn='{$pay_sn}'");
    
        if (empty($bill_info))
        {
            /* 没有该支付单 */
            $this->show_warning('forbidden');
            return;
        }
    
        $model_payment =& m('payment');
    
        $payment_info  = $model_payment->get("payment_code='{$bill_info['pay_code']}'");
        if (empty($payment_info))
        {
            /* 没有指定的支付方式 */
            $this->show_warning('no_such_payment');
            return;
        }
    
        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($bill_info['pay_code'], $payment_info);
         
        /* 获取验证结果 */
      /*   $notify_result = $payment->verify_notify(array(
                'out_trade_sn' => $bill_info['payment_sn'],
                'order_amount'   => $bill_info['amount'],
        ));

        if ($notify_result === false){
            $this->show_warning($payment->get_error());
            return;
        } */

        //写入充值金额 //测试使用，生产环境下安全期间放在被动通知里
         //if(!$this->_change_money($bill_info)){
         //    $this->show_warning('充值失败');
         //    return;
        // }
		
		//获得用户余额
		$m = m('member');
		$memberinfo = $m->get($bill_info['member_id']);
        
        /* 只有支付时会使用到return_url，所以这里显示的信息是支付成功的提示信息 */
        $this->_curlocal(LANG::get('pay_successed'));
        $this->assign('money', $memberinfo['money']);
        $this->assign('payment', $payment_info);
        $this->display('paynotify.recharge.html');
    }
    
    /**
     * 通知地址
     */
    function accountnotify(){
        //file_put_contents(ROOT_PATH.'/data/sin.txt', serialize($_REQUEST));
        
        $args = $this->get_params();
  
        $pay_sn   = isset($args[0]) ? trim($args[0]) : 0; //支付单
    
        if (!$pay_sn){
            /* 无效的通知请求 */
			echo '无效的通知请求';
            return;
        }
        $model_paybill =& m("paymentbills");

        $bill_info = $model_paybill->get("payment_sn='{$pay_sn}'");

        if (empty($bill_info))
        {
            /* 没有该支付单 */
			echo '没有该支付单';
            return;
        }

        $model_payment =& m('payment');

        $payment_info  = $model_payment->get("payment_code='{$bill_info['pay_code']}'");
        if (empty($payment_info))
        {
            /* 没有指定的支付方式 */
            $this->show_warning('no_such_payment');
            return;;
        }
        
        $payment = $this->_get_payment($bill_info['pay_code'], $payment_info);
    
        /* 获取验证结果 
        $notify_result = $payment->verify_notify(array(
            'out_trade_sn' => $bill_info['payment_sn'],
            'order_amount' => $bill_info['amount'],
        ));
 
        if ($notify_result === false)
        {
            
            $payment->verify_result(false);
            return;
        }*/
 
        if(!$this->_change_money($bill_info)){
            $payment->verify_result(false);
            return;
        }
        
        $payment->verify_result(true);
    }
    
    //=========================== 充值 End
    
    /**
     *    充值金额改变
     *
     *    @author    Ruesin
     */
    function _change_money($info = array()) {
		//$info['member_id'] = 1263;
		//$info['amount']    = 20;
		//$info["pay_name"]='移动支付吧';	
		$mod_member  = &m('member');
		$transaction = $mod_member->beginTransaction();
		$res = $mod_member->recharge_money($info);  	
		if ($res['code']) {
			$mod_member->rollback();
			return false;
		}
		$mod_member->commit($transaction);
		return true;
    }

    /**
     *    改变订单状态
     *
     *    @author    yhao.bai
     *    @param     int $order_id
     *    @param     string $order_type
     *    @param     array  $notify_result
     *    @return    void
     */
    function _change_order_status($order_id, $order_type = "normal", $notify_result)
    {
        /* 将验证结果传递给订单类型处理 */
        $order_type  =& ot($order_type);
        $order_type->respond_notify($order_id, $notify_result);    //响应通知
        
    }
	
	//=========================== 购买酷卡、酷币 START-================
	
	/**
     * 返回地址
     */
    function kuka(){
		$args = $this->get_params();
        $order_sn   = isset($args[0]) ? trim($args[0]) : ''; 
        
        if (!$order_sn){
            echo 'fail';
            return;
        }
        
        $mOd = &m('order');
        $order_info = $order = $mOd->get("order_sn = '{$order_sn}' ");
        
        if (empty($order_info)){
            echo 'fail';
            return;
        }
		
		if ($order_info['status'] != ORDER_PENDING){
            echo 'fail';
            return;
        }
    
        $model_paybill =& m("paymentbills");
    
        /* 获取支付单信息 */
        $bill_info = $model_paybill->get("order_sn='{$order_sn}'");
    
        if (empty($bill_info))
        {
            /* 没有该支付单 */
            $this->show_warning('forbidden');
            return;
        }
    
        $model_payment =& m('payment');
    
        $payment_info  = $model_payment->get("payment_code='{$bill_info['pay_code']}'");
        if (empty($payment_info))
        {
            /* 没有指定的支付方式 */
            $this->show_warning('no_such_payment');
            return;
        }
    
        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($bill_info['pay_code'], $payment_info);
         
        /* 获取验证结果 */
        $notify_result = $payment->verify_notify(array(
			'out_trade_sn' => $bill_info['payment_sn'],
			'order_amount' => $bill_info['amount'],
        ));

        if ($notify_result === false){
            /* 支付失败 */
            $this->show_warning($payment->get_error());
            return;
        }
		
        //==============测试使用，生产环境下安全期间放在被动通知里================
        $changeRes = $this->_change_order_status($order_info['order_id'], $order_info['extension'], $notify_result);
	
	/* 	$bill_info = array(
			'member_id' => $this->visitor->get("user_id"),
			'type'      => $order_info['extension'],
			'order_id'  => $order_info['order_id'],
		);

		if(!$this->_change_coin($bill_info)){
			$payment->verify_result(false);
			return;
		} */
		//==============测试使用，生产环境下安全期间放在被动通知里================ 
		
		$this->assign('order',$order_info);

		//获得用户积分
		$m = m('member');
		$memberinfo = $m->get($bill_info['member_id']);
		
		/* 只有支付时会使用到return_url，所以这里显示的信息是支付成功的提示信息 */
		$this->_curlocal(LANG::get('pay_successed'));
		$this->assign('coin', $memberinfo['coin']);
		$this->assign('payment', $payment_info);
		$this->display('kuka/payment/success.html');


    }
    
    /**
     * 通知地址
     */
    function kukanotify(){
    
		$args = $this->get_params();
        $order_sn   = isset($args[0]) ? trim($args[0]) : ''; 
        
        if (!$order_sn){
            //echo 'success';
            echo 'fail';
            return;
        }
        
        $mOd = &m('order');
        $order_info = $order = $mOd->get("order_sn = '{$order_sn}' ");
     
        if (empty($order_info)){
            echo 'fail';
            return;
        }
     	
		if ($order_info['status'] != ORDER_PENDING){
            echo 'fail';
            return;
        }

        $model_paybill =& m("paymentbills");
    
        /* 获取支付单信息 */
        $bill_info = $model_paybill->get("order_sn='{$order_sn}'");

        if (empty($bill_info))
        {
            /* 没有该支付单 */
			echo '没有该支付单';
            return;
        }

        $model_payment =& m('payment');
  
        $payment_info  = $model_payment->get("payment_code='{$bill_info['pay_code']}'");
        if (empty($payment_info))
        {
            /* 没有指定的支付方式 */
            $this->show_warning('no_such_payment');
            return;
        }

        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($bill_info['pay_code'], $payment_info);
         
        /* 获取验证结果 */
        $notify_result = $payment->verify_notify(array(
			'out_trade_sn' => $bill_info['payment_sn'],
			'order_amount' => $bill_info['amount'],
        ));
	     		
		//改变订单状态
        $changeRes = $this->_change_order_status($order_info['order_id'], $order_info['extension'], $notify_result);
	    
        $payment->verify_result(true);
    }
    
    /*返修费用的 通知地址    auth  tangsj*/
    function fxanotify(){
        
        $args = $this->get_params();
        $order_sn   = isset($args[0]) ? trim($args[0]) : '';
        
        if (!$order_sn){
            //echo 'success';
            echo 'fail';
            return;
        }
        
        $mOd = &m('orderserve');
        $order_info = $order = $mOd->get("order_sn = '{$order_sn}' ");
         
        if (empty($order_info)){
            echo 'fail';
            return;
        }
        
        $model_paybill =& m("paymentbills");
    
        /* 获取支付单信息 */
        $bill_info = $model_paybill->get("order_sn='{$order_info['order_id']}'");

        if (empty($bill_info))
        {
            /* 没有该支付单 */
			echo '没有该支付单';
            return;
        }

        $model_payment =& m('payment');
  
        $payment_info  = $model_payment->get("payment_id='{$order_info['payment_id']}'");
        if (empty($payment_info))
        {
            /* 没有指定的支付方式 */
            $this->show_warning('no_such_payment');
            return;
        }
        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($bill_info['pay_code'], $payment_info);
        
        //改变订单状态
        $changeRes = $this->_change_order_status($order_info['order_id'], 'fx', array("target" => 7));
         
        $payment->verify_result(true);
        
        
    }
    
    function fx(){
        
        $args = $this->get_params();
        $order_sn   = isset($args[0]) ? trim($args[0]) : '';
        
        if (!$order_sn){
            echo 'fail';
            return;
        }
        
        $mOd = &m('orderserve');
        $order_info = $order = $mOd->get("order_sn = '{$order_sn}' ");
        
        if (empty($order_info)){
            echo 'fail';
            return;
        }
        
        $model_payment =& m('payment');
        
        $payment_info  = $model_payment->get("payment_id='{$order_info['payment_id']}'");
        if (empty($payment_info))
        {
            /* 没有指定的支付方式 */
            $this->show_warning('no_such_payment');
            return;
        }
        
        //==============测试使用，生产环境下安全期间放在被动通知里================
        
        $this->assign('order',$order_info);
       
        /* 只有支付时会使用到return_url，所以这里显示的信息是支付成功的提示信息 */
        $this->_curlocal(LANG::get('pay_successed'));
     
        $this->assign('payment', $payment_info);
        $this->display('kuka/payment/success.html');
        
    }
	/**
     *    购买酷卡、酷币修改酷币值
     *
     *    @author    Ruesin
     */
    function _change_coin($info = array()) {
	
		$mod_member  = &m('member');
		$transaction = $mod_member->beginTransaction();
		$res = $mod_member->recharge_coin($info);  	
		if ($res['code']) {
			$mod_member->rollback();
			return false;
		}
		$mod_member->commit($transaction);
		return true;
    }
    //=========================== 购买酷卡、酷币 End-================
	
}

?>
