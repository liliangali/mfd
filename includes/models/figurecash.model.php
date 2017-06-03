<?php
	/**
	 *提现模型
	 * @author liang.li <1184820705@qq.com>
	 * @version 1.0
	 * @copyright Copyright 2014 caifeng.com
	 * @package cash.app.php
	*/
	class figurecashModel extends BaseModel
	{
		var $table  = 'figure_cash';
		var $prikey = 'id';
		var $_name  = 'figurecash';
		
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
		 	$cash_money = $data['cash_money'];
		 	
		 	//===== member表的money和frozen字段减去和加上提现的金额 =====
		 	$member_mod = m('member');
		 	$user = $member_mod->get($data['user_id']);
		 	if ($cash_money > $user['money']) 
		 	{
		 		return false;
		 	}
		 	/* $money = $user['money'] - $cash_money;
		 	$frozen = $user['frozen'] + $cash_money;
		 	
		 	
		 	if(!$member_mod->edit($user['user_id'],array('money'=>$money)))
		 	{
		 		return false;
		 	}
		 	
		 	if(!$member_mod->edit($user['user_id'],array('frozen'=>$frozen)))
		 	{
		 		return false;
		 	} */
		 	$money_mod = m('membermoney');
		 	$res = $money_mod->change_money($data['user_id'], -$cash_money, 3);
		 	if (!$res) 
		 	{
		 		return false;
		 	}
		 	
		 	
		 	//===== 添加提现表 =====
		 	if (!$this->add($data)) 
		 	{
		 		return false;
		 	}
		 	
		 	#TODO  这里要往收支明细表里面写数据
		 	
		 	return true;
		 	
		 	
		 }
		 
		 
		 
		 
		 /**
		  *后台客服审核 修改审核状态 并且扣除member表的冻结资金
		  *@author liang.li <1184820705@qq.com>
		  *@2015年6月2日
		  */
		 function submitCash($id,$stauts)
		 {
		     $earr = array('code' => 1,'msg' => '');
		     $sarr = array('code' => 0,'msg' => '');
		     $info = $this->get_info($id);
		     if (!$info)
		     {
		         $earr['msg'] = "无此数据";
		         return $earr;
		     }
		      
		      
		     if($this->edit($id,array('status'=>1,'audit_time'=>time())) === false)
		     {
		         $earr['msg'] = "无此数据";
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
		     if ($user_info['frozen'] < $info['cash_money'])
		     {
		         $earr['msg'] = "异常!此账号冻结资金少于 需要扣除的资金 ";
		         return $earr;
		     }
		     
		     if ($stauts == 1) 
		     {
		         if(!$member_mod->setDec("user_id=$user_id","frozen",$info['cash_money']))
		         {
		             $earr['msg'] = "扣除资金失败";
		             return $earr;
		         }
		     }
		     else 
		     {
		         if(!$member_mod->setDec("user_id=$user_id","frozen",$info['cash_money']))
		         {
		             $earr['msg'] = "扣除资金失败";
		             return $earr;
		         }
		         
		         if(!$member_mod->setInc("user_id=$user_id","money",$info['cash_money']))
		         {
		             $earr['msg'] = "返回资金余额失败";
		             return $earr;
		         }
		     }
		      
		     
		      
		     return $sarr;
		      
		      
		 }
		 
		 
		 
		 
	}