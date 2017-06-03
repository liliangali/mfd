<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	
	include_once './../config.php';
	
	$class = 'Channel';
	
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
	
	$data = _g();

	$category = 0;
	
	if(!isset($data->type)){
	    $data->type = "man";
	}
	
	if($data->type == 'man'){
	    $category = '128493';
	}
	 
	if($data->type == 'womman'){
	    //$category = '';
	}
	
	$data->category = $category;
	
	$arr = array(
	    'data' => $data,
	);
	
	$rs = getSoapClients($class, $action, $arr);
	die($rs);
?>