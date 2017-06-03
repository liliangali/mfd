<?php
/**
 * 面料库存预警
 * 
 * @date 2015-9-2 上午10:39:06
 * @author Ruesin
 */

chdir(dirname(__FILE__));// cd 到php脚本所在的目录

echo "start:time".date("Y-m-d H:i:s").PHP_EOL;
$start_time = time();

define('LOGPATH', '/data/logs/mfd_order/fabric_sms/');
define('LOGFILE',  LOGPATH.date('Y-m-d').'_fabric_sms.txt');
if(!is_dir(LOGPATH)){
    mkdir(LOGPATH,0777,true);
}

$phones = array(18806390080,15318775959,15092071569);
//$phones = array(15936722475,13969769630);
$msg = $log = array();

include_once 'order_func.php';
initDb();

$logs = readLogs();

$sql = "SELECT * FROM `diy_custom_fabric` GROUP BY `item_id`";
$cstFbs = getAlls($sql);
foreach ($cstFbs as $row){
    $cstIds[$row['item_id']] = $row['item_id'];
}
//$sql = "SELECT * FROM `diy_fabric` where `CATEGORYID` IN (8001,8030,8050) AND (( is_sale = 0 AND `ID` IN (".implode(',', $cstIds).") ) OR is_sale = 1)";
$sql = "SELECT * FROM `diy_fabric` where `CATEGORYID` IN (8001,8030,8050) AND `STOCK` <= 15 AND `STOCK` > 0  AND is_warning = 1 AND ( `ID` IN (".implode(',', $cstIds).")  OR is_sale = 1)";
$fWarning = getAlls($sql);

$sql = "SELECT * FROM `diy_fabric` where `CATEGORYID` IN (8001,8030,8050) AND  `STOCK` <= 0 AND is_warning = 1 AND ( `ID` IN (".implode(',', $cstIds).")  OR is_sale = 1)";
$fZero = getAlls($sql);


foreach ((array)$fWarning as $key=>$val){
    if(isset($logs['warning'][$val['CODE']])){
        continue;
    }
    $msg[] = "【面料库存预警通知】{$val['tname']}({$val['CODE']})面料已达到预警值15米，请及时供应面料，确保库存充足。";
    
    $log['warning'][$val['CODE']] = $val['CODE'];
    
}
    
foreach ((array)$fZero as $key=>$val){
    if(isset($logs['zero'][$val['CODE']])){
        continue;
    }
    $msg[] = "【面料下架通知】{$val['tname']}({$val['CODE']})面料因库存不足已自动下架，请及时供应面料，确保库存充足。";
    
    $log['zero'][$val['CODE']] = $val['CODE'];
}
if($msg){
    foreach ($msg as $val){
        foreach ($phones as $ph){
            $rs = SendSms($ph, $val);
            sleep(10);
        }
    }
    echo 'Send ' . count($msg) . 'message.';
    writeLogs($log);
}

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


/*短信发送*/
function SendSms($phone, $msg) {
    $err = array('-1'=>'传递参数错误','-2'=>'用户id或密码错误','-3'=>'通道id错误','-4'=>'手机号码错误','-5'=>'短信内容错误','-6'=>'余额不足错误=','-7'=>'绑定ip错误',
            '-8'=>'未带签名','-9'=>'签名字数不对','-10'=>'通道暂停','-11'=>'该时间禁止发送','-12'=>'时间戳错误','-13'=>'编码异常','-4'=>'发送被限制');

    //获得配置项
    //$conf = include ROOT_PATH.'/data/settings.inc.php';//获得配置信息
    //$user_id   = $conf['msg_pid']; // sms9平台用户id
    //$pass      = $conf['msg_key']; // 用户密码
    //$channelid = $conf['channelid']; // 发送频道id

    //后台无配置地方，暂时写死--
    $timestamp = time();
    $channelid = 12852 ; // 发送频道id
    $cpid = 11664;
    $ps = md5("mfd123_".$timestamp."_topsky");
    
    $msg = iconv("UTF-8",'gbk//ignore',$msg);
    $url = "http://admin.sms9.net/houtai/sms.php?cpid={$cpid}&password={$ps}&channelid={$channelid}&tele={$phone}&timestamp={$timestamp}&msg={$msg}";

    $rs = file_get_contents($url);

    //$back = substr($rs,6);
    $pre = substr($rs,0,7);
    if($pre != 'success') {
        return false;
    }
    return true;
}

exit();

