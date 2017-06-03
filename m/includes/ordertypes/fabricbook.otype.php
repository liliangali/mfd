<?php

/**
 *    @author    yhao.bai
 */
class FabricbookOrder extends BaseOrder
{
    var $_name = 'fabricbook';
 
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
        $where = "order_id = '{$order_id}'";
        $data = array('status' => $notify_result['target']);
    
        //测试环境下支付成功直接变成已完成
        //$data = array('status' => ORDER_FINISHED);
        $res = 1;
    
        $transaction = $this->_mod_order->beginTransaction();
    
        switch ($notify_result['target'])
        {
            case ORDER_ACCEPTED:
                $where .= ' AND status=' . ORDER_PENDING;   //只有待付款的才会修改为已付款
    
                $data['pay_time']   =   gmtime();
    
                break;
    
            case ORDER_WAITFIGURE:
    
                $where .= ' AND status=' . ORDER_PENDING;   //只有待付款的才会修改为已付款  只有需要量体的订单才会被改成待量体
    
                $data['pay_time']   =   gmtime();
    
                break;
    
            case ORDER_SHIPPED:
                $where .= ' AND status=' . ORDER_ACCEPTED;  //只有等待发货的订单才会被修改为已发货
                $data['ship_time']  =   gmtime();
                break;
    
            case ORDER_FINISHED:
                $where .= ' AND status=' . ORDER_SHIPPED;   //只有已发货的订单才会被自动修改为交易完成
                $data['finished_time'] = gmtime();
                break;
            case ORDER_CANCLED:                             //任何情况下都可以关闭
                /* 加回商品库存 待定状态 */
                // $model_order->change_stock('+', $order_id);
                break;
        }
        
        $res = $this->_mod_order->edit($where, $data);
        
        $oInfo = $this->_mod_order->get($order_id);
        
        $member_mod  = &m("member");
        
        if (!$res){
            $this->_mod_order->rollback();
            return false;
        }

        //给管理员发信息
        /* import("notice.lib");
         $msg = new Notice();
         $msg->send(array(k
         "content" => $notify_result['user_name']."-订单付款（ID-".$order_id."）<a href='".site_url()."/admin/index.php?app=order&act=view&id=".$order_id."'>点击查看</a>",
         'node'    => 'order',
         )); */
//         import("notice.lib");
//         $msg = new Notice();
//         $msg->send(array(
//             "content" => "{$notify_result['user_name']}-订单付款（ID-{$notify_result['order_sn']}）<a href=\"/admin/index.php?app=order&act=view&id={$order_id}\">点击查看</a>",
//             'node'    => 'pay',
//         ));
    
        /* 提交 */
         if($oInfo["money_amount"] > 0){
            $aData[] = array(
                "name"         => "购买面料册使用余额",
                "order_id"     => $order_id,
                "order_sn"     => $oInfo["order_sn"],
                "user_id"      => $oInfo["user_id"],
                "type"         => "4", //余额
                "minus"        => "1",
                "cash_money"   => $oInfo["money_amount"],
                "mark"         => "-",
                "add_time"     => time(),
            );
            
           $member_mod->setDec("user_id='{$oInfo["user_id"]}'", "frozen", $oInfo["money_amount"]);
        }
     
        if($oInfo["coin"] > 0){
            $aData[] = array(
                "name"         => "购买面料册使用麦富迪币",
                "order_id"     => $order_id,
                "order_sn"     => $oInfo["order_sn"],
                "user_id"      => $oInfo["user_id"],
                "type"         => "2",  //麦富迪币
                "minus"        => "1",
                "cash_money"   => $oInfo["coin"],
                "mark"         => "-",
                "add_time"     => time(),
            );
            
           $member_mod->setDec("user_id='{$oInfo["user_id"]}'", "freezes_coin", $oInfo["coin"]);
        }
        $cash_mod    = &m("ordercashlog");
        
        if(!empty($aData)){
         $cash_mod->add($aData);
        }
        
        if($oInfo["final_amount"] > 0){
            get_client_finance($oInfo["user_id"], $oInfo["out_trade_sn"], 5, $oInfo["final_amount"]);
        }
        
        $this->_mod_order->commit($transaction);
    
        if($notify_result["target"] == ORDER_ACCEPTED){
            $_mod_fabricbook    = &m("fabricbook");
        
            $book = $this->_mod_ordergoods->get("order_id='{$order_id}'");
        
            $_mod_fabricbook->setDec("id='{$book["goods_id"]}'", "store", $book["quantity"]);
        }
        return $res;
    }
    
    
    function submit($data){
    	
    	extract($data);
    	
    	$order_sn = $this->_gen_order_sn();
    	
//     	if($_cart['total']['order_amount'] <= 0){
//     	    $order_status = ORDER_ACCEPTED; //已付款
//     	}else{
//     	    $order_status = ORDER_PENDING; //待付款
//     	}
    	
    	/* 订单基本信息 */
    	$baseinfo = array(
    	    'order_sn'       => $order_sn,
    	    'extension'      => $this->_name,
    	    'discount'       => 0,
    	    'goods_amount'   => $_cart['total']['goods_amount'],
    	    'final_amount'   => $_cart['total']['order_amount'],
    	    'order_amount'   => $_cart['total']['goods_amount'],
    	    'money_amount'   => $_cart["total"]["useSurplus"],
    	    'coin'           => $_cart["total"]["useCoin"],
    	    'user_id'        => $_order['user']['user_id'],
    	    'user_name'      => $_order['user']['user_name'],
    	   // 'has_measure'    => $measure['has_measure'],
    	    'status'         => ORDER_PENDING,
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
    	    'source_from'   => 'wap',
    	);
    
    	/* 处理订单收货人信息 */
    	$consignee_info = $this->_handle_consignee_info($_order, $_cart);

    	if (!$consignee_info)
    	{
    		/* 收货人信息验证不通过 */
    		return false;
    	}
    
    	/* 保存订单信息 */
    	$order_id = $this->_mod_order->add($baseinfo);
    
    	if (!$order_id)
    	{
    		/* 保存基本信息失败 */
    		$this->_error('create_order_failed');
    
    		return false;
    	}

    	$goods_items[] = array(
    				'order_id'      =>  $order_id,
    				'goods_id'      =>  $_cart["book"]['id'],
    				'goods_name'    =>  $_cart["book"]['name'],
    				'spec_id'       =>  0,
    				'specification' =>  0,
    				'price'         =>  $_cart["book"]['price'],
    	            'subtotal'      =>  $_cart["book"]['subtotal'],
    				'quantity'      =>  $_cart['book']['number'],
    				'goods_image'   =>  $_cart['book']['img'],
        );
    	
    	$res = $this->_mod_ordergoods->add(addslashes_deep($goods_items)); //防止二次注入
    
    	if (!$res)
    	{
    		/* 保存商品数据 */
    		$this->_error('create_order_failed');
    
    		return false;
    	}
    
    	return array('order_id' => $order_id, 'order_sn' => $baseinfo['order_sn']);
    
    }
}

?>