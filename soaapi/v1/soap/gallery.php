<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';
	
	$class = 'Gallery';
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	
	
	$data = _g();
	
	//=====  抵用券列表  =====
	if ($action != 'getList')
	{
		echo "Lack of method ?action";
	}
	
	$arr = array(
	    'data' => $data,
	);
	$rs = getSoapClients($class, $action, $arr);
	die($rs);
	

?>