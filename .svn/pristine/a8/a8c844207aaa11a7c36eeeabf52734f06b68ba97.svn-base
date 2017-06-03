<?php
/**
 * 支付响应 新版
 *
 * @author Ruesin
 */
class PaynotifysApp extends PaycenterbaseApp
{
    /**
     * 支付完成后返回的URL
     * 
     * @author Ruesin
     */
    function index(){

        //$this->_user_id = $_SESSION['user_info']['user_id'];
    	$args = $this->get_params();
        $sn   = isset($args[0]) ? trim($args[0]) : '';
        if (!$sn){
			$this->assign('error_msg', '无效的通知请求');
			$this->display('orders/error.html');
			return false;
        }
        $mOd = &m('order');
        $order = $order = $mOd->get("order_sn = '{$sn}' "); //AND user_id = '{$this->_user_id}'  //坑爹的回跳没session了，也懒得在url里加参数了，直接调取吧，反正也没有什么操作。
        if (empty($order)){
			$this->assign('error_msg', '没有该订单');
			$this->display('orders/error.html');
			return false;
        }
        
        $model_payment =& m('payment');
        
        $payment_info  = $model_payment->get("payment_code='{$order['payment_code']}'");
        if (empty($payment_info)){
			$this->assign('error_msg', '没有指定的支付方式');
			$this->display('orders/error.html');
			return false;
        }

        /* 调用相应的支付方式 */
        $payment = $this->_get_paymentm($order['payment_code'], $payment_info);
     
        /* 获取验证结果 */
        //$notify_result = $payment->verify_notify($order_info);
        //手机支付宝主动请求和被动请求的参数不一样，验证方法也不一样，但是这个页面就是提示支付成功的，不对订单做任何操作，所以可以屏蔽掉
// 		$notify_result = $order['payment_code'] == 'malipay' ? $payment->veryfy_malipay($order) : $payment->verify_notify($order, true);
		
//         if ($notify_result === false){
// 			$this->assign('error_msg', '支付失败');
// 			$this->display('orders/error.html');
// 			return false;
//         }
        //$notify_result['user_name'] = $order['user_name'];
        #TODO 临时在此也改变订单状态为方便调试，实际发布时应把此段去掉，订单状态的改变以notify为准
        //$this->_change_order_status($order['order_id'], $order['extension'], $notify_result);
		
        
		//计算订单预计时间
        $del_data = strtotime('+10 day');
		if($order['has_measure']){
		    $mFg = &m('orderfigure');
		    $fData = $mFg->get("order_id = '{$order['order_id']}'");
		    if($fData['time']){
		        $del_data = strtotime($fData['time'])+864000;
		    }
		}
		
		
        /* 只有支付时会使用到return_url，所以这里显示的信息是支付成功的提示信息 */
        /*  支付完成之后 判断 有无实名认证 过*/
        $auth_mod = &m('auth');
        $authinfo = $auth_mod->get("user_id ='{$order['user_id']}'");
        $this->_curlocal(LANG::get('pay_successed'));
        $this->assign('notify',$notify_result);
        $this->assign('order', $order);
        $this->assign('payment', $payment_info);
		$this->assign('del_data', $del_data);
        $this->assign('auth_id', $authinfo['id']);
        $this->display('orders/payment/success.html');
    }
    
    /**
     * 支付成功响应地址
     * 
     * @author Ruesin
     */
    function notify(){
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
        
        $mPayBill = &m('paymentbills');
        $order_info['out_trade_sns'] = $mPayBill->find("order_sn = '{$order_sn}'");  //member_id pay_id
        
        $model_payment =& m('payment');
        
        $payment_info  = $model_payment->get("payment_code='{$order_info['payment_code']}'");
        if (empty($payment_info)){
            echo 'fail';
            return;
        }

        $payment = $this->_get_paymentm($order_info['payment_code'], $payment_info);

        $notify_result = $order_info['payment_code'] == 'malipay' ? $payment->veryfy_malipay($order_info) : $payment->verify_notify($order_info, true);

        if ($notify_result === false){
            $payment->verify_result(false);
            return;
        }

        $notify_result['user_name'] = $order_info['user_name'];
        $notify_result['order_sn'] = $order_info['order_sn'];
        //改变订单状态
        $changeRes = $this->_change_order_status($order_info['order_id'], $order_info['extension'], $notify_result);
        
        if ($changeRes){
            $payment->verify_result(true);
            
            $this->lastSomeThing($order_info['order_id']);
            
            imports("orderLogs.lib");
            $oLogs = new OrderLogs();
            $oLogs->_record(array(
                    'order_id' => $order_info['order_id'],
                    'op_id'    => $order_info['user_id'],
                    'op_name'  => $order_info['user_name'],
                    'behavior' => 'payment',
                    'remark'   => '在线支付成功，支付单号为:'.$order_info['out_trade_sn'],
            ));
        }
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
        return $order_type->respond_notify($order_id, $notify_result);    //响应通知
        
    }
    
    public function lastSomeThing($order_id = ''){
        
        $mFom = &m('figureorderm');
        $fom = $mFom->get("order_id = '{$order_id}'");
        if($fom && $fom['server_id']){
            
            $mServer = &m('serve');
            $sv = $mServer->get($fom['server_id']);
            
            $mMem = &m('member');
            $dz = $mMem->get($sv['userid']);

            if($dz['user_token']){
                //量体订单提交之后发消息
                include(ROOT_PATH . '/includes/xinge/xinggemeg.php');
                $push = new XingeMeg();
                //$noon = array('am'=>'上午','pm'=>'下午');
                //$msg = "尊敬的客户，您预约{$fom['time']} {$noon[$fom['time_noon']]}到{$sv['serve_name']}量体，请做好出行安排，店铺地址：{$sv['serve_address']}，联系电话：{$sv['store_mobile']}【麦富迪】";
                $push->toMasterXinApp($dz['user_token'], '【mfd】预约量体提醒', '你有新的订单需要指派，订单号 - '.$fom['order_sn'], array('url_type'=>'figure', 'location_id'=>$fom['id']));
                //$push->toMasterXinApp($dz['user_token'], '【mfd】预约量体提醒', $msg, array('url_type'=>'figure', 'location_id'=>$fom['id']));
            }
            if($fom['phone'] || preg_match('/^1[34578][0-9]{9}$/', $fom['phone'])){
                $noon = array('am'=>'上午','pm'=>'下午');
                
                $sms_msg = "您预约".$fom['time'].$noon[$fom['time_noon']]."到".$sv['serve_name']."量体，地址：".$sv['serve_address']."，电话：".$sv['store_mobile'];
                $rs = SendSms($fom['phone'], $sms_msg);
                
                if($rs && $rs > 0){
                    $mFom->edit("order_sn= '{$fom['order_sn']}' AND has_sms = '0'" , array('has_sms' => '1'));
                }
            }
            
        }
    }
    
    
}



