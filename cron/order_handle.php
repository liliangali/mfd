<?php
chdir(dirname(__FILE__));// cd 到php脚本所在的目录
$end = "\r\n";

echo "start:time".date("Y-m-d H:i:s").$end;
$start_time = time();
include_once 'order_func.php';
initDb();
 
$customs = array(
        '0003' => array(
                'cate_id' => '3',
                'cate_name' => '西服',
                'fabric_m' => '1.95',
                'lining_m' => '2',
        ),
        '0004' => array(
                'cate_id' => '2000',
                'cate_name' => '西裤',
                'fabric_m' => '1.45',
                'lining_m' => '0',
        ),
        '0005' => array(
                'cate_id' => '4000',
                'cate_name' => '马夹',
                'fabric_m' => '0.78',
                'lining_m' => '0',
        ),
        '0006' => array(
                'cate_id' => '3000',
                'cate_name' => '衬衣',
                'fabric_m' => '1.6',
                'lining_m' => '0',
        ),
        '0007' => array(
                'cate_id' => '6000',
                'cate_name' => '大衣',
                'fabric_m' => '2.58',
                'lining_m' => '2',
        ),
        '0017' => array(
                'cate_id' => '15000',
                'cate_name' => '男短裤',
                //'fabric_m' => '2.58',
                //'lining_m' => '2',
        ),
        '0011' => array(
                'cate_id' => '95000',
                'cate_name' => '女西服',
                'fabric_m' => '1.95',
                'lining_m' => '2',
        ),
        '0012' => array(
                'cate_id' => '98000',
                'cate_name' => '女西裤',
                'fabric_m' => '1.45',
                'lining_m' => '0',
        ),
        '0016' => array(
                'cate_id' => '11000',
                'cate_name' => '女衬衣',
                'fabric_m' => '1.6',
                'lining_m' => '0',
        ),
        '0021' => array(
                'cate_id' => '103000',
                'cate_name' => '女大衣',
                'fabric_m' => '2.58',
                'lining_m' => '2',
        ),

);

//$exTime = time() - 7200;
$exTime = time() - 86400;

$sql = "SELECT order_id,order_sn,status,add_time,user_id,money_amount,coin FROM cf_order as o WHERE  add_time < {$exTime} AND status = 11 AND extension = 'news' ";

$data_list = getAlls($sql);

if(!$data_list)
    exit(" no data...".$end);

foreach ($data_list as $row){
    $orders[$row['order_id']] = $row;
    $oIds[$row['order_id']] = $row['order_id'];
    $oSns[$row['order_sn']] = $row['order_sn'];
    if($row['money_amount'] > 0 ){
        $moneys[$row['user_id']] = isset($moneys[$row['user_id']]) ? $moneys[$row['user_id']] : 0;
        $moneys[$row['user_id']] += $row['money_amount'];
    }
}

//返还余额 Bgn ↓
if(isset($moneys)){
    foreach ($moneys as $key=>$val){
        mysql_query("UPDATE cf_member SET money = money + {$val} , frozen = frozen - {$val}  WHERE  user_id = '{$key}'");
        echo "$key money update success".$end;
    }
}

//返还库存 Bgn ↓
$ogSql = " SELECT quantity,cloth,fabric FROM cf_order_goods WHERE  order_id IN (".implode(',', $oIds).") ";
$orderGoods = getAlls($ogSql);

foreach ($orderGoods as $row){
    if($row['fabric'] && isset($customs[$row['cloth']])){
        $stc[$row['fabric']] = isset($stc[$row['fabric']]) ? $stc[$row['fabric']] : 0 ;
        $stc[$row['fabric']] += $row['quantity']*($customs[$row['cloth']]['fabric_m']);
    }
}
if(isset($stc)){
    $fbs = '';
    foreach ($stc as $key=>$val){
        mysql_query("UPDATE diy_fabric SET STOCK = STOCK + $val WHERE  CODE = '{$key}'");
        $fbs .= $key.',';
    }
    echo "fabric $fbs update success".$end;
}

$kukas = array();

$orderKukas = getAlls(" SELECT * FROM cf_order_kuka WHERE  order_id IN (".implode(',', $oIds).") ");

if($orderKukas){
    foreach ($orderKukas as $row){
        $oKukaIds[$row['id']] = $row['id'];
        $kukaIds[$row['k_id']] = $row['k_id'];
        if (isset($kukas[$row['order_id']])){
            $kukas[$row['order_id']] += $row['k_money'];
        }else{
            $kukas[$row['order_id']] = $row['k_money'];
        }
    }
    mysql_query("UPDATE cf_special_code SET is_order = '0' WHERE id IN (".implode(',', $kukaIds).") ");
    mysql_query("UPDATE cf_order_kuka SET is_active = '0' WHERE id IN (".implode(',', $oKukaIds).") ");
    
    echo "kuka update success".$end;
}
    



//返还抵扣券 Bgn ↓
$orderDebits = getAlls(" SELECT d_id FROM cf_order_debit WHERE  order_id IN (".implode(',', $oIds).") ");
if($orderDebits){
    foreach ($orderDebits as $row){
        $dIds[$row['d_id']] = $row['d_id'];
    }
    mysql_query("UPDATE cf_debit SET is_used = '0' WHERE id IN (".implode(',', $dIds).") ");
    echo "debits update success".$end;
}


//返还首单名额 Bgn ↓
mysql_query("UPDATE cf_order_first_log SET is_active = '0' WHERE order_id IN (".implode(',', $oIds).") ");
echo "first_log update success".$end;

//返还买一赠一名额 + 更新状态 Bgn ↓
mysql_query("UPDATE cf_order SET status = '0' ,one_status = '0'  WHERE order_id IN (".implode(',', $oIds).") ");
echo "order ".implode(',', $oIds)." status&one_status update success".$end;




//foreach ($oIds as $val){
foreach ($orders as $val){
    
    mysql_query("INSERT INTO cf_order_logs (order_id,op_id,op_name,alttime,behavior,log_text,remark) VALUES ('{$val['order_id']}','0','auto cron','{$start_time}','autoCancel','订单状态从[待付款]更新到[已取消]!','待付款超时')");
    
    //您的订单201601058234已取消，用于结算的500元酷卡、100元余额、400麦富迪币已退回相关账户，请注意查收【麦富迪定制】
    $smsStr = $phone = '';
    $phone = empty($val['ship_mobile']) ? $val['user_name'] : $val['ship_mobile'];
    $smsStr = '您的订单'.$val['order_sn'].'已取消';
    
    if (isset($kukas[$val['order_id']]) || intval($val['money_amount'])>0 || intval($val['coin'])>0 ){
        $smsStr .= '，用于结算的';
        
        if (isset($kukas[$val['order_id']])){
            $smsStr .= intval($kukas[$val['order_id']]).'元酷卡、';
        }
        
        if (intval($val['money_amount']) >0){
            $smsStr .= intval($val['money_amount']).'元余额、';
        }
        
        if (intval($val['coin'])>0){
            $smsStr .= intval($val['coin']).'麦富迪币、';
        }
        
        $smsStr .= '已退回相关账户，请注意查收';
    }
    
    $smsStr .= '【麦富迪定制】';
    
    SendSms($phone, $smsStr);
    echo $smsStr.PHP_EOL;
}

die();
