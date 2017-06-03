<?php
/**
 * 面料无库存自动下架
 *
 * @date 2015年11月30日 下午1:18:14 
 * 
 * @author Ruesin
 */

chdir(dirname(__FILE__));

echo "start:time".date("Y-m-d H:i:s").PHP_EOL;
$start_time = time();

define('LOGPATH', '/data/logs/mfd_order/fabric_sms/');
define('LOGFILE',  LOGPATH.date('Y-m-d_H:i:s').'_fabric_sale.txt');
if(!is_dir(LOGPATH)){
    mkdir(LOGPATH,0777,true);
}

$msg = $log = array();

include_once 'order_func.php';
initDb();

$logs = readLogs();


//套装
$cstIds = [];  //套装使用的所有面料ID
$sql = "SELECT * FROM `diy_custom_fabric` GROUP BY `item_id`";
$cstFbs = getAlls($sql);
foreach ($cstFbs as $row){
    $cstIds[$row['item_id']] = $row['item_id'];
}

if ($cstIds){
    $sql = "SELECT * FROM `diy_fabric` where `CATEGORYID` IN (8001,8030,8050) AND `STOCK` < 3 AND `ID` IN (".implode(',', $cstIds).")  ";
    $cstNoStock = getAlls($sql);//样衣无库存的面料
    foreach ($cstNoStock as $cRow){
        $customIds[$cRow['ID']] = $cRow['ID'];  //样衣需要下架的面料IDS
    }
    
    if ($customIds){
        
        //面料下架
        $usql = "UPDATE diy_fabric SET is_sale = 0  WHERE `ID` IN (".implode(',', $customIds).")";
        mysql_query($usql);
        $log['suit_fabrics'] = $customIds;
        
        $sql = "SELECT * FROM `diy_custom_fabric` WHERE `item_id` IN (".implode(',', $customIds).")  GROUP BY `custom_id`";
        $cstWillSale = getAlls($sql);//样衣需要下架的样衣IDS
    }
    
    if ($cstWillSale){
        $sql = "SELECT * FROM `cf_suit_relation` WHERE 1 = 0 ";
        
        foreach ($cstWillSale as $cwVal){
            $sql .= " OR  FIND_IN_SET('{$cwVal['custom_id']}',`jbk_id`)";
        }
        
        $suits = getAlls($sql); //需要下架的套装
        foreach ($suits as $sVal){
            $suitIds[$sVal['tz_id']] = $sVal['tz_id'];
        }
        if ($suitIds){
            //套装下架
            $usql = "UPDATE cf_suit_list SET is_sale = 0  WHERE is_sale = 1 AND `id` IN (".implode(',', $suitIds).")";
            mysql_query($usql);
            $log['suit_ids'] = $suitIds;
        }
    }
}

// Diy

$sql = "SELECT * FROM `diy_fabric` where `CATEGORYID` IN (8001,8030,8050) AND `STOCK` <= 3  AND is_sale = 1";
$diyFabrics = getAlls($sql);
foreach ($diyFabrics as $dVal){
    $diyIds[$dVal['ID']] = $dVal['ID'];
}

$usql = "UPDATE diy_fabric SET is_sale = 0  WHERE `ID` IN (".implode(',', $diyIds).")";
mysql_query($usql);
$log['diy_fabrics'] = $diyIds;
writeLogs($log);

function readLogs(){
    if(!is_file(LOGFILE)){
        return array();
    }
    $str = file_get_contents(LOGFILE);
    return json_decode($str,1);
}

function writeLogs($data = array()){
    $str = json_encode($data);
    return file_put_contents(LOGFILE, $str);
}


exit();

