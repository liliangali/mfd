<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';
	
	$class = 'First';
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	
	
	$data = _g();
	
	//=====  轮播图  =====
	if ($action == 'shuffling')
	{
		
	}
	
	//=====  女衬衣列表  =====
	elseif ($action == 'blouseList') 
	{
	}
	//=====  女衬衣详情  =====	elseif ($action == 'blouseInfo')	{	}
	
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