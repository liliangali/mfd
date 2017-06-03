<?php

/**
 *   支付中心
 *
 *    @author    yhao.bai
 */
class PaycenterApp extends PaycenterbaseApp
{
    var $_mod_pay;
    var $_mod_order;
    var $_store;
    function __construct()
    {
        parent::__construct();
        $this->_mod_pay = &m("payment");
        $this->_mod_order = &m('order');
        $this->_store = $this->visitor->get('has_store');
    }
    /**
     *    根据提供的订单信息进行支付
     *
     *    @author    yhao.bai
     *    @param    none
     *    @return    void
     */
    function index()
    {
    	/* 外部提供订单号 */
    	$args = $this->get_params();
        $order_sn = !empty($_GET) ? key($_GET) : 0;
        
        if (!$order_sn)
        {
            $this->show_warning('no_such_order');
            return;
        }
        
        /* 内部根据订单号收银,获取收多少钱，使用哪个支付接口 */
         $order = $this->_get_order($order_sn);
         
         if(!$order) return;
         
         //$this->_check_payment($order);

         $payments = $this->payments();
         $this->assign('payments',$payments);
         $this->assign('order', $order);

         $this->assign('has_store',intval($this->_store));
         $this->assign('obj', "order");
         $this->display('paycenter/paycenter.payform.html');
    }
    
	/**
	 * @author yhao.bai
	 * @return void
	 */
    function goToPay(){
    	/**
    	$order_type =& ot('normal');
    	$order_type->respond_notify(1417, array('target' => ORDER_ACCEPTED));
    	die();
    	*/

    	$obj      = isset($_POST['obj']) ? $_POST['obj'] : '';
        $order_sn = isset($_POST['os']) ?  trim($_POST['os']) : '';
        
    	if(!in_array($obj,array("order"))){
    		$this->show_warning("支付对象错误");
    		return;
    	}
 
    	if (!$order_sn){
    		$this->show_warning('no_such_order');
    		return;
    	}
    	
    	$order = $this->_get_order($order_sn);

    	if(!$order) return;

    	//$this->_check_payment($order);
    	
    	$payment_model =& m('payment');
    	
    	/* 验证支付方式是否可用，若不在白名单中，则不允许使用 */
    	if (!$payment_model->in_white_list($order['payment_code']))
    	{
    		$this->show_warning('payment_disabled_by_system');
    		return;
    	}
    	
    	$payment_info  = $payment_model->get("payment_code = '{$order['payment_code']}'");
    	 
    	/* 没有启用，则不允许使用 */
    	if (!$payment_info['enabled'])
    	{
    		$this->show_warning('payment_disabled');
    		return;
    	}

    	/* 生成支付URL或表单 */
    	$payment    = $this->_get_payment($order['payment_code'], $payment_info);
    
    	$res = $payment->createBill($order,$this->_store);
    	 
    	if(!$res){
    		$this->show_warning('意外错误无法支付，请联系客服！');
    		return;
    	}
    	
    	$payment_form = $payment->get_payform($order);
    	$this->assign('payform', $payment_form);
    	$this->assign('payment', $payment_info);
    	header('Content-Type:text/html;charset=' . CHARSET);
    	$this->display('/paycenter/paycenter.dopay.html');
    }
    
    /**
     * 支付校验
     * 
     * @version 1.0.0 (Jan 20, 2015)
     * @author Ruesin
     */
    function _check_payment(&$order){
        
//         if(!$order['kh_payment_id'] || !$order['payment_id']){
//             $this->show_warning('您的订单存在异常，请联系管理员！');
//             return;
//             die();
//         }

        if($this->_store){
            if($order['status'] != ORDER_PENDING ){
                $this->show_warning('该订单不是待支付状态，请检查订单状态！');
                die();
            }
        }else{
            if($order['kh_ship_pay'] == 2){
                if($order['status'] != STORE_SHIPPED ){
                    $this->show_warning('订单不是待支付状态，请您联系裁缝！');
                    die();
                }
            }else{
                if($order['status'] != STORE_ACCEPTED ){
                    $this->show_warning('订单不是待支付状态，请您联系裁缝！');
                    die();
                }
            }
            	
            //为了后面的各个支付方式不做太大修改，用户支付时把order变量更改一下。
            $order['order_amount'] = $order['kh_order_amount'];
            $order['payment_id']   = $order['kh_payment_id'];
            $order['payment_name'] = $order['kh_payment_name'];
            $order['payment_code'] = $order['kh_payment_code'];
        }
    }
    
    /**
     * 支付方式列表
     * 
     * @version 1.0.0 (2014-12-31)
     * @author Ruesin
     */
    function payments(){
        return $this->_mod_pay->find(array(
                'conditions' => "enabled=1 AND ismobile = 0",
                'order'      => "sort_order DESC"
        ));
    }
    /**
     * Ajax 更改订单支付方式
     * 
     * @version 1.0.0 (2014-12-31)
     * @author Ruesin
     */
    function change_payment(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $sn = isset($_POST['sn']) ?  trim($_POST['sn']) : '';
        if(!$id || !$sn){
            $this->json_error('params_error');
            return ;
        }
        $payment = $this->_mod_pay->get(" payment_id = '{$id}' AND enabled=1 AND ismobile = 0");
        if(empty($payment)){
            $this->json_error('payment_error');
            return ;
        }

        if (!$this->_mod_pay->in_white_list($payment['payment_code']))
        {
            $this->json_error('payment_disabled_by_system');
            return;
        }
        if($this->_store){
            $eData = array(
                    'payment_id'   => $payment['payment_id'],
                    'payment_code' => $payment['payment_code'],
                    'payment_name' => $payment['payment_name']
            );
            $edit = $this->_mod_order->edit("order_sn = '{$sn}' AND store_id = '{$this->visitor->get('user_id')}' AND status = '".ORDER_PENDING."'" ,$eData);
        }else{
            $eData = array(
                    'kh_payment_id'   => $payment['payment_id'],
                    'kh_payment_code' => $payment['payment_code'],
                    'kh_payment_name' => $payment['payment_name']
            );
            $order = $this->_mod_order->get("order_sn = '{$sn}' AND user_id = '{$this->visitor->get('user_id')}'");
            if($order['kh_ship_pay'] == 2){
                $edit = $this->_mod_order->edit("order_sn = '{$sn}' AND user_id = '{$this->visitor->get('user_id')}' AND status = '".STORE_SHIPPED."' " ,$eData);
            }else {
                $edit = $this->_mod_order->edit("order_sn = '{$sn}' AND user_id = '{$this->visitor->get('user_id')}' AND status = '".STORE_ACCEPTED."' " ,$eData);
            }
        }
        
        if($edit >= 0){
            $this->json_result(array('nm'=>$payment['payment_name']));
            return ;
        }else{
            $this->json_error('change_payment_error');
            return ;
        }
    }
}

