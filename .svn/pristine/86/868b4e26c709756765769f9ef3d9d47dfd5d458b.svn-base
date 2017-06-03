<?php

!defined('ROOT_PATH') && exit('Forbidden');

/**
 *    支付方式基础类
 *
 *    @author    yhao.bai
 *    @usage    none
 */
class BasePayment extends Object
{ 
    /* 外部处理网关 */
    var $_gateway   = '';
    /* 支付方式唯一标识 */
    var $_code      = '';

    var $_mod_bills;

    var $_mod_order;
    
    var $out_trade;
    
    
    function __construct($payment_info = array())
    {
        $this->BasePayment($payment_info);
    }
    function BasePayment($payment_info = array())
    {
        $this->_info   = $payment_info;
        $this->_config = unserialize($payment_info['config']);
        $this->_mod_bills = &m("paymentbills");
        $this->_mod_order = &m("order");
    }

    /**
     *    获取支付表单
     *
     *    @author    yhao.bai
     *    @param     array $order_info
     *    @return    array
     */
    function get_payform()
    {
        return $this->_create_payform('POST');
    }

    /**
     *    获取规范的支付表单数据
     *
     *    @author    yhao.bai
     *    @param     string $method
     *    @param     array  $params
     *    @return    void
     */
    function _create_payform($method = '', $params = array())
    {
        return array(
            'online'    =>  $this->_info['is_online'],
            'desc'      =>  $this->_info['payment_desc'],
            'method'    =>  $method,
            'gateway'   =>  $this->_gateway,
            'params'    =>  $params,
        );
    }

    /**
     *    获取通知地址 被动请求
     *
     *    @author    yhao.bai
     *    @param     int $store_id
     *    @param     int $order_id
     *    @return    string
     */
    function _create_notify_url($order_id, $type="order")
    {
    	if($type == "order"){
        	return SITE_URL . "paynotify-notify-{$order_id}.html";
    	}elseif($type =="fx"){
    		return 'http://'.$_SERVER['SERVER_NAME']."/paynotify-fxanotify-{$order_id}.html";
    	}else{
    	    return 'http://'.$_SERVER['SERVER_NAME']."/paynotify-accountnotify-{$order_id}.html";
    	} 
    }
	
    
    /**
     *    获取返回地址 主动请求
     *
     *    @author    yhao.bai
     *    @param     int $store_id
     *    @param     int $order_id
     *    @return    string
     */
    function _create_return_url($order_id, $type="order")
    {
    	if($type == "order"){
    		return SITE_URL . "/paynotify-index-{$order_id}.html";
    	}elseif($type =="fx"){
    		return 'http://'.$_SERVER['SERVER_NAME']."/paynotify-fx-{$order_id}.html";
    	}else{
    	    return 'http://'.$_SERVER['SERVER_NAME']."/paynotify-account-{$order_id}.html";
    	}
        
    }

    /**
     *    获取外部交易号
     *
     *    @author    yhao.bai
     *    @return    string
     */
    function _get_trade_sn()
    {
        return $this->out_trade;
    }

    /**
     *    获取商品简介
     *
     *    @author    yhao.bai
     *    @param     array $order_info
     *    @return    string
     */
    function _get_subject($order_info)
    {
        return '麦富迪订单:' . $order_info['order_sn'];
    }

    /**
     *    获取通知信息
     *
     *    @author    yhao.bai
     *    @return    array
     */
    function _get_notify()
    {
        /* 如果有POST的数据，则认为POST的数据是通知内容 */
        if (!empty($_POST))
        {
            return $_POST;
        }

        /* 否则就认为是GET的 */
        return $_GET;
    }

    /**
     * 创建付款单 
     * 
     * @author yhao.bai
     * @param arr $order
     * @param arr $payment
     * @return bool
     */
    function createBill($order,$store = ''){
    	
    	$payment_sn = $this->get_payment_sn();
 
    	$bill = array(
    		'payment_sn' => $payment_sn,
    		'amount'     => $order['order_amount'],
    		'member_id'  => $store ? $order['store_id'] : $order['user_id'],
    		'account'    => $this->_config['alipay_account'],
    		'ip'         => real_ip(),
    		'start_time' => time(),
    		'order_sn'   => $order['order_sn'],
    		'pay_id'     => $order['payment_id'],
    		'pay_code'   => $order['payment_code'],
    		'pay_name'   => $order['payment_name'],
    	);
    	
    	$this->out_trade = $payment_sn;
    	
    	$res = $this->_mod_bills->add($bill);
    	if(!$res){
    		return false;
    	}
    	
    	$res = $this->_mod_order->edit("order_id='{$order['order_id']}'" ,array("out_trade_sn" => $payment_sn));
    	
//     	if($store){
//     	    $res = $this->_mod_order->edit("order_id='{$order['order_id']}'" ,array("out_trade_sn" => $payment_sn));
//     	}else{
//     	    $res = $this->_mod_order->edit("order_id='{$order['order_id']}'" ,array("kh_out_trade_sn" => $payment_sn));
//     	}
    	
    	if(!$res){
    		return false;
    	}
    	return true;
    	
    }
    
    function createBillForAccount($order){
         
        $payment_sn = $this->get_payment_sn();
    
        $bill = array(
                'payment_sn' => $payment_sn,
                'amount'     => $order['order_amount'],
                'member_id'  => $order['user_id'],
                'account'    => $this->_config['alipay_account'], //?
                'ip'         => real_ip(),
                'start_time' => gmtime(),
                'order_sn'   => "R001",
                'pay_id'     => $order['payment_id'],
                'pay_code'   => $order['payment_code'],
                'pay_name'   => $order['payment_name'],
        );
         
        $this->out_trade = $payment_sn;   ///外部交易号
         
        $res = $this->_mod_bills->add($bill);
        if(!$res){
            return false;
        }
        
        return $payment_sn;
    }
    
    
    /**
     * 创建付款单
     *
     * @author tangsj 返修费用
     * @param arr $order
     * @param arr $payment
     * @return bool
     */
    
    function createFxForAccount($order){
    
        $payment_sn = $this->get_payment_sn();
    
        $bill = array(
            'payment_sn' => $payment_sn,
            'amount'     => $order['order_amount'],
            'member_id'  => $order['user_id'],
            'account'    => $this->_config['alipay_account'], //?
            'ip'         => real_ip(),
            'start_time' => gmtime(),
            'order_sn'   => $order['order_id'],
            'pay_id'     => $order['payment_id'],
            'pay_code'   => $order['payment_code'],
            'pay_name'   => $order['payment_name'],
        );
    
    
        $this->out_trade = $payment_sn;   ///外部交易号
         
        $res = $this->_mod_bills->add($bill);
        if(!$res){
            return false;
        }
    
        return $payment_sn;
    
    }
    
    /**
     * 更新支付单
     * @author yhao.bai
     * @param string $payment_sn 支付单号
     * @param string $status 状态
     * @return boolean
     */
    function updateBill($payment_sn, $data){
    	  
    	$this->_mod_bills->edit("payment_sn='{$payment_sn}'", $data);
    }
    
	/**
     * 得到唯一的payment_sn
     * @author yhao.bai
     * @params null
     * @return string payment_sn
     */
    public function get_payment_sn(){
        $i = rand(0,9999);
        do{
            if(9999==$i){
                $i=0;
            }
            $i++;
            $payment_sn = time().str_pad($i,4,'0',STR_PAD_LEFT);
            $row = $this->_mod_bills->find("payment_sn='{$payment_sn}'");
        }while($row);
        return $payment_sn;
    }
    /**
     *    验证支付结果
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function verify_notify()
    {
        #TODO
    }

    /**
     *    将验证结果反馈给网关
     *
     *    @author    yhao.bai
     *    @param     bool   $result
     *    @return    void
     */
    function verify_result($result)
    {
        if ($result)
        {
            echo 'success';
        }
        else
        {
            echo 'fail';
        }
    }
}

?>