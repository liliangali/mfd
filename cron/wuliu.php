<?php



//定时获取物流单号
require '/edbdemo.php';

$cl=new EdbDemo();
$wuliu=$cl->edbTradeGet(); //获取物流



?>
