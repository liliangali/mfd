<?php

!defined('ROOT_PATH') && exit('Forbidden');

/**
 *    订单类型基类
 *
 *    @author    yhao.bai
 *    @usage    none
 */
class BaseOrder extends Object
{
    var $_mod_order;
    var $_mod_orderextm;
    var $_mod_orderfigure;
    var $_mod_ordergoods;
    var $_mod_orderlog;
    var $_mod_ordercron;
    var $_mod_orderpccron;
    var $_mod_orderpmt;
    var $_errors;
    
    public $_mod_pay;
    public $_measure_way;

    function __construct(){
    	$this->_mod_order       = &m("order");
    	$this->_mod_orderextm   = &m('orderextm');
    	$this->_mod_orderfigure = &m('orderfigure');
    	$this->_mod_ordergoods  = &m('ordergoods');
    	$this->_mod_orderlog    = &m('orderlog');
    	$this->_mod_ordercron   = &m("ordercron");
    	$this->_mod_orderpccron   = &m("orderpccron");
    	$this->_mod_orderpmt    = &m("orderpmt");
    	$this->_mod_pay = &m("payment");
    	$this->_measure_way = array(/*'1'=>'预约上门量体',*/'2'=>'去附近门店量体','3'=>'现有量体数据'/*,'4'=>'标准尺码'*/);//标准尺码没算法  先屏蔽 
    }

    /**
     *    获取订单类型名称
     *
     *    @author    yhao.bai
     *    @return    string
     */
    function get_name()
    {
        return $this->_name;
    }

    /**
     *    获取订单详情
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

        /* 订单基本信息 */
        $data['order_info'] =   $order_info;

        return array('data' => $data, 'template' => 'normalorder.view.html');
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
            
                $mOrderpccron   = &m("orderpccron");
                $res = $mOrderpccron->_addToList($order_id);
                if(!$res){
                    for ($tNum = 0;$tNum<5;$tNum++){
                        $res = $mOrderpccron->_addToList($order_id);
                        if($res){
                            $data['status'] = ORDER_PRODUCTION;   // 生产中
                            break;
                        }
                    }
                }else{
                    $data['status'] = ORDER_PRODUCTION;
                }
                    
                $data['pay_time']   =   gmtime();

            break;
            
            case ORDER_WAITFIGURE:
                
                $where .= ' AND status=' . ORDER_PENDING ." AND has_measure = '1' ";   //只有待付款的才会修改为已付款  只有需要量体的订单才会被改成待量体
            
                //$data['status'] = ORDER_WAITFIGURE;       //待量体
            
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

        if (!$res){
            $this->_mod_order->rollback();
            return false;
        }
        
        $res = $this->_mod_order->edit($where, $data);
        if (!$res){
            $this->_mod_order->rollback();
            return false;
        }
        
        $this->_mod_order->commit($transaction);


        
        return true;
        //return $res;
        
    }


    /**
     *    生成订单号
     *
     *    @author    yhao.bai
     *    @return    string
     */
    function _gen_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        $timestamp = gmtime();
        $y = date('y', $timestamp);
        $z = date('z', $timestamp);
        $order_sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $orders = $this->_mod_order->find("order_sn='{$order_sn}'");
        if (empty($orders))
        {
            /* 否则就使用这个订单号 */
            return $order_sn;
        }

        /* 如果有重复的，则重新生成 */
        return $this->_gen_order_sn();
    }

    /**
     *    获取商品列表
     *
     *    @author    yhao.bai
     *    @param     int $order_id
     *    @return    array
     */
    function _get_goods_list($order_id)
    {
        if (!$order_id)
        {
            return array();
        }

        return $this->_mod_ordergoods->find("order_id='{$order_id}'");
    }

    /**
     * 获取订单优惠信息
     * @author yhao.bai
     * @param  int $order_id
     * @return arr
     */
    function _get_order_pmt($order_id){
    	if (!$order_id)
    	{
    		return array();
    	}

    	return $this->_mod_orderpmt->find("order_id='{$order_id}'");
    }
    /**
     *    获取扩展信息
     *
     *    @author    yhao.bai
     *    @param     int $order_id
     *    @return    array
     */
    function _get_order_extm($order_id)
    {
        if (!$order_id)
        {
            return array();
        }

        return $this->_mod_orderextm->get($order_id);
    }

    /**
     *    获取订单操作日志
     *
     *    @author    yhao.bai
     *    @param     int $order_id
     *    @return    array
     */
    function _get_order_logs($order_id)
    {
        if (!$order_id)
        {
            return array();
        }

        return $this->_mod_orderlog->find("order_id = {$order_id}");
    }

    /**
     *    处理订单基本信息,返回有效的订单信息数组
     *
     *    @author    yhao.bai
     *    @param     array $order
     *    @param     array $cart
     *    @return    array
     */
    function _handle_order_info($order, $cart)
    {
        /* 默认都是待付款 */
        $order_status = ORDER_PENDING;

		if($cart['amount'] == 0){
			$order_status = ORDER_ACCEPTED;
		}

        /* 买家信息 */
        $visitor     =& env('visitor');
        $user_id     =  $visitor->get('user_id');
        $user_name   =  $visitor->get('user_name');

        $payment = $order['payment'];
        if(empty($payment)){
        	$this->_error('没有选择支付方式');
        	return false;
        }

        $order_sn = $this->_gen_order_sn();

        $invoice = $order["invoice"];

        if($invoice && $invoice['invoiceneed']){
        	$baseinfo['invoiceneed'] = $invoice['invoiceneed'];
        	$baseinfo['invoicetitle'] = $invoice['invoicetitle'];
        	$baseinfo['invoicetype'] = $GLOBALS['__ECLANG__']['invoicetype'][$invoice['invoicetype']];
        	$baseinfo['invoicecontent'] = $GLOBALS['__ECLANG__']['invoicecontent'][$invoice['invoicecontent']];
        }

        /* 返回基本信息 */
        return array(
            'order_sn'      =>  $order_sn,
        	'out_trade_sn'  =>  '',
            'type'          =>  $this->_name,
            'extension'     =>  $this->_name,
            'seller_id'     =>  0,
            'seller_name'   =>  '',
            'buyer_id'      =>  $user_id,
            'buyer_name'    =>  addslashes($user_name),
            'buyer_email'   =>  $visitor->get('email'),
            'status'        =>  $order_status,
            'add_time'      =>  gmtime(),
            'surplus'       =>  0,
            'goods_amount'  =>  $cart['goods_amount'],
            'discount'      =>  $cart['discount'],
            'anonymous'     =>  0,
            'postscript'    =>  $order['postscript'],
        	'order_amount'  =>  $cart['amount'],
    		'payment_id'    =>  $payment['payment_id'],
    		'payment_name'  =>  $payment["payment_name"],
    		'payment_code'  =>  $payment["payment_code"],
    		'coin'          =>  $order['coin'],
    		'point'         =>  $order['point'],
    		'coin_fee'      =>  $cart['coin_fee'],
    		'point_fee'     =>  $cart['point_fee'],
    		'coupon_fee'    =>  $cart['coupon_fee'],
        	'figure_fee'    =>  $cart['figure_fee'],
        	'invoiceneed'   =>  $invoice['invoiceneed'],
        	'invoicetitle'  =>  $invoice['invoicetitle'],
        	'invoicetype'   =>  $invoice['invoiceneed'] ? $GLOBALS['__ECLANG__']['invoicetype'][$invoice['invoicetype']] : '',
        	'invoicecontent' => $invoice['invoiceneed'] ? $GLOBALS['__ECLANG__']['invoicecontent'][$invoice['invoicecontent']] : '',
        );
    }

    /**
	 *    处理收货人信息，返回有效的收货人信息
	 *
	 *    @author    yhao.bai
	 *    @param     array $order
	 *    @param     array $cart
	 *    @return    array
	 */
	function _handle_consignee_info($order, $cart)
	{
		$address = $order['address'];

		/* 验证收货人信息填写是否完整 */
		$consignee_info = $this->_valid_consignee_info($address);

		if (!$consignee_info)
		{
			return false;
		}

		if (!$order['shipping']['shipping_id'])
		{
			$this->_error('shipping_required');

			return false;
		}

		return array(
				'consignee'     =>  $consignee_info['consignee'],
				'region_id'     =>  $consignee_info['region_id'],
				'region_name'   =>  $consignee_info['region_name'],
				'address'       =>  $consignee_info['address'],
				'zipcode'       =>  $consignee_info['zipcode'],
				'phone_tel'     =>  $consignee_info['phone_tel'],
				'phone_mob'     =>  $consignee_info['phone_mob'],
				'shipping_id'   =>  $order['shipping']['shipping_id'],
				'shipping_name' =>  addslashes($order['shipping']['shipping_name']),
				'shipping_fee'  =>  $cart['shipping_fee'],
		);
	}


	/**
	 *    验证收货人信息是否合法
	 *
	 *    @author    yhao.bai
	 *    @param     array $consignee
	 *    @return    void
	 */
	function _valid_consignee_info($consignee)
	{
		if (!$consignee['consignee'])
		{
			//$this->_error('consignee_empty');
			$this->_error('收货信息不完整，请去个人中心完善收货信息！');

			return false;
		}
		if (!$consignee['region_id'])
		{
			$this->_error('region_empty');

			return false;
		}
		if (!$consignee['address'])
		{
			$this->_error('address_empty');

			return false;
		}
		if (!$consignee['phone_tel'] && !$consignee['phone_mob'])
		{
			$this->_error('phone_required');

			return false;
		}

		return $consignee;
	}

	/**
	 *    处理订单量体数据,返回有效的量体数据
	 *
	 *    @author    yhao.bai
	 *    @param     array $figure
	 */
	function _handle_figure_info($figure){
		//unset($figure['service']);
		$data = $this->_valid_figure_info($figure);

		if (!$data)
		{
			return false;
		}

		return array(
				'figure_name' =>  $data['figure_name'],
				'lw ' =>  $data['lw'],
				'xw ' =>  $data['xw'],
				'zyw ' =>  $data['zyw'],
				'tw ' =>  $data['tw'],
				'stw ' =>  $data['stw'],
				'zjk ' =>  $data['zjk'],
				'yxc ' =>  $data['yxc'],
				'zxc ' =>  $data['zxc'],
				'qjk ' =>  $data['qjk'],
				'hyc ' =>  $data['hyc'],
				'yw ' =>  $data['yw'],
				'td' =>  $data['td'],
				'hyg ' =>  $data['hyg'],
				'qyg' =>  $data['qyg'],
				'kk' =>  $data['kk'],
				'hyjc ' =>  $data['hyjc'],
				'qyj' =>  $data['qyj'],
				'tgw ' =>  $data['tgw'],
				'zkc ' =>  $data['zkc'],
				'ykc' =>  $data['ykc'],
				'xiw ' =>  $data['xiw'],
				'body_type_19 ' =>  $data['body_type_19'],
				'body_type_20 ' =>  $data['body_type_20'],
				'body_type_24 ' =>  $data['body_type_24'],
				'body_type_25 ' =>  $data['body_type_25'],
				'body_type_26 ' =>  $data['body_type_26'],
				'body_type_3 ' =>  $data['body_type_3'],
				'body_type_2000 ' =>  $data['body_type_2000'],
				'styleLength ' =>  $data['styleLength'],
				'part_label_10130' => $data['part_label_10130'],
				'part_label_10131' => $data['part_label_10131'],
				'part_label_10725' => $data['part_label_10725'],
				'part_label_10726' => $data['part_label_10726'],
				'realname' => $data['realname'],
				'mobile'  => $data['mobile'],
				'region_name' => $data['region_name'],
				'region_id' => $data['region_id'],
				'address' => $data['address'],
				'retime' => strtotime($data['retime']),
				'serviceid' => $data['service'],
				'figure'  => $data['figure'],
		);
	}

	/*验证量体数据*/
	function _valid_figure_info($figure){
		if($figure['figure'] > 0){
			if(!$figure['figure_name']){
				$this->_error("量体名称不能为空");
				return false;
			}

			if(!$figure['lw']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['xw']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['zyw']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['tw']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['stw']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['zjk']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['yxc']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['zxc']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['qjk']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['hyc']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['yw']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['td']){
				$this->_error("量体数量不合法");
				return false;
			}

			/*
			if(!$figure['hyg']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['qyg']){
				$this->_error("量体数量不合法");
				return false;
			}


			if(!$figure['kk']){
				$this->_error("量体数量不合法");
				return false;
			}
			*/

			if(!$figure['hyjc']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['qyj']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['tgw']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['zkc']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['ykc']){
				$this->_error("量体数量不合法");
				return false;
			}

			/*
			if(!$figure['xiw']){
				$this->_error("量体数量不合法");
				return false;
			}
			*/

			if(!$figure['body_type_19']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['body_type_20']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['body_type_24']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['body_type_25']){
				$this->_error("量体数量不合法");
				return false;
			}
			if(!$figure['body_type_26']){
				$this->_error("量体数量不合法");
				return false;
			}

			if(!$figure['body_type_3']){
				$this->_error("量体数量不合法");
				return false;
			}

			if(!$figure['body_type_2000']){
				$this->_error("量体数量不合法");
				return false;
			}

			if(!$figure['styleLength']){
				$this->_error("量体数量不合法");
				return false;
			}

			if(!$figure['part_label_10130']){
				$this->_error("量体数量不合法");
				return false;
			}

			if(!$figure['part_label_10131']){
				$this->_error("量体数量不合法");
				return false;
			}

			/*
			if(!$figure['part_label_10725']){
				$this->_error("量体数量不合法");
				return false;
			}

			if(!$figure['part_label_10726']){
				$this->_error("量体数量不合法");
				return false;
			}
			*/

			if(!$figure['service']){
				$this->_error("量体数据没有服务点！");
				return false;
			}
		}else{

			if(!in_array($figure['figure'], array("-1","-2"))){
				$this->_error("参数错误");
				return false;
			}

			if(!$figure['realname']){
				$this->_error("请填写真实姓名！");
				return false;
			}

			if(!$figure['mobile']){
				$this->_error("请填写手机号码！");
				return false;
			}

			/**
			if(!$figure['region_id']){
				$this->_error("请选择服务地区！");
				return false;
			}

			if(!$figure['region_name']){
				$this->_error("请选择服务地区！");
				return false;
			}
			*/

			if(!$figure['retime']){
				$this->_error("请选择预约量体的时间！");
				return false;
			}

			if(!$figure['address']){
				$this->_error("请填写详细地址！");
				return false;
			}

			if(!$figure['service']){
				$this->_error("没有选择服务点！");
				return false;
			}


			$ntime = date("Ymd");
			$rtime = date("Ymd",strtotime($figure['retime']));

			if($rtime <= $ntime){
				$this->_error("您选择的时间不能进行量体！");
				return false;
			}
		}
		return $figure;
	}

	/**
	 * 保存促销信息
	 * @author yhao.bai
	 * @param  arr $order
	 * @return boolean
	 */
	function _save_order_pmt($order, $order_id){
		$pmts = array();
		foreach((array)$order['coupons'] as $key => $val){
			$pmt = array(
					'order_id'   => $order_id,
					'goods_id'   => 0,
					'pmt_type'   => "coupon",
					'pmt_amount' => $val["money"],
					'pmt_tag'    => "优惠券:{$val['sn']}",
					'pmt_desc'   => $val['name'],
					'sendtype'   => '',
			);
			$pmts[] = $pmt;
		}
		foreach((array)$order['promotions'] as $key => $val){
			$pmt = array(
					'order_id'   => $order_id,
					'goods_id'   => 0,
					'pmt_type'   => $val['type'],
					'pmt_amount' => $val["money"],
					'pmt_tag'    => $val['gift'] ? "送赠品" : '折扣',
					'pmt_desc'   => $val['msg'],
					'sendtype'   => $val['sendtype']
			);
			$pmts[] = $pmt;
		}

		if(empty($pmts)) return true;

		return $this->_mod_orderpmt->add(addslashes_deep($pmts));

	}

	/**
	 *    返回订单基本信息
	 *
	 *    @author    yhao.bai
	 *    @param     array $order_id
	 */
	function _order_info($order_id){
		if(!$order_id){
			return ;
		}
		return $this->_mod_order->get_info($order_id);
	}

	/* 设置错误信息 */
	function _error($msg, $obj=''){
		$this->_errors = $msg;
	}
}

?>