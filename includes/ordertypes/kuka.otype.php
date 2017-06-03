<?php

/**
 *    @author    yhao.bai
 */
class KukaOrder extends BaseOrder
{
    var $_name = 'kuka';
 
    var $_mod_order;
    var $_mod_orderextm;
    var $_mod_orderfigure;
    var $_mod_ordergoods;
    var $_mod_orderlog;
    var $_mod_ordercron;
    var $_mod_orderpmt;
    var $_errors;

    function __construct(){
        parent::__construct();
    	$this->_mod_order       = &m("order");
    	$this->_mod_orderextm   = &m('orderextm');
    	$this->_mod_orderfigure = &m('orderfigure');
    	$this->_mod_ordergoods  = &m('ordergoods');
    	$this->_mod_orderlog    = &m('orderlog');
    	$this->_mod_ordercron   = &m("ordercron");
    	$this->_mod_orderpmt    = &m("orderpmt");
    }
    
    /**
     *    查看订单
     *
     *    @author    yhao.bai
     *    @param     int $order_id
     *    @param     array $order_info
     *    @return    array
     */
    function get_order_detail($order_id, $order_info)
    {
    	if (!$order_id)
    	{
    		return array();
    	}
    
    	/* 获取商品列表 */
    	$data['goods_list'] =   $this->_get_goods_list($order_id);
    
    	/* 配送信息 */
    	$data['order_extm'] =   $this->_get_order_extm($order_id);
    
    	/* 支付方式信息 */
    	if ($order_info['payment_id'])
    	{
    		$payment_model      =& m('payment');
    		$payment_info       =  $payment_model->get("payment_id={$order_info['payment_id']}");
    		$data['payment_info']   =   $payment_info;
    	}
    
    	/* 订单操作日志 */
    	$data['order_logs'] =   $this->_get_order_logs($order_id);
    
    	return array('data' => $data);
    }  
    /**
     *    响应支付通知
     *
     *    @author    yhao.bai
     *    @param     int    $order_id
     *    @param     array  $notify_result
     *    @return    bool
     */
    function respond_notify($order_id, $notify_result)
    {
        $notify_result['target'] = STORE_ACCEPTED;
        $where = "order_id = '{$order_id}'";
        $data = array('status' => $notify_result['target']);
        $transaction = $this->_mod_order->beginTransaction();
        switch ($notify_result['target'])
        {
			case STORE_ACCEPTED:
        	    $where .= ' AND status=' . ORDER_PENDING;  //代付款状态
        	    $data['status'] = ORDER_ACCEPTED;       //状态直接进入已支付
    
        	    $data['pay_time']   =   gmtime();
    
        	    break;
        	case ORDER_ACCEPTED:
        	    $where .= ' AND status=' . ORDER_PENDING;  //代付款状态
        	    $data['status'] = ORDER_CHECKING;       //状态直接进入审核中
    
        	    $data['pay_time']   =   gmtime();
    
        	    break;
        	case ORDER_SHIPPED:
        	    $where .= ' AND status=' . ORDER_ACCEPTED;  //只有等待发货的订单才会被修改为已发货
        	    $data['ship_time']  =   gmtime();
        	    break;
        	case ORDER_FINISHED:
        	    $where .= ' AND status=' . STORE_SHIPPED;   //只有 品牌商 已发货的订单才会被自动修改为交易完成
        	    $data['finished_time'] = gmtime();
        	    break;
        	case ORDER_CANCLED:                             //任何情况下都可以关闭
        	    /* 加回商品库存 待定状态 */
        	    // $model_order->change_stock('+', $order_id);
        	    break;
        }
        
        $res = $this->_mod_order->edit($where, $data);
        if (!$res){
            $this->_mod_order->rollback();
            return false;
        }
        $this->_mod_order->commit($transaction);
    
        $mOd = &m('order');
        $order_info =  $mOd->get("order_id = '{$order_id}' ");

        if($notify_result['target'] == STORE_ACCEPTED){
        
            $bill_info = array(
                'member_id' => $order_info['user_id'],
                'type'      => $order_info['extension'],
                'order_id'  => $order_info['order_id'],
            );
            
             
            $this->_change_coin($bill_info);
        }
    
        return $res;
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
    
    
    function submit($data){
    	
    	extract($data);
    	$order_sn = $this->_gen_order_sn();
    	$total  = $data['total'];
		$_order = $data['_order'];
		$aData  = $data['kuka'];
		
    	if($total['order_amount'] <= 0){
    	    $order_status = ORDER_ACCEPTED; //已付款
    	}else{
    	    $order_status = ORDER_PENDING; //待付款
    	}

    	/* 订单基本信息 */
    	$baseinfo = array(
    	    'order_sn'       => $order_sn,
    	    'extension'      => $this->_name,
    	    'discount'       => 0,
    	    'goods_amount'   => $total['goods_amount'],
    	    'final_amount'   => $total['order_amount'],
    	    'order_amount'   => $total['goods_amount'],
    	    'coin'           => $total['coin'],
    	    'money_amount'   => $total['surplus'],
    	    'user_id'        => $_order['user']['user_id'],
    	    'user_name'      => $_order['user']['user_name'],
    	   // 'has_measure'    => $measure['has_measure'],
    	    'status'         => $order_status,
    	    'add_time'       => gmtime(),
    	    //'finished_time'  => '',
    	    'last_modified'  => gmtime(),
    	    //是否上门量体
    	    'measure'        => 0,
    	    'pay_time'       => $order_status == ORDER_ACCEPTED ? gmtime() : 0,
    	    'measure_fee'    => 0,
    	    //支付
    	    'payment_id'     => $_order['payment']['payment_id'],
    	    'payment_name'   => $_order['payment']['payment_name'],
    	    'payment_code'   => $_order['payment']['payment_code'],
    	    'ship_area'      => $_order['address']['region_name'],
    	    'ship_addr'      => $_order['address']['address'],
    	    'ship_name'      => $_order['address']['consignee'],
    	    'ship_zip'       => $_order['address']['zipcode'],
    	    'ship_mobile'    => $_order['address']['phone_mob'],
    	    'ship_tel'       => $_order['address']['phone_tel'],
    	    'ship_email'     => $_order['address']['email'],
    	    'shipping_id'    => $_order["shipping"]['shipping_id'],
    	    'shipping'       => $_order['shipping']['shipping_name'],
    	    'source_from'   => 'pc',
    	);
      
    	/* 保存订单信息 */
    	$order_id = $this->_mod_order->add($baseinfo);
    
    	if (!$order_id)
    	{
    		/* 保存基本信息失败 */
    		$this->_error('create_order_failed');
    
    		return false;
    	}
		
		//保存产品信息
    	$goods_items[] = array(
			'order_id'      =>  $order_id,
			'goods_id'      =>  $aData['id'],
			'goods_name'    =>  $aData['name'],
			'spec_id'       =>  0,
			'specification' =>  0,
			'price'         =>  $aData['price'],
			'subtotal'      =>  $aData['subtotal'],
			'quantity'      =>  $aData['num'],
			'goods_image'   =>  '',
        );
        
    	$res = $this->_mod_ordergoods->add(addslashes_deep($goods_items)); //防止二次注入
    
    	if (!$res)
    	{
    		/* 保存商品数据 */
    		$this->_error('create_order_failed');
    
    		return false;
    	}
	
    	return array('order_id' => $order_id, 'order_sn' => $baseinfo['order_sn'],'status' => $baseinfo['status'] );
    
    }
}

?>