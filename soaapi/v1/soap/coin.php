<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	
	include_once './../config.php';
	
	$class = 'Coin';
	
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
	
	$data = _g();
	
	$arr = array(
	    'data' => $data,
	);
	
	$rs = getSoapClients($class, $action, $arr);
	die($rs);
?>