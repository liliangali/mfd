<?php
	/**
	 *提现模型
	 * @author liang.li <1184820705@qq.com>
	 * @version 1.0
	 * @copyright Copyright 2014 caifeng.com
	 * @package cash.app.php
	*/
	class cashModel extends BaseModel
	{
		var $table  = 'cash';
		var $prikey = 'id';
		var $_name  = 'cash';
		
		 var $_relation = array(
		 		// 一条记录只能属于一个会员
		 		'belongs_to_member' => array(
		 				'model'             => 'member',
		 				'type'              => BELONGS_TO,
		 				'foreign_key'       => 'user_id',
		 				'reverse'           => 'has_cash',
		 		),
    	);
		 
		 /**
		 * 添加提现列表修改member的金额
		 * @version 1.0.0
		 * @author liang.li <1184820705@qq.com>
		 * @2015-2-7
		 */
		 function submit($data) 
		 {
		    $member_mod = m('member');
		    $order_cash_log_mod = m('ordercashlog');
		 	$cash_money = $data['cash_money'];
		 	$user = $data['user_info'];
		 	$user_id = $user['user_id'];
		 	$data['user_id'] = $user_id;
		 	unset($data['user_info']);
		 	//===== member表的money和frozen字段减去和加上提现的金额 =====
		 	if ($cash_money > $user['money']) 
		 	{
		 		return false;
		 	}
		 	$money = $user['money'] - $cash_money;
		 	$frozen = $user['frozen'] + $cash_money;
		 	if(!$member_mod->edit($user['user_id'],array('money'=>$money)))
		 	{
		 		return false;
		 	}
		 	
		 	if(!$member_mod->edit($user['user_id'],array('frozen'=>$frozen)))
		 	{
		 		return false;
		 	} 
		 	//===== 添加提现表 =====
		 	$cash_id = $this->add($data);
		 	if (!$cash_id) 
		 	{
		 		return false;
		 	}
		 	#TOOD  这里要往收支明细表里面写数据
		 	$order_cash_log_data['name'] = '用户提现扣除余额';
		 	$order_cash_log_data['order_id'] = $cash_id;
		 	$order_cash_log_data['user_id'] = $user_id;
		 	$order_cash_log_data['type'] = 4;
		 	$order_cash_log_data['minus'] = 5;
		 	$order_cash_log_data['cash_money'] = $cash_money;
		 	$order_cash_log_data['add_time'] = time();
		 	$order_cash_log_data['mark'] = '-';
		 	if (!$order_cash_log_mod->add($order_cash_log_data)) 
		 	{
		 	    return false;
		 	}
		 	
		 	return true;
		 }
		 
		 
	}