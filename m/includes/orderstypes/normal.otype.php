<?php
/**
 * 新版普通订单
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: normal.otype.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2015 redcollar
 */
class NormalOrders extends BaseOrders
{
    var $_name = 'normal';
 
    function __construct(){
		parent::__construct();
    }
    
    /**
     *    查看订单
     *
     *    @author Ruesin
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
    
    
    function submit($data){
    	
    	extract($data);
    	
    	/* 订单基本信息 */
    	$baseinfo = $this->_handle_order_info($_order, $_cart);
    
		if(!$baseinfo){
			
			return false;
		}
		
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
    
    	/* 保存收货地址信息 */
    	$consignee_info['order_id'] = $order_id;
    
    	$res = $this->_mod_orderextm->add($consignee_info);
    
    	if (!$res)
    	{
    		/* 保存地址失败 */
    		$this->_error('create_order_failed');
    
    		return false;
    	}
    	
    	/* 保存促销信息 */
    	$res = $this->_save_order_pmt($_order, $order_id);
    	 
    	if(!$res){
    		/* 保存优惠活动失败 */
    		$this->_error('保存优惠活动失败');
    	
    		return false;
    	}

    	/* 保存商品数据 */
    	foreach ($_cart['goods_list'] as $key => $value)
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
    				'source_id'     =>  $value['source_id'],
    				'cst_cate'      =>  $value['cst_cate'],
    				'source_title'  =>  $value['source_title'],
    				'type_name'     =>  $value['type_name'],
    				'cst_author'    => 	$value['cst_author'],
    				'cst_source'    => 	$value['cst_source'],
    				'cst_source_id' => 	$value['cst_source_id'],
    				'is_diy' 		=> 	$value['is_diy'],
    				'height' 		=> 	$value['height'],
    				'weight' 		=> 	$value['weight'],
    				'emb_con'		=>  $value['emb_con'],
    				'fabric'		=>  $value['fabric'],
    				'items'         =>  $value['items']
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
}

?>