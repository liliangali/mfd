<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';
	
	$class = 'Profit';
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	
	
	$data = _g();
	
	//=====  抵用券列表  =====
	if ($action == 'debitList')
	{
	}
	
	//=====  抵用券个数  =====
	elseif ($action == 'debitNum')
	{
	
	}
	
	
	//=====  红包列表  =====
	elseif ($action == 'giftList') 
	{
	}
	
	//=====  商品详情  =====
	elseif ($action == "goodsInfo")
	{
	}
	
	//=====  收入记录  =====
	elseif ($action == "incomeList")
	{
	}
	
	
	//=====  首页推荐  =====
	elseif ($action == 'indexList')
	{
	}
	
	//=====   订单列表  =====
	elseif ($action == 'orderList')
	{
	}
	//=====   订单列表  =====
	elseif ($action == 'orderList_v2')
	{
	}
	
	//=====   订单详情  =====
	elseif ($action == 'orderInfo')
	{
	}
	//=====   订单详情  =====
	elseif ($action == 'orderInfoF')
	{
	}
	
	//=====  订单详情量体信息接口  =====
	elseif ($action == 'orderInfoS')
	{
	}
	
	//=====  订单详情量体信息接口  =====
	elseif ($action == 'orderInfoS_v3')
	{
	}
	
	//=====  订单详情工艺接口  =====
	elseif ($action == 'orderInfoT')
	{
	}
	
	//=====  订单详情快递单号  =====
	elseif ($action == 'orderInfoK')
	{
	}
	
	//=====  收益提现  =====
	elseif ($action == 'profitCash')
	{
	}
	
	//=====  收益提现列表  =====
	elseif ($action == 'profitCashList')
	{
	}
	
	//=====  收益列表  =====
	elseif ($action == 'profitList')
	{
	}
	
	//=====  收益转余额  =====
	elseif ($action == 'profitToMoney')
	{
	}
	
	//=====  修复抵用券的数据  =====
	elseif ($action == 'bugDebit')
	{
	}
	
	elseif ($action == 'orderCash')
	{
	}
	
	//=====  激活酷卡   =====
	elseif ($action == 'activDebit')
	{
	}
	// =====添加收藏
	elseif ($action == 'addCollect')
	{
	}
	
	// =====删除收藏
	elseif ($action == 'dropCollect')
	{
	}
	
	// =====我的收藏列表
	elseif ($action == 'myCollect')
	{
	}
	

	else
	{
		echo "Lack of method ?action";
	}
	
	$arr = array(
	    'data' => $data,
	);
	
	$rs = getSoapClients($class, $action, $arr);
	die($rs);
	

?>