<?php

/**
 * RCTailor: 用户收货地址
 * ============================================================================
 * @author yaho.bai 2014/6/11
 */

if (!defined('IN_ECM'))
{
    die('Hacking attempt');
}

class Order extends Object{
	
	var $_mod_order;
	var $_mod_orderextm;
	var $_mod_orderfigure;
	var $_mod_ordergoods;
	var $_errors;
	
	function __construct(){
		$this->_mod_order = &m("order");
		$this->_mod_orderextm  = &m('orderextm');
		$this->_mod_orderfigure  = &m('orderfigure');
		$this->_mod_ordergoods  = &m('ordergoods');
	}

	
	function submit($data){
		extract($data);
		$this->_handle_order_info($baseinfo);
		
		/* 处理订单收货人信息 */
		$consignee_info = $this->_handle_consignee_info($address);
		if (!$consignee_info)
		{
			/* 收货人信息验证不通过 */
			return false;
		}
		
		$figure_info = $this->_handle_figure_info($figure);
		
		if (!$figure_info)
		{
			/* 量体数据验证不通过 */
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
		
		/* 保存收货地址信息 */
		$consignee_info['order_id'] = $order_id;
		
		$res = $this->_mod_orderextm->add($consignee_info);
		
		if (!$res)
		{
			/* 保存地址失败 */
			$this->_error('create_order_failed');
		
			return false;
		}
		

		/* 保存量体数据 */
		$figure_info["order_id"] = $order_id;
		$res = $this->_mod_orderfigure->add($figure_info);
		
		if (!$res)
		{
			/* 保存量体数据失败 */
			$this->_error('create_order_failed');
		
			return false;
		}
		
		/* 保存商品数据 */
		foreach ($goodsinfo as $key => $value)
		{
			$goods_items[] = array(
					'order_id'      =>  $order_id,
					'goods_id'      =>  $value['goods_id'],
					'goods_name'    =>  $value['goods_name'],
					'spec_id'       =>  $value['spec_id'],
					'specification' =>  $value['specification'],
					'price'         =>  $value['price'],
					'quantity'      =>  $value['quantity'],
					'goods_image'   =>  $value['goods_image'],
			);
		}
		
		$res = $this->_mod_ordergoods->add(addslashes_deep($goods_items)); //防止二次注入
		
		if (!$res)
		{
			/* 保存商品数据 */
			$this->_error('create_order_failed');
		
			return false;
		}
		
		return array('order_id' => $order_id, 'order_sn' => $baseinfo['order_sn']);
		
	}
	
	/**
	 *    处理订单基本信息,返回有效的订单信息数组
	 *
	 *    @author    yhao.bai
	 *    @param     array $baseinfo
	 */
	function _handle_order_info(&$baseinfo)
	{
		/* 默认都是待付款 */
		$baseinfo["order_sn"] = $this->_gen_order_sn();
		$baseinfo["type"]    = "normal";
		$baseinfo["extension"] = "normal";
		$baseinfo["seller_id"] = 0;
		$baseinfo["seller_name"] = '';
		
		$baseinfo["add_time"] = gmtime();
		
		$baseinfo["status"] = ORDER_PENDING;
	}
	
	/**
	 *    处理订单量体数据,返回有效的量体数据
	 *
	 *    @author    yhao.bai
	 *    @param     array $baseinfo
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
	
	
	function _valid_figure_info($figure){
			foreach($figure as $key => $val){
				if(empty($val)){
					$this->_error('选择的量体数据不完整！');
					return false;
				}
			}
		return $figure;
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
	
		$orders = $this->_mod_order->find('order_sn=' . $order_sn);
		if (empty($orders))
		{
			/* 否则就使用这个订单号 */
			return $order_sn;
		}
	
		/* 如果有重复的，则重新生成 */
		return $this->_gen_order_sn();
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
	
		if (!$consignee['shipping_id'])
		{
			$this->_error('shipping_required');
	
			return false;
		}
	
		return $consignee;
	}
	
	/**
	 *    处理收货人信息，返回有效的收货人信息
	 *
	 *    @author    yhao.bai
	 *    @param     array $goods_info
	 *    @param     array $post
	 *    @return    array
	 */
	function _handle_consignee_info($address)
	{
		/* 验证收货人信息填写是否完整 */
		$consignee_info = $this->_valid_consignee_info($address);
		if (!$consignee_info)
		{
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
				'shipping_id'   =>  $address['shipping_id'],
				'shipping_name' =>  addslashes($address['shipping_name']),
				'shipping_fee'  =>  $address['shipping_fee'],
		);
	}
	
	function _error($msg){
		$this->_errors = $msg;
	}
}
?>