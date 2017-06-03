<?php
	$end = "\n";
	echo "start:time".date("Y-m-d H:i:s");
	$now_time = time();
	
	define('ROOT_PATH', dirname(dirname(__FILE__)));   //指到项目目录
	include_once('../eccore/router.php');
	include_once('../eccore/rctailor.php');
	ecm_define(ROOT_PATH . '/data/config.inc.php');//数据库配置统一使用主站
	
	include_once(ROOT_PATH . '/eccore/controller/app.base.php');
	include_once(ROOT_PATH . '/includes/ecapp.base.php');
	include_once(ROOT_PATH . '/eccore/model/model.base.php');//必须要在functions.php 前面 否则m方法不能用
	include_once(ROOT_PATH . '/app/frontend.base.php');
	include_once(ROOT_PATH . '/includes/functions.lib.php');
	include_once(ROOT_PATH . '/includes/global.lib.php');
	
	//获得已发货订单
	$mod_order  = m('order');
	$order_list = $mod_order->findAll(array(
		"conditions" => "status = 30 and ship_time != ''",
	));

	if(!$order_list)
		exit(" no data process...").$end;

	echo "data count:".count($order_list).$end;
	
	$mOsd = m('ordershipdelay');
	foreach($order_list as $key => $value) {

		//判断是否延期收货
		$delay_list  = $mOsd->findAll(array(
			'conditions' => "order_id=" . $value['order_id'],
			'order'      => 'delay_time desc',
			'count'      => true,
			'index_key'	 => '',
		));

		if($delay_list) {//如果有操作延期收获，则使用延期后的时间比较
			$delay_time_total = 0;
			foreach($delay_list as $val) {
				$delay_time_total = $delay_time_total + $val['delay_days'];
			}
			//系统自动确认时间：发货时间+自动确认收货时间（15天）+加上多次延长的时间
			$auto_time = $value['ship_time'] + 15*86400 + $delay_time_total*86400;
		} else {
			$auto_time = $value['ship_time'] + 15*86400;//自动确认收获时间		
		}

		//当前时间大于延期最后时间，执行自动确认收获操作
		if($now_time >= $auto_time) {
			$transaction = $mod_order->beginTransaction();
			$res = $mod_order->submit($value['order_id'], 1);
			if (!$res['code']) {
				$mod_order->rollback();
			}
			$mod_order->commit($transaction);
			//修改状态
			$result = $mod_order->edit('order_id='.$value['order_id'], array('status' => 40));
		}
		
	}
