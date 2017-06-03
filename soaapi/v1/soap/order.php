 <?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';

	include_once PROJECT_PATH.'kernel/class/Order.class.php';;
	//设定允许进行操作的action数组
	$class = 'Order';
	$act_arr = array('index');
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	$data = _g();
	$arr = array(
			'data' => $data,
	);


	//判断一下是否是允许进行的操作
	if (!in_array($action, $act_arr))
	{
	}

	//发布订单
	if ($action == 'orderList')
	{
	}
	
	//发布订单
	elseif ($action == 'orderInfo')
	{
	}
	
	//订单状态
	elseif ($action == 'orderStatus')
	{
	}
	
	elseif ($action == 'subOrder')
	{
	}
	
	elseif ($action == 'canlOrder')
	{
	}

	//=====裁缝的需求列表=====
	elseif ($action == 'demandList')
	{
	    $order = new Order();
	    $res = $order->demandList(array(1));
	echo json_encode($res);exit;    
	}

	
	else
	{
		echo "Lack of method ?action";
	}
	$rs = getSoapClients($class, $action, $arr);
	die($rs);


?>