<?php
$end = "\n";
echo "start:time".date("Y-m-d H:i:s");
$start_time = time();
include_once 'order_func.php';
initDb();

$sql = "select * from cf_order_cron where status = 1 ";
$data = getAll($sql);
if(!$data)
	exit(" no data process...").$end;

echo "data count:".count($data).$end;
$url = "https://api.rcmtm.com:443/order-api/resources/order/status";
foreach($data as $k=>$v){
	echo $k . " ". $v['order_id'] . " " . $v['rcmtem_order_id'] . " ";
	$respon_data = curl_get_ssl($url.$v['rcmtem_order_id']);
	$up_data = array('up_time'=>time());
	if($respon_data['err']){
		
		$up_data['status'] = 2;
		$up_data['err_msg'] = $respon_data['msg'];
		$up_data['rcmtem_status'] = -1;//CURL请求过程中的错误~
	}else{
		$respon_code = json_decode($respon_data['data'],1);
		if(10034 == $respon_code['code']){
			$up_data['status'] = 3;
		}
		
		$up_data['rcmtem_status']  = $respon_code['code'];
	}
    //还要 更新  order_goods 表
	$sql = update($up_data," id = " . $v['id'],"cf_order_cron");
	echo $sql.$end;
	mysql_query($sql);
}
