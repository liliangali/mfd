<?php

/**
 *    @author    yhao.bai
 */
class CoinOrder extends BaseOrder
{
    var $_name = 'coin';
 
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
        $res = 0;
    
        $transaction = $this->_mod_order->beginTransaction();
    
        switch ($notify_result['target'])
        {
            case ORDER_ACCEPTED:
                $where .= ' AND status=' . ORDER_PENDING;   //只有待付款的才会修改为已付款
    
                $data['pay_time']   =   gmtime();
    
                break;
    
            case ORDER_WAITFIGURE:
    
                $where .= ' AND status=' . ORDER_PENDING ." AND has_measure = '1' ";   //只有待付款的才会修改为已付款  只有需要量体的订单才会被改成待量体
    
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
        
        $cash_mod    = &m("ordercashlog");
        
        $member_mod  = &m("member");
        
        $aData = array();
        
        $aData[] = array(
            "name"         => "购买麦富迪币",
            "order_id"     => $order_id,
            "order_sn"     => $oInfo["order_sn"],
            "user_id"      => $oInfo["user_id"],
            "type"         => "2",
            "minus"        => "1",
            "cash_money"   => $oInfo["goods_amount"],
            "mark"         => "+",
            "add_time"     => time(),
        );
        
        //加麦富迪币
        $member_mod->setInc("user_id='{$oInfo["user_id"]}'", "coin", $oInfo["goods_amount"]);
        
        
        if($oInfo["point_g"] > 0){
            $aData[] = array(
                "name"         => "购买麦富迪币赠送积分",
                "order_id"     => $order_id,
                "order_sn"     => $oInfo["order_sn"],
                "user_id"      => $oInfo["user_id"],
                "type"         => "1", //积分
                "minus"        => "1",
                "cash_money"   => $oInfo["point_g"],
                "mark"         => "+",
                "add_time"     => time(),
            );
            
            $member_mod->setInc("user_id='{$oInfo["user_id"]}'", "point", $oInfo["point_g"]);
        }
        
        if($oInfo["money_amount"] > 0){
            $aData[] = array(
                "name"         => "购买麦富迪币使用余额",
                "order_id"     => $order_id,
                "order_sn"     => $oInfo["order_sn"],
                "user_id"      => $oInfo["user_id"],
                "type"         => "4", //余额
                "minus"        => "1",
                "cash_money"   => $oInfo["money_amount"],
                "mark"         => "-",
                "add_time"     => time(),
            );
            
            $member_mod->setDec("user_id='{$oInfo["user_id"]}'", "money", $oInfo["money_amount"]);
        }
        
        if($oInfo["coin"] > 0){
            $aData[] = array(
                "name"         => "购买麦富迪币使用麦富迪币",
                "order_id"     => $order_id,
                "order_sn"     => $oInfo["order_sn"],
                "user_id"      => $oInfo["user_id"],
                "type"         => "2",  //麦富迪币
                "minus"        => "1",
                "cash_money"   => $oInfo["coin"],
                "mark"         => "-",
                "add_time"     => time(),
            );
            
            $member_mod->setDec("user_id='{$oInfo["user_id"]}'", "coin", $oInfo["coin"]);
        }
        
        $cash_mod->add($aData);

        if (!$res){
            $this->_mod_order->rollback();


            return false;
        }
    
        
//         if($notify_result["target"] == ORDER_ACCEPTED){
//             $_mod_fabricbook    = &m("fabricbook");
            
//             $book = $this->_mod_ordergoods->get("order_id='{$order_id}'");
            
//             $_mod_fabricbook->setDec("id='{$book["goods_id"]}'", "store", $book["quantity"]);
//         }
        
        //给管理员发信息
        /* import("notice.lib");
         $msg = new Notice();
         $msg->send(array(k
         "content" => $notify_result['user_name']."-订单付款（ID-".$order_id."）<a href='".site_url()."/admin/index.php?app=order&act=view&id=".$order_id."'>点击查看</a>",
         'node'    => 'order',
         )); */
        
        import("notice.lib");
        $msg = new Notice();
        $msg->send(array(
            "content" => "{$notify_result['user_name']}-订单付款（ID-{$notify_result['order_sn']}）<a href=\"/admin/index.php?app=order&act=view&id={$order_id}\">点击查看</a>",
            'node'    => 'pay',
        ));
        
        if($oInfo["final_amount"] > 0){
            get_client_finance($oInfo["user_id"], $oInfo["out_trade_sn"], 6, $oInfo["final_amount"]);
        }
        
        /* 提交 */
        $this->_mod_order->commit($transaction);
        
        // 购币 升级 调整 优化  author tangsj
        $userinfo = $member_mod->get($oInfo['user_id']);
        if($userinfo['member_lv_id']==1){
            changelv($oInfo["user_id"]);
        }else{
            reloadMember($oInfo["user_id"]);
        }
        
        // 由于等级改变 调整二维码跳转链接
        //editerweimaUrl($oInfo["user_id"]);
    
        return $res;
    }
    
    
    function submit($data){
    	
    	extract($data);
    	$order_sn = $this->_gen_order_sn();
    	$_cart['total']['order_amount'] =0.01;
    	/* 订单基本信息 */
    	$baseinfo = array(
    	    'order_sn'       => $order_sn,
    	    'extension'      => $this->_name,
    	    'discount'       => 0,
    	    'goods_amount'   => $_cart["coin"]["facevalue"],
    	    'final_amount'   => $_cart['total']['order_amount'],
    	    'order_amount'   => $_cart['total']['goods_amount'],
    	    'money_amount'   => $_cart["total"]["surplus"],
    	    'coin'           => $_cart["total"]["coin"],
    	    'user_id'        => $_order['user']['user_id'],
    	    'user_name'      => $_order['user']['user_name'],
    	   // 'has_measure'    => $measure['has_measure'],
    	    'status'         => ORDER_PENDING,
    	    'add_time'       => gmtime(),
    	    //'finished_time'  => '',
    	    'last_modified'  => gmtime(),
    	    //是否上门量体
    	    'measure'        => 0,
    	    'pay_time'       => 0,
    	    'measure_fee'    => 0,
    	    //支付
    	    'payment_id'     => $_order['payment']['payment_id'],
    	    'payment_name'   => $_order['payment']['payment_name'],
    	    'payment_code'   => $_order['payment']['payment_code'],
    	    'ship_area'      => '',
    	    'ship_addr'      => '',
    	    'ship_name'      => '',
    	    'ship_zip'       => '',
    	    'ship_mobile'    => '',
    	    'ship_tel'       => '',
    	    'ship_email'     => '',
    	    'shipping_id'    => '',
    	    'shipping'       => '',
    	    'source_from'    => 'wap',
    	    'point_g'        => $_cart["coin"]["integral"],
    	);
    
    	/* 处理订单收货人信息 */
//     	$consignee_info = $this->_handle_consignee_info($_order, $_cart);

//     	if (!$consignee_info)
//     	{
//     		/* 收货人信息验证不通过 */
//     		return false;
//     	}
    
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
    				'goods_id'      =>  $_cart["coin"]['id'],
    				'goods_name'    =>  '麦富迪币',
    				'spec_id'       =>  0,
    				'specification' =>  0,
    				'price'         =>  $_cart["coin"]['price'],
    	            'subtotal'      =>  $_cart["coin"]['subtotal'],
    				'quantity'      =>  $_cart['coin']['number'],
    				'goods_image'   =>  '',
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