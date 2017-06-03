<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';
	
	$class = 'Rcmtm';
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	
	$data = _g();
	
	if ($action == 'gStatusByJson')
	{
	    die();
	    //http://api.alicaifeng/soap/rcmtm.php?act=gStatusByJson&token=15e06eb2927e19c8ff2cf6e9a2e84285&params={"101":["SN1"],"103":["SN4","SN5","SN6"]}
	    $data->params = str_replace('\\"', '"', $data->params);
	    
	}
	
	elseif ($action == 'gStatusById')
	{
	    die();
	    //http://api.alicaifeng/soap/rcmtm.php?act=gStatusById&token=893268bd846c3ab7cbc257cd9ea6e149&status=101&ids=SN111,SN112,SN113
	    
	}
	
	elseif ($action == 'gUpdateStatus')
	{
// 	    $data = '{"DeliveryNo":"null","Porxy":"QC2C","ShippingCode":"LGEU151016044002927","Status":"10033","WaybillNo":"1Z8633X90491096602"}';
// 	    $data = '{"Porxy":"QC2C","ShippingCode":"LGNE151012083432636","Status":"10032"}';
// 	    $data = '{"DeliveryDate":"2015-10-24 00:00:00.0","OrderNo":"QC2C15100179","Porxy":"QC2C","Status":"10031","SysCode":"NI1510160178"}';
// 	    $data = '{"DeliveryDate":"2015-10-26 00:00:00.0","OrderNo":"QC2C15100179","Porxy":"QC2C","Status":"10030","SysCode":"NI1510090079"}';
	    $data = file_get_contents( "php://input");
	    if(!$data) die();
        	    
	    ///<form action="http://api.mfd.cn/soap/rcmtm.php?act=gUpdateStatus" method="post">
//         <input type="text" value='{"OrderNo":"TEST15070713","Status":"10030","Porxy":"QC2C","SysCode":"NI1507310259","DeliveryDate":"2015-08-20"}' name="params" />
//         <input type="submit" />
//         </form>
	    //http://api.mfd.cn/soap/rcmtm.php?act=gUpdateStatus&params={"OrderNo":"TEST15070713","Status":"10030","Porxy":"SADA","SysCode":"NI1507310259","DeliveryDate":"2015-08-20"}
	    //$data->params = str_replace('\\"', '"', $data->params);
	}
	
	elseif ($action == 'gUpdateStatusTest'){
// 	    $data = '{"DeliveryNo":"null","Porxy":"QC2C","ShippingCode":"liuguangxin","Status":"10033","WaybillNo":"1Z8633X90491096602"}';
// 	    $action = 'gUpdateStatus';
	}

    elseif ($action== 'gOrderServeStatus'){

        $data = file_get_contents( "php://input");
        if(!$data) die();
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
