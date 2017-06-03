 <?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';
	//设定允许进行操作的action数组
	$class = 'Shao';
	$act_arr = array();
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	//var_dump($action);
	//判断一下是否是允许进行的操作
	if (!in_array($action, $act_arr))
	{
	}
	$data = _g();
	//创业者
	if ($action == 'cus_list')
	{
	}elseif($action == 'cus_list_new'){

	}elseif($action == 'check'){
		
	}elseif($action == 'check_new'){
		
	}elseif($action == 'addcustom'){
		
	}elseif($action == 'editcustom'){
		
	}elseif($action == 'dropcustom'){
		
	}
	elseif($action == 'activation'){
		
	}
	elseif($action == 'coupon'){
		
	}
	elseif($action == 'add_cus'){
		
	}elseif($action == 'about')
	{
	}
	elseif($action == 'addrlist'){
		
	}elseif($action == 'cuscontent'){
		
	}elseif($action == 'addAddress'){
		
	}elseif($action == 'editAddress'){
		
	}elseif($action == 'dropAddress'){
		
	}elseif($action == 'regionlist'){
		
	}elseif($action == 'remarks'){
		
	}elseif($action == 'figure_list'){
		
	}
	 elseif($action == 'cuslists_edit'){
		
	}  elseif($action == 'order_num'){
		
	}  elseif($action == 'order_assess'){
		
	}  elseif($action == 'kuka_cotte'){
		
	}elseif($action == 'informations'){
		
	}elseif($action == 'serve_c2m'){
		
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