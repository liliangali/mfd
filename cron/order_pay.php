<?php
/**
 * 订单支付成功后 给店长和量体师推送通知  给顾客发短息
 *
 * @date 2015年12月11日 上午10:22:28
 *
 * @author Ruesin
 */

error_reporting(E_ALL & ~E_DEPRECATED);

chdir(dirname(__FILE__));
$root = str_replace('/cron/order_pay.php', '', str_replace('\\', '/', __FILE__));
$time =  microtime(true);
echo "start:time".date("Y-m-d H:i:s").PHP_EOL;
$start_time = time();

include_once 'order_func.php';
initDb();


$lasttime = 1449479008;
$sql = "SELECT * FROM `cf_order` WHERE `status` = '12' AND has_measure = '1' AND pay_time > ".$lasttime;
$orders = getAlls($sql);

if (!$orders){
    echo "No Data Need Send!".PHP_EOL;
    die();
}

foreach ($orders as $o){
    $oIds[$o['order_id']] = $o['order_id'];
}

$sql = " SELECT * FROM `cf_figure_order_m` WHERE `order_id` IN (".implode(',', $oIds).") AND `has_sms` = 0 ";
$fom = getAlls($sql);

if (empty($fom)){
    echo "No Data Need Send!".PHP_EOL;
    die();
}

$svIds = $ltIds = $snIds = array();

foreach ($fom as $fVal){
    if ($fVal['measure'] == 6 && $fVal['liangti_id']){
        $ltIds[$fVal['liangti_id']] = $fVal['liangti_id'];  //量体师IDS
        $ltSnIds[$fVal['id']]       = $fVal['liangti_id'];  //量体师关联主键ID
    }else{
        $snIds[$fVal['id']]        = $fVal['server_id'];    //门店关联主键ID
    }
    $svIds[$fVal['server_id']] = $fVal['server_id'];//门店IDS
}

$sql = " SELECT * FROM `cf_serve` WHERE `idserve` IN (".implode(',', $svIds).")";
$sv = getAlls($sql);

if (!$sv){
    echo "No Serve Can Select!".PHP_EOL;
    die();
}

foreach ($sv as $sVal){
    $dzIds[$sVal['userid']] = $sVal['userid'];   //店长IDS
    $svToU[$sVal['idserve']] = $sVal['userid'];  //门店关联店长
    
    $serve[$sVal['idserve']] = $sVal;
}

include($root . '/includes/xinge/xinggemeg.php');
$push = new XingeMeg();

//给店长推送通知
if ($snIds){
    $sql  = " SELECT * FROM `cf_member` WHERE `user_id` IN (".implode(',', $dzIds).")";
    $user = getAlls($sql);
    foreach ($user as $uVal){
        $dzs[$uVal['user_id']] = $uVal;
    }
    foreach ($snIds as $sfId=>$svId){
        // 门店存在 && 店长存在 && 店长有token
        if (isset($svToU[$svId]) && isset($dzs[$svToU[$svId]]) && $dzs[$svToU[$svId]]['user_token'] ){
            $push->toMasterXinApp($dzs[$svToU[$svId]]['user_token'], '【mfd】预约量体提醒', '你有新的订单需要指派', array('url_type'=>'figure', 'location_id'=>$sfId));
        }
    }
}

//给量体师推送通知
if(!empty($ltIds) && !empty($ltSnIds)){
    $sql  = " SELECT * FROM `cf_member` WHERE `user_id` IN (".implode(',', $ltIds).")";
    $liangti = getAlls($sql);
    foreach ($liangti as $lVal){
        $lts[$lVal['user_id']] = $lVal;
    }
    
    foreach ($ltSnIds as $ltK=>$ltV){
        if (isset($lts[$ltV]) && $lts[$ltV]['user_token']){
            $push->toMasterXinApp($lts[$ltV]['user_token'], '【mfd】预约量体提醒', '你有一个量体派工任务', array('url_type'=>'figure', 'location_id'=>$ltK));
        }
    }
}

//给客户发短信
$noon = array('am'=>'上午','pm'=>'下午');

foreach ($fom as $fV){
    
    if($fV['phone'] || preg_match('/^1[34578][0-9]{9}$/', $fV['phone']) && isset($serve[$fV['server_id']])){
        
        $sms_msg = "您预约".$fV['time'].$noon[$fV['time_noon']]."到".$serve[$fV['server_id']]['serve_name']."量体，地址：".$serve[$fV['server_id']]['serve_address']."，电话：".$serve[$fV['server_id']]['store_mobile'];
        
        $rs = SendSms($fV['phone'], $sms_msg);

        if($rs && $rs > 0){
            $usql = "UPDATE cf_figure_order_m SET has_sms = 1  WHERE order_sn= '{$fV['order_sn']}' AND has_sms = '0'";
            mysql_query($usql);
        }
    }
}


echo $end = microtime(true);
echo PHP_EOL;
echo $end-time();
echo PHP_EOL;
echo PHP_EOL;


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

