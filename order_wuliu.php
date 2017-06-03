<?php


define('ROOT_PATH', dirname(__FILE__));
include(ROOT_PATH . '/eccore/router.php');
include(ROOT_PATH . '/eccore/rctailor.php');

/* 定义配置信息 */
ecm_define(ROOT_PATH . '/data/config.inc.php');

//定时获取物流单号
//require './cron/edbdemo.php';
include(ROOT_PATH . '/cron/edbdemo.php');

$cl=new EdbDemo();
$wuliu=$cl->edbTradeGet(); //获取物流



?>
