<?php
use Cyteam\Goods\Orders;
/**
	 * 订单
	 * @author liang.li <1184820705@qq.com>
	 * @version $Id: Order.class.php 4291 2015-05-30 02:41:45Z gaofei $
	 * @copyright Copyright 2014 mfd.com
	 * @package app
	 */
	class Order extends Result
	{

		public  $return = array();
		public  $json;
		public  $_cloth;
		public  $_embs;

		public $_mod_mtm_bt;
		public $_mod_craft;
		public $_mod_craft_parent;
		public $_mod_fabric;
		public $_mod_emb;
		public $_mod_part;
		function __construct()
		{
		    parent::__construct();
		}
		
		
		/**
		*content
		*@author  liang.li
		*/
		function orderStatus($data) 
		{
		    $status = [['status'=>0,'name'=>"全部"],['status'=>10,'name'=>"待付款"],['status'=>20,'name'=>"待发货"],
		        ['status'=>30,'name'=>"待收货"],['status'=>40,'name'=>"已确认"]];
		    $this->result = $status;
		    return $this->sresult();
		}
		
		/**
		*确认收货
		*@author  liang.li
		*/
		function subOrder($data) 
		{
		    include_once ROOT_PATH.'/vendor/autoload.php';
		    $token = isset($data->token) ? $data->token : '';
		    $orderId = isset($data->order_id) ? $data->order_id : 0;
		    $user_info = getUserInfo($token);
		    if (!$user_info)
		    {
		        return $this->tresult();
		    }
		    $user_id = $user_info['user_id'];
		    if (!$orderId) 
		    {
		        return $this->eresult('缺少参数');
		    }
		    $orderLib = new Orders();
		    if ($orderLib->subOrder($orderId, $user_id)) 
		    {
		        return $this->sresult('确认收货成功');
		    }
		    return $this->eresult();
		}
		
		/**
		*取消订单
		*@author  liang.li
		*/
		function canlOrder($data) 
		{
		    include_once ROOT_PATH.'/vendor/autoload.php';
		    $token = isset($data->token) ? $data->token : '';
		    $orderId = isset($data->order_id) ? $data->order_id : 0;
		    $user_info = getUserInfo($token);
		    if (!$user_info)
		    {
		        return $this->tresult();
		    }
		    $user_id = $user_info['user_id'];
		    if (!$orderId)
		    {
		        return $this->eresult('缺少参数');
		    }
		    $orderLib = new Orders();
		    if ($orderLib->canlOrder($orderId, $user_id))
		    {
		        return $this->sresult('订单取消成功');
		    }
		    
		    return $this->eresult();
		}

		/**
		 *门店列表
		 *@author liang.li <1184820705@qq.com>
		 *@2015年10月22日
		 */
		function orderList($data)
		{
		    include_once ROOT_PATH.'/vendor/autoload.php';
		    $token = isset($data->token) ? $data->token : '';
		    $status = isset($data->status) ? $data->status : 0;
		    $pageSize  = isset($data->pageSize) ? $data->pageSize : 10;//页面大小
		    $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;//当前第几页
		    $limit = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
		    $user_info = getUserInfo($token);
		    if (!$user_info) 
		    {
		        return $this->tresult();
		    }
		    $user_id = $user_info['user_id'];
		    $conditions = "";
		    if ($status == 10) 
		    {
		        $conditions = "status=11 AND ";
		    }
		    elseif ($status == 20)
		    {
		        $conditions = "(status=20 OR status=61) AND ";
		    }
		    elseif ($status == 30)
		    {
		        $conditions = "status=30 AND ";
		    }
		    elseif ($status == 40)
		    {
		        $conditions = "status=40 AND ";
		    }
		    elseif ($status == 50)//===== 取消  =====
		    {
		        $conditions = "status=0 AND ";
		    }
		    $conditions .= "user_id = $user_id";
		    
		    
		    $orderLib = new Orders();
		    $res = $orderLib->getOrderList($conditions, $limit);
		    $this->result = $res;
		    return $this->sresult();
		    
		}
		
		/**
		*content
		*@author  liang.li
		*/
		function orderInfo($data) 
		{
		    include_once ROOT_PATH.'/vendor/autoload.php';
		    $token = isset($data->token) ? $data->token : '';
		    $orderId = isset($data->order_id) ? $data->order_id : 0;
		    $user_info = getUserInfo($token);
		    if (!$user_info)
		    {
		        return $this->tresult();
		    }
		    $user_id = $user_info['user_id'];
		    if (!$orderId)
		    {
		        return $this->eresult('缺少参数');
		    }
		    
		    $orderLib = new Orders();
		    $orderInfo = $orderLib->getOrderInfo($orderId);
		    $this->result = $orderInfo;
		   
		    
		    
		}


        
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	}