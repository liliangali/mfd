<?php
use Cyteam\Shop\Type;
use Cyteam\Shop\Type\Types;
class PaynotifyApp extends PaycenterbaseApp
{
    public $_mod_order;
    function __construct()
    {
        parent::__construct();
        $this->_mod_order =& m('order');
    }
    public function weixin(){
        
        $args = $this->get_params();
        $order_sn   = isset($args[0]) ? trim($args[0]) : ''; //订单id
        if (!$order_sn)
        {
            die('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>');
        }
        $order_info  = $this->_mod_order->get(" order_sn = '{$order_sn}' ");
        
        if (empty($order_info))
        {
            die('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>');
        }
        if ($order_info['payment_code'] != 'weixin'){
            die('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>');
        }
        $model_payment =&m('payment');
        $payment_info = $model_payment->get("payment_code='{$order_info['payment_code']}'");
        if (empty($payment_info))
        {
            die('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>');
        }
        
        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($order_info['payment_code'], $payment_info);

        $mPayBill = &m('paymentbills');
        $order_info['out_trade_sns'] = $mPayBill->find("order_sn = '{$order_sn}'");  //member_id pay_id
        
        /* 获取验证结果 */
        $notify_result = $payment->verify_notify($order_info);
        
        if ($notify_result === false)
        {
            $payment->verify_result(false);
            return;
        }
        
        $order_info['target']     = $notify_result['target'];
        
        $order_type  =& ot($order_info['extension']);
        $status = $order_type->respond_notify($order_info);
        
        if ($status === false){
            $payment->verify_result(false);
            return;
        }
        $order_info['newStatus'] = $status;
        $payment->verify_result(true);
        $this->lastSomeThing($order_info);
    }
    
    /**
     * 只是显示订单支付成功消息，并不做任何处理。
     *
     * @date 2015-10-22 下午2:40:23
     * @author Ruesin
     */
    function index()
    {
//        ShopCommon::recordLogs(1111);
    	$args = $this->get_params();
        $order_sn   = isset($args[0]) ? trim($args[0]) : 0;
        if (!$order_sn){
            $this->show_warning('订单号错误!');
            return;
        }
        
        $order_info  = $this->_mod_order->get(" order_sn = '{$order_sn}' ");
        
        if (empty($order_info)){
            $this->show_warning('订单不存在!');
            return;
        }
         
        $this->_curlocal(LANG::get('pay_successed'));
        $this->assign('order', $order_info);
        $this->display('paynotify/index.html');
        die();
        $model_payment =& m('payment');
        $payment_info  = $model_payment->get("payment_code='{$order_info['payment_code']}'");
        if (empty($payment_info))
        {
            $this->show_warning('no_such_payment');
            return;
        }

        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($order_info['payment_code'], $payment_info);
     
        $mPayBill = &m('paymentbills');
        $order_info['out_trade_sns'] = $mPayBill->find("order_sn = '{$order_sn}'");  //member_id pay_id
        
        /* 获取验证结果 */
        $notify_result = $payment->verify_notify($order_info);
        
        if ($notify_result === false)
        {
            /* 支付失败 */
            $this->show_warning($payment->get_error());
            return;
        }
        $notify_result['order_sn'] = $order_info['order_sn'];
		
        $this->_curlocal(LANG::get('pay_successed'));
        $this->assign('notify',$notify_result);
        $this->assign('order', $order_info);
        $this->assign('payment', $payment_info);
        $this->display('paynotify/index.html');
    }
    
    function test(){
        
        $str = file_get_contents(ROOT_PATH.'/upload/order/diy/sin.txt');
        echo $str;
        echo '<pre>';
        print_r(unserialize($str));
        exit;
    }
    
    /**
     * 支付宝异步通知
     * 
     * @date 2015-10-22 下午2:50:36
     * @author Ruesin
     */
    function notify()
    {

		$args = $this->get_params();
        $order_sn   = isset($args[0]) ? trim($args[0]) : ''; //订单id
        if (!$order_sn)
        {
            $this->show_warning('forbidden');
            return;
        }
        /* 获取订单信息 */
        $order_info  = $this->_mod_order->get(" order_sn = '{$order_sn}' ");
        if (empty($order_info))
        {
            $this->show_warning('forbidden');
            return;
        }
        if ($order_info['status'] != ORDER_PENDING)
        {
            $this->show_warning('待付款订单才可支付');
            return;
        }
        $model_payment =&m('payment');
        $payment_info = $model_payment->get("payment_code='{$order_info['payment_code']}'");
        if (empty($payment_info))
        {
            $this->show_warning('no_such_payment');
            return;
        }
        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($order_info['payment_code'], $payment_info);
        $mPayBill = &m('paymentbills');
        $order_info['out_trade_sns'] = $mPayBill->find("order_sn = '{$order_sn}'");  //member_id pay_id
        /* 获取验证结果 */
        $notify_result = $order_info['payment_code'] == 'malipay' ? $payment->veryfy_malipay($order_info) : $payment->verify_notify($order_info, true);
        /* 记录log 抓被动请求 */
//        if (file_exists('/var/www/html/project.mfdplatform/includes/payments/upop/logs/')){
////             error_log(print_r($payment->get_error(), 1),3,'/var/www/html/project.mfdplatform/includes/payments/upop/logs/errors.log');
//        }
        
        if ($notify_result === false)
        {
            $payment->verify_result(false);
            return;
        }
        $order_info['target']     = $notify_result['target'];
        
        //改变订单状态
        $order_type  =& ot($order_info['extension']);
        $status = $order_type->respond_notify($order_info);
        
        if ($status === false){
            $payment->verify_result(false);
            return;
        }
        $order_info['newStatus'] = $status;
        /* 记录log 抓被动请求 */
//        if (file_exists('/var/www/html/project.mfdplatform/includes/payments/upop/logs/')){
////            error_log(print_r('交易成功!', 1),3,'/var/www/html/project.mfdplatform/includes/payments/upop/logs/errors.log');
//        }
        $payment->verify_result(true);
        $this->lastSomeThing($order_info);
    }
    
    //=========================== 充值 Bgn
    
     /**
     * 返回地址
     */
    function accountnotify(){ 
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
        $mod_member = m('member');
        $transaction = $mod_member->beginTransaction();
        /* 获取支付单信息 */
        $bill_info = $model_paybill->get("payment_sn='{$pay_sn}'");
    
        if (empty($bill_info))
        { 
            $mod_member->rollback();
            echo 'fail'; 
            /* 没有该支付单 */
            $this->show_warning('forbidden');
            return;
        }
        if ($bill_info['status']) 
        {
            $mod_member->rollback();
            echo 'fail';
            return;
        }
        $model_payment =& m('payment');
    
        $payment_info  = $model_payment->get("payment_code='{$bill_info['pay_code']}'");
        if (empty($payment_info))
        {
            $mod_member->rollback();
            /* 没有指定的支付方式 */
            $this->show_warning('no_such_payment');
            return;
        }
  
        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($bill_info['pay_code'], $payment_info);
         
        
        $notify_result = $payment->verify_notify(array(
                'out_trade_sns' => $bill_info['payment_sn'],
                'out_trade_sn'  => $bill_info['payment_sn'],
                'final_amount'   => $bill_info['amount'],
                'order_amount'   => $bill_info['amount'],
                'user_id' => $bill_info['member_id'],
                
        ));

        if ($notify_result === false){
            $mod_member->rollback();
            $this->show_warning($payment->get_error());
            return;
        }
		//获得用户余额
		if(!$this->_change_money($bill_info)){
		    $mod_member->rollback();
            $payment->verify_result(false);
            return;
        }
        $mod_member->commit($transaction);
        $payment->verify_result(true);
        /* 只有支付时会使用到return_url，所以这里显示的信息是支付成功的提示信息 */
        
    }
    
    /**
     * 通知地址
     */
    function account(){
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
 
        /* if(!$this->_change_money($bill_info)){
            $payment->verify_result(false);
            return;
        } */

        $m = m('member');
        $memberinfo = $m->get($bill_info['member_id']);
        $this->_curlocal(LANG::get('pay_successed'));
        $this->assign('money', $memberinfo['money']);
        $this->assign('payment', $payment_info);
        $this->display('paynotify.recharge.html');
    }
    
    public function weixin_account(){
    
        $args = $this->get_params();
        $order_sn   = isset($args[0]) ? trim($args[0]) : ''; //订单id
        if (!$order_sn)
        {
            die('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>');
        }
        
        $mod_bills = &m("paymentbills");
        $mod_member = m('member');
        $transaction = $mod_member->beginTransaction();
        $payment_info  = $mod_bills->get("payment_sn = '{$order_sn}' ");
    
        if (empty($payment_info))
        {
            $mod_member->rollback();
            die('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>');
        }
        if ($payment_info['pay_code'] != 'weixin'){
            $mod_member->rollback();
            die('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>');
        }
        if ($payment_info['status']) 
        {
            $mod_member->rollback();
            die('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>');
        }
        /* $model_payment =&m('payment');
        $payment_info = $model_payment->get("payment_code='{$order_info['payment_code']}'");
        if (empty($payment_info))
        {
            die('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>');
        } */

        /* 调用相应的支付方式 */
        $payment = $this->_get_payment($payment_info['pay_code'], $payment_info);
    
        /* $mPayBill = &m('paymentbills');
        $order_info['out_trade_sns'] = $mPayBill->find("order_sn = '{$order_sn}'");  //member_id pay_id */
        $payment_info['out_trade_sn'] = $order_sn;
        $payment_info['final_amount'] = $payment_info['amount'];
        /* 获取验证结果 */
        $notify_result = $payment->verify_notify($payment_info);
    
        if ($notify_result === false)
        {
            $mod_member->rollback();
            $payment->verify_result(false);
            return;
        }
      
        //获得用户余额
        if(!$this->_change_money($payment_info)){
            $mod_member->rollback();
            $payment->verify_result(false);
            return;
        }
        $mod_member->commit($transaction);
        $payment->verify_result(true);
        /* 只有支付时会使用到return_url，所以这里显示的信息是支付成功的提示信息 */
        $m = m('member');
        $memberinfo = $m->get($bill_info['member_id']);
        $this->_curlocal(LANG::get('pay_successed'));
        $this->assign('money', $memberinfo['money']);
        $this->assign('payment', $payment_info);
        $this->display('paynotify.recharge.html');
        /* $notify_result['order_sn'] = $order_info['order_sn'];
        $changeRes = $this->_change_order_status($order_info['order_id'], $order_info['extension'], $notify_result);
    
        if ($changeRes){
            $payment->verify_result(true);
            $this->lastSomeThing($order_info,$notify_result);
        } */
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
		
		$res = $mod_member->recharge_money($info);  
		if ($res['code']) {
			return false;
		}
		
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
        return $order_type->respond_notify($order_id, $notify_result);    //响应通知
        
    }
    
    /**
     * 支付成功后各种操作
     * 
     * @date 2015-10-30 上午9:28:30
     * @author Ruesin
     */
    public function lastSomeThing($order_info){
    
         //支付订单写入电商仓库
            //获得订单下的商品
		$ordergoods_mod=m('ordergoods');
		   $region_mod=m('region');
		   $goods_mod=m('goods');
		   $orders_mod=m('order');
           $order_goods=$ordergoods_mod->find("order_id='{$order_info['order_id']}'");
		 
           $regions=$region_mod->find('1=1');
		    $region='';
		   if($regions){		    
			   foreach($regions as $k1=>$v1){
				   $region[$v1['region_id']]=$v1['region_name'];
				   
			   }
		   }
		
           if($order_goods){
               $ins='0';
              
               foreach($order_goods as $key=>$val){
                   if($val['type']=='fdiy'){
                       $ins +=1;
                   }
				  
		$params=json_decode($val['params'],true); 
	    $bns=$params['oProducts']['goods_id'];
		$goodsn=$goods_mod->get("goods_id='{$bns}'");
		
		if($goodsn['bn']){
			$order_goods[$key]['goodsn']=$goodsn['bn'];	  
	
		}else{
			$order_goods[$key]['goodsn']='';
	
		}
               }
           }
	
          if($ins==0){
               require './cron/edbdemo1.php';
               $cl=new EdbDemo();
			     if($order_info['ship_area_id']){
	               $order_region=explode(',' , $order_info['ship_area_id']);
				    $region1=$region[$order_region[0]]?$region[$order_region[0]]:0;
				   $region2=$region[$order_region[1]]?$region[$order_region[1]]:0;
				   $region3=$region[$order_region[2]]?$region[$order_region[2]]:0;
                   }
				  
               $rey=$cl->edbTradeAdd($order_info,$num,$order_goods,$region1,$region2,$region3); //添加订单
			
			   if($rey){
                $reys=json_decode($rey, true);	
				
					 foreach($reys as $key=>$row){
					if($row['items']['item'][0]['is_success']=='True'){
						
						  $edData = array(
							'mes_status'   =>1,
							);
						
						  $orders_mod->edit("order_sn = '{$order_info['order_sn']}'" ,$edData);
			
						
					}else{
							$edData = array(
							'mes_status'   =>2,
							);
					
					     $orders_mod->edit("order_sn = '{$order_info['order_sn']}'" ,$edData);
			
					}

				   }
				  
			   }
           }

        $orderLogs[] = [
                'order_id' => $order_info['order_id'],
                'op_id'    => $order_info['user_id'],
                'op_name'  => $order_info['user_name'],
                'from'     => $order_info['status'],
                'to'       => $order_info['newStatus'],
                'behavior' => 'payment',
                'remark'   => '在线支付成功，支付单号为:'.$order_info['out_trade_sn'],
        ];
        ShopCommon::recordLogs($orderLogs);
        ShopCommon::recordFinance($order_info);
        ShopCommon::give_debit($order_info['final_amount'],$order_info['user_id']);
        ShopCommon::f_goods($order_info);

        //===== 执行成功 推送mes       =====
        $goods = Types::createObj("fdiy");
        $res = $goods->mesf($order_info['order_id']);
        /* 订单满额 送券 接口  $amount : 订单总额  $user_id:user_id */
//        give_debit($order_info['final_amount'],$order_info['user_id']);
 		//支付成功短信通知
 		fukuan($order_info['order_id']);
		
		
     
	}

    /*返修费用的 通知地址    auther  tangsj*/
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
    
    /*返修费用的 主动请求   auther  tangsj*/
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
        $this->display('fxfee/paycenter/success.html');
    
    }
}
