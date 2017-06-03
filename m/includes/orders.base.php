<?php

!defined('ROOT_PATH') && exit('Forbidden');

/**
 *    订单类型基类
 *
 *    @author Ruesin
 */
class BaseOrders extends Object
{
    var $_mod_order;
    var $_mod_orderextm;
    var $_mod_orderfigure;
    var $_mod_ordergoods;
    var $_mod_orderlog;
    var $_mod_ordercron;
    var $_mod_orderpmt;
    var $_errors;

    function __construct(){
    	$this->_mod_order       = &m("order");
    	$this->_mod_orderextm   = &m('orderextm');
    	$this->_mod_orderfigure = &m('orderfigure');
    	$this->_mod_ordergoods  = &m('ordergoods');
    	$this->_mod_orderlog    = &m('orderlog');
    	$this->_mod_ordercron   = &m("ordercron");
    	$this->_mod_orderpmt    = &m("orderpmt");
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
		//$data = array('status' => STORE_ACCEPTED);

        switch ($notify_result['target'])
        {
            case STORE_ACCEPTED:
                $where .= ' AND status=' . ORDER_PENDING;   //只有待付款的订单才会被修改为 品牌商已付款
                if($notify_result['has_store']){
                    $data['pay_time']   =   gmtime();
                }else {
                    $data['kh_pay_time']   =   gmtime();
                }

            break;

            case ORDER_ACCEPTED:
                $where .= ' AND status=' . STORE_ACCEPTED;   //只有品牌商已付款的订单才会被修改为已付款
                $data['status'] = ORDER_CHECKING;       //状态直接进入审核中

                if($notify_result['has_store']){
                    $data['pay_time']   =   gmtime();
                }else {
                    $data['kh_pay_time']   =   gmtime();
                }

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

        return $this->_mod_order->edit($where, $data);
    }


    /**
     * 生成订单号
     *
     * @author Ruesin
     */
    function _gen_order_sn()
    {
        do{
            mt_srand((double) microtime() * 1000000);
            $timestamp = gmtime();
            $y = date('y', $timestamp);
            $z = date('z', $timestamp);
            $order_sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            
        }while ($this->_mod_order->find("order_sn='{$order_sn}'"));
        
        return $order_sn;
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
     * 处理订单基本信息,返回有效的订单信息数组
     *
     * @author Ruesin
     */
    function _handle_order_info($order, $cart)
    {
        $order_status = ORDER_PENDING; //待付款

        /* 买家信息 */
        $user_id     =  $order['user']['user_id'];
        $user_name   =  $order['user']['user_name'];

        $order_sn = $this->_gen_order_sn($cart['global']['city_id']);

        $tax = $order["tax"];

        if($tax && $tax['is_tax']){
        	$baseinfo['is_tax']      = $tax['is_tax'];
        	$baseinfo['tax_type']    = $tax['tax_id'];
        	$baseinfo['tax_content'] = $tax['taxcon'];
        	$baseinfo['tax_company'] = $tax['taxtit'];
        }
        /* 返回基本信息 */
        return array(
            'order_sn'       => $order_sn,
            'goods_amount'   => $cart['goods_amount'],
            'order_amount'   => $cart['final_amount'],
            'offline_amount' => $cart['offline_amount'],
            'discount'       => $cart['discount'],
            'order_type'     => 'diy',//...
            'status'         => $order_status,
            'payment_id'     => $order['pay']['id'],
            'payment_name'   => $order['pay']['name'],
            'payment_code'   => $order['pay']['code'],
            'shipping_id'    => $order['ship']['id'],
            'shipping'       => $order['ship']['name'],
            'shipping_store' => $order['ship']['store'],
            'cost_freight'   => 0,//免邮
            'weight'         => $cart['weight'],
            'ship_area'      => $order['address']['region_name'],
            'ship_name'      => $order['address']['consignee'],
            'ship_addr'      => $order['address']['address'],
            'ship_zip'       => $order['address']['zipcode'],
            'ship_email'     => $order['address']['email'],
            'ship_time'      => $order['address']['ship_time'],
            'ship_mobile'    => $order['address']['phone_mob'],
            'ship_tel'       => $order['address']['phone_tel'],
            'createtime'    => gmtime(),
            'last_modified' => gmtime(),
            'user_id'       => $user_id,
            'holder_id'     => $holder_id,
            'is_tax'        => 1,
            'tax_type'      => $baseinfo['tax_type'],
            'tax_content'   => $baseinfo['tax_content'],
            'cost_tax'      => '',
            'tax_company'   => $baseinfo['tax_company'],
            'is_protect'    => 0,//保价
            'cost_protect'  => 0,
            //'figure_type'   => $cart['global']['measure_id'],
            //'figure_store'  => 0, //量体门店,线下
            'figure_fee'    => $cart['figure_fee'],
            'point_u'       => $cart['point_u'],
            'point_fee'     => $cart['point_fee'],
            'point_g'       => $cart['point_g'],
            'coupon_sn'     => '',
            'coupon_fee'    => '',
            'pmt_goods'     => '',
            'pmt_order'     => '',
            'cost_payment'  => $cart['final_amount'],  //订单支付金额
            'currency'      => $order['source']['cur'],  //货币
            'memo'          => '',
            'tostr'         => '',
            'itemnum'       => count($cart['object']),
            'ip'            => $order['source']['ip'] ,
            'source_from'   => $order['source']['from'],
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
			$this->_error('consignee_empty');

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
	function _handle_figure_info($glb){
	    $figure = unserialize($glb['measure_data']);
		$data = $this->_valid_figure_info($figure);
		if (!$data){
			return false;
		}

		return array(
		        'lw'   => $data['lw'],
		        'qjk'  => $data['qjk'],
		        'zjk'  => $data['zjk'],
		        'xw'   => $data['xw'],
		        'tw'   => $data['tw'],
		        'hyc'  => $data['hyc'],
		        'zyw'  => $data['zyw'],
		        'qyj'  => $data['qyj'],
		        'hyjc' => $data['hyjc'],
		        'stw'  => $data['stw'],
		        'zxc'  => $data['zxc'],
		        'yxc'  => $data['yxc'],
		        'tgw'  => $data['tgw'],
		        'td'   => $data['td'],
		        'qyg'  => $data['qyg'],
		        'hyg'  => $data['hyg'],
		        'zkc'  => $data['zkc'],
		        'ykc'  => $data['ykc'],
		        'kk'   => $data['kk'],
		        'xiw'  => $data['xiw'],
		);
	}

	/*验证量体数据*/
	function _valid_figure_info($figure){
	    /*if(!$figure['lw']){
	        $this->_error("量体数量不合法");
	        return false;
	    }*/
	    if(!$figure['qjk']){
	        $this->_error("量体数量不合法");
	        return false;
	    }
	    if(!$figure['zjk']){
	        $this->_error("量体数量不合法");
	        return false;
	    }
	    if(!$figure['xw']){
			$this->_error("量体数量不合法");
			return false;
		}
		if(!$figure['tw']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['hyc']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['zyw']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['qyj']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['hyjc']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['stw']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['zxc']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['yxc']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['tgw']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['td']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['qyg']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['hyg']){
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
		/*if(!$figure['kk']){
		    $this->_error("量体数量不合法");
		    return false;
		}
		if(!$figure['xiw']){
		    $this->_error("量体数量不合法");
		    return false;
		}*/
	    //..
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