<?php

/**
 *    @author    yhao.bai
 */
class FxOrder extends BaseOrder
{
    var $_name = 'fx';
 
    var $_mod_order;
    var $_mod_ordergoods;
    var $_mod_orderlog;
    var $_mod_ordercron;
    var $_mod_orderpmt;
    var $_errors;
    var $_fxinfo_mod;
    

    function __construct(){
        parent::__construct();
    	$this->_mod_order       = &m("orderserve");
    	$this->_mod_ordergoods  = &m('ordergoods');
    	$this->_mod_orderlog    = &m('orderlog');
    	$this->_mod_ordercron   = &m("ordercron");
    	$this->_mod_orderpmt    = &m("orderpmt");
    	$this->_fxinfo_mod      = &m('orderserveinfo');
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
        $now_time = time();
        $data = array('status' => $notify_result['target'],'last_modified'=>$now_time);

        $fxinfo['info_status'] = $notify_result['target'];
   
        $res = 0;
    
        $transaction = $this->_mod_order->beginTransaction();
    
        if($notify_result['target'] ==7){
            
            $res = $this->_mod_order->edit($where, $data);
            $fx_info_edit = $this->_fxinfo_mod->edit($where,$fxinfo);
            
        }else
        {
            $this->show_message("支付不成功!");
            
            return false;
        }
        
        $oInfo = $this->_mod_order->get("order_id ='{$order_id}'");
        
        $cash_mod    = &m("ordercashlog");
        $fx_log_mod  = &m("orderfxlog");
        
        $member_mod  = &m("member");
        
        $aData = array();
        
        if($oInfo["money_amount"] > 0){
            $aData[] = array(
                "name"         => "支付返修费,使用余额",
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
                "name"         => "支付返修费,使用麦富迪币",
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
        
        if($aData){
            
            $cash_mod->add($aData);
        }
        // 写入日志
        if($res){
            $fxlog[] = array(
                "name"         => "支付返修费",
                "order_id"     => $order_id,
                "order_sn"     => $oInfo["order_sn"],
                "user_id"      => $oInfo["user_id"],
                "fx_fee"       => $oInfo["price"],
                "mark"         => "",
                "order_money"  =>$oInfo['order_amount'],
                "add_time"     => time(),
                "change_into"  =>"1"
            );
            $fx_log_mod->add($fxlog);// 返修费用写入log 日志 
        }
        
        if (!$res){
            $this->_mod_order->rollback();
            return false;
        }
    
        /* 提交 */
        $this->_mod_order->commit($transaction);
        
        return $res;
    }
    
    function submit($data){
    	extract($data);
    	$order_id = $_cart['fx']['order_id'];
    	$order_sn = $_cart['fx']['order_sn'];
    	$_cart['total']['order_amount'] = 0.01;
       	/* 订单基本信息 */
    	$data = array(
    	    'final_amount'   => trim($_cart['total']['order_amount']),
    	    'order_amount'   => trim($_cart['total']['goods_amount']),
    	    'money_amount'   => trim($_cart["total"]["surplus"]),
    	    'coin'           => trim($_cart["total"]["coin"]),
    	    'payment_id'     => trim($_order['payment']['payment_id']),
    	  
    	);
        // 检查是否有 之前支付失败的数据 需要清空 来 处理 最新的一次支付（没有冻结余额  和 麦富迪币）
        $fx_order = $this->_mod_order->get("order_id = '{$order_id}'");

        $temp = array(
            'final_amount'   => '',
            'order_amount'   => '',
            'money_amount'   => '',
            'coin'           => '',
            'payment_id'     => '',
          
        );

        if($fx_order['order_amount'] || $fx_order['final_amount'] || $fx_order['money_amount'] || $fx_order['coin'] ||  $fx_order['payment_id'] ){
            $this->_mod_order->edit("order_id ='{$order_id}'",$temp);
        }

    	/* 保存订单信息 */
    	$update_id = $this->_mod_order->edit("order_id ='{$order_id}'",$data);
        
    	if (!$update_id)
    	{
    		/* 保存基本信息失败 */
    		$this->_error('update_order_failed');
    
    		return false;
    	}

    	return array('order_id' => $order_id, 'order_sn' => $order_sn);
    
    }
}

?>