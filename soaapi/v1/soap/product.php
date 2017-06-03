<?php
    header('Access-Control-Allow-Origin: *');
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';

	//设定允许进行操作的action数组
	$class = 'Product';
	$act_arr = array();
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	//判断一下是否是允许进行的操作
	if (!in_array($action, $act_arr))
	{
	}
	$data = _g();
	if ($action == 'goods_list')
	{
		
	}elseif($action == 'goodscontent')
	{
	}elseif($action == 'goodsspec')
	{
		
	}elseif($action == 'access')
	{
		
	}elseif($action == 'addcart')
	{
		
	}elseif($action == 'priceaso')
	{
		
	}elseif($action == 'cartcount')
	{
		
	}
	
	else
	{
		echo "Lack of method ?action";
	}
	$arr  =  array(
			'data'=>$data,
	);

	$rs   = getSoapClients($class,$action,$arr);
	die($rs);


?>