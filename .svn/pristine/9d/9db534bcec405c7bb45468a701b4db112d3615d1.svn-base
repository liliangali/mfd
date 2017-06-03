<?php
/**
 * 返修订单物流自动同步
 * --------------------------------------------------------
 * @author       小五
 * $Id: order_logistics_fx.php 13600 2016-01-13 03:01:09Z xiao5 $
 * $Date: 2016-01-13 11:01:09 +0800 (Wed, 13 Jan 2016) $
 * --------------------------------------------------------
 * @example
 */
/*
 select *,
from_unixtime(delivery_date,'%Y-%m-%d') as u
,curdate() as n,
date_sub(from_unixtime(delivery_date,'%Y-%m-%d'),interval 3 day) as x
,GROUP_CONCAT( distinct rcmtm_id ORDER BY rcmtm_id DESC) AS courses
,GROUP_CONCAT( distinct order_goods_id ORDER BY order_goods_id DESC) AS cgid
,GROUP_CONCAT(id ORDER BY id DESC) AS ids
from cf_order_mtm_logs
where date_sub(from_unixtime(delivery_date,'%Y-%m-%d'),interval 3 day) = curdate()
and status=10031 and logistics =0 GROUP BY  order_id order by delivery_date desc 
*/
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * 1、物流推送时间（生产完成前两天推送）
 *
 *2、推送物流的订单不可修改收货地址
 *
 *3、接口见附件 有 接口地址和内容格式，这个物流接口目前不支持修改，c2m 也是 对接的这个接口
 */
chdir(dirname(__FILE__));// cd 到php脚本所在的目录

// var_dump(strtotime('2015-09-09'));
// // var_dump(date('Y-m-d h:i:s','1440108000'));
// echo '2015-08-21 00:00:00';
// exit();
$end = "\r\n";
echo "start:time".date("Y-m-d H:i:s").$end;
$start_time = time();
include_once 'order_func.php';
/* 定制所需配置一些信息 */
include('../includes/constants.base.php');
initDb();

$day = 3; //交货前三天推送
$err_starts = 44;//物流推送失败状态码


// $sql = "select *,from_unixtime(delivery_date,'%Y-%m-%d') as u,curdate() as n,date_sub(curdate(),interval 2 day) as x from cf_order_mtm_logs where from_unixtime(delivery_date,'%Y-%m-%d') = date_sub(curdate(),interval $day day)";

$sql = "select *,
from_unixtime(delivery_date,'%Y-%m-%d') as u
,curdate() as n,
date_sub(from_unixtime(delivery_date,'%Y-%m-%d'),interval $day day) as x
,GROUP_CONCAT( distinct rcmtm_id ORDER BY rcmtm_id DESC) AS courses
,GROUP_CONCAT( distinct order_goods_id ORDER BY order_goods_id DESC) AS cgid
,GROUP_CONCAT(id ORDER BY id DESC) AS ids 
from cf_order_fx_mtm_logs 
where rcmtm_id REGEXP '^[FXNI]' and status=10031 and logistics in (0,2) GROUP BY  order_id order by delivery_date desc";

$data_list = getAll($sql);
if(!$data_list)
    exit(" no data process...".$end);

echo " data count:".count($data_list).$end;

$url = "http://api.rcmtm.com:7070/order-api/resources/ordershipService/applydelivery";

$res = array();
foreach($data_list as $k=>$v){
	$order_goods_cnt = current(getAlls("select *,rec_id AS ogid from cf_order_serve where order_id ={$v['order_id']} GROUP BY order_id"));
	if (isset($order_goods_cnt['ogid']) && isset($v['cgid'])){
		$rs = array();
		$o_message = array();
		$rs = array_diff(explode(',', $order_goods_cnt['ogid']),explode(',', $v['cgid']));
		if ($rs){
			/* 记录错误提醒*/
			$o_message['push_error'] = '订单缺少商品:'.implode($rs, ',');
			$o_message['status'] = $err_starts;
			mysql_query(update($o_message," order_id = " . $v['order_id'],"cf_order"));
			continue;
		}
	}else{
		/* 记录错误提醒*/
			$o_message['push_error'] = '订单缺少商品:'.$v['cgid'];
			$o_message['status'] = $err_starts;
			mysql_query(update($o_message," order_id = " . $v['order_id'],"cf_order"));
			continue;
	}
	
	//订单信息
	$orderInfo   = array();
	$orderExtm   = array();
	$provinces   = array();
	
	

	$orderInfo = $order_goods_cnt;
	
	//配送信息 $orderInfo['shipping_id']  1是物流 ， 2是门店
	$orderExtm['consignee'] = $orderInfo['user_name'];
	$orderExtm['email'] = '';
	$orderExtm['region_name'] = $orderInfo['ship_area'];
	$orderExtm['address'] = $orderInfo['ship_addr'];
	$orderExtm['phone_mob'] =  $orderInfo['phone_mob'];
	$orderExtm['zipcode'] = $orderInfo['ship_zip'];
	if ($orderInfo['shipping_id'] == 2){
		$sql = "SELECT * FROM `cf_serve` WHERE `idserve` = '{$orderInfo['ship_store']}'";
		$data = current(getAlls($sql));
		$orderExtm['consignee'] = $orderInfo['ship_name'];
		if (empty($orderExtm['consignee'])){
			$orderExtm['consignee'] = $data['linkman'];
		}
		$orderExtm['zipcode'] = $data['post_code'];
		$orderExtm['email'] = $data['email'];
		$orderExtm['region_name'] = $data['region_name'];
		$orderExtm['address'] =  $data['serve_address'];
		$orderExtm['phone_mob'] = $data['mobile'];
	}
	
	$provinces = preg_split('/[\n\r\t\s\,]+/i', $orderExtm['region_name']);
	
	//处理省会.
	foreach ((array)$provinces as $rk=>$rv){
		$province = str_replace('市','',$rv);
		if ($rk == 1 && in_array($province, array('北京','天津','上海','重庆'))){
			$provinces[1] = $province;
			$provinces[2] = $province;
		}
	}
	/* 收货人手机号码＋收货人 唯一 */
	$res[$orderExtm['phone_mob'].'_'.$orderExtm['consignee']][] = array('o'=>$orderInfo,'e'=>$orderExtm,'p'=>$provinces,'om'=>$v);
	
}


foreach ($res as $m=>$d){
	$order = array();
	foreach ($d as $key=>$val){
		$order['o_ids']                     .=  $val['o']['order_id'].',';
		$order['courses']                 .=  $val['om']['courses'].',';
		$order['order_goods_id']     .=  $val['om']['order_goods_id'].',';
		$order['ids']                        .=  $val['om']['ids'].',';
		$order['cgid']                      .=  $val['om']['cgid'].',';
		if ($val['o']['memo']){
			$order['meo']                      .= 'sn：'.$val['o']['order_sn'].'-'.$val['o']['memo'];
		}else{
			$order['meo']                      .= '';
		}
		$order['provinces']               = $val['p'];
		$order['xtm']                       = $val['e'];
	}
	
		$data = getXml($order);

		$content = addslashes($data);
		$sql = "INSERT INTO cf_rcmtm_logistics_log (order_id,is_fx, rec_id, content,rs) VALUES ('".rtrim($order['o_ids'], ",")."','1','".rtrim($order['order_goods_id'], ",")."', '{$content}','')";

		mysql_query($sql);
	
	
		$respon_data = curl_post_ssl($url, $data);
		$up_order = array();
		$up_logistics_data = array();
		$up_data = array();
		if($respon_data['data']['code'] == 101){//不是101就是错误的
			$up_data['logistics']  = 1;
	// 		$up_data['up_time'] = time();
		}else{
			/* 成功更改状态 */
			$up_data['logistics']  = 2;
		}
	
		/* 推送log记录返回状态和返回值 */
		$up_logistics_data['rs'] = $respon_data['data']['rs'];
		$up_logistics_data['code'] = $respon_data['data']['code'];
		mysql_query(update($up_logistics_data," id = " . mysql_insert_id(),"cf_rcmtm_logistics_log"));
	
	
	
		$sql = update($up_data," id in (" . rtrim($order['ids'],',').")","cf_order_fx_mtm_logs");
		
	    mysql_query($sql);
	
	    /* 同步订单表 */
	    if ($respon_data['data']['code'] == 101){
	    	$usql = "UPDATE cf_order SET express='{$respon_data['data']['rs']}',push_error='',push_xml='' WHERE order_id in (".rtrim($order['o_ids'],',').")";
	        mysql_query($usql);
	        
	        //返修订单修改表状态
	        mysql_query("UPDATE cf_order_serve SET status=10,send_wl_sn='{$respon_data['data']['rs']}' WHERE order_id in (".rtrim($order['o_ids'],',').")");
	        echo "UPDATE cf_order_serve SET status=10,send_wl_sn='{$respon_data['data']['rs']}' WHERE order_id in (".rtrim($order['o_ids'],',').")";
	        echo "\n\t\r";
	        mysql_query("UPDATE order_serve_info SET info_status=10 WHERE order_id in (".rtrim($order['o_ids'],',').")");
	        echo "UPDATE cf_order_serve_info SET info_status=10 WHERE order_id in (".rtrim($order['o_ids'],',').")";
	        echo "\n\t\r";
	        //订单操作日志
	        $isql = "INSERT INTO cf_order_logs (order_id,op_id,op_name,alttime,behavior,log_text,to_status,remark) VALUES ";
	        foreach (explode(',', $order['o_ids']) as $id){
	        	if ($id){
	        		$isql .= "('".$id."','0','auto cron','{$start_time}','pushDelFx','返修订单状态从[物流队列]更新到[LG已生成]!',60,'物流同步成功：{$respon_data['data']['rs']}'),";
	        	}
	        	 
	        }
	        mysql_query(rtrim($isql, ","));
	         
	
	    }else{//订单异常
	    	$usql = "UPDATE cf_order SET status=$err_starts,push_error='{$respon_data['data']['rs']}',push_xml='{$content}'  WHERE order_id in (".rtrim($order['o_ids'],',').")";
	    	mysql_query($usql);
	    	
	    	//返修订单修改表状态
	    	mysql_query("UPDATE cf_order_serve SET status=9 WHERE order_id in (".rtrim($order['o_ids'],',').")");
// 	    	echo "UPDATE cf_order_serve SET status=7 WHERE order_id in (".rtrim($order['o_ids'],',').")";
// 	    	echo "\n\t\r";
	    	mysql_query("UPDATE cf_order_serve_info SET info_status=9 WHERE order_id in (".rtrim($order['o_ids'],',').")");
// 	    	echo "UPDATE order_serve_info SET info_status=7 WHERE order_id in (".rtrim($order['o_ids'],',').")";
// 	    	echo "\n\r\t";
	    	
	    	 
	    	
	    	//订单操作日志
	    	$isql = "INSERT INTO cf_order_logs (order_id,op_id,op_name,alttime,behavior,log_text,to_status,remark) VALUES ";
	    	foreach (explode(',', $order['o_ids']) as $id){
	    		if ($id){
	    			$isql .= "('".$id."','0','auto cron','{$start_time}','pushDelFx','返修订单状态从[物流队列]更新到[LG已生成]!',$err_starts,'物流同步失败：{$respon_data['data']['rs']}'),";
	    		}
	    		 
	    	}
	    	mysql_query(rtrim($isql, ","));
	    	
	    }


}

function getXml($order){

	$ordernoS                         =    rtrim($order['courses'], ",");                            //订单ID 多个id请用英文逗号隔开
	$sideordernoS                   =     '';                                                                    //周边产品订单，多个id用英文逗号隔开
	$repairordernoS                =     '';                                                                    //返修产品订单，多个id用英文逗号隔开
	$expresS                          =    101;                                                                    //快递公司代码 必填 默认顺丰
	$expressNamE                  =    '顺风快递';                                                         //快递公司名称 必填
	$countrY                           =     $order['provinces'][0];                                                   //国家名 必填
	$statE                              =    $order['provinces'][1];                                                     //省/州名称 必填
	$citY                                =    $order['provinces'][2];                                                     //市名称  必填
	$addresS                          =   $order['xtm']['address'];                                      //详细地址 必填
	$teL                                  =    $order['xtm']['phone_mob'];                                //联系方式 必填
	$memO                              =    $order['meo'];                                          //备注信息
	$companY                          = '';                                                                         //公司名       
	$contacT                           =    $order['xtm']['consignee'];                                   //联系人 必填
	$zipcodE                           =    '111';                                                                  //邮政编码 必填
	$payTypE                          =    '1';                                                                    //支付方式 必填 
	$payAccounT                     =    '1111111';                                                           //支付账号 必填
	$payAccountZipcodE          =    '266000';                                                          //支付账号所在地邮编 必填
	$taxPayTypE                     =    '1';                                                                    //关税支付方式  必填
	$taxPayAccounT                =    '111111';                                                              //关税支付账号 必填
	$taxPayAccountZipcodE     =     '266000';                                                        //关税支付账号所在地邮编
	$emaiL                              =     $order['xtm']['email'];                                          //邮箱地址
	$deliveryTypE                   =    '1';                                                                     //发货方式 必填
	
/* 	注：订单和返修单以及返修单三者不可都为空
	express字段 101 顺风快递 102 联邦快递 103 DHL快递 104 UPS快递 105 圆通快递 106 自提
	country,state,city字段内容请参照dict
	payType 1 寄货方付 2 收货方付 3 第三方付
	taxPayType 1 寄货方付 2 收货方付 3 第三方付 */
	
	
$xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<logisticInfo>
<ordernos></ordernos>  <!--订单ID 多个id请用英文逗号隔开  -->
<sideordernos>{$sideordernoS}</sideordernos> <!--周边产品订单，多个id用英文逗号隔开-->
<repairordernos>{$ordernoS}</repairordernos> <!--返修产品订单，多个id用英文逗号隔开-->
<express>{$expresS}</express>  <!-- 快递公司代码 必填 请根据给出的代码填写 -->
<expressName>{$expressNamE}</expressName> <!-- 快递公司名称 必填 -->
<country>{$countrY}</country>  <!-- 国家名 必填 -->
<state>{$statE}</state>  <!-- 省/州名称 必填 -->
<city>{$citY}</city>    <!-- 市名称  必填-->
<address>{$addresS}</address>  <!-- 详细地址 必填 -->
<tel>{$teL}</tel> <!-- 联系方式 必填 -->
<memo>{$memO}</memo> <!-- 备注信息 -->
<company>{$companY}</company> <!-- 公司名 -->
<contact>{$contacT}</contact> <!-- 联系人 必填 -->
<zipcode>{$zipcodE}</zipcode> <!-- 邮政编码 必填 -->
<payType>{$payTypE}</payType>  <!-- 支付方式 必填 请根据根除的代码填写 -->
<payAccount>{$payAccounT}</payAccount> <!-- 支付账号 必填 -->
<payAccountZipcode>{$payAccountZipcodE}</payAccountZipcode> <!-- 支付账号所在地邮编 必填 -->
<taxPayType>{$taxPayTypE}</taxPayType> <!-- 关税支付方式  必填  请根据根除的代码填写-->
<taxPayAccount>{$taxPayAccounT}</taxPayAccount> <!-- 关税支付账号 必填  -->
<taxPayAccountZipcode>{$taxPayAccountZipcodE}</taxPayAccountZipcode> <!-- 关税支付账号所在地邮编 必填 -->
<email>{$emaiL}</email> <!-- 邮箱地址 -->
<deliveryType>{$deliveryTypE}</deliveryType> <!-- 发货方式 必填 -->
</logisticInfo>
EOT;

	return $xml;
}
