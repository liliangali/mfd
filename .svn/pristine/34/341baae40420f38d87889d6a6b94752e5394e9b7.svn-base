 <?php

    /**
    *创业培训
    *@author zhaoxr<2621270755@qq.com>
    *@2015年5月13日
    */


	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';

	$class = 'SYB';
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	$data = _g();

	//=====推送关于创业图文推送资料====
	if ($action == 'push')
	{
	    
	}

	//===== test  =====
	elseif ($action == 'test')
	{
	}
	
	else if ($action == 'searchRecord')
	{
		 
	}


	else
	{
		echo "Lack of method ?action";
	}

	$arr  =  array(
	    'data' => $data,
	);
	$rs   = getSoapClients($class,$action,$arr);
	die($rs);


?>