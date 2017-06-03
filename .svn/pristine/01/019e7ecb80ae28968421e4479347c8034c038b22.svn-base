<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';
	
	$class = 'Cart';
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	
	
	$data = _g();
	
	//=====  地区列表  =====
	if ($action == 'regionList')
	{
	}
	
	//=====  门店列表  =====
	elseif ($action == 'serveList')
	{
	}
	//=====  历史量体数据  =====
	elseif ($action == 'getFigure')
	{
	}
	//=====  获得量体师列表  =====
	elseif ($action == 'getFigureInfo')
	{
	}
	//=====  获得量体师列表  =====
	elseif ($action == 'getLiangti')
	{ 
	}
	
	
	//=====   添加购物车  =====
	elseif ($action == 'addCart')
	{
	}    
	
	//=====   添加购物车  =====
	elseif ($action == 'addCartDiy')
	{
	}
	
	//=====   我的财富接口  =====
	elseif ($action == 'wealth')
	{
	}
	
	//=====   根据品类获取品类对应的尺码  =====
	elseif ($action == 'getSize')
	{
	}
	
	//=====  历史量体数据  =====
	elseif ($action == 'getFigureMoney')
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