 <?php
	
	/**
	 *意见反馈
	 *@author zhaoxr<2621270755@qq.com>
	 *@2015年5月13日
	 */
	ini_set ( "soap.wsdl_cache_enabled", "0" );
	include_once './../config.php';
	
	$class = 'Feedback';
	$action = isset ( $_REQUEST ['act'] ) ? trim ( $_REQUEST ['act'] ) : '';
	$data = _g ();
	
	// =====意见反馈推送====
	if ($action == 'feedback') {
	}	

	// ===== test =====
	elseif ($action == 'test') {
	} 

	else {
		echo "Lack of method ?action";
	}
	
	$arr = array (
			'data' => $data 
	);
	$rs = getSoapClients ( $class, $action, $arr );
	die ( $rs );
	
	?>