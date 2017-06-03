<?php
chdir(dirname(__FILE__));// cd 到php脚本所在的目录
$end = "\r\n";
die();

//header("Content-type: text/html; charset=gbk");
echo "start:time".date("Y-m-d H:i:s").$end;
$start_time = time();
include_once 'order_func.php';
initDb();

$sql = "SELECT m.*,o.status FROM cf_figure_order_m as m,cf_order as o WHERE (m.liangti_state = '0' OR m.liangti_state = '1') AND m.has_sms = '0' AND m.order_id = o.order_id AND o.status=20 ";

$data_list = getAlls($sql);

if(!$data_list)
    exit(" no data figure_order_m...".$end);

/* foreach ($data_list as $row){
    $oSn[$row['order_sn']] = $row['order_sn'];
    $oId[$row['order_id']] = $row['order_id'];
} */

$today = strtotime(date('Y-m-d'));
$serves = $users = $figure = array();
foreach ($data_list as $row){
    if($row['time'] && $time = strtotime($row['time']) ){
        if (($time-$today) == 86400){
            //echo $row['id'].'--'.$time.$end;
            $figure[$row['id']] = $row;
        }
    }
}

if ($figure) {
    foreach ($figure as $row){
        if($row['server_id']){
            $svIds[$row['server_id']] = $row['server_id'];
        }
        if($row['userid']){
            $uIds[$row['userid']] = $row['userid'];
        }
    }
}else{
    exit(" no data figure_sms...".$end);
}

if($svIds){
    $svSql = "SELECT * FROM `cf_serve` WHERE `idserve` IN ('".implode("','",$svIds)."') ";
    $temServes = getAlls($svSql);
    foreach ($temServes as $row){
        $serves[$row['idserve']] = $row;
    }
}
if($uIds){
    $uSql  = "SELECT * FROM `cf_member` WHERE `user_id` IN ('".implode("','",$uIds)."') ";
    $temUsers = getAlls($uSql);
    foreach ($temUsers as $row){
        $users[$row['user_id']] = $row;
    }
}
$success = $failure = 0;
$noon = array('am'=>'上午','pm'=>'下午');
$fail = '';
foreach ($figure as $row){
    //$row['phone'] = '15936722475';
    //$row['phone'] = '18611479886';
    //$row['phone'] = '18510332021';
    
    if(!$row['phone'] || !preg_match('/^1[34578][0-9]{9}$/', $row['phone'])){
        continue;
    }
    if(!$row['server_id'] || !$row['userid']){
        continue;
    }
    
     $msg = "尊敬的客户，您预约".$row['time'].$noon[$row['time_noon']]."到".$serves[$row['server_id']]['serve_name']."量体，请做好出行安排，店铺地址：".$serves[$row['server_id']]['serve_address']."，联系电话：".$serves[$row['server_id']]['store_mobile'];
     $rs = SendSms($row['phone'], $msg);
     
     if(!$rs || $rs < 0){
        $failure++;
        $fail .= $row['order_sn'].',';
     }else{
         $usql = "UPDATE cf_figure_order_m SET has_sms = 1  WHERE order_sn= '{$row['order_sn']}' AND has_sms = '0'";
         mysql_query($usql);
         $success++;
     }
     sleep(1);
}



echo " success count:".$success.$end;
echo " failure count:".$failure." That is ".$fail.$end;


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

