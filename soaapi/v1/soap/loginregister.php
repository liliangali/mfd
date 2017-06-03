<?php
ini_set("soap.wsdl_cache_enabled", "0");
include_once './../config.php';

$class  = 'Loginregister';
$act_arr = array('to_entrepreneur','if_login','test');
$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';

//判断一下是否是允许进行的操作
if (!in_array($action, $act_arr))
{
    echo '非法操作';
    return false;
    //返回false
}



$data = _g();
//=====  普通会员转创业者  =====
if ($action == 'to_entrepreneur') {

}
//=====  是否登录   =====
elseif ($action == 'if_login') {

}
//=====    =====
elseif ($action == 'test') {

}
//=====    =====
//elseif ($action == 'test') {
//}
//
////=====  test  =====
//elseif ($action == 'test') {
//}

else {
    echo "Lack of method ?action";
}


$arr = array ( 'data' => $data, );
$rs = getSoapClients($class, $action, $arr);
die($rs);



?>