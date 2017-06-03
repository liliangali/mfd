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
		    $sarr = array('code'=> 1);
		    $earr = array('code'=> 0,'msg'=>'错误');
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
		 	    $earr['msg'] = '1';
		 	    return $earr;
		 	}
		 	$money = $user['money'] - $cash_money;
		 	$frozen = $user['frozen'] + $cash_money;
		 	if(!$member_mod->edit($user['user_id'],array('money'=>$money),0))
		 	{
		 		 $earr['msg'] = '2';
		 	    return $earr;
		 	}
		 	
		 	if(!$member_mod->edit($user['user_id'],array('frozen'=>$frozen),0))
		 	{
		 		$earr['msg'] = '3';
		 	    return $earr;
		 	} 
		 	//===== 添加提现表 =====
		 	$cash_id = $this->add($data,false,0);
		 	if (!$cash_id) 
		 	{
		 		$earr['msg'] = '4';
		 	    return $earr;
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
		 	if (!$order_cash_log_mod->add($order_cash_log_data,false,0)) 
		 	{
		 	   $earr['msg'] = '5';
		 	   return $earr;
		 	}
		 	
		 	return $sarr;
		 }
		 
		 
		 /**
		 *后台客服审核 修改审核状态 并且扣除member表的冻结资金
		 *@author liang.li <1184820705@qq.com>
		 *@2015年6月2日
		 */
		 function submitCash($id,$status) 
		 {
		     $cash_tax = 0.2;
		     $earr = array('code' => 1,'msg' => '');
		     $sarr = array('code' => 0,'msg' => '');
		     $info = $this->get_info($id);
		     if (!$info) 
		     {
		         $earr['msg'] = "无此数据";
		         return $earr;
		     }
		     
		     
		     if($this->edit($id,array('status'=>$status),0) == false)
		     {
		         $earr['msg'] = "未知错误";
		         return $earr;
		     }
		     
		     $member_mod = m('member');
		     $user_id = intval($info['user_id']);
		     if (!$user_id) 
		     {
		        $earr['msg'] = "无此数据";
		         return $earr;
		     }
		     
		     $user_info = $member_mod->get_info($user_id);
		     
		     
		     if ($info['type'] == 0) //=====  余额提现  =====
		     {
		         if ($user_info['frozen'] < $info['cash_money'])
		         {
		             $earr['msg'] = "异常!此账号冻结资金少于 需要扣除的资金 ";
		             return $earr;
		         }
		         if ($status == 1) 
		         {
		             if(!$member_mod->setDec("user_id=$user_id","frozen",$info['cash_money']))
		             {
		                 $earr['msg'] = "扣除资金失败";
		                 return $earr;
		             }
		             
		             //=====  xinran.zhao log表加入  =====
		             get_client_finance($user_id,'',2,$info['cash_money']);
					 //=====  加入余额提现日志  =====
		             //加入系统消息
					 sendSystem($info['user_id'], '16', '余额提现', '您的提现审核通过，预计1~3个工作日到账，请注意查收！');
		         }
		         else 
		         {
		             if(!$member_mod->setDec("user_id=$user_id","frozen",$info['cash_money']))
		             {
		                 $earr['msg'] = "扣除冻结资金失败";
		                 return $earr;
		             }
		             if(!$member_mod->setInc("user_id=$user_id","money",$info['cash_money']))
		             {
		                 $earr['msg'] = "余额资金恢复失败";
		                 return $earr;
		             }
		             
		             $cashLogs[] = array(
		                 'name'        => '余额提现失败返还余额',
		                 'order_id'    => $id,
		                 'user_id'     => $info['user_id'],
		                 'minus'       => 5,
		                 'cash_money'  => $info['cash_money'],
		                 'add_time'    => time(),
		                 'order_money' => $info['money'],//帐号余额
		                 'mark'        => '+',
		                 'type'        => 4,
		             );
		             $order_cash_log = m('ordercashlog');
		             if (!$order_cash_log->add($cashLogs,false,0))
		             {
		                 $earr['msg'] = "order_cash_log操作失败";
		                 return $earr;
		             }
		            
		         }
		     }
		     else //=====  收益提现  =====
		     {
		          $earr['msg'] = "收益不可提现";
		           return $earr;
		        /*  if ($user_info['profit_frozen'] < $info['cash_money'])
		         {
		             $earr['msg'] = "异常!此账号冻结资金少于 需要扣除的资金 ";
		             return $earr;
		         }
		         
		         if ($status == 1) 
		         {
		             if(!$member_mod->setDec("user_id=$user_id","profit_frozen",$info['cash_money']))
		             {
		                 $earr['msg'] = "扣除资金失败";
		                 return $earr;
		             }
		             
		             //=====  把20%的税作为现金券保存起来  =====
		             $cash_voucher = $info['cash_money'] * $cash_tax;
		             $data['name'] = '收益提现所得';
		             $data['order_id'] = $info['id'];
		             $data['user_id'] = $info['user_id'];
		             $data['cash_money'] = $cash_voucher;
		             $data['add_time'] = time();
		             $data['type'] = 2;
		             $order_cash_log = m('ordercashlog');
		             if (!$order_cash_log->add($data))
		             {
		                 $earr['msg'] = "现金券操作失败";
		                 return $earr;
		             }
		              
		             if(!$member_mod->setInc("user_id=$user_id","cash_voucher",$cash_voucher))
		             {
		                 $earr['msg'] = "添加现金券失败";
		                 return $earr;
		             }
		             
		         }
		         else 
		         {
		             if(!$member_mod->setDec("user_id=$user_id","profit_frozen",$info['cash_money']))
		             {
		                 $earr['msg'] = "扣除资金失败";
		                 return $earr;
		             }
		             if(!$member_mod->setInc("user_id=$user_id","profit",$info['cash_money']))
		             {
		                 $earr['msg'] = "余额资金回复失败";
		                 return $earr;
		             }
		         } */
		     }
		     
		     
		     return $sarr;
		     
		     
		 }
		 
	}